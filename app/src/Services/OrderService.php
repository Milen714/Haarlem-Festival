<?php

namespace App\Services;

use App\Models\Enums\OrderStatus;
use App\Models\Enums\TicketSchemeEnum;
use App\Models\Payment\Order;
use App\Models\User;
use App\Models\Payment\OrderItem;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\OrderRepository;
use App\Services\Interfaces\IOrderService;

class OrderService implements IOrderService
{
    private IOrderRepository $orderRepository;
    private TicketService $ticketService;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->ticketService   = new TicketService();
    }
    public function createOrder(Order $order): bool
    {
        return $this->orderRepository->createOrder($order);
    }

    public function getOrderById(int $orderId): ?Order
    {
        return $this->orderRepository->getOrderById($orderId);
    }

    public function getOrdersByUserId(int $userId): array
    {
        return $this->orderRepository->getOrdersByUserId($userId);
    }
    public function getOpenOrderByUserId(int $userId, ?array $statuses = null): ?Order
    {
        $order = $this->orderRepository->getOpenOrderByUserId($userId, $statuses);
        if ($order === null) {
            return null;
        }

        foreach ($order->orderItems as $item) {
            $item->sessionOrderitem_id = array_search($item, $order->orderItems) + 1;
        }
        return $order;
    }

    public function updateOrderStatus(int $orderId, OrderStatus $status): bool
    {
        return $this->orderRepository->updateOrderStatus($orderId, $status);
    }

    public function addOrderItem(OrderItem $orderItem): bool
    {
        return $this->orderRepository->addOrderItem($orderItem);
    }
    public function updateOrderItemQuantity(OrderItem $orderItem): bool
    {
        return $this->orderRepository->updateOrderItemQuantity($orderItem);
    }

    public function getOrderItemsByOrderId(int $orderId): array
    {
        return $this->orderRepository->getOrderItemsByOrderId($orderId);
    }

    public function getPaidTicketsByUser(int $userId): array
    {
        return $this->orderRepository->getPaidTicketsByUser($userId);
    }
    public function createSessionCart(): Order
    {
        $order = new Order();
        $order->order_date = new \DateTime();
        $order->status = OrderStatus::In_Cart;
        if(!isset($_SESSION['session_cart'])){
            $_SESSION['session_cart'] = $order;
        }
        return $order;
    }
    public function getSessionCart(): ?Order
    {
        $cart = $_SESSION['session_cart'] ?? null;
        if ($cart){
            $this->assignSessionOrderItemIds($cart);
        }
        return $cart;
    }
    public function clearSessionCart(): void
    {
        unset($_SESSION['session_cart']);
    }
    private function assignSessionOrderItemIds(Order $cart): void
    {
        foreach ($cart->orderItems as $index => $item) {
            $item->sessionOrderitem_id = $index;
        }
    }

    // Soft availability check then adds to session cart. Persisted carts also hard-lock the seat immediately.
    public function addOrderItemToSessionCart(OrderItem $item): void
    {
        $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;

        if ($ticketTypeId !== null) {
            $available = $this->ticketService->getAvailableCapacity($ticketTypeId);

            // Also count seats already in the cart for this ticket type so the
            // soft check accounts for items the user already has in their session.
            $alreadyInCart = 0;
            $cart = $this->getSessionCart();
            if ($cart !== null) {
                foreach ($cart->orderItems as $existing) {
                    if (($existing->ticket_type?->ticket_type_id ?? null) === $ticketTypeId) {
                        $alreadyInCart += (int) $existing->quantity;
                    }
                }
            }

            if (($item->quantity + $alreadyInCart) > $available) {
                $remaining = max(0, $available - $alreadyInCart);
                throw new \OverflowException(
                    "Only {$remaining} seat(s) remaining for this ticket type."
                );
            }
        }

        if (!isset($cart)) {
            $cart = $this->getSessionCart();
        }
        if ($cart === null) {
            $cart = $this->createSessionCart();
        }
        $cart->orderItems[] = $item;
        $this->assignSessionOrderItemIds($cart);
        $cart->calculateTotals();
        
        if($cart->order_id !== null){
            // Cart is already in the DB — lock the seat now
            $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;
            if ($ticketTypeId !== null) {
                $reserved = $this->ticketService->reserveSeats($ticketTypeId, (int)$item->quantity);
                if (!$reserved) {
                    throw new \OverflowException(
                        "Ticket type {$ticketTypeId} is sold out or has insufficient capacity."
                    );
                }
            }
            array_last($cart->orderItems)->order_id = $cart->order_id;
            $this->orderRepository->addOrderItem($item);
            $this->orderRepository->updateOrderTotals($cart);
        }
        $this->hydrateSessionCart($cart);
    }
    // Saves the session cart to the DB and returns the new order ID.
    public function persistSessionCart(Order $order, User $user, bool $ticketsAlreadyLocked = false): int
    {
        $order->user = $user;
        $order->order_id = null;
        $order->order_date = new \DateTime();
        $order->status = OrderStatus::In_Cart;
        $order->calculateTotals();

        if (!$ticketsAlreadyLocked) {
            // Build items array and reserve all seats in a single transaction.
            // For pass-type tickets, expand to all sibling ticket types sharing the same scheme
            // so that 1 pass purchase deducts 1 slot from every schedule for that pass.
            $items = [];
            foreach ($order->orderItems as $item) {
                $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;
                $schemeEnum   = $item->ticket_type?->ticket_scheme?->scheme_enum ?? null;
                $schemeId     = $item->ticket_type?->ticket_scheme?->ticket_scheme_id ?? null;
                $quantity     = (int)$item->quantity;

                if ($ticketTypeId === null) {
                    continue;
                }

                if ($schemeEnum !== null && TicketSchemeEnum::isPassType($schemeEnum) && $schemeId !== null) {
                    $siblingIds = $this->ticketService->getTicketTypeIdsBySchemeId($schemeId);
                    foreach ($siblingIds as $siblingId) {
                        $items[] = ['ticket_type_id' => (int)$siblingId, 'quantity' => $quantity];
                    }
                } else {
                    $items[] = ['ticket_type_id' => $ticketTypeId, 'quantity' => $quantity];
                }
            }

            if (!empty($items) && !$this->ticketService->reserveMultiple($items)) {
                throw new \OverflowException(
                    'One or more ticket types are sold out or have insufficient capacity. Please review your cart.'
                );
            }
        }

        $this->orderRepository->createOrder($order);  // sets $order->order_id

        foreach ($order->orderItems as $item) {
            $item->orderitem_id = null;
            $item->order_id = $order->order_id;
            $this->orderRepository->addOrderItem($item);
        }

        if ($order->order_id === null) {
            throw new \RuntimeException('Failed to persist session cart: missing order ID after insert.');
        }

        return $order->order_id;
    }
    public function hydrateSessionCart(Order $order): void
    {
        $_SESSION['session_cart'] = $order;
    }
    public function getOrderByStripeCheckoutSessionId(string $sessionId): ?Order
    {
        return $this->orderRepository->getOrderByStripeCheckoutSessionId($sessionId);
    }

    public function setStripeCheckoutSessionId(int $orderId, string $sessionId): bool
    {
        return $this->orderRepository->setStripeCheckoutSessionId($orderId, $sessionId);
    }

    public function hydrateSessionCartFormDbOnLogin(User $user): void{
        $openStatuses = [OrderStatus::In_Cart, OrderStatus::Pending_Payment];
        $dbCart = $this->getOpenOrderByUserId($user->id, $openStatuses);
        $sessionCart = $this->getSessionCart();
        $sessionCartHasItems = $sessionCart !== null && count($sessionCart->orderItems) > 0;

        if ($sessionCartHasItems) {
            // Cancel all currently open orders before persisting the session cart as the new active one.
            while ($dbCart !== null) {
                $cancelled = $this->updateOrderStatus($dbCart->order_id, OrderStatus::Cancelled);
                if (!$cancelled) {
                    throw new \RuntimeException("Failed to cancel open order ID {$dbCart->order_id}.");
                }
                $dbCart = $this->getOpenOrderByUserId($user->id, $openStatuses);
            }

            $newOrderId = $this->persistSessionCart($sessionCart, $user);
            $persistedOrder = $this->getOrderById($newOrderId);
            if ($persistedOrder !== null) {
                $this->hydrateSessionCart($persistedOrder);
            } else {
                $this->hydrateSessionCart($sessionCart);
            }
            return;
        }

        if ($dbCart !== null) {
            $this->hydrateSessionCart($dbCart);
        }
    }
    public function getOrderItemFromCartBySessionItemId(Order $cart, int $sessionOrderItemId): ?OrderItem
    {
        foreach ($cart->orderItems as $item) {
            if ($item->sessionOrderitem_id === $sessionOrderItemId) {
                return $item;
            }
        }
        return null;
    }
    public function removeOrderItemFromSessionCart(int $orderItemId): void
    {
        $cart = $this->getSessionCart();
        if ($cart === null) {
            throw new \RuntimeException('No session cart found when trying to remove order item.');
        }
        $itemToRemove = $this->getOrderItemFromCartBySessionItemId($cart, $orderItemId);
        
        array_splice($cart->orderItems, $itemToRemove->sessionOrderitem_id, 1);
        
        $this->assignSessionOrderItemIds($cart);
        $cart->calculateTotals();
        $this->hydrateSessionCart($cart);
        if ($cart->order_id !== null && $itemToRemove->orderitem_id !== null) {
            $this->orderRepository->removeOrderItem($itemToRemove->orderitem_id);
            $this->orderRepository->updateOrderTotals($cart);
        }
        
    }
    public function updateOrderItemInSessionCart(int $sessionOrderItemId, int $newQuantity): void
    {
        $cart = $this->getSessionCart();
        if ($cart === null) {
            throw new \RuntimeException('No session cart found when trying to update order item.');
        }
        $itemToUpdate = $this->getOrderItemFromCartBySessionItemId($cart, $sessionOrderItemId);
        if ($itemToUpdate === null) {
            throw new \RuntimeException("No order item found in cart with sessionOrderitem_id {$sessionOrderItemId}.");
        }
        $itemToUpdate->calculateTotalPriceWithNewQuantity($newQuantity);

        $cart->calculateTotals();
        $this->hydrateSessionCart($cart);

        if ($cart->order_id !== null && $itemToUpdate->orderitem_id !== null) {
            $this->orderRepository->updateOrderItemQuantity($itemToUpdate);
            $this->orderRepository->updateOrderTotals($cart);
        }
    }

    public function generateTicketHashes(int $orderId): void
    {
        $items = $this->orderRepository->getOrderItemsByOrderId($orderId);

        foreach ($items as $item) {
            $uniqueHash = bin2hex(random_bytes(8));

            $this->orderRepository->updateItemHash($item->orderitem_id, $uniqueHash);
        }
    }

    public function getOrderItemByHash(string $hash): ?OrderItem
    {
        return $this->orderRepository->getOrderItemByHash($hash);
    }

    public function markAsScanned(int $orderItemId): bool
    {
        return $this->orderRepository->markAsScanned($orderItemId);
    }

    public function getPaidOrderItemsByUserId(int $userId): array
    {
        $orders = $this->getOrdersByUserId($userId);
        $paidItems = [];

        foreach ($orders as $order) {
            if ($order->status === OrderStatus::Fulfilled) {
                $items = $this->getOrderItemsByOrderId($order->order_id);
                $paidItems = array_merge($paidItems, $items);
            }
        }

        return $paidItems;
    }
        public function getOrdersWhereStatusIn(array $statuses): array
        {
            return $this->orderRepository->getOrdersWhereStatusIn($statuses);
        }
}