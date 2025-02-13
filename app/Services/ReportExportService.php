<?php

namespace App\Services;

use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;

class ReportExportService
{
    /**
     * Export data to Excel with dynamic headers and data
     *
     * @param array $data Array of data to export
     * @param array $headers Column headers
     * @param string $filename Custom filename
     * @return void
     */
    public function export($data, array $headers, string $filename)
    {
        $writer = new Writer();
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $writer->openToFile('php://output');
        
        // Create header style
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(12);

        // Add headers
        $writer->addRow(Row::fromValues($headers, $headerStyle));
        
        // Add data rows
        foreach ($data as $row) {
            // Format row data
            $rowData = array_values($row); // Ensure sequential array
            $writer->addRow(Row::fromValues($rowData));
        }
        
        $writer->close();
    }
}