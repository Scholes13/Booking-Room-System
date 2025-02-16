<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActivityExportService
{
    public function export($data, $headers, $filename)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $column++;
            }

            // Set data
            $row = 2;
            foreach ($data as $item) {
                $column = 'A';
                foreach ($item as $value) {
                    $sheet->setCellValue($column . $row, $value);
                    $column++;
                }
                $row++;
            }

            // Auto-size columns
            foreach (range('A', $column) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create file
            $writer = new Xlsx($spreadsheet);
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function formatEmployeeData($activities)
    {
        return $activities->map(function($activity) {
            $startDate = Carbon::parse($activity->start_datetime);
            $endDate = Carbon::parse($activity->end_datetime);
            $totalDays = $startDate->startOfDay()->diffInDays($endDate->startOfDay()) + 1;

            return [
                'Name' => $activity->name,
                'Department' => $activity->department,
                'Start Time' => $activity->start_datetime,
                'End Time' => $activity->end_datetime,
                'Total Days' => $totalDays . ' days',
                'Category' => $activity->activity_type,
                'Description' => $activity->description
            ];
        })->toArray();
    }

    public function formatDepartmentData($activities)
    {
        $departmentsData = [];
        
        foreach ($activities as $act) {
            $dept = $act->department ?: 'Unknown';
            if (!isset($departmentsData[$dept])) {
                $departmentsData[$dept] = [
                    'Department' => $dept,
                    'Total Activities' => 0,
                    'Hours Used' => 0,
                    'Total Days' => 0
                ];
            }
            
            $departmentsData[$dept]['Total Activities']++;
            
            $startDate = Carbon::parse($act->start_datetime);
            $endDate = Carbon::parse($act->end_datetime);
            
            $days = $startDate->startOfDay()->diffInDays($endDate->startOfDay()) + 1;
            $hours = $startDate->diffInHours($endDate);
            
            $departmentsData[$dept]['Total Days'] = ($departmentsData[$dept]['Total Days'] + $days);
            $departmentsData[$dept]['Hours Used'] = ($departmentsData[$dept]['Hours Used'] + $hours);
        }

        // Format the final values
        foreach ($departmentsData as &$dept) {
            $dept['Total Days'] = $dept['Total Days'] . ' days';
            $dept['Hours Used'] = $dept['Hours Used'] . ' hours';
        }
        
        return array_values($departmentsData);
    }
}