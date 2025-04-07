<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Services\ReportExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Department;

class ReportController extends Controller
{
    protected $exportService;

    public function __construct(ReportExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    // Halaman utama reports
    public function index()
    {
        $meetingRooms = MeetingRoom::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        
        return view('admin.reports.index', compact('meetingRooms', 'departments'));
    }

    // Endpoint untuk mendapatkan data report (digunakan oleh client, misalnya via AJAX)
    public function getData(Request $request)
    {
        try {
            $reportType = $request->input('report_type', 'bookings');
            $timePeriod = $request->input('time_period', 'monthly');
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);
            $quarter = $request->input('quarter', ceil(now()->month / 3));

            // Log request parameters for debugging
            Log::info('Report getData parameters:', [
                'report_type' => $reportType,
                'time_period' => $timePeriod,
                'year' => $year,
                'month' => $month,
                'quarter' => $quarter
            ]);

            // Get date range based on time period
            $dateRange = $this->getDateRange($timePeriod, $year, $month, $quarter);

            // Get report data based on type
            $data = null;
            switch ($reportType) {
                case 'rooms':
                    $data = $this->getRoomData($dateRange);
                    break;
                case 'departments':
                    $data = $this->getDepartmentData($dateRange);
                    break;
                case 'bookings':
                    $data = $this->getBookingData($dateRange);
                    break;
                default:
                    return response()->json([
                        'error' => true,
                        'message' => 'Invalid report type'
                    ], 400);
            }
            
            // If data is already a response object, return it
            if (is_object($data) && method_exists($data, 'getStatusCode')) {
                return $data;
            }
            
            // Determine the view path based on the request URL
            $viewPrefix = 'admin';
            $referer = $request->header('referer');
            if ($referer && strpos($referer, '/bas/') !== false) {
                $viewPrefix = 'admin_bas';
            }
            
            Log::info('Using view prefix for report:', [
                'prefix' => $viewPrefix, 
                'referer' => $referer
            ]);
            
            // Create custom JSON response since we don't have view templates yet
            return response()->json([
                'html' => $this->generateHtmlReport($reportType, $data, $dateRange),
                'chartData' => $this->generateChartData($reportType, $data),
                'raw' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => true,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Endpoint untuk export report ke file (Excel, PDF, atau CSV)
    public function export(Request $request)
    {
        try {
            $request->validate([
                'report_type' => 'required|in:rooms,departments,bookings',
                'time_period' => 'required|in:monthly,quarterly,yearly',
                'year'        => 'required|integer',
                'month'       => 'nullable|integer|between:1,12',
                'quarter'     => 'nullable|integer|between:1,4',
                'format'      => 'required|in:excel,pdf,csv',
            ]);

            Log::info("Export Request Parameters:", $request->all());

            $dateRange = $this->getDateRange(
                $request->time_period,
                $request->year,
                $request->month,
                $request->quarter
            );

            Log::info("Export Date Range", [
                'start' => $dateRange['start']->toDateString(),
                'end'   => $dateRange['end']->toDateString()
            ]);

            $data = $this->getExportData($request->report_type, $dateRange);
            $headers = $this->getExportHeaders($request->report_type);
            $filename = $this->generateExportFilename($request->report_type, $request->time_period, $request->format);

            Log::info("Exporting file: " . $filename);

            return $this->exportService->export($data, $headers, $filename, $request->format);
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    /* === METODE HELPER === */

    // Menghasilkan rentang tanggal sesuai periode yang dipilih
    private function getDateRange($timePeriod, $year, $month, $quarter)
    {
        // Pastikan parameter bertipe integer
        $year = (int) $year;
        $month = (int) $month;
        $quarter = (int) $quarter;

        // Log parameter untuk debugging
        Log::debug('getDateRange params', [
            'timePeriod' => $timePeriod,
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter,
            'types' => [
                'year' => gettype($year),
                'month' => gettype($month),
                'quarter' => gettype($quarter)
            ]
        ]);

        $start = Carbon::create($year);
        $end = Carbon::create($year);

        switch ($timePeriod) {
            case 'monthly':
                $start->setMonth($month)->startOfMonth();
                $end->setMonth($month)->endOfMonth();
                break;
            case 'quarterly':
                $startMonth = ($quarter - 1) * 3 + 1;
                $start->setMonth($startMonth)->startOfMonth();
                $end->setMonth($startMonth + 2)->endOfMonth();
                break;
            case 'yearly':
                $start->startOfYear();
                $end->endOfYear();
                break;
        }

        Log::debug('getDateRange result', [
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString()
        ]);

        return [
            'start' => $start,
            'end' => $end
        ];
    }

    // === REPORT: RUANGAN ===
    private function getRoomData($dateRange)
    {
        $rooms = MeetingRoom::all();
        $data = [];
        $totalBookingsOverall = 0;

        foreach ($rooms as $room) {
            Log::info("Processing room", ['ID' => $room->id, 'Name' => $room->name]);

            $bookings = Booking::where('meeting_room_id', $room->id)
                ->whereBetween('date', [
                    $dateRange['start']->toDateString(),
                    $dateRange['end']->toDateString()
                ])
                ->get();

            $totalBookings = $bookings->count();
            $totalBookingsOverall += $totalBookings;

            // Hitung total jam yang dipakai (hours_used)
            $hoursUsed = $bookings->sum(function ($booking) {
                if (empty($booking->date) || empty($booking->start_time) || empty($booking->end_time)) {
                    return 0;
                }
                $start = Carbon::parse($booking->date . ' ' . $booking->start_time);
                $end   = Carbon::parse($booking->date . ' ' . $booking->end_time);
                return $end->greaterThan($start)
                    ? round($start->diffInMinutes($end) / 60, 2)
                    : 0;
            });

            // Asumsi: 9 jam kerja per hari kerja
            $workingDays     = max($dateRange['start']->diffInWeekdays($dateRange['end']), 1);
            $availableHours  = $workingDays * 9;
            $usagePercentage = ($availableHours > 0 && $hoursUsed >= 0)
                ? round(($hoursUsed / $availableHours) * 100, 2)
                : 0;

            // Status ruangan: 'occupied' jika ada booking pada saat ini
            $currentBooking = Booking::where('meeting_room_id', $room->id)
                ->where('date', Carbon::now()->toDateString())
                ->where('start_time', '<=', Carbon::now()->format('H:i:s'))
                ->where('end_time', '>=', Carbon::now()->format('H:i:s'))
                ->first();
            $status = $currentBooking ? 'occupied' : 'available';

            $data[] = [
                'id'               => $room->id,
                'name'             => $room->name,
                'capacity'         => $room->capacity,
                'total_bookings'   => $totalBookings,
                'hours_used'       => $hoursUsed,
                'usage_percentage' => $usagePercentage,
                'status'           => $status,
            ];
        }

        if ($totalBookingsOverall == 0) {
            return response()->json([
                'no_data' => true,
                'message' => 'Tidak ada data untuk periode yang dipilih.'
            ]);
        }

        $result = [
            'total_rooms'   => $rooms->count(),
            'average_usage' => $rooms->count()
                ? round(array_sum(array_column($data, 'usage_percentage')) / $rooms->count(), 2)
                : 0,
            'most_used_room'=> $rooms->count()
                ? collect($data)->sortByDesc('hours_used')->first()['name']
                : 'No data',
            'rooms'         => $data
        ];

        return response()->json($result);
    }

    // === REPORT: DEPARTEMEN ===
    private function getDepartmentData($dateRange)
    {
        $departments = Booking::select('department')->distinct()->get();
        $data = [];
        $totalBookingsOverall = 0;

        foreach ($departments as $dept) {
            $deptBookings = Booking::where('department', $dept->department)
                ->whereBetween('date', [
                    $dateRange['start']->toDateString(),
                    $dateRange['end']->toDateString()
                ])
                ->get();

            $totalBookings = $deptBookings->count();
            $totalBookingsOverall += $totalBookings;

            $hoursUsed = $deptBookings->sum(function ($booking) {
                if (empty($booking->date) || empty($booking->start_time) || empty($booking->end_time)) {
                    return 0;
                }
                $start = Carbon::parse($booking->date . ' ' . $booking->start_time);
                $end   = Carbon::parse($booking->date . ' ' . $booking->end_time);
                return $end->greaterThan($start)
                    ? $start->diffInHours($end)
                    : 0;
            });

            $averageDuration = $totalBookings > 0
                ? round($hoursUsed / $totalBookings, 2)
                : 0;

            $data[] = [
                'department'       => $dept->department,
                'total_bookings'   => $totalBookings,
                'hours_used'       => $hoursUsed,
                'average_duration' => $averageDuration,
            ];
        }

        if ($totalBookingsOverall == 0) {
            return response()->json([
                'no_data' => true,
                'message' => 'Tidak ada data untuk periode yang dipilih.'
            ]);
        }

        $result = [
            'total_departments' => $departments->count(),
            'departments'       => $data,
        ];

        return response()->json($result);
    }

    // === REPORT: BOOKING (dengan KATEGORI) ===
    private function getBookingData($dateRange)
    {
        $bookings = Booking::with('meetingRoom')
            ->whereBetween('date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString()
            ])->get();

        if ($bookings->count() == 0) {
            return response()->json([
                'no_data' => true,
                'message' => 'Tidak ada data untuk periode yang dipilih.'
            ]);
        }

        // Inisialisasi penghitung kategori
        $categoryCounts = [
            'meeting'   => 0,
            'interview' => 0,
            'training'  => 0,
            'hosting'   => 0,
            'other'     => 0,
        ];

        $mapped = $bookings->map(function ($b) use (&$categoryCounts) {
            $descLower = strtolower($b->description);
            $category = 'other';
            if (strpos($descLower, 'meeting') !== false) {
                $category = 'meeting';
            } elseif (strpos($descLower, 'interview') !== false) {
                $category = 'interview';
            } elseif (strpos($descLower, 'training') !== false) {
                $category = 'training';
            } elseif (strpos($descLower, 'hosting') !== false) {
                $category = 'hosting';
            }
            $categoryCounts[$category]++;
            return [
                'id'           => $b->id,
                'nama'         => $b->nama,
                'department'   => $b->department,
                'date'         => $b->date,
                'start_time'   => $b->start_time,
                'end_time'     => $b->end_time,
                'meeting_room' => $b->meetingRoom ? $b->meetingRoom->name : null,
                'description'  => $b->description,
                'category'     => $category,
            ];
        });

        $total = $bookings->count();
        $categories = ['meeting', 'interview', 'training', 'hosting'];
        $categoryStats = [];

        foreach ($categories as $cat) {
            $count = $categoryCounts[$cat];
            $percentage = $total > 0 ? round(($count / $total) * 100, 2) : 0;
            $categoryStats[$cat] = [
                'count'      => $count,
                'percentage' => $percentage,
            ];
        }

        $result = [
            'total_bookings' => $total,
            'category_stats' => $categoryStats,
            'bookings'       => $mapped,
        ];

        return response()->json($result);
    }

    // ==================== EXPORT METHODS ====================
    private function getExportData($reportType, $dateRange)
    {
        switch ($reportType) {
            case 'rooms':
                return $this->getRoomExportData($dateRange);
            case 'departments':
                return $this->getDepartmentExportData($dateRange);
            case 'bookings':
                return $this->getBookingExportData($dateRange);
            default:
                return [];
        }
    }

    private function getRoomExportData($dateRange)
    {
        $rooms = MeetingRoom::all();
        $data = [];

        foreach ($rooms as $room) {
            $bookings = Booking::where('meeting_room_id', $room->id)
                ->whereBetween('date', [
                    $dateRange['start']->toDateString(),
                    $dateRange['end']->toDateString()
                ])
                ->get();

            $totalBookings = $bookings->count();

            $hoursUsed = $bookings->sum(function ($booking) {
                if (empty($booking->date) || empty($booking->start_time) || empty($booking->end_time)) {
                    return 0;
                }
                $start = Carbon::parse($booking->date . ' ' . $booking->start_time);
                $end   = Carbon::parse($booking->date . ' ' . $booking->end_time);
                return $end->greaterThan($start)
                    ? $start->diffInHours($end)
                    : 0;
            });

            $data[] = [
                'Room Name'      => $room->name,
                'Capacity'       => $room->capacity,
                'Total Bookings' => $totalBookings,
                'Hours Used'     => $hoursUsed,
            ];
        }

        return $data;
    }

    private function getDepartmentExportData($dateRange)
    {
        $departments = Booking::select('department')->distinct()->get();
        $data = [];

        foreach ($departments as $dept) {
            $deptBookings = Booking::where('department', $dept->department)
                ->whereBetween('date', [
                    $dateRange['start']->toDateString(),
                    $dateRange['end']->toDateString()
                ])
                ->get();

            $totalBookings = $deptBookings->count();

            $hoursUsed = $deptBookings->sum(function ($booking) {
                if (empty($booking->date) || empty($booking->start_time) || empty($booking->end_time)) {
                    return 0;
                }
                $start = Carbon::parse($booking->date . ' ' . $booking->start_time);
                $end   = Carbon::parse($booking->date . ' ' . $booking->end_time);
                return $end->greaterThan($start)
                    ? $start->diffInHours($end)
                    : 0;
            });

            $averageDuration = $totalBookings > 0
                ? round($hoursUsed / $totalBookings, 2)
                : 0;

            $data[] = [
                'Department'      => $dept->department,
                'Total Bookings'  => $totalBookings,
                'Hours Used'      => $hoursUsed,
                'Average Duration'=> $averageDuration,
            ];
        }

        return $data;
    }

    private function getBookingExportData($dateRange)
    {
        $bookings = Booking::with('meetingRoom')
            ->whereBetween('date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString()
            ])->get();
        $data = [];

        foreach ($bookings as $booking) {
            $data[] = [
                'ID'           => $booking->id,
                'Name'         => $booking->nama,
                'Department'   => $booking->department,
                'Date'         => $booking->date,
                'Start Time'   => $booking->start_time,
                'End Time'     => $booking->end_time,
                'Room'         => $booking->meetingRoom ? $booking->meetingRoom->name : null,
                'Description'  => $booking->description,
            ];
        }

        return $data;
    }

    private function getExportHeaders($reportType)
    {
        switch ($reportType) {
            case 'rooms':
                return ['Room Name', 'Capacity', 'Total Bookings', 'Hours Used'];
            case 'departments':
                return ['Department', 'Total Bookings', 'Hours Used', 'Average Duration'];
            case 'bookings':
                return ['ID', 'Name', 'Department', 'Date', 'Start Time', 'End Time', 'Room', 'Description'];
            default:
                return [];
        }
    }

    private function generateExportFilename($reportType, $timePeriod, $format)
    {
        $timestamp = now()->format('Ymd_His');
        $extension = ($format === 'pdf') ? 'pdf'
            : (($format === 'csv') ? 'csv' : 'xlsx');

        return "{$reportType}_report_{$timePeriod}_{$timestamp}.{$extension}";
    }

    /**
     * Generate chart data for reports
     */
    private function generateChartData($reportType, $data)
    {
        $chartData = [];
        
        switch ($reportType) {
            case 'rooms':
                if (isset($data['rooms']) && count($data['rooms']) > 0) {
                    // Room usage chart
                    $labels = [];
                    $usageData = [];
                    
                    foreach ($data['rooms'] as $room) {
                        $labels[] = $room['name'];
                        $usageData[] = $room['usage_percentage'];
                    }
                    
                    $chartData[] = [
                        'type' => 'bar',
                        'data' => [
                            'labels' => $labels,
                            'datasets' => [
                                [
                                    'label' => 'Room Usage %',
                                    'data' => $usageData,
                                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                                    'borderColor' => 'rgb(54, 162, 235)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ],
                        'options' => [
                            'responsive' => true,
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top',
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => 'Room Usage Percentage'
                                ]
                            ]
                        ]
                    ];
                }
                break;
                
            case 'departments':
                if (isset($data['departments']) && count($data['departments']) > 0) {
                    // Department bookings chart
                    $labels = [];
                    $bookingsData = [];
                    
                    foreach ($data['departments'] as $dept) {
                        $labels[] = $dept['department'];
                        $bookingsData[] = $dept['total_bookings'];
                    }
                    
                    $chartData[] = [
                        'type' => 'bar',
                        'data' => [
                            'labels' => $labels,
                            'datasets' => [
                                [
                                    'label' => 'Total Bookings',
                                    'data' => $bookingsData,
                                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                                    'borderColor' => 'rgb(75, 192, 192)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ],
                        'options' => [
                            'responsive' => true,
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top',
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => 'Bookings by Department'
                                ]
                            ]
                        ]
                    ];
                }
                break;
                
            case 'bookings':
                if (isset($data['category_stats']) && !empty($data['category_stats'])) {
                    // Booking categories pie chart
                    $labels = [];
                    $counts = [];
                    
                    foreach ($data['category_stats'] as $category => $stats) {
                        $labels[] = ucfirst($category);
                        $counts[] = $stats['count'];
                    }
                    
                    $chartData[] = [
                        'type' => 'pie',
                        'data' => [
                            'labels' => $labels,
                            'datasets' => [
                                [
                                    'data' => $counts,
                                    'backgroundColor' => [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)',
                                        'rgba(153, 102, 255, 0.7)'
                                    ],
                                    'borderColor' => [
                                        'rgb(255, 99, 132)',
                                        'rgb(54, 162, 235)',
                                        'rgb(255, 206, 86)',
                                        'rgb(75, 192, 192)',
                                        'rgb(153, 102, 255)'
                                    ],
                                    'borderWidth' => 1
                                ]
                            ]
                        ],
                        'options' => [
                            'responsive' => true,
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top',
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => 'Bookings by Category'
                                ]
                            ]
                        ]
                    ];
                }
                break;
        }
        
        return $chartData;
    }

    /**
     * Generate HTML report directly since we don't have view templates
     */
    private function generateHtmlReport($reportType, $data, $dateRange)
    {
        $html = '';
        
        if (is_object($data)) {
            // Convert JSON response to array
            $data = json_decode(json_encode($data), true);
        }
        
        switch ($reportType) {
            case 'rooms':
                $html = $this->generateRoomsHtml($data);
                break;
            case 'departments':
                $html = $this->generateDepartmentsHtml($data);
                break;
            case 'bookings':
                $html = $this->generateBookingsHtml($data);
                break;
        }
        
        return $html;
    }
    
    private function generateRoomsHtml($data)
    {
        $html = '<div class="space-y-6">';
        
        // Stats Cards
        $html .= '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
        $html .= $this->createStatCard('Total Rooms', $data['total_rooms'] ?? 0, 'bg-blue-50 text-blue-900');
        $html .= $this->createStatCard('Average Usage', ($data['average_usage'] ?? 0) . '%', 'bg-green-50 text-green-900');
        $html .= $this->createStatCard('Most Used Room', $data['most_used_room'] ?? '-', 'bg-purple-50 text-purple-900');
        $html .= '</div>';
        
        // Table
        $html .= '<div class="table-container mt-8">';
        $html .= '<h3 class="text-lg font-medium text-gray-900 mb-4">Room Details</h3>';
        $html .= '<table class="min-w-full divide-y divide-gray-200">';
        $html .= '<thead class="bg-gray-50">';
        $html .= '<tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours Used</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage %</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                 </tr>';
        $html .= '</thead>';
        $html .= '<tbody class="bg-white divide-y divide-gray-200">';
        
        if (isset($data['rooms']) && is_array($data['rooms'])) {
            foreach ($data['rooms'] as $room) {
                $html .= '<tr>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($room['name'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($room['capacity'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($room['total_bookings'] ?? 0) . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($room['hours_used'] ?? 0) . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($room['usage_percentage'] ?? 0) . '%</td>';
                $html .= '<td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm ' . 
                            (($room['status'] ?? '') === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . '">
                                ' . ($room['status'] ?? '-') . '
                            </span>
                          </td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function generateDepartmentsHtml($data)
    {
        $html = '<div class="space-y-6">';
        
        // Stats Card
        $html .= '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
        $html .= $this->createStatCard('Total Departments', $data['total_departments'] ?? 0, 'bg-blue-50 text-blue-900');
        $html .= '</div>';
        
        // Table
        $html .= '<div class="table-container mt-8">';
        $html .= '<h3 class="text-lg font-medium text-gray-900 mb-4">Departments Details</h3>';
        $html .= '<table class="min-w-full divide-y divide-gray-200">';
        $html .= '<thead class="bg-gray-50">';
        $html .= '<tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours Used</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average Duration</th>
                 </tr>';
        $html .= '</thead>';
        $html .= '<tbody class="bg-white divide-y divide-gray-200">';
        
        if (isset($data['departments']) && is_array($data['departments'])) {
            foreach ($data['departments'] as $dept) {
                $html .= '<tr>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($dept['department'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($dept['total_bookings'] ?? 0) . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($dept['hours_used'] ?? 0) . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($dept['average_duration'] ?? '-') . '</td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function generateBookingsHtml($data)
    {
        $html = '<div class="space-y-6">';
        
        // Stats Cards
        $html .= '<div class="grid grid-cols-1 md:grid-cols-5 gap-4">';
        $html .= $this->createStatCard('Total Bookings', $data['total_bookings'] ?? 0, 'bg-blue-50 text-blue-900');
        
        // Add category stats cards
        $categories = ['meeting', 'interview', 'training', 'hosting'];
        $colors = [
            'meeting' => 'bg-blue-100 text-blue-900',
            'interview' => 'bg-green-100 text-green-900',
            'training' => 'bg-orange-100 text-orange-900',
            'hosting' => 'bg-red-100 text-red-900',
        ];
        
        foreach ($categories as $cat) {
            $count = $data['category_stats'][$cat]['count'] ?? 0;
            $percentage = $data['category_stats'][$cat]['percentage'] ?? 0;
            $html .= $this->createStatCard(
                ucfirst($cat),
                $count . ' (' . $percentage . '%)',
                $colors[$cat]
            );
        }
        
        $html .= '</div>';
        
        // Table
        $html .= '<div class="table-container mt-8">';
        $html .= '<h3 class="text-lg font-medium text-gray-900 mb-4">Bookings Details</h3>';
        $html .= '<table class="min-w-full divide-y divide-gray-200">';
        $html .= '<thead class="bg-gray-50">';
        $html .= '<tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                 </tr>';
        $html .= '</thead>';
        $html .= '<tbody class="bg-white divide-y divide-gray-200">';
        
        if (isset($data['bookings']) && is_array($data['bookings'])) {
            foreach ($data['bookings'] as $booking) {
                $html .= '<tr>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($booking['nama'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($booking['department'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($booking['date'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($booking['start_time'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($booking['end_time'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($booking['meeting_room'] ?? '-') . '</td>';
                $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($booking['description'] ?? '-') . '</td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function createStatCard($title, $value, $bgColorClass) {
        return "
            <div class=\"p-4 rounded-lg font-semibold text-center transition transform hover:-translate-y-1 {$bgColorClass}\">
                <h3 class=\"text-sm\">{$title}</h3>
                <p class=\"mt-2 text-2xl\">{$value}</p>
            </div>
        ";
    }
}
