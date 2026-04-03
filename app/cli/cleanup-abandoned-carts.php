<?php
// app/cli/cleanup-abandoned-carts.php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php';

use App\Services\OrderService;
use App\Services\TicketService;
use App\Models\Enums\OrderStatus;

try {
    $orderService = new OrderService();
    $ticketService = new TicketService();

    // Get all pending/unpaid orders older than 30 minutes
    $abandonedOrders = $orderService->getOrdersWhereStatusIn([OrderStatus::In_Cart, OrderStatus::Pending_Payment, OrderStatus::Pending_Payment,]); // 30 minutes

    if (empty($abandonedOrders)) {
        echo "[" . date('Y-m-d H:i:s') . "] No abandoned carts found.\n";
        exit(0);
    }

    $releasedCount = 0;
    foreach ($abandonedOrders as $order) {
        try {
            if ($order->orderItems && count($order->orderItems) > 0) {
                // Release all tickets back to inventory
                $ticketService->releaseOrderItems($order->orderItems);
                
                // Mark order as cancelled
                $orderService->updateOrderStatus($order->order_id, OrderStatus::Cancelled);
                
                $releasedCount++;
                echo "[" . date('Y-m-d H:i:s') . "] Released order #{$order->order_id} with " . count($order->orderItems) . " items\n";
            }
        } catch (\Exception $e) {
            echo "[" . date('Y-m-d H:i:s') . "] ERROR: Failed to release order #{$order->order_id}: " . $e->getMessage() . "\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Cleanup complete. Released {$releasedCount} abandoned carts.\n";
    exit(0);

} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>