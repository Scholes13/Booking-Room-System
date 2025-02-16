<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ActivityReportController extends Controller
{
    public function index()
    {
        Log::debug('ActivityReportController@index called');
        return view('admin.activityreports.index');
    }

    public function getData(Request $request)
    {
        Log::debug('ActivityReportController@getData input', $request->all());

        $reportType = $request->input('report_type');   
        $timePeriod = $request->input('time_period');   
        $year       = $request->input('year');          
        $month      = $request->input('month');         
        $quarter    = $request->input('quarter');       

        Log::debug('Parsed params', [
            'reportType' => $reportType,
            'timePeriod' => $timePeriod,
            'year'       => $year,
            'month'      => $month,
            'quarter'    => $quarter,
        ]);

        try {
            if (!$reportType) {
                Log::debug('No report_type found, returning no_data');
                return response()->json([
                    'no_data' => true,
                    'message' => 'Missing report type.'
                ]);
            }

            $query = DB::table('activities');

            if ($timePeriod === 'monthly') {
                if ($month && $year) {
                    Log::debug('Applying monthly filter', ['month' => $month, 'year' => $year]);
                    $query->whereMonth('start_datetime', $month)
                          ->whereYear('start_datetime', $year);
                }
            } elseif ($timePeriod === 'quarterly') {
                if ($quarter && $year) {
                    Log::debug('Applying quarterly filter', ['quarter' => $quarter, 'year' => $year]);
                    $startMonth = (($quarter - 1) * 3) + 1;
                    $endMonth   = $startMonth + 2;
                    $query->whereMonth('start_datetime', '>=', $startMonth)
                          ->whereMonth('start_datetime', '<=', $endMonth)
                          ->whereYear('start_datetime', $year);
                }
            } elseif ($timePeriod === 'yearly') {
                if ($year) {
                    Log::debug('Applying yearly filter', ['year' => $year]);
                    $query->whereYear('start_datetime', $year);
                }
            }

            if ($reportType === 'employee_activity') {
                Log::debug('Report Type: employee_activity');

                $activities = $query
                    ->leftJoin('employees', 'activities.nama', '=', 'employees.name')
                    ->select(
                        'activities.nama as name',
                        'activities.department',
                        'activities.start_datetime AS start_time',
                        'activities.end_datetime AS end_time',
                        'activities.activity_type AS category',
                        'activities.description'
                    )->get();

                // Tambahkan perhitungan total hari untuk setiap aktivitas
                $activities = $activities->map(function($activity) {
                    $startDate = Carbon::parse($activity->start_time)->startOfDay();
                    $endDate = Carbon::parse($activity->end_time)->startOfDay();
                    
                    // Hitung selisih hari (inclusive)
                    $activity->total_days = $startDate->diffInDays($endDate) + 1;
                    
                    return $activity;
                });

                Log::debug('Query result (employee_activity)', [
                    'count' => $activities->count(),
                    'sample' => $activities->first()
                ]);

                $total = $activities->count();

                $categoryStats = [
                    'Meeting'    => ['count' => 0, 'percentage' => 0],
                    'Invitation' => ['count' => 0, 'percentage' => 0],
                    'Survey'     => ['count' => 0, 'percentage' => 0],
                ];

                foreach ($activities as $act) {
                    Log::debug('Activity category:', ['category' => $act->category]);
                    if (isset($categoryStats[$act->category])) {
                        $categoryStats[$act->category]['count']++;
                    }
                }

                if ($total > 0) {
                    foreach ($categoryStats as $cat => &$stat) {
                        $stat['percentage'] = round(($stat['count'] / $total) * 100);
                    }
                }

                return response()->json([
                    'total_activities' => $total,
                    'category_stats'   => $categoryStats,
                    'activities'       => $activities,
                ]);

            } elseif ($reportType === 'department_activity') {
                Log::debug('Report Type: department_activity');

                $activities = $query->select(
                    'department',
                    'start_datetime',
                    'end_datetime',
                    'activity_type'
                )->get();

                Log::debug('Query result (department_activity)', ['count' => $activities->count()]);

                $total = $activities->count();

                $categoryStats = [
                    'Meeting'    => ['count' => 0, 'percentage' => 0],
                    'Invitation' => ['count' => 0, 'percentage' => 0],
                    'Survey'     => ['count' => 0, 'percentage' => 0],
                ];
                
                foreach ($activities as $act) {
                    if (isset($categoryStats[$act->activity_type])) {
                        $categoryStats[$act->activity_type]['count']++;
                    }
                }

                if ($total > 0) {
                    foreach ($categoryStats as $cat => &$stat) {
                        $stat['percentage'] = round(($stat['count'] / $total) * 100);
                    }
                }

                $departmentsData = [];
                foreach ($activities as $act) {
                    $dept = $act->department ?: 'Unknown';
                    if (!isset($departmentsData[$dept])) {
                        $departmentsData[$dept] = [
                            'name'             => $dept,
                            'total_activities' => 0,
                            'hours_used'       => 0,
                            'total_days'       => 0
                        ];
                    }
                    $departmentsData[$dept]['total_activities']++;

                    // Hitung total hari
                    $startDate = Carbon::parse($act->start_datetime)->startOfDay();
                    $endDate = Carbon::parse($act->end_datetime)->startOfDay();
                    $days = $startDate->diffInDays($endDate) + 1;
                    $departmentsData[$dept]['total_days'] += $days;

                    // Hitung hours_used
                    $start = strtotime($act->start_datetime);
                    $end   = strtotime($act->end_datetime);
                    $diff  = ($end - $start) / 3600;
                    if ($diff > 0) {
                        $departmentsData[$dept]['hours_used'] += $diff;
                    }
                }

                $departmentsArray = array_values($departmentsData);

                return response()->json([
                    'total_activities' => $total,
                    'category_stats'   => $categoryStats,
                    'departments'      => $departmentsArray,
                ]);

            } else {
                Log::debug('Unsupported report type', ['report_type' => $reportType]);
                return response()->json([
                    'no_data' => true,
                    'message' => 'Unsupported report type: ' . $reportType
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in getData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while processing the report'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            Log::debug('ActivityReportController@export input', $request->all());

            $reportType = $request->input('report_type');
            $timePeriod = $request->input('time_period');
            $year = $request->input('year');
            $month = $request->input('month');
            $quarter = $request->input('quarter');

            // Base query
            $query = DB::table('activities');

            // Apply time filters
            if ($timePeriod === 'monthly') {
                if ($month && $year) {
                    $query->whereMonth('start_datetime', $month)
                          ->whereYear('start_datetime', $year);
                }
            } elseif ($timePeriod === 'quarterly') {
                if ($quarter && $year) {
                    $startMonth = (($quarter - 1) * 3) + 1;
                    $endMonth = $startMonth + 2;
                    $query->whereMonth('start_datetime', '>=', $startMonth)
                          ->whereMonth('start_datetime', '<=', $endMonth)
                          ->whereYear('start_datetime', $year);
                }
            } elseif ($timePeriod === 'yearly') {
                if ($year) {
                    $query->whereYear('start_datetime', $year);
                }
            }

            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            if ($reportType === 'employee_activity') {
                // Setup employee activity sheet
                $sheet->setCellValue('A1', 'Name');
                $sheet->setCellValue('B1', 'Department');
                $sheet->setCellValue('C1', 'Start Time');
                $sheet->setCellValue('D1', 'End Time');
                $sheet->setCellValue('E1', 'Total Days');
                $sheet->setCellValue('F1', 'Category');
                $sheet->setCellValue('G1', 'Description');

                $activities = $query
                    ->leftJoin('employees', 'activities.nama', '=', 'employees.name')
                    ->select(
                        'activities.nama as name',
                        'activities.department',
                        'activities.start_datetime AS start_time',
                        'activities.end_datetime AS end_time',
                        'activities.activity_type AS category',
                        'activities.description'
                    )->get();

                $row = 2;
                foreach ($activities as $activity) {
                    $startDate = Carbon::parse($activity->start_time)->startOfDay();
                    $endDate = Carbon::parse($activity->end_time)->startOfDay();
                    $totalDays = $startDate->diffInDays($endDate) + 1;

                    $sheet->setCellValue('A'.$row, $activity->name);
                    $sheet->setCellValue('B'.$row, $activity->department);
                    $sheet->setCellValue('C'.$row, $activity->start_time);
                    $sheet->setCellValue('D'.$row, $activity->end_time);
                    $sheet->setCellValue('E'.$row, $totalDays . ' days');
                    $sheet->setCellValue('F'.$row, $activity->category);
                    $sheet->setCellValue('G'.$row, $activity->description);
                    $row++;
                }
            } else {
                // Setup department activity sheet
                $sheet->setCellValue('A1', 'Department');
                $sheet->setCellValue('B1', 'Total Activities');
                $sheet->setCellValue('C1', 'Hours Used');
                $sheet->setCellValue('D1', 'Total Days');

                $activities = $query->select(
                    'department',
                    'start_datetime',
                    'end_datetime',
                    'activity_type'
                )->get();

                $departmentsData = [];
                foreach ($activities as $act) {
                    $dept = $act->department ?: 'Unknown';
                    if (!isset($departmentsData[$dept])) {
                        $departmentsData[$dept] = [
                            'name' => $dept,
                            'total_activities' => 0,
                            'hours_used' => 0,
                            'total_days' => 0
                        ];
                    }
                    $departmentsData[$dept]['total_activities']++;

                    // Calculate days
                    $startDate = Carbon::parse($act->start_datetime)->startOfDay();
                    $endDate = Carbon::parse($act->end_datetime)->startOfDay();
                    $days = $startDate->diffInDays($endDate) + 1;
                    $departmentsData[$dept]['total_days'] += $days;

                    // Calculate hours
                    $start = strtotime($act->start_datetime);
                    $end = strtotime($act->end_datetime);
                    $diff = ($end - $start) / 3600;
                    if ($diff > 0) {
                        $departmentsData[$dept]['hours_used'] += $diff;
                    }
                }

                $row = 2;
                foreach ($departmentsData as $dept) {
                    $sheet->setCellValue('A'.$row, $dept['name']);
                    $sheet->setCellValue('B'.$row, $dept['total_activities']);
                    $sheet->setCellValue('C'.$row, round($dept['hours_used']) . ' hours');
                    $sheet->setCellValue('D'.$row, $dept['total_days'] . ' days');
                    $row++;
                }
            }

            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create file
            $writer = new Xlsx($spreadsheet);
            $filename = 'activity_report_' . date('Y-m-d_His') . '.xlsx';

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    public function getStatistics()
    {
        try {
            $today = Carbon::today();
            
            $statistics = [
                'today_activities' => DB::table('activities')
                    ->whereDate('start_datetime', $today)
                    ->count(),
                'total_employees' => Employee::count(),
                'activity_types' => DB::table('activities')
                    ->select('activity_type', DB::raw('count(*) as total'))
                    ->groupBy('activity_type')
                    ->get()
            ];
            
            return response()->json($statistics);
            
        } catch (\Exception $e) {
            Log::error('Error in getStatistics: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get statistics'], 500);
        }
    }
}