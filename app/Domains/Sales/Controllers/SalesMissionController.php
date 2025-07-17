<?php

namespace App\Domains\Sales\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\SalesMissionDetail;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ActivityType;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Sales\ActivityExport;

class SalesMissionController extends Controller
{
    /**
     * Dashboard utama Sales Mission
     */
    public function dashboard()
    {
        // Total Sales Mission (modified - only count activities that have already started for all time)
        $totalSalesMissions = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->where('start_datetime', '<=', now())
            ->count();
            
        // Sales Mission bulan ini (modified - only count activities that have already started)
        $thisMonthSalesMissions = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->where('start_datetime', '<=', now())
            ->count();
            
        // Janji temu bulan ini
        $appointmentsThisMonth = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->count();
            
        // Perusahaan yang dikunjungi
        $totalCompanies = SalesMissionDetail::distinct('company_name')->count();
            
        // 5 Sales Mission terbaru
        $recentSalesMissions = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->with('salesMissionDetail', 'department')
            ->orderBy('start_datetime', 'desc')
            ->limit(5)
            ->get();
            
        // Data untuk chart - Sales Mission per bulan tahun ini
        $monthlySalesMissions = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->whereYear('start_datetime', now()->year)
            ->select(DB::raw('MONTH(start_datetime) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $chartData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            $count = 0;
            
            foreach ($monthlySalesMissions as $data) {
                if ($data->month == $monthNumber) {
                    $count = $data->count;
                    break;
                }
            }
            
            $chartData[] = [
                'month' => $month,
                'count' => $count
            ];
        }
        
        // Data untuk lokasi - Sales Mission per provinsi
        $provinceData = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->select('province', DB::raw('COUNT(*) as count'))
            ->groupBy('province')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
            
        // Data untuk lokasi - Sales Mission per kota
        $cityData = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->select('city', DB::raw('COUNT(*) as count'))
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
            
        return view('sales_mission.dashboard.index', compact(
            'totalSalesMissions',
            'thisMonthSalesMissions',
            'appointmentsThisMonth',
            'recentSalesMissions',
            'chartData',
            'provinceData',
            'cityData'
        ));
    }
    
    /**
     * Daftar aktivitas Sales Mission
     */
    public function activitiesIndex(Request $request)
    {
        $query = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->with(['department', 'salesMissionDetail', 'teamAssignments.team']);
            
        // Join dengan sales_mission_details untuk sorting
        $query->join('sales_mission_details', 'activities.id', '=', 'sales_mission_details.activity_id');

        // Filter by search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('salesMissionDetail', function($sq) use ($searchTerm) { // This whereHas will now use the joined table context
                    $sq->where('sales_mission_details.company_name', 'like', "%{$searchTerm}%")
                       ->orWhere('sales_mission_details.company_pic', 'like', "%{$searchTerm}%")
                       ->orWhere('sales_mission_details.company_contact', 'like', "%{$searchTerm}%");
                })
                ->orWhere('activities.description', 'like', "%{$searchTerm}%") // Prefix with table name
                ->orWhere('activities.name', 'like', "%{$searchTerm}%")       // Prefix with table name
                ->orWhere('activities.city', 'like', "%{$searchTerm}%")         // Prefix with table name
                ->orWhere('activities.province', 'like', "%{$searchTerm}%");  // Prefix with table name
            });
        }
        
        // Filter by assignment status (new filter)
        if ($request->filled('assignment_status')) {
            $assignmentStatus = $request->assignment_status;
            if ($assignmentStatus === 'assigned') {
                $query->whereHas('teamAssignments');
            } elseif ($assignmentStatus === 'not_assigned') {
                $query->whereDoesntHave('teamAssignments');
            }
        }
        
        // Filter by Location (City)
        $filterLocationValue = $request->input('filter_location');
        if ($filterLocationValue) {
            $query->where('activities.city', $filterLocationValue); // Prefix with table name
        }
        
        // Filter by a single date (start_date)
        if ($request->filled('start_date')) {
            $selectedDate = Carbon::parse($request->start_date)->toDateString();
            $query->whereDate('activities.start_datetime', $selectedDate); // Prefix with table name
        }
        
        // Sorting logic
        $sortBy = $request->input('sort_by', 'activities.start_datetime'); // Default sort column
        $sortDirection = $request->input('sort_direction', 'desc');       // Default sort direction

        // Validate sort_by to prevent SQL injection and ensure it's a valid column
        $allowedSortColumns = [
            'sales_mission_details.company_name', 
            'sales_mission_details.company_pic', 
            'activities.city', 
            'activities.start_datetime'
        ];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'activities.start_datetime'; // Fallback to default if not allowed
        }
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc'; // Fallback to default
        }

        $query->orderBy($sortBy, $sortDirection);
        
        // Select a_activities.* to avoid issues with join columns if names are the same
        $query->select('activities.*');

        $activities = $query->paginate(10);
        $activities->appends($request->all());
        
        $cities = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->select('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city', 'asc')
            ->pluck('city');
            
        return view('sales_mission.activities.index', compact(
            'activities', 
            'cities', 
            'filterLocationValue',
            'sortBy',       // Pass to view
            'sortDirection' // Pass to view
        ));
    }
    
    /**
     * Daftar aktivitas Sales Mission untuk Superadmin
     */
    public function superAdminIndex(Request $request)
    {
        $query = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->with('department', 'salesMissionDetail');
            
        // Filter by search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('salesMissionDetail', function($sq) use ($searchTerm) {
                    $sq->where('company_name', 'like', "%{$searchTerm}%")
                       ->orWhere('company_pic', 'like', "%{$searchTerm}%")
                       ->orWhere('company_contact', 'like', "%{$searchTerm}%");
                })
                ->orWhere('description', 'like', "%{$searchTerm}%")
                ->orWhere('name', 'like', "%{$searchTerm}%")
                ->orWhere('city', 'like', "%{$searchTerm}%")
                ->orWhere('province', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->where(function($q) use ($request) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                
                // Activities that start within the date range
                $q->whereBetween('start_datetime', [$startDate, $endDate])
                  // OR activities that end within the date range
                  ->orWhereBetween('end_datetime', [$startDate, $endDate])
                  // OR activities that span the entire date range
                  ->orWhere(function($subq) use ($startDate, $endDate) {
                      $subq->where('start_datetime', '<=', $startDate)
                           ->where('end_datetime', '>=', $endDate);
                  });
            });
        } 
        // Filter by single start date only
        else if ($request->filled('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->start_date);
        }
        // Filter by single end date only
        else if ($request->filled('end_date')) {
            $query->whereDate('end_datetime', '<=', $request->end_date);
        }
        
        // Order by start date descending
        $query->orderBy('start_datetime', 'desc');
        
        $activities = $query->paginate(15);
        $activities->appends($request->all()); // Maintain filters in pagination
        
        return view('superadmin.activities.sales_mission', compact('activities'));
    }
    
    /**
     * Tampilan kalendar Sales Mission
     */
    public function activitiesCalendar()
    {
        $departments = Department::all();
        return view('sales_mission.activities.calendar', compact('departments'));
    }
    
    /**
     * Data JSON untuk kalendar Sales Mission
     */
    public function activitiesJson(Request $request)
    {
        // Query dasar
        $query = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->with(['department', 'salesMissionDetail']);
            
        // Filter berdasarkan department jika ada
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter berdasarkan range tanggal
        if ($request->filled('start') && $request->filled('end')) {
            $query->where(function($q) use ($request) {
                // Kegiatan yang dimulai dalam rentang waktu
                $q->whereBetween('start_datetime', [$request->start, $request->end])
                  // ATAU kegiatan yang berakhir dalam rentang waktu
                  ->orWhereBetween('end_datetime', [$request->start, $request->end])
                  // ATAU kegiatan yang mencakup seluruh rentang waktu
                  ->orWhere(function($subq) use ($request) {
                      $subq->where('start_datetime', '<', $request->start)
                           ->where('end_datetime', '>', $request->end);
                  });
            });
        }
        
        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('salesMissionDetail', function($sq) use ($search) {
                    $sq->where('company_name', 'like', "%{$search}%")
                      ->orWhere('company_pic', 'like', "%{$search}%")
                      ->orWhere('company_contact', 'like', "%{$search}%");
                })
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('province', 'like', "%{$search}%");
            });
        }
        
        $activities = $query->get();
        
        // Format untuk FullCalendar
        $events = [];
        foreach ($activities as $activity) {
            if ($activity->salesMissionDetail) {
                $events[] = [
                    'id' => $activity->id,
                    'title' => $activity->salesMissionDetail->company_name,
                    'start' => $activity->start_datetime,
                    'end' => $activity->end_datetime,
                    'className' => 'sales-mission-event',
                    'extendedProps' => [
                        'department' => 'WG',
                        'activity_type' => 'Sales Mission',
                        'description' => $activity->description,
                        'location' => $activity->city . ', ' . $activity->province,
                        'company_name' => $activity->salesMissionDetail->company_name,
                        'company_pic' => $activity->salesMissionDetail->company_pic,
                        'company_position' => $activity->salesMissionDetail->company_position,
                        'company_contact' => $activity->salesMissionDetail->company_contact,
                        'company_email' => $activity->salesMissionDetail->company_email,
                        'company_address' => $activity->salesMissionDetail->company_address
                    ]
                ];
            }
        }
        
        return response()->json($events);
    }
    
    /**
     * Form untuk edit Sales Mission
     */
    public function editActivity($id)
    {
        $activity = Activity::with('salesMissionDetail')->findOrFail($id);
        
        // Pastikan ini adalah Sales Mission
        if ($activity->activity_type !== 'Sales Mission' || !$activity->salesMissionDetail) {
            return redirect()->route('sales_mission.activities.index')
                ->with('error', 'Data sales mission tidak ditemukan.');
        }
        
        $departments = Department::all();
        $employees = Employee::orderBy('name')->get();
        
        // Data provinsi dan kota (contoh data)
        $provinces = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan',
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 'DKI Jakarta',
            'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 'Bali',
            'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
            'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara',
            'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo',
            'Sulawesi Barat', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat'
        ];
        
        $cities = [
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur',
            'Bandung', 'Bekasi', 'Tangerang', 'Depok', 'Bogor', 'Semarang', 'Yogyakarta',
            'Surabaya', 'Malang', 'Medan', 'Palembang', 'Makassar', 'Balikpapan', 'Banjarmasin',
            'Pontianak', 'Padang', 'Pekanbaru', 'Denpasar', 'Manado'
        ];
        
        return view('sales_mission.activities.edit', compact(
            'activity', 
            'departments', 
            'employees',
            'provinces',
            'cities'
        ));
    }
    
    /**
     * Update Sales Mission
     */
    public function updateActivity(Request $request, $id)
    {
        $activity = Activity::with('salesMissionDetail')->findOrFail($id);
        
        // Validasi
        $request->validate([
            'name' => 'required|exists:employees,name',
            'department_id' => 'required|exists:departments,id',
            'description' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime' => 'required|date_format:Y-m-d H:i|after:start_datetime',
            'company_name' => 'required|string|max:255',
            'company_pic' => 'required|string|max:255',
            'company_position' => 'required|string|max:255',
            'company_contact' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_address' => 'required|string'
        ]);
        
        // Update activity
        $activity->update([
            'name' => $request->name,
            'department_id' => $request->department_id,
            'description' => $request->description,
            'city' => $request->city,
            'province' => $request->province,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
        ]);
        
        // Update sales mission detail
        $activity->salesMissionDetail->update([
            'company_name' => $request->company_name,
            'company_pic' => $request->company_pic,
            'company_position' => $request->company_position,
            'company_contact' => $request->company_contact,
            'company_email' => $request->company_email,
            'company_address' => $request->company_address,
        ]);
        
        // Log the activity
        ActivityLogService::logUpdate(
            'sales_mission',
            'Updated sales mission to ' . $request->company_name,
            [
                'activity_id' => $activity->id,
                'company_name' => $request->company_name,
                'city' => $request->city,
                'province' => $request->province,
                'start_date' => $request->start_datetime
            ]
        );
        
        return redirect()->route('sales_mission.activities.index')
            ->with('success', 'Data sales mission berhasil diperbarui.');
    }
    
    /**
     * Hapus Sales Mission
     */
    public function destroyActivity($id)
    {
        $activity = Activity::findOrFail($id);
        
        // Pastikan ini adalah Sales Mission
        if ($activity->activity_type !== 'Sales Mission') {
            return redirect()->route('sales_mission.activities.index')
                ->with('error', 'Data sales mission tidak ditemukan.');
        }
        
        $companyName = $activity->salesMissionDetail ? $activity->salesMissionDetail->company_name : 'Unknown';
        
        // Log the activity before deletion
        ActivityLogService::logDelete(
            'sales_mission',
            'Deleted sales mission to ' . $companyName,
            [
                'activity_id' => $activity->id,
                'company_name' => $companyName,
                'city' => $activity->city,
                'province' => $activity->province
            ]
        );
        
        // Activity::delete akan otomatis menghapus salesMissionDetail karena relasi onDelete('cascade')
        $activity->delete();
        
        return redirect()->route('sales_mission.activities.index')
            ->with('success', 'Data sales mission berhasil dihapus.');
    }
    
    /**
     * Laporan Sales Mission
     */
    public function reports()
    {
        // Data untuk filter
        $provinces = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->select('province')
            ->distinct()
            ->get()
            ->pluck('province');
            
        $cities = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->select('city')
            ->distinct()
            ->get()
            ->pluck('city');
            
        return view('sales_mission.reports.index', compact('provinces', 'cities'));
    }
    
    /**
     * Ambil data untuk laporan
     */
    public function getReportData(Request $request)
    {
        // Get filter parameters
        $reportType = $request->report_type ?? 'sales_missions';
        $timePeriod = $request->time_period ?? 'monthly';
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $quarter = $request->quarter ?? ceil(now()->month / 3);
        
        // Query dasar - get ALL Sales Mission activities
        $query = Activity::where('activity_type', 'Sales Mission')
            ->with(['salesMissionDetail', 'department']);
            
        // Apply time period filters
        if ($timePeriod === 'monthly' && $month) {
            $query->whereYear('start_datetime', $year)
                ->whereMonth('start_datetime', $month);
        } elseif ($timePeriod === 'quarterly' && $quarter) {
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth = $quarter * 3;
            $query->whereYear('start_datetime', $year)
                ->whereMonth('start_datetime', '>=', $startMonth)
                ->whereMonth('start_datetime', '<=', $endMonth);
        } elseif ($timePeriod === 'yearly') {
            $query->whereYear('start_datetime', $year);
        }
        
        // Additional optional filters
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        
        if ($request->filled('company')) {
            $search = $request->company;
            $query->whereHas('salesMissionDetail', function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%");
            });
        }
        
        $activities = $query->orderBy('start_datetime', 'desc')->get();
        
        // Format data untuk berbagai chart dan tabel
        
        // 1. Data kunjungan per bulan
        $monthlyData = [];
        
        // Use the selected year rather than current year
        $chartYear = intval($year);
        
        for ($m = 1; $m <= 12; $m++) {
            $count = $activities->filter(function($activity) use ($m, $chartYear) {
                return Carbon::parse($activity->start_datetime)->month == $m &&
                       Carbon::parse($activity->start_datetime)->year == $chartYear;
            })->count();
            
            $monthlyData[] = [
                'month' => Carbon::createFromDate(null, $m, 1)->format('M'),
                'count' => $count
            ];
        }
        
        // 2. Data kunjungan per provinsi
        $provinceData = [];
        $provinceGroups = $activities->groupBy('province');
        
        foreach ($provinceGroups as $province => $items) {
            if (!empty($province)) {
                $provinceData[] = [
                    'province' => $province,
                    'count' => $items->count()
                ];
            }
        }
        
        // Sort provinsi berdasarkan jumlah
        usort($provinceData, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // 3. Daftar kunjungan lengkap untuk tabel
        $tableData = $activities->map(function($activity) {
            $detail = $activity->salesMissionDetail;
            return [
                'id' => $activity->id,
                'company_name' => $detail ? $detail->company_name : $activity->id . ' - No company info',
                'company_pic' => $detail ? $detail->company_pic : '-',
                'company_position' => $detail ? $detail->company_position : '-',
                'company_contact' => $detail ? $detail->company_contact : '-',
                'company_email' => $detail ? $detail->company_email : '-',
                'location' => $activity->city . ', ' . $activity->province,
                'date' => Carbon::parse($activity->start_datetime)->format('d M Y'),
                'employee' => $activity->name,
                'department' => $activity->department ? $activity->department->name : 'N/A'
            ];
        });
        
        return response()->json([
            'monthly' => $monthlyData,
            'provinces' => $provinceData,
            'table' => $tableData,
            'total' => $activities->count(),
            'filters' => [
                'report_type' => $reportType,
                'time_period' => $timePeriod,
                'year' => $year,
                'month' => $month,
                'quarter' => $quarter
            ]
        ]);
    }
    
    /**
     * Export laporan ke Excel
     */
    public function exportReport(Request $request)
    {
        // Ambil data seperti pada getReportData
        $reportType = $request->report_type ?? 'sales_missions';
        $timePeriod = $request->time_period ?? 'monthly';
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $quarter = $request->quarter ?? ceil(now()->month / 3);
        $format = $request->format ?? 'excel';
        
        // Query dasar - get ALL Sales Mission activities without filtering
        $query = Activity::where('activity_type', 'Sales Mission');
        
        // First check if there's any data at all
        $totalRecords = $query->count();
        
        // Skip time filtering for debugging purposes
        if ($request->has('debug')) {
            // Don't apply time filters for debug mode
        } else {
            // Filter berdasarkan periode waktu (only for non-debug)
            if ($timePeriod === 'monthly' && $month) {
                $query->whereYear('start_datetime', $year)
                    ->whereMonth('start_datetime', $month);
            } elseif ($timePeriod === 'quarterly' && $quarter) {
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;
                $query->whereYear('start_datetime', $year)
                    ->whereMonth('start_datetime', '>=', $startMonth)
                    ->whereMonth('start_datetime', '<=', $endMonth);
            } elseif ($timePeriod === 'yearly') {
                $query->whereYear('start_datetime', $year);
            }
        }
        
        // Use left join to include activities without salesMissionDetail
        $query->with(['salesMissionDetail', 'department']);
        $activities = $query->orderBy('start_datetime', 'desc')->get();
        
        // Tentukan judul laporan berdasarkan jenis dan periode
        $reportTitle = $this->getReportTitle($reportType, $timePeriod, $year, $month, $quarter);
        
        // Buat file Excel menggunakan OpenSpout
        $filename = 'sales_mission_report_' . $timePeriod . '_' . now()->format('Ymd_His') . '.xlsx';
        
        // Set headers for direct download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $writer = new Writer();
        $writer->openToFile('php://output');
        
        // Add header row dengan style (menggunakan API yang benar)
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(12);
        
        // Set report title
        $writer->addRow(Row::fromValues([$reportTitle], $headerStyle));
        $writer->addRow(Row::fromValues(['Generated at: ' . now()->format('Y-m-d H:i:s')]));
        
        // Add more detailed debug info
        $writer->addRow(Row::fromValues([
            'Debug Info: Found ' . $activities->count() . ' records out of ' . $totalRecords . ' total Sales Missions' 
        ]));
        
        // List activity IDs for debugging
        $ids = $activities->pluck('id')->join(', ');
        $writer->addRow(Row::fromValues(['Activity IDs: ' . $ids]));
        
        // Count activities with and without salesMissionDetail
        $withDetails = $activities->filter(function($activity) {
            return $activity->salesMissionDetail !== null;
        })->count();
        
        $withoutDetails = $activities->count() - $withDetails;
        
        $writer->addRow(Row::fromValues([
            'Activities with details: ' . $withDetails . ' | Activities without details: ' . $withoutDetails
        ]));
        
        $writer->addRow(Row::fromValues(['']));
        
        // Add headers based on report type
        if ($reportType === 'sales_missions' || empty($reportType)) {
            $headers = ['Company', 'PIC', 'Position', 'Contact', 'Email', 'Date', 'Location', 'Employee', 'Department', 'Description'];
            $writer->addRow(Row::fromValues($headers, $headerStyle));
            
            // Add data rows
            foreach ($activities as $activity) {
                try {
                    // Check if salesMissionDetail exists
                    if (!$activity->salesMissionDetail) {
                        $writer->addRow(Row::fromValues([
                            $activity->id . " - No company info", // Use ID as identifier
                            "-",
                            "-",
                            "-",
                            "-",
                            Carbon::parse($activity->start_datetime)->format('Y-m-d H:i'),
                            $activity->city . ', ' . $activity->province,
                            $activity->name,
                            $activity->department->name ?? 'N/A',
                            $activity->description
                        ]));
                        continue;
                    }
                    
                    $writer->addRow(Row::fromValues([
                        $activity->salesMissionDetail->company_name,
                        $activity->salesMissionDetail->company_pic,
                        $activity->salesMissionDetail->company_position,
                        $activity->salesMissionDetail->company_contact,
                        $activity->salesMissionDetail->company_email,
                        Carbon::parse($activity->start_datetime)->format('Y-m-d H:i'),
                        $activity->city . ', ' . $activity->province,
                        $activity->name,
                        $activity->department->name ?? 'N/A',
                        $activity->description
                    ]));
                } catch (\Exception $e) {
                    // Log the error but continue processing
                    $writer->addRow(Row::fromValues([
                        "Error with ID " . $activity->id . ": " . $e->getMessage(),
                        "-",
                        "-",
                        "-",
                        "-",
                        "-",
                        "-",
                        "-",
                        "-",
                        "-"
                    ]));
                }
            }
        } elseif ($reportType === 'companies') {
            // Group data by company
            $companies = [];
            foreach ($activities as $activity) {
                $companyName = $activity->salesMissionDetail->company_name;
                if (!isset($companies[$companyName])) {
                    $companies[$companyName] = [
                        'company_name' => $companyName,
                        'company_pic' => $activity->salesMissionDetail->company_pic,
                        'company_position' => $activity->salesMissionDetail->company_position,
                        'company_contact' => $activity->salesMissionDetail->company_contact,
                        'company_email' => $activity->salesMissionDetail->company_email,
                        'visits' => 0,
                        'last_visit' => null
                    ];
                }
                $companies[$companyName]['visits']++;
                
                $visitDate = Carbon::parse($activity->start_datetime);
                if ($companies[$companyName]['last_visit'] === null || 
                    $visitDate->gt(Carbon::parse($companies[$companyName]['last_visit']))) {
                    $companies[$companyName]['last_visit'] = $visitDate->format('Y-m-d');
                }
            }
            
            // Add headers
            $headers = ['Company', 'PIC', 'Position', 'Contact', 'Email', 'Visits', 'Last Visit'];
            $writer->addRow(Row::fromValues($headers, $headerStyle));
            
            // Add company data
            foreach ($companies as $company) {
                $writer->addRow(Row::fromValues([
                    $company['company_name'],
                    $company['company_pic'],
                    $company['company_position'],
                    $company['company_contact'],
                    $company['company_email'],
                    $company['visits'],
                    $company['last_visit']
                ]));
            }
        } elseif ($reportType === 'locations') {
            // Group data by location
            $locations = [];
            foreach ($activities as $activity) {
                $key = $activity->province . ' - ' . $activity->city;
                if (!isset($locations[$key])) {
                    $locations[$key] = [
                        'province' => $activity->province,
                        'city' => $activity->city,
                        'visits' => 0,
                        'companies' => []
                    ];
                }
                $locations[$key]['visits']++;
                if (!in_array($activity->salesMissionDetail->company_name, $locations[$key]['companies'])) {
                    $locations[$key]['companies'][] = $activity->salesMissionDetail->company_name;
                }
            }
            
            // Add headers
            $headers = ['Province', 'City', 'Total Visits', 'Unique Companies'];
            $writer->addRow(Row::fromValues($headers, $headerStyle));
            
            // Add location data
            foreach ($locations as $location) {
                $writer->addRow(Row::fromValues([
                    $location['province'],
                    $location['city'],
                    $location['visits'],
                    count($location['companies'])
                ]));
            }
        }
        
        $writer->close();
    }
    
    /**
     * Helper untuk mendapatkan judul laporan
     */
    private function getReportTitle($reportType, $timePeriod, $year, $month = null, $quarter = null)
    {
        $typeTitle = match($reportType) {
            'sales_missions' => 'Sales Mission Report',
            'companies' => 'Company Visit Report',
            'locations' => 'Location Report',
            default => 'Sales Mission Report'
        };
        
        $periodTitle = match($timePeriod) {
            'monthly' => 'Monthly - ' . Carbon::createFromDate($year, $month, 1)->format('F Y'),
            'quarterly' => 'Quarterly - Q' . $quarter . ' ' . $year,
            'yearly' => 'Yearly - ' . $year,
            default => date('Y')
        };
        
        return $typeTitle . ' - ' . $periodTitle;
    }
    
    /**
     * Field Management - Team Assignments
     */
    public function fieldAssignments()
    {
        return view('sales_mission.field.assignments.index');
    }
    
    public function createFieldAssignment()
    {
        $departments = Department::all();
        $employees = Employee::all();
        return view('sales_mission.field.assignments.create', compact('departments', 'employees'));
    }
    
    public function storeFieldAssignment(Request $request)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.assignments')
            ->with('success', 'Team assignment created successfully.');
    }
    
    public function editFieldAssignment($id)
    {
        $departments = Department::all();
        $employees = Employee::all();
        // Placeholder for field assignment edit view
        return view('sales_mission.field.assignments.edit', compact('departments', 'employees'));
    }
    
    public function updateFieldAssignment(Request $request, $id)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.assignments')
            ->with('success', 'Team assignment updated successfully.');
    }
    
    public function destroyFieldAssignment($id)
    {
        // Logic will be implemented when model is created
        return redirect()->route('sales_mission.field.assignments')
            ->with('success', 'Team assignment deleted successfully.');
    }
    
    /**
     * Field Management - Target Companies
     */
    public function fieldCompanies()
    {
        return view('sales_mission.field.companies.index');
    }
    
    public function createFieldCompany()
    {
        return view('sales_mission.field.companies.create');
    }
    
    public function storeFieldCompany(Request $request)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.companies')
            ->with('success', 'Target company created successfully.');
    }
    
    public function editFieldCompany($id)
    {
        return view('sales_mission.field.companies.edit');
    }
    
    public function updateFieldCompany(Request $request, $id)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.companies')
            ->with('success', 'Target company updated successfully.');
    }
    
    public function destroyFieldCompany($id)
    {
        // Logic will be implemented when model is created
        return redirect()->route('sales_mission.field.companies')
            ->with('success', 'Target company deleted successfully.');
    }
    
    /**
     * Field Management - Appointments
     */
    public function fieldAppointments()
    {
        return view('sales_mission.field.appointments.index');
    }
    
    public function createFieldAppointment()
    {
        return view('sales_mission.field.appointments.create');
    }
    
    public function storeFieldAppointment(Request $request)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.appointments')
            ->with('success', 'Appointment created successfully.');
    }
    
    public function editFieldAppointment($id)
    {
        return view('sales_mission.field.appointments.edit');
    }
    
    public function updateFieldAppointment(Request $request, $id)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.appointments')
            ->with('success', 'Appointment updated successfully.');
    }
    
    public function destroyFieldAppointment($id)
    {
        // Logic will be implemented when model is created
        return redirect()->route('sales_mission.field.appointments')
            ->with('success', 'Appointment deleted successfully.');
    }
    
    /**
     * Field Management - Visit Schedules
     */
    public function fieldSchedules()
    {
        return view('sales_mission.field.schedules.index');
    }
    
    public function createFieldSchedule()
    {
        return view('sales_mission.field.schedules.create');
    }
    
    public function storeFieldSchedule(Request $request)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.schedules')
            ->with('success', 'Visit schedule created successfully.');
    }
    
    public function editFieldSchedule($id)
    {
        return view('sales_mission.field.schedules.edit');
    }
    
    public function updateFieldSchedule(Request $request, $id)
    {
        // Validation will be implemented when model is created
        return redirect()->route('sales_mission.field.schedules')
            ->with('success', 'Visit schedule updated successfully.');
    }
    
    public function destroyFieldSchedule($id)
    {
        // Logic will be implemented when model is created
        return redirect()->route('sales_mission.field.schedules')
            ->with('success', 'Visit schedule deleted successfully.');
    }

    /**
     * Export data aktivitas Sales Mission ke Excel.
     */
    public function exportActivities(Request $request)
    {
        $filename = 'sales_mission_activities_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ActivityExport($request), $filename);
    }
}
