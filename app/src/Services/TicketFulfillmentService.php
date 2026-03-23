<?php 
namespace App\Services;

use chillerlan\QRCode\QRCode;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Models\Payment\OrderItem;

class TicketFulfillmentService implements ITicketFulfillmentService
{
    public function fulfillTicketsForOrder(int $orderId): void
    {
        // Logic to mark tickets as fulfilled in the database
        // This could involve updating ticket status, sending notifications, etc.
    }

    public function generateQrCode(OrderItem $item): string
    {
        $qrData = "Ticket for Order Item ID: {$item->orderitem_id}, Hash: {$item->qr_code_hash}";
        
        $img = '<img style="width: 150px; height: 150px;" src="'.(new QRCode)->render($qrData).'" alt="QR Code" />';
        return $img;
    }
}