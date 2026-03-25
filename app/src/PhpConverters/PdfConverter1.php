<?php 
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

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

function generatePDF($html, $filename, $paperSize ='A4', $orientation = 'landsacpe', $download = true, $save = false, $savePath = 'Assets/documents/'){
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

//Call this function with both true to save and download other was one to false
generatePDF($html, 'invoice', 'A4', 'landscape', true, true, 'documents/');
?>