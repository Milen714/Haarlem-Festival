<?php
namespace App\Services\Interfaces;
use App\Models\Enums\OrderStatus;
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
interface IOrderService
{
    public function createOrder(Order $order): bool;
    public function getOrderById(int $orderId): ?Order;
    public function getOrdersByUserId(int $userId): array;
    public function updateOrderStatus(int $orderId, OrderStatus $status): bool;
    public function addOrderItem(OrderItem $orderItem): bool;
    public function getOrderItemsByOrderId(int $orderId): array;
}