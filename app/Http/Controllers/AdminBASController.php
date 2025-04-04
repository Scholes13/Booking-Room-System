<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogService;
use Carbon\Carbon;

class AdminBASController extends Controller
{
    public function dashboard()
    {
        // Counts for dashboard cards
        $totalActivities = Activity::count();
        $todayActivities = Activity::whereDate('date', Carbon::today())->count();
        $weekActivities = Activity::whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        $monthActivities = Activity::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->count();

        // Upcoming activities (next 7 days)
        $upcomingActivities = Activity::with('room')
            ->whereDate('date', '>=', Carbon::today())
            ->whereDate('date', '<=', Carbon::today()->addDays(7))
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Recent activities
        $recentActivities = Activity::with('room')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin_bas.dashboard.index', compact(
            'totalActivities',
            'todayActivities',
            'weekActivities',
            'monthActivities',
            'upcomingActivities',
            'recentActivities'
        ));
    }

    /**
     * Show the activities index page
     */
    public function activitiesIndex(Request $request)
    {
        $query = Activity::with('room');
        
        // Filter by search
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Filter by date
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('date', $request->date);
        }
        
        // Filter by week
        if ($request->has('week') && $request->week == 'current') {
            $query->whereBetween('date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }
        
        // Filter by month
        if ($request->has('month') && !empty($request->month)) {
            $month = explode('-', $request->month);
            if (count($month) == 2) {
                $query->whereYear('date', $month[0])
                    ->whereMonth('date', $month[1]);
            }
        }
        
        $activities = $query->latest('date')->paginate(15);
        
        return view('admin_bas.activities.index', compact('activities'));
    }

    /**
     * Show form to create new activity
     */
    public function createActivity()
    {
        $rooms = MeetingRoom::all();
        return view('admin_bas.activities.create', compact('rooms'));
    }

    /**
     * Store a new activity
     */
    public function storeActivity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'room_id' => 'required|exists:meeting_rooms,id',
            'organizer' => 'nullable|string|max:255',
        ]);

        $activity = Activity::create($validated);
        
        // Log aktivitas admin
        ActivityLogService::logCreate(
            'activities', 
            "Admin BAS menambahkan aktivitas baru: {$activity->name}",
            $validated
        );

        return redirect()->route('bas.activities.index')
            ->with('success', 'Aktivitas berhasil dibuat');
    }

    /**
     * Show form to edit activity
     */
    public function editActivity(Activity $activity)
    {
        $rooms = MeetingRoom::all();
        return view('admin_bas.activities.edit', compact('activity', 'rooms'));
    }

    /**
     * Update activity
     */
    public function updateActivity(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'room_id' => 'required|exists:meeting_rooms,id',
            'organizer' => 'nullable|string|max:255',
        ]);
        
        $oldData = $activity->toArray();
        $activity->update($validated);
        
        // Log aktivitas admin
        ActivityLogService::logUpdate(
            'activities', 
            "Admin BAS memperbarui aktivitas: {$activity->name}",
            [
                'old_data' => $oldData,
                'new_data' => $validated
            ]
        );

        return redirect()->route('bas.activities.index')
            ->with('success', 'Aktivitas berhasil diperbarui');
    }

    /**
     * Delete activity
     */
    public function destroyActivity(Activity $activity)
    {
        $activityName = $activity->name;
        $activityId = $activity->id;
        
        $activity->delete();
        
        // Log aktivitas admin
        ActivityLogService::logDelete(
            'activities', 
            "Admin BAS menghapus aktivitas: {$activityName}",
            ['id' => $activityId, 'name' => $activityName]
        );

        return redirect()->route('bas.activities.index')
            ->with('success', 'Aktivitas berhasil dihapus');
    }

    /**
     * Show activity calendar
     */
    public function activitiesCalendar()
    {
        return view('admin_bas.activities.calendar');
    }
    
    /**
     * Get activity data in JSON format for calendar
     */
    public function activitiesJson()
    {
        $activities = Activity::with('room')->get()->map(function($activity) {
            return [
                'id' => $activity->id,
                'name' => $activity->name,
                'date' => $activity->date,
                'start_time' => $activity->start_time,
                'end_time' => $activity->end_time,
                'status' => $activity->status,
                'description' => $activity->description,
                'organizer' => $activity->organizer,
                'room' => $activity->room->name
            ];
        });
        
        return response()->json($activities);
    }
} 