<?php 
namespace App\Services;
use Dompdf\Dompdf;

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
    /**
 * Generate and output a PDF
 * 
 * @param string $html The HTML content to convert to PDF
 * @param string $filename The filename for the PDF (without .pdf extension)
 * @param string $paperSize The paper size (default: 'A4')
 * @param string $orientation The orientation (default: 'landscape')
 * @param bool $download Whether to download the PDF (default: true)
 * @param bool $save Whether to save to server (default: false)
 * @param string $savePath The path to save the PDF (default: 'documents/')
 * @return void
 */

public function generatePDF($html, $filename, $paperSize ='A4', $orientation = 'landsacpe', 
$download = true, $save = true, $savePath = 'Assets/documents/') : void
{
    $dompdf = new Dompdf();

    // this loads the html content
    $dompdf->loadHtml($html);

    //sets up the size and orientation of orientation
    $dompdf->setPaper($paperSize, $orientation);

    //renders the PDF 
    $dompdf->render();
    $output = $dompdf->output();

    //if you wish to save it to the server
    if ($save) {
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }
        file_put_contents($savePath . $filename . '.pdf', $output);
    }

    if($download){
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=" . $filename . ".pdf");
        echo $output;
        exit;
    }
}
}