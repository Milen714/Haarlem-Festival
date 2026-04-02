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
    public function getPaidTicketsByUser(int $userId): array;
    public function getOpenOrderByUserId(int $userId, ?array $statuses = null): ?Order;
    public function updateOrderStatus(int $orderId, OrderStatus $status, ?string $pdf = null): bool;
    public function addOrderItem(OrderItem $orderItem): bool;
    public function getOrderItemsByOrderId(int $orderId): array;
    public function getOrderByStripeCheckoutSessionId(string $sessionId): ?Order;
    public function setStripeCheckoutSessionId(int $orderId, string $sessionId): bool;
    public function removeOrderItem(int $orderItemId): bool;
    public function updateOrderTotals(Order $order): bool;
    public function updateOrderItemQuantity(OrderItem $orderItem): bool;
    public function updateItemHash(int $orderItemId, string $hash): bool;
    public function markAsScanned(int $orderItemId): bool;
    public function getOrderItemByHash(string $hash): ?OrderItem;
    public function getOrdersWhereStatusIn(array $statuses): array;
}