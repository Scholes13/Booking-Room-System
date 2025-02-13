<?php
// app/Services/BookingExportService.php
namespace App\Services;

use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class BookingExportService
{
    public function export($bookings)
    {
        $writer = new Writer();
        
        // Set headers untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="booking_report.xlsx"');
        
        $writer->openToFile('php://output');
        
        // Tambah header kolom
        $writer->addRow(Row::fromValues([
            'Nama',
            'Departemen',
            'Tanggal',
            'Jam Mulai',
            'Jam Selesai',
            'Ruang Meeting',
            'Deskripsi'
        ]));
        
        // Tambah data
        foreach ($bookings as $booking) {
            $writer->addRow(Row::fromValues([
                $booking->nama,
                $booking->department,
                $booking->date,
                $booking->start_time,
                $booking->end_time,
                $booking->meetingRoom->name,
                $booking->description
            ]));
        }
        
        $writer->close();
    }
}