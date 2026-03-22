<?php
namespace App\Services;

use App\Models\Enums\OrderStatus;
use App\Models\Payment\Order;
use App\Models\User;
use App\Models\Payment\OrderItem;
use App\Repositories\Interfaces\IOrderRepository;
use App\Services\Interfaces\IOrderService;
use App\Repositories\OrderRepository;

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

    public function getPaidTicketsByUser(int $userId): array
    {
        return $this->orderRepository->getPaidTicketsByUser($userId);
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
    
    public function addOrderItemToSessionCart(OrderItem $item): void
    {
        $cart = $this->getSessionCart();
        if ($cart === null) {
            $cart = $this->createSessionCart();
        }
        $cart->orderItems[] = $item;
        $cart->calculateTotals();
        $_SESSION['session_cart'] = $cart;
    }
    public function persistSessionCart(Order $order, User $user): int
    {
        $order->user = $user;
        $order->order_date = new \DateTime();
        $order->status = OrderStatus::Pending;
        $order->calculateTotals();

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
    public function hydrateSessionCartFormDbOnLogin(User $user): void{
        $dbCart = $this->getOpenOrderByUserId($user->id);
        if ($dbCart !== null) {
            $this->hydrateSessionCart($dbCart);
        }

    }

}