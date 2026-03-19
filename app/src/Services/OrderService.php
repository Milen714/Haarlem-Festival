<?php
namespace App\Services;

use App\Models\Enums\OrderStatus;
use App\Models\Payment\Order;
use App\Models\User;
use App\Models\Payment\OrderItem;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\OrderRepository;
use App\Services\Interfaces\IOrderService;

class OrderService implements IOrderService
{
    private IOrderRepository $orderRepository;
    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
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
        return $this->orderRepository->getOpenOrderByUserId($userId, $statuses);
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
        $order->status = OrderStatus::In_Cart;
        if(!isset($_SESSION['session_cart'])){
            $_SESSION['session_cart'] = $order;
        }
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
    
    public function addOrderItemToSessionCart(OrderItem $item): void
    {
        $cart = $this->getSessionCart();
        if ($cart === null) {
            $cart = $this->createSessionCart();
        }
        $cart->orderItems[] = $item;
        $cart->calculateTotals();
        
        if($cart->order_id !== null){
            array_last($cart->orderItems)->order_id = $cart->order_id;
            $this->orderRepository->addOrderItem($item);
        }
        $this->hydrateSessionCart($cart);
    }
    //this method maps the orderId of the passed in order with the last insterted order's id 
    public function persistSessionCart(Order $order, User $user): int
    {
        $order->user = $user;
        $order->order_id = null;
        $order->order_date = new \DateTime();
        $order->status = OrderStatus::In_Cart;
        $order->calculateTotals();

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
    public function removeOrderItemFromSessionCart(int $orderItemId): void
    {
        $cart = $this->getSessionCart();
        
    }

}