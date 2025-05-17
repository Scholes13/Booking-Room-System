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
        
        // Filter by status - collect results first since status is a computed attribute
        $statusFilter = $request->has('status') && !empty($request->status) ? $request->status : null;
        
        // Filter by date range
        if ($request->has('start_date') && !empty($request->start_date) && $request->has('end_date') && !empty($request->end_date)) {
            $query->whereBetween('start_datetime', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } 
        // Filter by single date (for backward compatibility)
        else if ($request->has('date') && !empty($request->date)) {
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
        
        // Order by latest created_at to show the most recent activities first
        $query->latest('created_at');
        
        // Get all activities that match the database-level filters
        $allActivities = $query->get();
        
        // Apply status filter in PHP if needed (since status is a computed attribute)
        if ($statusFilter) {
            $filteredActivities = $allActivities->filter(function ($activity) use ($statusFilter) {
                return $activity->status === $statusFilter;
            });
        } else {
            $filteredActivities = $allActivities;
        }
        
        // Manual pagination since we filtered in PHP
        $page = $request->get('page', 1);
        $perPage = 15;
        $items = $filteredActivities->forPage($page, $perPage);
        
        $activities = new LengthAwarePaginator(
            $items,
            $filteredActivities->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Append all filter parameters to pagination links
        $activities->appends($request->all());
        
        return view('admin_bas.activities.index', compact('activities', 'statusFilter'));
    }

    /**
     * Show form to create new activity
     */
    public function createActivity()
    {
        $rooms = MeetingRoom::all();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $activityTypes = \App\Models\ActivityType::where('is_active', true)->orderBy('name')->get();
        
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
            // Contoh kota-kota besar di Indonesia
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur',
            'Bandung', 'Bekasi', 'Tangerang', 'Depok', 'Bogor', 'Semarang', 'Yogyakarta',
            'Surabaya', 'Malang', 'Medan', 'Palembang', 'Makassar', 'Balikpapan', 'Banjarmasin',
            'Pontianak', 'Padang', 'Pekanbaru', 'Denpasar', 'Manado', 'Sleman'
        ];
        
        return view('admin_bas.activities.create', compact('rooms', 'employees', 'activityTypes', 'provinces', 'cities'));
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
            ->with('success', 'Aktivitas berhasil ditambahkan');
    }

    /**
     * Show form to edit activity
     */
    public function editActivity(Activity $activity)
    {
        $rooms = MeetingRoom::all();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $activityTypes = \App\Models\ActivityType::where('is_active', true)->orderBy('name')->get();
        
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
            // Contoh kota-kota besar di Indonesia
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur',
            'Bandung', 'Bekasi', 'Tangerang', 'Depok', 'Bogor', 'Semarang', 'Yogyakarta',
            'Surabaya', 'Malang', 'Medan', 'Palembang', 'Makassar', 'Balikpapan', 'Banjarmasin',
            'Pontianak', 'Padang', 'Pekanbaru', 'Denpasar', 'Manado', 'Sleman'
        ];
        
        return view('admin_bas.activities.edit', compact('activity', 'rooms', 'provinces', 'cities', 'employees', 'activityTypes'));
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
        try {
            $activityData = $activity->toArray();
            $activity->delete();
            
            // Log aktivitas admin
            ActivityLogService::logDelete(
                'activities', 
                "Admin BAS menghapus aktivitas: {$activity->name}",
                $activityData
            );
            
            return redirect()->route('bas.activities.index')
                ->with('success', 'Aktivitas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus aktivitas: ' . $e->getMessage());
        }
    }

    /**
     * Show activity calendar
     */
    public function activitiesCalendar()
    {
        $departments = Department::all();
        return view('admin_bas.activities.calendar', compact('departments'));
    }

    /**
     * Get activity data in JSON format for calendar
     */
    public function activitiesJson()
    {
        // Get all activities with eager loading for relationships
        $activities = Activity::with(['room', 'department'])->get();
        $events = [];
        
        foreach ($activities as $activity) {
            // Format dates for proper handling of multi-day events
            $startDateTime = Carbon::parse($activity->start_datetime);
            $endDateTime = Carbon::parse($activity->end_datetime);
            
            // For multi-day events, FullCalendar needs end date to be exclusive
            // If end date doesn't end at 00:00, add 1 day for proper display
            if ($startDateTime->format('Y-m-d') !== $endDateTime->format('Y-m-d') && 
                !($endDateTime->hour === 0 && $endDateTime->minute === 0)) {
                $endDateTime = $endDateTime->addDay()->startOfDay();
            }
            
            // Get status - default to 'scheduled' if not set
            $status = $activity->status ?? 'scheduled';
            
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $activity->start_datetime,
                'end' => $endDateTime->format('Y-m-d H:i:s'),
                'status' => $status,
                'extendedProps' => [
                    'description' => $activity->description,
                    'room' => $activity->room ? $activity->room->name : null,
                    'organizer' => $activity->organizer ?? null,
                    'department' => $activity->department ? $activity->department->name : null,
                    'original_end' => $activity->end_datetime, // Store original end date
                    'status' => $status
                ]
            ];
        }
        
        // Add debug info
        info('Activities JSON: ' . count($activities) . ' records found');
        
        return response()->json($events);
    }

    /**
     * Departments management for BAS role
     */
    public function departments()
    {
        $departments = Department::withCount('employees')->orderBy('name', 'asc')->get();
        
        // Count employees by department
        $totalEmployees = Employee::count();
        
        return view('admin_bas.departments.index', compact('departments', 'totalEmployees'));
    }
    
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
        ]);

        $department = Department::create($validated);
        
        // Log aktivitas admin
        ActivityLogService::logCreate(
            'departments', 
            "Admin BAS menambahkan departemen baru: {$department->name}",
            $validated
        );

        return redirect()->route('bas.departments')
            ->with('success', 'Departemen berhasil ditambahkan!');
    }
    
    public function editDepartment($id)
    {
        $department = Department::findOrFail($id);
        return view('admin_bas.departments.edit', compact('department'));
    }
    
    public function updateDepartment(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $oldData = $department->toArray();
        $department->update($validated);
        
        // Log aktivitas admin
        ActivityLogService::logUpdate(
            'departments', 
            "Admin BAS memperbarui departemen: {$department->name}",
            [
                'old_data' => $oldData,
                'new_data' => $department->toArray()
            ]
        );

        return redirect()->route('bas.departments')
            ->with('success', 'Departemen berhasil diupdate!');
    }
    
    public function deleteDepartment($id)
    {
        try {
            $department = Department::findOrFail($id);
            
            // Check if department has employees
            if ($department->employees()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Departemen tidak dapat dihapus karena masih memiliki karyawan!');
            }
            
            $departmentData = $department->toArray();
            $department->delete();
            
            // Log aktivitas admin
            ActivityLogService::logDelete(
                'departments', 
                "Admin BAS menghapus departemen: {$department->name}",
                $departmentData
            );
            
            return redirect()->route('bas.departments')
                ->with('success', 'Departemen berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus departemen: ' . $e->getMessage());
        }
    }
    
    /**
     * Employees management for BAS role
     */
    public function employees(Request $request)
    {
        $query = Employee::with('department');
        
        // Filter by department
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter by gender
        if ($request->has('gender') && !empty($request->gender)) {
            $query->where('gender', $request->gender);
        }
        
        // Filter by search
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sort by
        $sortField = $request->sort_by ?? 'name';
        $sortDirection = $request->sort_direction ?? 'asc';
        $query->orderBy($sortField, $sortDirection);
        
        // Count by gender
        $maleCount = Employee::where('gender', 'L')->count();
        $femaleCount = Employee::where('gender', 'P')->count();
        
        // Pagination
        $employees = $query->paginate(15)->appends([
            'department_id' => $request->department_id,
            'gender' => $request->gender,
            'search' => $request->search,
            'sort_by' => $sortField,
            'sort_direction' => $sortDirection,
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
        $departments = Department::orderBy('name', 'asc')->get();
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
