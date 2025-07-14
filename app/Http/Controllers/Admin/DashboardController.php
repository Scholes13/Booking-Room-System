<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
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
        
        $employees = Employee::with('department')->orderBy('name', 'asc')->get();
        $departments = Department::orderBy('name', 'asc')->get();

        return view('admin.dashboard.index', compact('bookings', 'todayBookings', 'meetingRooms', 'employees', 'departments'));
    }
} 