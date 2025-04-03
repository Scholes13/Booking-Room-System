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
        Log::info('DEBUG: getData() terpanggil', $request->all());

        try {
            // Validasi parameter
            $request->validate([
                'report_type' => 'required|in:rooms,departments,bookings',
                'time_period' => 'required|in:monthly,quarterly,yearly',
                'year'        => 'required|integer',
                'month'       => 'nullable|integer|between:1,12',
                'quarter'     => 'nullable|integer|between:1,4',
            ]);

            // Hitung rentang tanggal (dateRange)
            $dateRange = $this->getDateRange(
                $request->time_period,
                $request->year,
                $request->month,
                $request->quarter
            );

            Log::info("Computed Date Range", [
                'start' => $dateRange['start']->toDateString(),
                'end'   => $dateRange['end']->toDateString()
            ]);

            // Pilih report berdasarkan report_type
            switch ($request->report_type) {
                case 'rooms':
                    $result = $this->getRoomUsageData($dateRange);
                    break;
                case 'departments':
                    $result = $this->getDepartmentUsageData($dateRange);
                    break;
                case 'bookings':
                    $result = $this->getBookingData($dateRange);
                    break;
                default:
                    return response()->json(['error' => 'Invalid report type'], 400);
            }

            Log::info('DEBUG: Final report result', $result instanceof \Illuminate\Http\JsonResponse ? $result->getData(true) : $result);
            return $result;
        } catch (\Exception $e) {
            Log::error('Report generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report'], 500);
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
    private function getDateRange($timePeriod, $year, $month = null, $quarter = null)
    {
        $startDate = Carbon::create($year);
        $endDate   = Carbon::create($year);

        Log::info("Initial Date Parameters", [
            'time_period' => $timePeriod,
            'year'        => $year,
            'month'       => $month,
            'quarter'     => $quarter
        ]);

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

        Log::info("getDateRange() computed", [
            'start' => $startDate->toDateString(),
            'end'   => $endDate->toDateString()
        ]);
        return ['start' => $startDate, 'end' => $endDate];
    }

    // === REPORT: RUANGAN ===
    private function getRoomUsageData($dateRange)
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
    private function getDepartmentUsageData($dateRange)
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
}
