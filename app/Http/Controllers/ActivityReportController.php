<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use Carbon\Carbon;
use App\Services\ReportExportService;

class ActivityReportController extends Controller
{
    protected $exportService;

    public function __construct(ReportExportService $exportService)
    {
        $this->exportService = $exportService;
    }

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

            // Apply time filters
            $dateRange = $this->getDateRange($timePeriod, $year, $month, $quarter);
            $query->whereBetween('start_datetime', [$dateRange['start'], $dateRange['end']]);

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

                // Calculate total days for each activity
                $activities = $activities->map(function($activity) {
                    $startDate = Carbon::parse($activity->start_time)->startOfDay();
                    $endDate = Carbon::parse($activity->end_time)->startOfDay();
                    $activity->total_days = $startDate->diffInDays($endDate) + 1;
                    return $activity;
                });

                // Generate stats for activity categories
                $categoryStats = $this->getCategoryStats($activities);
                $total = $activities->count();

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

                $categoryStats = $this->getCategoryStats($activities);
                $departmentsData = $this->getDepartmentStats($activities);

                return response()->json([
                    'total_activities' => $activities->count(),
                    'category_stats'   => $categoryStats,
                    'departments'      => $departmentsData,
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
            // Validate input
            $request->validate([
                'report_type' => 'required|in:employee_activity,department_activity',
                'time_period' => 'required|in:monthly,quarterly,yearly',
                'year'         => 'required|integer',
                'month'        => 'nullable|integer|between:1,12',
                'quarter'      => 'nullable|integer|between:1,4',
                'format'       => 'required|in:excel,pdf,csv', // Added format
            ]);

            Log::info("Export Request Parameters:", $request->all());

            // Compute date range
            $dateRange = $this->getDateRange($request->time_period, $request->year, $request->month, $request->quarter);

            Log::info("Export Date Range", [
                'start' => $dateRange['start']->toDateString(),
                'end'   => $dateRange['end']->toDateString()
            ]);

            // Get data and headers for export
            $data = $this->getExportData($request->report_type, $dateRange);
            $headers = $this->getExportHeaders($request->report_type);
            $filename = $this->generateExportFilename($request->report_type, $request->time_period, $request->format);

            Log::info("Exporting file: " . $filename);

            // Call the ReportExportService to export the data
            return $this->exportService->export($data, $headers, $filename, $request->format);
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    /* === HELPER METHODS === */

    // Get the date range based on the selected period
    private function getDateRange($timePeriod, $year, $month = null, $quarter = null)
    {
        $startDate = Carbon::create($year);
        $endDate = Carbon::create($year);

        switch ($timePeriod) {
            case 'monthly':
                $startDate->setMonth((int)$month)->startOfMonth();
                $endDate->setMonth((int)$month)->endOfMonth();
                break;
            case 'quarterly':
                $startMonth = ((int)$quarter - 1) * 3 + 1;
                $startDate->setMonth($startMonth)->startOfMonth();
                $endDate->setMonth($startMonth + 2)->endOfMonth();
                break;
            case 'yearly':
                $startDate->startOfYear();
                $endDate->endOfYear();
                break;
        }

        return ['start' => $startDate, 'end' => $endDate];
    }

    // Get the category stats from the activities data
    private function getCategoryStats($activities)
    {
        $categoryStats = [
            'Meeting'    => ['count' => 0, 'percentage' => 0],
            'Invitation' => ['count' => 0, 'percentage' => 0],
            'Survey'     => ['count' => 0, 'percentage' => 0],
        ];

        foreach ($activities as $act) {
            if (isset($categoryStats[$act->category])) {
                $categoryStats[$act->category]['count']++;
            }
        }

        $total = $activities->count();
        if ($total > 0) {
            foreach ($categoryStats as $cat => &$stat) {
                $stat['percentage'] = round(($stat['count'] / $total) * 100);
            }
        }

        return $categoryStats;
    }

    // Get department stats from the activities data
    private function getDepartmentStats($activities)
    {
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

            // Calculate total days
            $startDate = Carbon::parse($act->start_datetime)->startOfDay();
            $endDate = Carbon::parse($act->end_datetime)->startOfDay();
            $days = $startDate->diffInDays($endDate) + 1;
            $departmentsData[$dept]['total_days'] += $days;

            // Calculate hours used
            $start = strtotime($act->start_datetime);
            $end   = strtotime($act->end_datetime);
            $diff  = ($end - $start) / 3600;
            if ($diff > 0) {
                $departmentsData[$dept]['hours_used'] += $diff;
            }
        }

        return array_values($departmentsData);
    }

    // Get data for export based on report type
    private function getExportData($reportType, $dateRange)
    {
        switch ($reportType) {
            case 'employee_activity':
                return $this->getEmployeeActivityExportData($dateRange);
            case 'department_activity':
                return $this->getDepartmentActivityExportData($dateRange);
            default:
                return [];
        }
    }

    // Generate the headers for export based on report type
    private function getExportHeaders($reportType)
    {
        switch ($reportType) {
            case 'employee_activity':
                return ['Name', 'Department', 'Start Time', 'End Time', 'Total Days', 'Category', 'Description'];
            case 'department_activity':
                return ['Department', 'Total Activities', 'Hours Used', 'Total Days'];
            default:
                return [];
        }
    }

    // Generate filename for export
    private function generateExportFilename($reportType, $timePeriod, $format)
    {
        $timestamp = now()->format('Ymd_His');
        $extension = ($format === 'pdf') ? 'pdf'
            : (($format === 'csv') ? 'csv' : 'xlsx');

        return "{$reportType}_report_{$timePeriod}_{$timestamp}.{$extension}";
    }

    // Get employee activity export data
    private function getEmployeeActivityExportData($dateRange)
    {
        $activities = DB::table('activities')
            ->leftJoin('employees', 'activities.nama', '=', 'employees.name')
            ->select(
                'activities.nama as name',
                'activities.department',
                'activities.start_datetime AS start_time',
                'activities.end_datetime AS end_time',
                'activities.activity_type AS category',
                'activities.description'
            )
            ->whereBetween('start_datetime', [$dateRange['start'], $dateRange['end']])
            ->get();

        $data = [];
        foreach ($activities as $activity) {
            $startDate = Carbon::parse($activity->start_time)->startOfDay();
            $endDate = Carbon::parse($activity->end_time)->startOfDay();
            $totalDays = $startDate->diffInDays($endDate) + 1;

            $data[] = [
                'Name'        => $activity->name,
                'Department'  => $activity->department,
                'Start Time'  => $activity->start_time,
                'End Time'    => $activity->end_time,
                'Total Days'  => $totalDays . ' days',
                'Category'    => $activity->category,
                'Description' => $activity->description,
            ];
        }

        return $data;
    }

    // Get department activity export data
    private function getDepartmentActivityExportData($dateRange)
    {
        $activities = DB::table('activities')
            ->select('department', 'activity_type', 'start_datetime', 'end_datetime')
            ->whereBetween('start_datetime', [$dateRange['start'], $dateRange['end']])
            ->get();

        $data = [];
        foreach ($activities as $activity) {
            $startDate = Carbon::parse($activity->start_datetime)->startOfDay();
            $endDate = Carbon::parse($activity->end_datetime)->startOfDay();
            $days = $startDate->diffInDays($endDate) + 1;

            // Calculate hours_used
            $start = strtotime($activity->start_datetime);
            $end = strtotime($activity->end_datetime);
            $hoursUsed = ($end - $start) / 3600;

            $data[] = [
                'Department'     => $activity->department,
                'Total Activities' => 1,
                'Hours Used'     => round($hoursUsed, 2),
                'Total Days'     => $days,
            ];
        }

        return $data;
    }
}
