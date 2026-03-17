<?php

namespace App\Services;

use App\Models\Enums\OrderStatus;
use App\Models\Payment\Order;
use App\Models\User;
use App\Models\Payment\OrderItem;
use App\Repositories\Interfaces\IOrderRepository;
use App\Services\Interfaces\IOrderService;
use App\Repositories\OrderRepository;
use App\Services\TicketService;

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
    public function getOpenOrderByUserId(int $userId): ?Order
    {
        return $this->orderRepository->getOpenOrderByUserId($userId);
    }

    public function updateOrderStatus(int $orderId, OrderStatus $status): bool
    {
        return $this->orderRepository->updateOrderStatus($orderId, $status);
    }

    public function addOrderItem(OrderItem $orderItem): bool
    {
        return $this->orderRepository->addOrderItem($orderItem);
    }

    public function getOrderItemsByOrderId(int $orderId): array
    {
        return $this->orderRepository->getOrderItemsByOrderId($orderId);
    }
    public function createSessionCart(): Order
    {
        $order = new Order();
        $order->order_date = new \DateTime();
        $order->status = OrderStatus::Pending;
        $_SESSION['session_cart'] = $order;
        return $order;
    }
    public function getSessionCart(): ?Order
    {
        return $_SESSION['session_cart'] ?? null;
    }
    public function clearSessionCart(): void
    {
        unset($_SESSION['session_cart']);
    }

    // Adds an item to the session cart with a soft availability check., 
    //but does not persist anything to the database yet 
    //(no hard reservation until checkout).
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
        $cart->calculateTotals();
        $_SESSION['session_cart'] = $cart;
    }
    // This method should be called at the moment of checkout to 
    //persist the order and make hard seat reservations. 
    //It will throw if any item cannot be reserved, in which case the caller should inform the user 
    //and not persist the order.
    public function persistSessionCart(Order $order, User $user): int
    {
        $order->user       = $user;
        $order->order_date = new \DateTime();
        $order->status     = OrderStatus::Pending;
        $order->calculateTotals();

        foreach ($order->orderItems as $item) {
            $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;
            if ($ticketTypeId === null) {
                continue;
            }

            $reserved = $this->ticketService->reserveSeats($ticketTypeId, (int) $item->quantity);

            if (!$reserved) {

                throw new \OverflowException(
                    "Ticket type {$ticketTypeId} is sold out or has insufficient capacity. " .
                        "Please review your cart."
                );
            }
        }

        $this->orderRepository->createOrder($order);  // sets $order->order_id

        foreach ($order->orderItems as $item) {
            if ($item->order_id === null) {
                $item->order_id = $order->order_id;
                $this->orderRepository->addOrderItem($item);
            }
        }

        return $order->order_id;
    }
    public function hydrateSessionCart(Order $order): void
    {
        $_SESSION['session_cart'] = $order;
    }
    public function hydrateSessionCartFormDbOnLogin(User $user): void
    {
        $dbCart = $this->getOpenOrderByUserId($user->id);
        if ($dbCart !== null) {
            $this->hydrateSessionCart($dbCart);
        }
    }
}
