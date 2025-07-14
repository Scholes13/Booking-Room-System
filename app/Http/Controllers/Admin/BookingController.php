<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $bookingData = $booking->toArray(); // Get data before deleting for logging
        
        $booking->delete();

        ActivityLogService::logDelete(
            'bookings', 
            'Menghapus booking: ' . $bookingData['nama'],
            $bookingData
        );

        $routeName = 'admin.bookings.index';
        if (session('user_role') === 'superadmin') {
            $routeName = 'superadmin.bookings.index';
        }

        return redirect()->route($routeName)->with('success', 'Booking berhasil dihapus.');
    }

    public function getBookings(Request $request)
    {
        $query = Booking::with(['meetingRoom', 'user']);

        $filter = $request->input('filter', 'today');
        $date = $request->input('date');

        switch ($filter) {
            case 'week':
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();
                $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
                break;
            case 'month':
                $query->whereMonth('date', Carbon::now()->month)
                      ->whereYear('date', Carbon::now()->year);
                break;
            case 'custom':
                if ($date) {
                    $dates = explode(' to ', $date);
                    if (count($dates) === 2) {
                        $query->whereBetween('date', [Carbon::parse($dates[0])->startOfDay(), Carbon::parse($dates[1])->endOfDay()]);
                    } else {
                        $query->whereDate('date', Carbon::parse($date));
                    }
                } else {
                    // Default to today if date is not provided for custom filter
                    $query->whereDate('date', Carbon::today());
                }
                break;
            case 'today':
            default:
                $query->whereDate('date', Carbon::today());
                break;
        }

        // We clone the query here to run calculations on the filtered set
        // before applying pagination or ordering for the final result.
        $statsQuery = clone $query;
        $bookings = $query->orderBy('date', 'desc')->orderBy('start_time', 'asc')->paginate(10);
        $statsBookings = $statsQuery->get();
        $currentUsageRate = $this->calculateRoomUsagePercentage($statsBookings, $request);

        return response()->json([
            'bookings' => $bookings,
            'stats' => [
                 'totalBookings' => [
                    'count' => $statsBookings->count()
                 ],
                 'bookingComparison' => $this->calculateBookingComparison($statsBookings->count(), $request),
                 'usageRate' => [
                    'percentage' => $currentUsageRate,
                    'trend' => $this->calculateUsageRateComparison($currentUsageRate, $request)
                 ],
                 'mostUsedRoom' => $this->calculateMostUsedRoom($statsBookings),
                 'topDepartments' => $this->calculateDepartmentStats($statsBookings),
            ]
        ]);
    }

    private function calculateBookingComparison($totalBookings, Request $request = null)
    {
        $filterType = $request ? $request->filter : 'today';
        $previousBookings = 0;
        $comparisonText = 'from yesterday';

        if ($filterType === 'week') {
            $previousWeekStart = Carbon::now()->startOfWeek()->subWeek();
            $previousWeekEnd = Carbon::now()->endOfWeek()->subWeek();
            $previousBookings = Booking::whereBetween('date', [$previousWeekStart, $previousWeekEnd])->count();
            $comparisonText = 'from last week';
        } elseif ($filterType === 'month') {
            $previousMonth = Carbon::now()->subMonthNoOverflow();
            $previousBookings = Booking::whereYear('date', $previousMonth->year)
                                    ->whereMonth('date', $previousMonth->month)
                                    ->count();
            $comparisonText = 'from last month';
        } else { // 'today' or 'custom'
            $yesterday = Carbon::yesterday();
            if ($request->date) {
                $yesterday = Carbon::parse($request->date)->subDay();
            }
            $previousBookings = Booking::whereDate('date', $yesterday)->count();
        }

        $percentageChange = 0;
        if ($previousBookings > 0) {
            $percentageChange = (($totalBookings - $previousBookings) / $previousBookings) * 100;
        } elseif ($totalBookings > 0) {
            $percentageChange = 100;
        }

        return [
            'percentage_change' => round($percentageChange),
            'is_increase' => $percentageChange >= 0,
            'comparison_text' => $comparisonText,
        ];
    }

    private function calculateRoomUsagePercentage(object $bookings, Request $request): int
    {
        // 1. More precise calculation for booked hours (using minutes)
        $totalBookedMinutes = $bookings->reduce(function ($carry, $booking) {
            $start = Carbon::parse($booking->date . ' ' . $booking->start_time);
            $end = Carbon::parse($booking->date . ' ' . $booking->end_time);
            // Use abs() to ensure the difference is always positive
            return $carry + abs($end->diffInMinutes($start));
        }, 0);
        $totalBookedHours = $totalBookedMinutes / 60;
    
        $totalRooms = \App\Models\MeetingRoom::count();
        if ($totalRooms === 0) return 0;
    
        // 2. Dynamic calculation for available hours
        $operatingHoursPerDay = 12; // 08:00 to 20:00
        $totalAvailableHours = 0;
        $filter = $request->input('filter', 'today');
        $dateRangeStr = $request->input('date');

        $startDate = Carbon::today();
        $endDate = Carbon::today();

        switch ($filter) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                if ($dateRangeStr && str_contains($dateRangeStr, ' to ')) {
                    $dates = explode(' to ', $dateRangeStr);
                    $startDate = Carbon::parse($dates[0]);
                    $endDate = Carbon::parse($dates[1]);
                } else if ($dateRangeStr) {
                    $startDate = Carbon::parse($dateRangeStr);
                    $endDate = Carbon::parse($dateRangeStr);
                }
                break;
        }

        $workDays = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $workDays++;
            }
        }
    
        $totalAvailableHours = $totalRooms * $operatingHoursPerDay * $workDays;
    
        if ($totalAvailableHours <= 0) return 0;
    
        $percentage = ($totalBookedHours / $totalAvailableHours) * 100;
        return round($percentage);
    }

    private function calculateMostUsedRoom($bookings)
    {
        if ($bookings->isEmpty()) {
            return ['name' => 'No data', 'hours' => 0];
        }

        return $bookings->groupBy('meeting_room_id')
            ->map(function ($group) {
                 $totalHours = $group->reduce(function ($carry, $booking) {
                    $start = Carbon::parse($booking->date . ' ' . $booking->start_time);
                    $end = Carbon::parse($booking->date . ' ' . $booking->end_time);
                    // Use abs() for precision and to ensure positivity
                    return $carry + abs($end->diffInMinutes($start));
                }, 0) / 60;

                return [
                    'name' => $group->first()->meetingRoom->name ?? 'Unknown Room',
                    'bookings_count' => $group->count(),
                    'hours' => round($totalHours, 1)
                ];
            })
            ->sortByDesc('bookings_count')
            ->first();
    }
    
    private function calculateDepartmentStats($bookings)
    {
        if ($bookings->isEmpty()) {
            return [];
        }

        // Get department from user if available, otherwise from booking itself.
        $departmentCounts = $bookings->map(function ($booking) {
            if ($booking->user && $booking->user->department) {
                return $booking->user->department->name;
            }
            return $booking->department;
        })
        ->filter() // Remove null/empty department names
        ->countBy();

        return $departmentCounts->map(function ($count, $name) {
            return ['name' => $name, 'count' => $count];
        })
        ->sortByDesc('count')
        ->values()
        ->take(3);
    }

    private function getPreviousPeriodData(Request $request): array
    {
        $filter = $request->input('filter', 'today');
        $dateStr = $request->input('date');

        $prevStartDate = null;
        $prevEndDate = null;

        if ($filter === 'week') {
            $prevStartDate = Carbon::now()->subWeek()->startOfWeek();
            $prevEndDate = Carbon::now()->subWeek()->endOfWeek();
        } elseif ($filter === 'month') {
            $prevStartDate = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $prevEndDate = $prevStartDate->copy()->endOfMonth();
        } elseif ($filter === 'custom' && $dateStr && str_contains($dateStr, ' to ')) {
            $dates = explode(' to ', $dateStr);
            $startDate = Carbon::parse($dates[0]);
            $endDate = Carbon::parse($dates[1]);
            $duration = $endDate->diffInDays($startDate);
            $prevStartDate = $startDate->copy()->subDays($duration + 1);
            $prevEndDate = $endDate->copy()->subDays($duration + 1);
        } else { // 'today' or single 'custom' date
            $currentDate = $dateStr ? Carbon::parse($dateStr) : Carbon::today();
            $prevStartDate = $currentDate->copy()->subDay();
            $prevEndDate = $prevStartDate->copy();
        }
        
        $previousBookings = Booking::whereBetween('date', [$prevStartDate->startOfDay(), $prevEndDate->endOfDay()])->get();
        
        $previousRequest = new Request([
            'filter' => 'custom', 
            'date' => $prevStartDate->toDateString() . ' to ' . $prevEndDate->toDateString()
        ]);

        return ['bookings' => $previousBookings, 'request' => $previousRequest];
    }

    private function calculateUsageRateComparison(int $currentUsageRate, Request $request)
    {
        $previousPeriodData = $this->getPreviousPeriodData($request);
        
        $previousUsageRate = $this->calculateRoomUsagePercentage(
            $previousPeriodData['bookings'], 
            $previousPeriodData['request']
        );

        $percentageChange = 0;
        if ($previousUsageRate > 0) {
            $percentageChange = (($currentUsageRate - $previousUsageRate) / $previousUsageRate) * 100;
        } elseif ($currentUsageRate > 0) {
            $percentageChange = 100;
        }

        return [
            'percentage_change' => round($percentageChange),
            'is_increase' => $percentageChange >= 0,
        ];
    }
} 