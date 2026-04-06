<?php
namespace App\Services;

use Dompdf\Dompdf;
use chillerlan\QRCode\QRCode;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Models\Payment\OrderItem;
use App\Models\Payment\Order;

class TicketFulfillmentService implements ITicketFulfillmentService
{
    public function fulfillTicketsForOrder(int $orderId): void
    {
        // Logic to mark tickets as fulfilled in the database
    }

    public function generateQrCode(OrderItem $item): string
    {
        $qrData = "Ticket for Order Item ID: {$item->orderitem_id}, Hash: {$item->qr_code_hash}";
        $img = '<img style="width: 150px; height: 150px;" src="' . (new QRCode)->render($qrData) . '" alt="QR Code" />';
        return $img;
    }

    /**
     * Generate a PDF from HTML.
     *
     * @param bool   $download Stream the PDF to the browser (true) or suppress output (false).
     * @param bool   $save     Persist the file to disk.
     * @param string $savePath Override the save directory. Defaults to the private storage dir.
     */
    public function generatePDF(
        $html,
        $filename,
        $paperSize   = 'A4',
        $orientation = 'landscape',
        $download    = false,
        $save        = true,
        $savePath    = ''
    ): void {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paperSize, $orientation);
        $dompdf->render();
        $output = $dompdf->output();

        if ($save) {
            $dir = $savePath !== '' ? $savePath : $this->storageDir();
            file_put_contents($dir . $filename . '.pdf', $output);
        }

        if ($download) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
            echo $output;
        }
    }

    public function generatePDFName(Order $order): string
    {
        return 'Tickets_' . $order->reference_number . '_' . date('YmdHis');
    }

    public function generatePDFAndReturnPath($html, Order $order, bool $download, bool $save, string $savePath = ''): string
    {
        $fileName = $this->generatePDFName($order);
        $dir      = $savePath !== '' ? $savePath : $this->storageDir();
        $this->generatePDF($html, $fileName, 'A4', 'landscape', $download, $save, $dir);
        return $dir . $fileName . '.pdf';
    }

    public function getTicketPdfPath(string $filename): string
    {
        return $this->storageDir() . $filename;
    }

    public function isTicketPdfReady(string $filename): bool
    {
        return !empty($filename) && file_exists($this->getTicketPdfPath($filename));
    }

    /**
     * Returns the absolute path to the private ticket storage directory,
     * creating it if it does not already exist.
     */
    private function storageDir(): string
    {
        // Store PDFs in a backend-only directory (not web-accessible)
        $dir = __DIR__ . '/../../TicketPDFs/';

        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException("Unable to create ticket storage directory: {$dir}");
        }

        return $dir;
    }
}