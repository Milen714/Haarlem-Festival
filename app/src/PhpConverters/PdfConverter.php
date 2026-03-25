<?php
// Include autoloader 
require_once 'dompdf/autoload.inc.php'; 
 
// Reference the Dompdf namespace 
use Dompdf\Dompdf; 
 
// Instantiate and use the dompdf class 
$dompdf = new Dompdf();


$html='
<style>
    table {
        font-family: arial;
        width:400px;
        border-collapse: collapse;
    }
    td, th {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }
    tr:nth-child(even) {
        background-color: grey;
    }
</style>
<h1>Haarlem Festival</h1>
<h3>Invoice transaction</h3>
<table>
    <tr>
        <th>Name</th>
        <th>address</th>
        <th>Date of Birth</th>
        <th>Country</th>
    </tr>
    <tr>
        <td>Shamerock Haarlem</td>
        <td>23</td>
        <td>20-12-1960</td>
        <td>Germany</td>
    </tr>
    <tr>
        <td>Jane Dutch</td>
        <td>35</td>
        <td>20-12-1978</td>
        <td>Kenya</td>
    </tr>

</table>

';
// Load HTML content 
$dompdf->loadHtml($html);

 
// (Optional) Setup the paper size and orientation 
$dompdf->setPaper('A4', 'landscape'); 
 
// Render the HTML as PDF 
$dompdf->render(); 
$output = $dompdf->output();

//save to server
file_put_contents("documents/invoice.pdf", $output);

//auto download
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=invoice.pdf");
echo $output;

// Output the generated PDF to Browser 
$dompdf->stream();
?>