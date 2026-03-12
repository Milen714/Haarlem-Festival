<?php
namespace App\Services;

use App\Models\Enums\OrderStatus;
use App\Models\Payment\Order;
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
}