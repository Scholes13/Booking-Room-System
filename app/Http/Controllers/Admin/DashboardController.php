<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk admin.
     * Misalnya: Tampilkan daftar booking.
     */
    public function dashboard()
    {
        // Get today's date
        $today = now()->startOfDay();
        
        // Get bookings for today
        $todayBookings = Booking::with('meetingRoom', 'user')
            ->whereDate('date', $today)
            ->orderBy('start_time', 'asc')
            ->get();
            
        // Get all bookings for stats
        $bookings = Booking::with('meetingRoom', 'user')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->limit(10)
            ->get();
            
        // Get meeting rooms for usage stats
        $meetingRooms = MeetingRoom::all();
        
        // Check if user is Admin BAS or regular Admin
        if (Auth::check() && Auth::user()->role === 'admin_bas') {
            // For Admin BAS, we need to get all activity statistics
            $totalActivities = Activity::count();
            
            // Use today's date for filtering
            $todayActivities = Activity::whereDate('start_datetime', $today)->count();
            
            // Get week statistics
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            $weekActivities = Activity::whereBetween('start_datetime', [$weekStart, $weekEnd])->count();
            
            // Get month statistics
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();
            $monthActivities = Activity::whereBetween('start_datetime', [$monthStart, $monthEnd])->count();
                
            // Get upcoming activities
            $upcomingActivities = Activity::with('room')
                ->where('start_datetime', '>=', now())
                ->orderBy('start_datetime')
                ->limit(5)
                ->get();
                
            // Get recent activities
            $recentActivities = Activity::with('room')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            return view('admin_bas.dashboard.index', compact(
                'bookings', 
                'todayBookings',
                'meetingRooms',
                'totalActivities', 
                'todayActivities', 
                'weekActivities', 
                'monthActivities',
                'upcomingActivities',
                'recentActivities'
            ));
        }

        $bookingsQuery = Booking::with(['user.department', 'meetingRoom'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc');
            
        $bookings = $bookingsQuery->paginate(10);
        
        // Get all necessary data for the edit modal dropdowns
        $employees = Employee::with('department')->orderBy('name', 'asc')->get();
        $departments = Department::orderBy('name', 'asc')->get();
        $allMeetingRooms = MeetingRoom::orderBy('name', 'asc')->get();

        return view('admin.dashboard.index', compact('bookings', 'todayBookings', 'meetingRooms', 'employees', 'departments', 'allMeetingRooms'));
    }

    /**
     * Get employee data by name for auto-population
     */
    public function getEmployeeByName(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $employee = Employee::with('department')
            ->where('name', $request->name)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee found',
            'data' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'department' => [
                    'id' => $employee->department->id ?? null,
                    'name' => $employee->department->name ?? null
                ],
                'position' => $employee->position,
                'email' => $employee->email,
                'phone' => $employee->phone
            ]
        ]);
    }

    /**
     * Search employees for autocomplete
     */
    public function searchEmployees(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255'
        ]);

        $employees = Employee::with('department')
            ->where('name', 'LIKE', '%' . $request->query . '%')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'department' => [
                        'id' => $employee->department->id ?? null,
                        'name' => $employee->department->name ?? null
                    ],
                    'position' => $employee->position
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Employees found',
            'data' => $employees
        ]);
    }

    /**
     * Get all employees with departments for dropdowns
     */
    public function getEmployees(): JsonResponse
    {
        $employees = Employee::with('department')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'department' => [
                        'id' => $employee->department->id ?? null,
                        'name' => $employee->department->name ?? null
                    ],
                    'position' => $employee->position,
                    'email' => $employee->email,
                    'phone' => $employee->phone
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Employees retrieved successfully',
            'data' => $employees
        ]);
    }

    /**
     * Get all departments for dropdowns
     */
    public function getDepartments(): JsonResponse
    {
        $departments = Department::orderBy('name', 'asc')
            ->get()
            ->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'description' => $department->description
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Departments retrieved successfully',
            'data' => $departments
        ]);
    }

    /**
     * Get all meeting rooms for dropdowns
     */
    public function getMeetingRooms(): JsonResponse
    {
        $meetingRooms = MeetingRoom::orderBy('name', 'asc')
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'capacity' => $room->capacity,
                    'location' => $room->location,
                    'facilities' => $room->facilities
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Meeting rooms retrieved successfully',
            'data' => $meetingRooms
        ]);
    }

    /**
     * Get bookings data for AJAX requests with filtering and pagination
     */
    public function bookings(Request $request): JsonResponse
    {
        $filter = $request->get('filter', 'today');
        $date = $request->get('date');
        $page = $request->get('page', 1);

        // Base query
        $query = Booking::with(['user.department', 'meetingRoom']);

        // Apply filters
        switch ($filter) {
            case 'today':
                $query->whereDate('date', now());
                break;
            case 'week':
                $query->whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('date', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ]);
                break;
            case 'custom':
                if ($date) {
                    $dates = explode(' to ', $date);
                    if (count($dates) === 2) {
                        $query->whereBetween('date', [
                            Carbon::parse($dates[0]),
                            Carbon::parse($dates[1])
                        ]);
                    }
                }
                break;
        }

        // Order by date and time
        $query->orderBy('date', 'desc')->orderBy('start_time', 'asc');

        // Paginate results
        $bookings = $query->paginate(10, ['*'], 'page', $page);

        // Calculate statistics
        $stats = $this->calculateBookingStats($filter, $date);

        return response()->json([
            'success' => true,
            'bookings' => $bookings,
            'stats' => $stats
        ]);
    }

    /**
     * Calculate booking statistics for dashboard
     */
    private function calculateBookingStats($filter, $date = null): array
    {
        $today = now();
        $yesterday = now()->copy()->subDay();

        // Get current period bookings
        $currentQuery = Booking::query();
        $previousQuery = Booking::query();

        switch ($filter) {
            case 'today':
                $currentQuery->whereDate('date', $today->toDateString());
                $previousQuery->whereDate('date', $yesterday->toDateString());
                break;
            case 'week':
                $weekStart = $today->copy()->startOfWeek();
                $weekEnd = $today->copy()->endOfWeek();
                $currentQuery->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
                $previousQuery->whereBetween('date', [
                    $weekStart->copy()->subWeek()->toDateString(),
                    $weekEnd->copy()->subWeek()->toDateString()
                ]);
                break;
            case 'month':
                $monthStart = $today->copy()->startOfMonth();
                $monthEnd = $today->copy()->endOfMonth();
                $currentQuery->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
                $previousQuery->whereBetween('date', [
                    $monthStart->copy()->subMonth()->toDateString(),
                    $monthEnd->copy()->subMonth()->toDateString()
                ]);
                break;
            case 'custom':
                if ($date) {
                    $dates = explode(' to ', $date);
                    if (count($dates) === 2) {
                        $startDate = Carbon::parse($dates[0]);
                        $endDate = Carbon::parse($dates[1]);
                        $currentQuery->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
                        
                        // For custom range, compare with previous period of same length
                        $daysDiff = $startDate->diffInDays($endDate);
                        $previousStart = $startDate->copy()->subDays($daysDiff + 1);
                        $previousEnd = $startDate->copy()->subDay();
                        $previousQuery->whereBetween('date', [$previousStart->toDateString(), $previousEnd->toDateString()]);
                    }
                }
                break;
        }

        $currentCount = $currentQuery->count();
        $previousCount = $previousQuery->count();

        // Calculate percentage change
        $percentageChange = 0;
        $isIncrease = false;
        if ($previousCount > 0) {
            $percentageChange = round((($currentCount - $previousCount) / $previousCount) * 100, 1);
            $isIncrease = $currentCount > $previousCount;
        } elseif ($currentCount > 0) {
            $percentageChange = 100;
            $isIncrease = true;
        }

        // Calculate room usage rate
        $totalRooms = MeetingRoom::count();
        $usedRoomsQuery = Booking::distinct('meeting_room_id');
        
        if ($filter === 'today') {
            $usedRoomsQuery->whereDate('date', $today->toDateString());
        } elseif ($filter === 'week') {
            $weekStart = $today->copy()->startOfWeek();
            $weekEnd = $today->copy()->endOfWeek();
            $usedRoomsQuery->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        } elseif ($filter === 'month') {
            $monthStart = $today->copy()->startOfMonth();
            $monthEnd = $today->copy()->endOfMonth();
            $usedRoomsQuery->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
        } elseif ($filter === 'custom' && $date) {
            $dates = explode(' to ', $date);
            if (count($dates) === 2) {
                $usedRoomsQuery->whereBetween('date', [Carbon::parse($dates[0])->toDateString(), Carbon::parse($dates[1])->toDateString()]);
            }
        }
        
        $usedRoomsCount = $usedRoomsQuery->count();
        $usagePercentage = $totalRooms > 0 ? round(($usedRoomsCount / $totalRooms) * 100, 1) : 0;

        // Get most used room with proper filtering
        $mostUsedRoomQuery = Booking::select('meeting_room_id')
            ->selectRaw('COUNT(*) as booking_count')
            ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time))/3600) as total_hours')
            ->with('meetingRoom')
            ->groupBy('meeting_room_id');

        // Apply same filter to most used room query
        if ($filter === 'today') {
            $mostUsedRoomQuery->whereDate('date', $today->toDateString());
        } elseif ($filter === 'week') {
            $weekStart = $today->copy()->startOfWeek();
            $weekEnd = $today->copy()->endOfWeek();
            $mostUsedRoomQuery->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        } elseif ($filter === 'month') {
            $monthStart = $today->copy()->startOfMonth();
            $monthEnd = $today->copy()->endOfMonth();
            $mostUsedRoomQuery->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
        } elseif ($filter === 'custom' && $date) {
            $dates = explode(' to ', $date);
            if (count($dates) === 2) {
                $mostUsedRoomQuery->whereBetween('date', [Carbon::parse($dates[0])->toDateString(), Carbon::parse($dates[1])->toDateString()]);
            }
        }

        $mostUsedRoom = $mostUsedRoomQuery->orderBy('booking_count', 'desc')->first();

        // Get top departments with proper filtering
        $topDepartmentsQuery = Booking::select('department')
            ->selectRaw('COUNT(*) as booking_count')
            ->whereNotNull('department')
            ->where('department', '!=', '');

        // Apply same filter to top departments query
        if ($filter === 'today') {
            $topDepartmentsQuery->whereDate('date', $today->toDateString());
        } elseif ($filter === 'week') {
            $weekStart = $today->copy()->startOfWeek();
            $weekEnd = $today->copy()->endOfWeek();
            $topDepartmentsQuery->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        } elseif ($filter === 'month') {
            $monthStart = $today->copy()->startOfMonth();
            $monthEnd = $today->copy()->endOfMonth();
            $topDepartmentsQuery->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
        } elseif ($filter === 'custom' && $date) {
            $dates = explode(' to ', $date);
            if (count($dates) === 2) {
                $topDepartmentsQuery->whereBetween('date', [Carbon::parse($dates[0])->toDateString(), Carbon::parse($dates[1])->toDateString()]);
            }
        }

        $topDepartments = $topDepartmentsQuery
            ->groupBy('department')
            ->orderBy('booking_count', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return ['name' => $item->department, 'count' => $item->booking_count];
            });

        return [
            'totalBookings' => [
                'count' => $currentCount
            ],
            'bookingComparison' => [
                'percentage_change' => $percentageChange,
                'is_increase' => $isIncrease
            ],
            'usageRate' => [
                'percentage' => $usagePercentage,
                'trend' => [
                    'percentage_change' => 0,
                    'is_increase' => false
                ]
            ],
            'mostUsedRoom' => [
                'name' => $mostUsedRoom && $mostUsedRoom->meetingRoom ? $mostUsedRoom->meetingRoom->name : 'N/A',
                'hours' => $mostUsedRoom ? round($mostUsedRoom->total_hours, 1) : 0
            ],
            'topDepartments' => $topDepartments
        ];
    }
}