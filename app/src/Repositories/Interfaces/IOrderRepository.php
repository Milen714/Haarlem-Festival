<?php
namespace App\Repositories\Interfaces;
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
use App\Models\Enums\OrderStatus;

interface IOrderRepository
{
    public function createOrder(Order $order): bool;
    public function getOrderById(int $orderId): ?Order;
    public function getOrdersByUserId(int $userId): array;
    public function updateOrderStatus(int $orderId, OrderStatus $status): bool;
    public function addOrderItem(OrderItem $orderItem): bool;
    public function getOrderItemsByOrderId(int $orderId): array;
}