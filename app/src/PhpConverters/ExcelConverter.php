<?php 
/**
 * Can generate CSV file from database without a library
 * @param PDO $pdo Database connection
 * @param string $query SQL query to execute
 * @param string $filename Filename without .csv extension
 * @param bool $download Whether to download the file (default: true)
 * @param bool $save Whether to save to server (default: false)
 * @param string $savePath Path to save the file (default: 'Assets/documents/')
 * @return bool
 * 
 */

function generateCSV($pdo, $query, $filename, $download = true, $save = false, $savePath = 'Assets/documents/'){
    try {
        $getData = $pdo->prepare($query);
        $getData->execute();
        $data = $getData->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            throw new Exception('No data returned from query');
        }

        //this will create a CSV in memory
        $output = fopen('php://memory', 'r+');

        //write header row, array of headers example name, country, date
        $headers = array_keys($data[0]);
        fputcsv($output, $headers);

        //writes the rows for the headers
        foreach($data as $row){
            fputcsv($output, array_values($row));
        }

        // get the csv content
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        //save to server if we want to 
        if($save){
            if (!is_dir($savePath)) {
                //0755 is the permissions 
                mkdir($savePath, 0755, true);
            }
            file_put_contents($savePath . $filename . '.csv', $csvContent);
        }
        //download the csv for the excelsheet
        if($download){
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachments; filename="' . $filename . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $csvContent;
            exit;
        }

        return true;

    } catch (Exception $e) {
        throw new Exception("Error: " . $e->getMessage());
    }
}

/**
 * 
 * Generate via HTML table in excel better formatting than CSV but still
 * no library
 * @param PDO $pdo Database connection
 * @param string $query SQL query to execute
 * @param string $filename Filename without extension
 * @param bool $download Whether to download (default: true)
 * @param bool $save Whether to save to server (default: false)
 * @param string $savePath Path to save (default: 'Assets/documents/')
 * @return bool
 */

function generateExcelViaHtml($pdo, $query, $filename, $download = true, $save = false, $savePath = 'Assets/documents/'){
    try {
        $getData = $pdo->prepare($query);
        $getData->execute();
        $data = $getData->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            throw new Exception('No data returned from query');
        }

        //Buid HTMl table
        $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
                    th { 
                        background-color: #4472C4; 
                        color: white; 
                        padding: 12px; 
                        border: 1px solid #ddd; 
                        font-weight: bold;
                        text-align: left;
                    }
                    td { 
                        padding: 8px; 
                        border: 1px solid #ddd; 
                    }
                    tr:nth-child(even) { 
                        background-color: #f9f9f9; 
                    }
                    tr:hover { 
                        background-color: #f0f0f0; 
                    }
                </style>
                </head>
                <body>
            <table>'
        ;

         //write header row, array of headers example name, country, date
        $headers = array_keys($data[0]);
        $html .= '<thead><tr>';
        foreach($headers as $header){
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead>';
        //add data rows
        $html .= '<tbody>';
        foreach($data as $row){
            $html .= '<tr>';
            foreach($row as $value){
                $html .= '<td>' . htmlspecialchars($value ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody</table> </body></html>';

        //save to server
        if($save){
            if (!is_dir($savePath)) {
                //0755 is the permissions 
                mkdir($savePath, 0755, true);
            }
            file_put_contents($savePath . $filename . '.csv', $html);
        }
        //download the csv for the excelsheet
        if($download){
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachments; filename="' . $filename . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $html;
            exit;
        }

        return true;
        
    } catch (Exception $e) {
        throw new Exception("Error: " . $e->getMessage());
    }
}

/**
 * example 
 * generateCSV($pdo, $query, 'orders', true, true/false, 'documents/');
 * generateExcelViaHtml($pdo, $query, 'orders', true, true/false, 'documents/');
 */