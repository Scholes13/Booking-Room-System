<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\SalesMissionDetail;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ActivityType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;

class SalesMissionController extends Controller
{
    /**
     * Dashboard utama Sales Mission
     */
    public function dashboard()
    {
        // Total Sales Mission
        $totalSalesMissions = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->count();
            
        // Sales Mission bulan ini
        $thisMonthSalesMissions = Activity::where('activity_type', 'Sales Mission')
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
        $locationData = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->select('province', DB::raw('COUNT(*) as count'))
            ->groupBy('province')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
            
        return view('sales_mission.dashboard.index', compact(
            'totalSalesMissions',
            'thisMonthSalesMissions',
            'totalCompanies',
            'recentSalesMissions',
            'chartData',
            'locationData'
        ));
    }
    
    /**
     * Daftar aktivitas Sales Mission
     */
    public function activitiesIndex()
    {
        $activities = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->with('department', 'salesMissionDetail')
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);
            
        return view('sales_mission.activities.index', compact('activities'));
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
                        'company_contact' => $activity->salesMissionDetail->company_contact,
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
            'company_contact' => 'required|string|max:255',
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
            'company_contact' => $request->company_contact,
            'company_address' => $request->company_address,
        ]);
        
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
                'company_contact' => $detail ? $detail->company_contact : '-',
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
            $headers = ['Company', 'PIC', 'Contact', 'Date', 'Location', 'Employee', 'Department', 'Description'];
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
                        $activity->salesMissionDetail->company_contact,
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
                        'company_contact' => $activity->salesMissionDetail->company_contact,
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
            $headers = ['Company', 'PIC', 'Contact', 'Visits', 'Last Visit'];
            $writer->addRow(Row::fromValues($headers, $headerStyle));
            
            // Add company data
            foreach ($companies as $company) {
                $writer->addRow(Row::fromValues([
                    $company['company_name'],
                    $company['company_pic'],
                    $company['company_contact'],
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
}
