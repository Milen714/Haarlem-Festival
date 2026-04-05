<?php
namespace App\Services;

use Dompdf\Dompdf;
use chillerlan\QRCode\QRCode;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Services\Interfaces\IMailService;
use App\Services\MailService;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;
use App\Models\Payment\OrderItem;
use App\Models\Payment\Order;
use App\Exceptions\ValidationException;

class TicketFulfillmentService implements ITicketFulfillmentService
{
    private IOrderService $orderService;
    private IMailService $mailService;
    private ILogService $logService;

    public function __construct() {
        $this->orderService = new OrderService();
        $this->mailService = new MailService();
        $this->logService = new LogService();
    }

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
    /**
     * Summary of generatePDFAndReturnPath
     * Generates a PDF from the provided HTML, saves it to disk, and returns the file path.
     * @param mixed $html
     * @param Order $order
     * @param bool $download
     * @param bool $save
     * @param string $savePath
     * @return string
     */
    public function generatePDFAndReturnPath($html, Order $order, bool $download, bool $save, string $savePath = ''): string
    {
        $fileName = $this->generatePDFName($order);
        $dir      = $savePath !== '' ? $savePath : $this->storageDir();
        $this->generatePDF($html, $fileName, 'A4', 'landscape', $download, $save, $dir);
        return $dir . $fileName . '.pdf';
    }
    // Helper methods for ticket PDF management 
    public function getTicketPdfPath(string $filename): string
    {
        return $this->storageDir() . $filename;
    }
    // Check if the ticket PDF is ready (exists on disk)
    public function isTicketPdfReady(string $filename): bool
    {
        return !empty($filename) && file_exists($this->getTicketPdfPath($filename));
    }

    /**
     * Returns the absolute path to the private ticket storage directory,
     * creating it if it does not already exist.
     * This directory is intended for storing generated ticket PDFs 
     * and should not be web-accessible for security reasons.
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

    /**
     * Generate ticket PDF, send email with attachment, and return PDF filename.
     * This method orchestrates the entire ticket fulfillment process for an order, including:
     * - Generating unique ticket hashes for QR codes
     * @param Order $order The order to generate tickets for
     * @param string $pdfHtml The rendered HTML for the PDF
     * @param string $emailHtml The rendered HTML for the email body
     * @return string The filename of the generated PDF (without path)
     */
    public function sendTicketEmail(Order $order, string $pdfHtml, string $emailHtml): string
    {
        $fileName      = $this->generatePDFName($order);
        $ticketPdfPath = $this->getTicketPdfPath($fileName . '.pdf');
        $mailTo        = $order->user->email ?? '';

        try {
            // Generate ticket hashes for QR codes
            $this->orderService->generateTicketHashes($order->order_id);

            // Re-fetch order with updated hashes
            $order = $this->orderService->getOrderById($order->order_id);
            $mailTo = $order->user->email ?? $mailTo;

            // Generate the PDF and save to disk
            $this->generatePDF(
                $pdfHtml,
                $fileName,
                'A4',
                'landscape',
                false,  // never stream PDF to HTTP response
                true    // always save to disk
            );

            // Update order with PDF path
            $order->ticket_pdf_path = $fileName . '.pdf';
            $this->orderService->updateOrderStatus($order->order_id, $order->status, $order->ticket_pdf_path);

            $this->logService->info('TicketFulfillment', 'PDF generated', ['path' => $ticketPdfPath]);

            // Send email with PDF attachment
            $this->mailService->sendEmail(
                $mailTo,
                "Your Festival Tickets - " . $order->reference_number,
                $emailHtml,
                [$ticketPdfPath]
            );

            $this->logService->info('TicketFulfillment', 'Ticket email sent', ['to' => $mailTo]);

        } catch (\Throwable $e) {
            $this->logService->error('TicketFulfillment', 'Failed to send ticket email', ['to' => $mailTo], $e->getTraceAsString());
        }

        return $fileName . '.pdf';
    }
    /**
     * Summary of validateAndGetTicketPdf
     * Validates that the ticket PDF is ready and returns the file path. 
     * Throws a ValidationException if the PDF is not ready.
     * @param string $pdfPath
     * @throws ValidationException
     * @return string
     */
    public function validateAndGetTicketPdf(string $pdfPath): string
    {
        if (!$pdfPath) {
            throw new ValidationException('Your tickets are still being generated. Please wait a few seconds and try again.');
        }

        if (!$this->isTicketPdfReady($pdfPath)) {
            throw new ValidationException('Your tickets are still being prepared. Please retry shortly.');
        }

        return $this->getTicketPdfPath($pdfPath);
    }
}