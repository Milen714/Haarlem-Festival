<?php
namespace App\Services\Interfaces;
use App\Models\Enums\OrderStatus;
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
use App\Models\User;
interface IOrderService
{
    public function createOrder(Order $order): bool;
    public function getOrderById(int $orderId): ?Order;
    public function getOrdersByUserId(int $userId): array;
    public function getOpenOrderByUserId(int $userId, ?array $statuses = null): ?Order;
    public function updateOrderStatus(int $orderId, OrderStatus $status): bool;
    public function addOrderItem(OrderItem $orderItem): bool;
    public function getOrderItemsByOrderId(int $orderId): array;
    public function createSessionCart(): Order;
    public function getSessionCart(): ?Order;
    public function clearSessionCart(): void;
    public function persistSessionCart(Order $order, User $user, bool $ticketsAlreadyLocked = false): int;
    public function getOrderByStripeCheckoutSessionId(string $sessionId): ?Order;
    public function setStripeCheckoutSessionId(int $orderId, string $sessionId): bool;
    public function addOrderItemToSessionCart(OrderItem $item): void;
    public function hydrateSessionCart(Order $order): void;
    public function hydrateSessionCartFormDbOnLogin(User $user): void;
    public function getOrderItemFromCartBySessionItemId(Order $cart, int $sessionOrderItemId): ?OrderItem;
    public function removeOrderItemFromSessionCart(int $orderItemId): void;
    public function updateOrderItemQuantity(OrderItem $orderItem): bool;
    public function updateOrderItemInSessionCart(int $sessionOrderItemId, int $newQuantity): void;
    
}