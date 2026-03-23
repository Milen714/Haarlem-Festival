<?php
namespace App\Services\Interfaces;
use App\Models\Payment\OrderItem;

interface ITicketFulfillmentService
{
    public function fulfillTicketsForOrder(int $orderId): void;
    public function generateQrCode(OrderItem $item): string;

    
}