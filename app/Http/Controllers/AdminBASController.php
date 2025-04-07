<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Activity;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminBASController extends Controller
{
    public function dashboard()
    {
        // Counts for dashboard cards
        $totalActivities = Activity::count();
        $todayActivities = Activity::whereDate('start_datetime', Carbon::today())->count();
        $weekActivities = Activity::whereBetween('start_datetime', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        $monthActivities = Activity::whereYear('start_datetime', Carbon::now()->year)
            ->whereMonth('start_datetime', Carbon::now()->month)
            ->count();

        // Upcoming activities (next 7 days)
        $upcomingActivities = Activity::with('room')
            ->whereDate('start_datetime', '>=', Carbon::today())
            ->whereDate('start_datetime', '<=', Carbon::today()->addDays(7))
            ->orderBy('start_datetime')
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
            $query->whereDate('start_datetime', $request->date);
        }
        
        // Filter by week
        if ($request->has('week') && $request->week == 'current') {
            $query->whereBetween('start_datetime', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }
        
        // Filter by month
        if ($request->has('month') && !empty($request->month)) {
            $month = explode('-', $request->month);
            if (count($month) == 2) {
                $query->whereYear('start_datetime', $month[0])
                    ->whereMonth('start_datetime', $month[1]);
            }
        }
        
        $activities = $query->orderBy('start_datetime', 'desc')->paginate(15);
        
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
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'room_id' => 'nullable|exists:meeting_rooms,id',
            'organizer' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'activity_type' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
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
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'room_id' => 'nullable|exists:meeting_rooms,id',
            'organizer' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'activity_type' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
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
                'start_datetime' => $activity->start_datetime,
                'end_datetime' => $activity->end_datetime,
                'status' => $activity->status,
                'description' => $activity->description,
                'organizer' => $activity->organizer,
                'room' => $activity->room ? $activity->room->name : null
            ];
        });
        
        return response()->json($activities);
    }
    
    /**
     * Departments management for BAS role
     */
    public function departments()
    {
        $departments = Department::withCount('employees')
                        ->orderBy('name', 'asc')
                        ->get();
        
        return view('admin_bas.departments.index', compact('departments'));
    }
    
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department = Department::create($validated);
        
        // Log aktivitas admin
        ActivityLogService::logCreate(
            'departments', 
            "Admin BAS menambahkan departemen baru: {$department->name}",
            $validated
        );
        
        return redirect()->route('bas.departments')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }
    
    public function editDepartment($id)
    {
        $department = Department::findOrFail($id);
        return view('admin_bas.departments.edit', compact('department'));
    }
    
    public function updateDepartment(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department = Department::findOrFail($id);
        $oldName = $department->name;
        
        $department->update($validated);
        
        // Log aktivitas admin
        ActivityLogService::logUpdate(
            'departments', 
            "Admin BAS memperbarui departemen dari {$oldName} menjadi {$department->name}",
            [
                'old_name' => $oldName,
                'new_name' => $department->name
            ]
        );
        
        return redirect()->route('bas.departments')
            ->with('success', 'Departemen berhasil diperbarui.');
    }
    
    public function deleteDepartment($id)
    {
        $department = Department::findOrFail($id);
        $departmentName = $department->name;
        
        Department::destroy($id);
        
        // Log aktivitas admin
        ActivityLogService::logDelete(
            'departments', 
            "Admin BAS menghapus departemen: {$departmentName}",
            ['id' => $id, 'name' => $departmentName]
        );
        
        return redirect()->route('bas.departments')
            ->with('success', 'Departemen berhasil dihapus.');
    }
    
    /**
     * Employees management for BAS role
     */
    public function employees(Request $request)
    {
        $employeesQuery = Employee::with('department')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(position) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
            })
            ->when($request->filled('gender'), function ($query) use ($request) {
                $query->where('gender', $request->gender);
            });

        // Manually sort by job title hierarchy
        $employees = $employeesQuery->get()->sort(function ($a, $b) {
            $positions = [
                'CEO' => 1,
                'Managing Director' => 2,
                'Manager' => 3, 
                'Coordinator' => 4,
                'Supervisor' => 5,
                'Staff' => 6
            ];
            
            $posA = array_key_exists($a->position, $positions) ? $positions[$a->position] : 999;
            $posB = array_key_exists($b->position, $positions) ? $positions[$b->position] : 999;
            
            if ($posA === $posB) {
                return $a->name <=> $b->name; // If positions are the same, sort by name
            }
            
            return $posA <=> $posB; // Sort by position weight
        });

        // Count employees by gender
        $maleCount = Employee::where('gender', 'L')->count();
        $femaleCount = Employee::where('gender', 'P')->count();

        // Apply pagination manually after sorting
        $page = $request->get('page', 1);
        $perPage = 10;
        $total = $employees->count();
        $currentItems = $employees->slice(($page - 1) * $perPage, $perPage);
        $employees = new LengthAwarePaginator($currentItems, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query()
        ]);

        $departments = Department::all();

        if ($request->ajax()) {
            return view('admin_bas.employees.partials.table', compact('employees', 'departments', 'maleCount', 'femaleCount'));
        }

        return view('admin_bas.employees.index', compact('employees', 'departments', 'maleCount', 'femaleCount'));
    }
    
    public function createEmployee()
    {
        $departments = Department::all();
        return view('admin_bas.employees.create', compact('departments'));
    }
    
    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $employee = Employee::create($validated);
        
        // Log aktivitas admin
        ActivityLogService::logCreate(
            'employees', 
            "Admin BAS menambahkan karyawan baru: {$employee->name}",
            $validated
        );

        return redirect()->route('bas.employees')
            ->with('success', 'Karyawan berhasil ditambahkan!');
    }
    
    public function editEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        return view('admin_bas.employees.edit', compact('employee', 'departments'));
    }
    
    public function updateEmployee(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $employee = Employee::findOrFail($id);
        $oldData = $employee->toArray();
        
        $employee->update($validated);
        
        // Log aktivitas admin
        ActivityLogService::logUpdate(
            'employees', 
            "Admin BAS memperbarui data karyawan: {$employee->name}",
            [
                'old_data' => $oldData,
                'new_data' => $employee->toArray()
            ]
        );

        return redirect()->route('bas.employees')
            ->with('success', 'Data karyawan berhasil diupdate!');
    }
    
    public function destroyEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employeeData = $employee->toArray();
            
            $employee->delete();
            
            // Log aktivitas admin
            ActivityLogService::logDelete(
                'employees', 
                "Admin BAS menghapus karyawan: {$employee->name}",
                $employeeData
            );
            
            return redirect()->route('bas.employees')
                ->with('success', 'Karyawan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }
    
    public function exportEmployees(Request $request)
    {
        return redirect()->route('bas.employees.export')
            ->with('success', 'Data karyawan berhasil diexport.');
    }
    
    /**
     * Show meeting rooms for BAS role
     */
    public function meetingRooms()
    {
        $rooms = MeetingRoom::orderBy('name', 'asc')->get();
        return view('admin_bas.meeting-rooms.index', compact('rooms'));
    }
    
    /**
     * Reports page for BAS role
     */
    public function reports()
    {
        $meetingRooms = MeetingRoom::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        
        return view('admin_bas.reports.index', compact('meetingRooms', 'departments'));
    }
} 