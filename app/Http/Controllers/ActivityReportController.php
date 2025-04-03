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
        return view('admin.activity-reports.index');
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
                    ->leftJoin('employees', 'activities.name', '=', 'employees.name')
                    ->select(
                        'activities.name',
                        'departments.name as department',
                        'activities.start_datetime',
                        'activities.end_datetime',
                        'activities.activity_type AS category',
                        'activities.description',
                        'activities.city',
                        'activities.province'
                    )
                    ->leftJoin('departments', 'activities.department_id', '=', 'departments.id')
                    ->get();

                // Calculate total days for each activity
                $activities = $activities->map(function($activity) {
                    $startDate = Carbon::parse($activity->start_datetime);
                    $endDate = Carbon::parse($activity->end_datetime);

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
                        'departments.name as department',
                        'activities.start_datetime',
                        'activities.end_datetime',
                        'activities.activity_type AS category'
                    )
                    ->leftJoin('departments', 'activities.department_id', '=', 'departments.id')
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

                // Calculate total hours across all departments
                $totalHours = 0;
                foreach ($departmentsData as $dept) {
                    $totalHours += $dept['hours_used'];
                }
                
                return response()->json([
                    'department_stats' => array_values($departmentsData),
                    'total_activities' => $activities->count(),
                    'total_hours'      => $totalHours,
                ]);
            } elseif ($reportType === 'location_activity') {
                Log::debug('Report Type: location_activity');

                $activities = $query
                    ->select(
                        'activities.city',
                        'activities.province',
                        'activities.start_datetime',
                        'activities.end_datetime',
                        'activities.activity_type AS category',
                        'activities.description',
                        'departments.name as department'
                    )
                    ->leftJoin('departments', 'activities.department_id', '=', 'departments.id')
                    ->get();

                // Group activities by location (city, province)
                $locationData = [];
                foreach ($activities as $act) {
                    $locationKey = ($act->city && $act->province) ? 
                                  $act->city . ', ' . $act->province : 
                                  ($act->city ?: $act->province ?: 'Unknown');
                    
                    if (!isset($locationData[$locationKey])) {
                        $locationData[$locationKey] = [
                            'location' => $locationKey,
                            'total_activities' => 0,
                            'hours_used' => 0,
                            'activities_by_type' => [
                                'Meeting' => 0,
                                'Invitation' => 0,
                                'Survey' => 0
                            ]
                        ];
                    }
                    
                    $locationData[$locationKey]['total_activities']++;
                    
                    // Calculate hours
                    $startDate = Carbon::parse($act->start_datetime ?? 'now');
                    $endDate = Carbon::parse($act->end_datetime ?? 'now');
                    $hours = $startDate->diffInHours($endDate);
                    
                    if (is_numeric($hours)) {
                        $locationData[$locationKey]['hours_used'] += $hours;
                    }
                    
                    // Count by activity type
                    if (isset($act->category)) {
                        $locationData[$locationKey]['activities_by_type'][$act->category] = 
                            ($locationData[$locationKey]['activities_by_type'][$act->category] ?? 0) + 1;
                    }
                }
                
                $total = $activities->count();
                
                return response()->json([
                    'location_stats' => array_values($locationData),
                    'total_activities' => $total,
                    'total_locations' => count($locationData)
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

    public function getDetailedData(Request $request)
    {
        Log::debug('ActivityReportController@getDetailedData input', $request->all());

        $timePeriod = $request->input('time_period');
        $year       = $request->input('year');
        $month      = $request->input('month');
        $quarter    = $request->input('quarter');

        try {
            // Build the date range based on selected time period
            $dateRange = $this->buildDateRange($timePeriod, $year, $month, $quarter);
            if (!$dateRange) {
                return response()->json([
                    'no_data' => true,
                    'message' => 'Invalid time period parameters.'
                ]);
            }

            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            // Query for detailed activity data
            $activities = DB::table('activities')
                ->whereBetween('start_datetime', [$startDate, $endDate])
                ->orWhereBetween('end_datetime', [$startDate, $endDate])
                ->select(
                    'nama',
                    'department',
                    'province',
                    'city',
                    'activity_type',
                    'description',
                    'start_datetime',
                    'end_datetime'
                )
                ->orderBy('start_datetime', 'desc')
                ->get();

            // Format activity data for display
            $formattedActivities = $activities->map(function($activity) {
                $startDate = Carbon::parse($activity->start_datetime);
                $endDate = Carbon::parse($activity->end_datetime);
                
                $duration = $startDate->diffInHours($endDate);
                $durationText = $duration == 1 ? '1 hour' : $duration . ' hours';
                
                // Format dates for display
                $formattedStartDate = $startDate->format('d M Y, H:i');
                $formattedEndDate = $endDate->format('d M Y, H:i');
                
                // Create location string
                $location = $activity->city && $activity->province 
                    ? $activity->city . ', ' . $activity->province 
                    : ($activity->city ?: $activity->province ?: 'Unknown location');
                
                return [
                    'nama' => $activity->nama,
                    'department' => $activity->department,
                    'location' => $location,
                    'activity_type' => $activity->activity_type,
                    'description' => $activity->description,
                    'time_range' => $formattedStartDate . ' - ' . $formattedEndDate,
                    'duration' => $durationText,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'activity_color' => $this->getActivityTypeColor($activity->activity_type)
                ];
            });

            return response()->json([
                'activities' => $formattedActivities,
                'total_activities' => $formattedActivities->count(),
                'date_range' => [
                    'start' => Carbon::parse($startDate)->format('d M Y'),
                    'end' => Carbon::parse($endDate)->format('d M Y')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDetailedData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'no_data' => true,
                'message' => 'Error getting activity data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            Log::debug('ActivityReportController@export input', $request->all());

            // Validasi input
            $request->validate([
                'report_type' => 'required|in:employee_activity,department_activity,location_activity,detailed_activity',
                'time_period' => 'required|in:monthly,quarterly,yearly',
                'year' => 'required|integer',
                'month' => 'nullable|integer|between:1,12',
                'quarter' => 'nullable|integer|between:1,4',
                'format' => 'required|in:xlsx,pdf,csv'
            ]);

            // Ambil data laporan sesuai jenis dan periode
            $reportType = $request->input('report_type');
            $timePeriod = $request->input('time_period');
            $year = $request->input('year');
            $month = $request->input('month');
            $quarter = $request->input('quarter');
            $format = $request->input('format', 'xlsx');

            // Buat rentang tanggal yang sesuai
            $dateRange = $this->getDateRange($timePeriod, $year, $month, $quarter);

            $query = DB::table('activities');
            $query->whereBetween('start_datetime', [$dateRange['start'], $dateRange['end']]);

            // Dapatkan data berdasarkan jenis laporan
            switch ($reportType) {
                case 'employee_activity':
                    $activities = $query
                        ->leftJoin('employees', 'activities.name', '=', 'employees.name')
                        ->select(
                            'activities.name',
                            'departments.name as department',
                            'activities.start_datetime',
                            'activities.end_datetime',
                            'activities.activity_type AS category',
                            'activities.description',
                            'activities.city',
                            'activities.province'
                        )
                        ->leftJoin('departments', 'activities.department_id', '=', 'departments.id')
                        ->get();

                    $headers = ['Name', 'Department', 'Province', 'City', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Category', 'Total Days', 'Description'];
                    $exportData = $activities->map(function ($activity) {
                        $startDate = Carbon::parse($activity->start_datetime);
                        $endDate = Carbon::parse($activity->end_datetime);
                        
                        // Hitung total hari
                        $totalDays = '1 day';
                        if ($startDate->isSameDay($endDate)) {
                            $hours = $startDate->diffInHours($endDate);
                            $totalDays = $hours . ' hours';
                        } else {
                            $totalHours = $startDate->diffInHours($endDate);
                            if ($totalHours > 48) {
                                $totalDays = ceil($totalHours / 24) . ' days';
                            }
                        }

                        return [
                            'Name' => $activity->name ?? 'N/A',
                            'Department' => $activity->department ?? 'N/A',
                            'Province' => $activity->province ?? 'N/A',
                            'City' => $activity->city ?? 'N/A',
                            'Start Date' => $activity->start_datetime ? Carbon::parse($activity->start_datetime)->format('Y-m-d') : 'N/A',
                            'Start Time' => $activity->start_datetime ? Carbon::parse($activity->start_datetime)->format('H:i') : 'N/A',
                            'End Date' => $activity->end_datetime ? Carbon::parse($activity->end_datetime)->format('Y-m-d') : 'N/A',
                            'End Time' => $activity->end_datetime ? Carbon::parse($activity->end_datetime)->format('H:i') : 'N/A',
                            'Category' => $activity->category ?? 'N/A',
                            'Total Days' => $totalDays,
                            'Description' => $activity->description ?? 'N/A'
                        ];
                    })->toArray();
                    break;

                case 'department_activity':
                    $activities = $query
                        ->select(
                            'departments.name as department',
                            'activities.start_datetime',
                            'activities.end_datetime',
                            'activities.activity_type AS category'
                        )
                        ->leftJoin('departments', 'activities.department_id', '=', 'departments.id')
                        ->get();

                    // Group by department and calculate stats
                    $departmentStats = $this->getDepartmentStats($activities);
                    
                    $headers = ['Department', 'Total Activities', 'Hours Used', 'Total Days'];
                    $exportData = collect($departmentStats)->map(function ($dept) {
                        return [
                            'Department' => $dept['name'] ?? 'N/A',
                            'Total Activities' => $dept['total_activities'] ?? '0',
                            'Hours Used' => ($dept['hours_used'] ?? '0') . ' hours',
                            'Total Days' => ($dept['total_days'] ?? '0') . ' days'
                        ];
                    })->toArray();
                    break;

                case 'location_activity':
                    $activities = $query
                        ->select(
                            'activities.city',
                            'activities.province',
                            'activities.start_datetime',
                            'activities.end_datetime',
                            'activities.activity_type AS category',
                            'activities.description',
                            'departments.name as department'
                        )
                        ->leftJoin('departments', 'activities.department_id', '=', 'departments.id')
                        ->get();

                    // Group by location and calculate stats
                    $locationData = [];
                    foreach ($activities as $act) {
                        $locationKey = ($act->city && $act->province) ? 
                                    $act->city . ', ' . $act->province : 
                                    ($act->city ?: $act->province ?: 'Unknown');
                        
                        if (!isset($locationData[$locationKey])) {
                            $locationData[$locationKey] = [
                                'location' => $locationKey,
                                'total_activities' => 0,
                                'hours_used' => 0,
                                'activities_by_type' => [
                                    'Meeting' => 0,
                                    'Invitation' => 0,
                                    'Survey' => 0
                                ]
                            ];
                        }
                        
                        $locationData[$locationKey]['total_activities']++;
                        
                        // Calculate hours
                        $startDate = Carbon::parse($act->start_datetime ?? 'now');
                        $endDate = Carbon::parse($act->end_datetime ?? 'now');
                        $hours = $startDate->diffInHours($endDate);
                        
                        if (is_numeric($hours)) {
                            $locationData[$locationKey]['hours_used'] += $hours;
                        }
                        
                        // Count by activity type
                        if (isset($act->category)) {
                            $locationData[$locationKey]['activities_by_type'][$act->category] = 
                                ($locationData[$locationKey]['activities_by_type'][$act->category] ?? 0) + 1;
                        }
                    }
                    
                    $headers = ['Location', 'Total Activities', 'Hours Used', 'Meetings', 'Invitations', 'Surveys'];
                    $exportData = collect(array_values($locationData))->map(function ($loc) {
                        return [
                            'Location' => $loc['location'] ?? 'N/A',
                            'Total Activities' => $loc['total_activities'] ?? '0',
                            'Hours Used' => ($loc['hours_used'] ?? '0') . ' hours',
                            'Meetings' => $loc['activities_by_type']['Meeting'] ?? '0',
                            'Invitations' => $loc['activities_by_type']['Invitation'] ?? '0',
                            'Surveys' => $loc['activities_by_type']['Survey'] ?? '0'
                        ];
                    })->toArray();
                    break;

                case 'detailed_activity':
                    // For detailed activity, use the same data as employee_activity but with more details
                    $activities = $query
                        ->leftJoin('employees', 'activities.name', '=', 'employees.name')
                        ->select(
                            'activities.name',
                            'departments.name as department',
                            'activities.start_datetime',
                            'activities.end_datetime',
                            'activities.activity_type AS category',
                            'activities.description',
                            'activities.city',
                            'activities.province'
                        )
                        ->leftJoin('departments', 'activities.department_id', '=', 'departments.id')
                        ->get();

                    $headers = ['Name', 'Department', 'Province', 'City', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Category', 'Duration', 'Description'];
                    $exportData = $activities->map(function ($activity) {
                        $startDate = Carbon::parse($activity->start_datetime);
                        $endDate = Carbon::parse($activity->end_datetime);
                        
                        // Hitung total hari
                        $totalDays = '1 day';
                        if ($startDate->isSameDay($endDate)) {
                            $hours = $startDate->diffInHours($endDate);
                            $totalDays = $hours . ' hours';
                        } else {
                            $totalHours = $startDate->diffInHours($endDate);
                            if ($totalHours > 48) {
                                $totalDays = ceil($totalHours / 24) . ' days';
                            }
                        }

                        return [
                            'Name' => $activity->name ?? 'N/A',
                            'Department' => $activity->department ?? 'N/A',
                            'Province' => $activity->province ?? 'N/A',
                            'City' => $activity->city ?? 'N/A',
                            'Start Date' => $activity->start_datetime ? Carbon::parse($activity->start_datetime)->format('Y-m-d') : 'N/A',
                            'Start Time' => $activity->start_datetime ? Carbon::parse($activity->start_datetime)->format('H:i') : 'N/A',
                            'End Date' => $activity->end_datetime ? Carbon::parse($activity->end_datetime)->format('Y-m-d') : 'N/A',
                            'End Time' => $activity->end_datetime ? Carbon::parse($activity->end_datetime)->format('H:i') : 'N/A',
                            'Category' => $activity->category ?? 'N/A',
                            'Duration' => $totalDays,
                            'Description' => $activity->description ?? 'N/A'
                        ];
                    })->toArray();
                    break;

                default:
                    return response()->json(['error' => 'Invalid report type'], 400);
            }

            // Export data sesuai format
            $filename = $this->generateExportFilename($reportType, $timePeriod, $format);

            // OpenSpout tidak mendukung PDF, jadi untuk format PDF lebih baik
            // menggunakan cara manual melalui view khusus
            if ($format === 'pdf') {
                // Implementasi PDF dibuat terpisah nanti
                return response()->json(['error' => 'PDF format not yet supported'], 500);
            }
            
            // Gunakan metode export yang tersedia
            return $this->exportService->export($exportData, $headers, $filename);
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    private function generateExportFilename($reportType, $timePeriod, $format)
    {
        $date = Carbon::now()->format('Ymd_His');
        return sprintf('activity_report_%s_%s_%s.%s', $reportType, $timePeriod, $date, $format);
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

    private function getActivityTypeColor($activityType)
    {
        $colors = [
            'Meeting' => '#4F46E5', // Indigo
            'Invitation' => '#10B981', // Emerald
            'Survey' => '#F59E0B', // Amber
        ];
        
        return $colors[$activityType] ?? '#6B7280'; // Gray as default
    }

    private function buildDateRange($timePeriod, $year, $month = null, $quarter = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        switch ($timePeriod) {
            case 'monthly':
                if (!$month) {
                    $month = date('m');
                }
                $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d H:i:s');
                $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d H:i:s');
                break;
                
            case 'quarterly':
                if (!$quarter) {
                    // Default to current quarter
                    $currentMonth = date('m');
                    $quarter = ceil($currentMonth / 3);
                }
                
                $startMonth = (($quarter - 1) * 3) + 1;
                $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth()->format('Y-m-d H:i:s');
                $endDate = Carbon::create($year, $startMonth + 2, 1)->endOfMonth()->format('Y-m-d H:i:s');
                break;
                
            case 'yearly':
                $startDate = Carbon::create($year, 1, 1)->startOfYear()->format('Y-m-d H:i:s');
                $endDate = Carbon::create($year, 12, 31)->endOfYear()->format('Y-m-d H:i:s');
                break;
                
            default:
                return null;
        }
        
        return ['start' => $startDate, 'end' => $endDate];
    }
}
