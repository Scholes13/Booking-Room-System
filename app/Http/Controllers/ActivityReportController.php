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
                        'activities.activity_type AS category',  // Use activity_type as category
                        'activities.description'
                    )->get();

                // Calculate total days for each activity
                $activities = $activities->map(function($activity) {
                    $startDate = Carbon::parse($activity->start_time);
                    $endDate = Carbon::parse($activity->end_time);

                    // If the activity lasts less than 24 hours, show it in hours
                    if ($startDate->isSameDay($endDate)) {
                        $hours = $startDate->diffInHours($endDate);
                        $activity->total_days = $hours . ' hours';
                    } else {
                        $totalHours = $startDate->diffInHours($endDate);

                        if ($totalHours <= 48) {
                            $activity->total_days = '1 day';
                        } else {
                            $activity->total_days = ceil($totalHours / 24) . ' day' . (ceil($totalHours / 24) > 1 ? 's' : '');
                        }
                    }

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

                $activities = $query
                    ->select(
                        'department',
                        'start_datetime',
                        'end_datetime',
                        'activity_type AS category'  // Use activity_type as category
                    )
                    ->get();

                // Calculate total days and hours for department activities
                $activities = $activities->map(function($activity) {
                    $startDate = Carbon::parse($activity->start_datetime ?? 'now'); // Menggunakan nilai default 'now' jika null
                    $endDate = Carbon::parse($activity->end_datetime ?? 'now'); // Menggunakan nilai default 'now' jika null

                    // Pastikan start_datetime dan end_datetime valid sebelum dihitung
                    if ($startDate && $endDate) {
                        $totalHours = $startDate->diffInHours($endDate);

                        // Pastikan totalHours adalah angka valid sebelum melakukan operasi
                        if (is_numeric($totalHours)) {
                            // Menjaga total_days tetap berupa angka
                            if ($totalHours < 24) {
                                $activity->total_days = $totalHours;
                            } elseif ($totalHours >= 24 && $totalHours < 48) {
                                $activity->total_days = 1;
                            } else {
                                $activity->total_days = ceil($totalHours / 24);
                            }

                            // Assign hours used
                            $activity->hours_used = $totalHours;
                        } else {
                            Log::error('Invalid hours calculated', ['start_datetime' => $activity->start_datetime, 'end_datetime' => $activity->end_datetime]);
                        }
                    } else {
                        Log::error('Invalid datetime values', ['start_datetime' => $activity->start_datetime, 'end_datetime' => $activity->end_datetime]);
                    }

                    return $activity;
                });

                // Handle stats generation
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

    /* === HELPER METHODS === */

    private function getCategoryStats($activities)
    {
        $categoryStats = [
            'Meeting'    => ['count' => 0, 'percentage' => 0],
            'Invitation' => ['count' => 0, 'percentage' => 0],
            'Survey'     => ['count' => 0, 'percentage' => 0],
        ];

        foreach ($activities as $act) {
            // Check if category exists before accessing it
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

            // Pastikan start_datetime dan end_datetime valid sebelum dihitung
            $startDate = Carbon::parse($act->start_datetime ?? 'now'); // Menggunakan nilai default 'now' jika null
            $endDate = Carbon::parse($act->end_datetime ?? 'now'); // Menggunakan nilai default 'now' jika null

            if ($startDate && $endDate) {
                $totalHours = $startDate->diffInHours($endDate);

                // Pastikan totalHours adalah angka valid sebelum melakukan operasi
                if (is_numeric($totalHours)) {
                    // Menjaga total_days tetap berupa angka
                    if ($totalHours < 24) {
                        $departmentsData[$dept]['total_days'] += $totalHours;
                    } elseif ($totalHours >= 24 && $totalHours < 48) {
                        $departmentsData[$dept]['total_days'] += 1;
                    } else {
                        $departmentsData[$dept]['total_days'] += ceil($totalHours / 24);
                    }

                    // Assign hours used for the department's activity
                    $departmentsData[$dept]['hours_used'] += $totalHours;
                } else {
                    Log::error('Invalid hours calculated', ['start_datetime' => $act->start_datetime, 'end_datetime' => $act->end_datetime]);
                }
            } else {
                Log::error('Invalid datetime values', ['start_datetime' => $act->start_datetime, 'end_datetime' => $act->end_datetime]);
            }
        }

        return array_values($departmentsData);
    }

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
}
