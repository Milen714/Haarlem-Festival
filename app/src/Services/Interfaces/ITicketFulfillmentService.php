<?php
namespace App\Services\Interfaces;
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;

interface ITicketFulfillmentService {
    public function generatePDF(string $htmlContent, string $fileName): void;
    public function fulfillTicketsForOrder(int $orderId): void;
    public function generateQrCode(OrderItem $item): string;
    public function generatePDFName(Order $order): string;
    public function generatePDFAndReturnPath($html, Order $order, bool $download, bool $save, string $savePath = ''): string;
    public function getTicketPdfPath(string $filename): string;
    public function isTicketPdfReady(string $filename): bool;
    public function sendTicketEmail(Order $order, string $pdfHtml, string $emailHtml): string;
}