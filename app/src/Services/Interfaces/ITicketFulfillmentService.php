<?php
namespace App\Services\Interfaces;
use App\Models\Payment\OrderItem;

interface ITicketFulfillmentService
{
    public function fulfillTicketsForOrder(int $orderId): void;
    public function generateQrCode(OrderItem $item): string;
    public function generatePDF($html, $filename, $paperSize ='A4', $orientation = 'landsacpe', 
$download = true, $save = false, $savePath = 'Assets/documents/') : void;

    
}