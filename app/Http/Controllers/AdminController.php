<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Department;
use App\Models\Activity;
use App\Models\User; // Tambahkan import model User
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ActivityReportController;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Menampilkan halaman login admin.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login admin/superadmin menggunakan Auth.
     * Input login bisa berupa username atau email.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $login    = $request->input('login');
        $password = $request->input('password');

        // Tentukan apakah input login merupakan email atau username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Gunakan Auth::attempt() untuk verifikasi user + password
        if (Auth::attempt([$field => $login, 'password' => $password])) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Set role in session without flushing first
            session(['user_role' => $user->role]);

            // Pisahkan admin & superadmin & admin_bas berdasarkan role
            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard')
                               ->with('success', 'Selamat datang, Super Admin!');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                               ->with('success', 'Selamat datang, Admin!');
            } elseif ($user->role === 'admin_bas') {
                return redirect()->route('bas.dashboard')
                               ->with('success', 'Selamat datang, Admin BAS!');
            } else {
                // Jika role tidak valid, logout dan kembalikan error
                Auth::logout();
                session()->forget('user_role');
                return redirect()->route('admin.login')
                    ->with('error', 'Anda tidak memiliki akses ke area admin. Role tidak valid.');
            }
        }

        return redirect()->back()
                       ->with('error', 'Login gagal. Periksa kembali login dan password anda.');
    }

    /**
     * Logout admin/superadmin.
     */
    public function logout()
    {
        Auth::logout();
        session()->forget('user_role');
        return redirect()->route('admin.login')
                       ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Dashboard untuk admin.
     * Misalnya: Tampilkan daftar booking.
     */
    public function dashboard()
    {
        // Get today's date
        $today = now()->startOfDay();
        
        // Get bookings for today
        $todayBookings = Booking::with('meetingRoom')
            ->whereDate('date', $today)
            ->orderBy('start_time', 'asc')
            ->get();
            
        // Get all bookings for stats
        $bookings = Booking::with('meetingRoom')
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

        return view('admin.dashboard.index', compact('bookings', 'todayBookings', 'meetingRooms'));
    }

    /**
     * Dashboard untuk superadmin.
     * Pastikan file resources/views/superadmin/dashboard.blade.php sudah ada.
     */
    public function superAdminDashboard()
    {
        return view('superadmin.dashboard.index');
    }

    /**
     * Tampilkan form untuk membuat Admin baru (khusus superadmin).
     */
    public function createAdmin()
    {
        return view('superadmin.users.create');
    }

    /**
     * Simpan Admin baru dan redirect ke dashboard superadmin.
     */
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Buat user baru dengan role = 'admin'
        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            // Jika di model User ada cast 'password' => 'hashed',
            // password akan otomatis di-hash.
            'password' => $validated['password'],
            'role'     => 'admin',
        ]);

        return redirect()->route('superadmin.dashboard')
                         ->with('success', 'Admin baru berhasil dibuat!');
    }

    // ----------------------------------------------------------------
    //                      K E L O L A   M E E T I N G   R O O M
    // ----------------------------------------------------------------

    /**
     * Helper method untuk mendapatkan view berdasarkan role user
     */
    private function getViewByRole($adminView, $defaultView = null)
    {
        if (session('user_role') === 'superadmin') {
            $superadminView = str_replace('admin.', 'superadmin.', $adminView);
            
            // Cek jika view superadmin ada, jika tidak gunakan view admin
            if (view()->exists($superadminView)) {
                return $superadminView;
            }
        }
        
        return $defaultView ?: $adminView;
    }

    public function meetingRooms()
    {
        $rooms = MeetingRoom::orderBy('name', 'asc')->get();
        
        // Only check for superadmin, BAS now has its own controller
        $view = $this->getViewByRole('admin.meeting-rooms.index');
        
        return view($view, compact('rooms'));
    }

    public function createMeetingRoom()
    {
        // Now that we're using modals, just redirect to the index page
        // which will show the Add Room modal
        if (Auth::check() && Auth::user()->role === 'admin_bas') {
            return redirect()->route('bas.meeting_rooms');
        }
        
        // For regular admin or superadmin
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return redirect()->route('superadmin.meeting_rooms');
        }
        
        return redirect()->route('admin.meeting_rooms');
    }

    public function storeMeetingRoom(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity'    => 'nullable|integer|min:1',
            'facilities'  => 'nullable|array',
        ]);

        // Konversi fasilitas ke format JSON jika ada
        if (isset($validated['facilities'])) {
            $validated['facilities'] = json_encode($validated['facilities']);
        }

        $room = MeetingRoom::create($validated);
        
        // Log aktivitas admin
        ActivityLogService::logCreate(
            'meeting_rooms', 
            "Menambahkan ruang meeting baru: {$room->name}",
            $validated
        );
        
        // Determine redirect route based on user role
        $routeName = 'admin.meeting_rooms';
        if (Auth::check()) {
            if (Auth::user()->role === 'admin_bas') {
                $routeName = 'bas.meeting_rooms';
            } elseif (Auth::user()->role === 'superadmin') {
                $routeName = 'superadmin.meeting_rooms';
            }
        }
        
        return redirect()->route($routeName)
                         ->with('success', 'Ruang meeting berhasil ditambahkan.');
    }

    public function deleteMeetingRoom($id)
    {
        $room = MeetingRoom::findOrFail($id);
        $roomName = $room->name;
        
        $room->delete();
        
        // Log aktivitas admin
        ActivityLogService::logDelete(
            'meeting_rooms', 
            "Menghapus ruang meeting: {$roomName}",
            ['id' => $id, 'name' => $roomName]
        );
    
        // Determine redirect route based on user role
        $routeName = 'admin.meeting_rooms';
        if (Auth::check()) {
            if (Auth::user()->role === 'admin_bas') {
                $routeName = 'bas.meeting_rooms';
            } elseif (Auth::user()->role === 'superadmin') {
                $routeName = 'superadmin.meeting_rooms';
            }
        }
        
        return redirect()->route($routeName)
                         ->with('success', 'Meeting room berhasil dihapus.');
    }

    public function editMeetingRoom($id)
    {
        // Now that we're using modals, just redirect to the index page
        // The edit button on index page will open the modal with room data
        
        if (Auth::check() && Auth::user()->role === 'admin_bas') {
            return redirect()->route('bas.meeting_rooms');
        }
        
        // For regular admin or superadmin
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return redirect()->route('superadmin.meeting_rooms');
        }
        
        return redirect()->route('admin.meeting_rooms');
    }

    public function updateMeetingRoom(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity'    => 'nullable|integer|min:1',
            'facilities'  => 'nullable|array',
        ]);

        // Konversi fasilitas ke format JSON jika ada
        if (isset($validated['facilities'])) {
            $validated['facilities'] = json_encode($validated['facilities']);
        }

        $room = MeetingRoom::findOrFail($id);
        $oldData = $room->toArray();
        
        $room->update($validated);
        
        // Log aktivitas admin
        ActivityLogService::logUpdate(
            'meeting_rooms', 
            "Memperbarui ruang meeting: {$room->name}",
            [
                'old_data' => $oldData,
                'new_data' => $validated
            ]
        );
        
        // Determine the route based on user role
        $routeName = 'admin.meeting_rooms';
        if (Auth::check()) {
            if (Auth::user()->role === 'admin_bas') {
                $routeName = 'bas.meeting_rooms';
            } elseif (Auth::user()->role === 'superadmin') {
                $routeName = 'superadmin.meeting_rooms';
            }
        }
        
        return redirect()->route($routeName)
                        ->with('success', 'Meeting room updated successfully.');
    }

    // ----------------------------------------------------------------
    //                      K E L O L A   B O O K I N G
    // ----------------------------------------------------------------

    public function editBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $meetingRooms = MeetingRoom::all();
        return view('admin.edit_booking', compact('booking', 'meetingRooms'));
    }

    public function updateBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'department'      => 'required|exists:departments,name',
            'date'            => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'required',
            'description'     => 'nullable|string',
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
        ]);

        // Cek konflik booking kecuali booking ini sendiri
        $conflict = Booking::where('meeting_room_id', $validated['meeting_room_id'])
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->where('id', '<>', $id)
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Maaf, ruangan sudah dibooking pada waktu tersebut.');
        }

        $booking->update($validated);
        return redirect()->route('admin.dashboard')->with('success', 'Booking berhasil diperbarui.');
    }

    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $bookingData = $booking->toArray();
        
        $booking->delete();
        
        // Log aktivitas admin
        ActivityLogService::logDelete(
            'bookings', 
            "Menghapus booking: {$booking->nama} pada {$booking->date} {$booking->start_time}-{$booking->end_time}",
            $bookingData
        );

        // Determine the route prefix based on user role
        $prefix = 'admin.';
        if (Auth::check()) {
            if (Auth::user()->role === 'superadmin') {
                $prefix = 'superadmin.';
            } elseif (Auth::user()->role === 'admin_bas') {
                $prefix = 'bas.';
            }
        }
        
        return redirect()->route($prefix . 'bookings.index')->with('success', 'Booking berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    //                    K E L O L A   D E P A R T E M E N
    // ----------------------------------------------------------------

    public function departments()
    {
        $departments = Department::withCount('employees')
                        ->orderBy('name', 'asc')
                        ->get();
        
        $view = $this->getViewByRole('admin.departments.index');
        
        return view($view, compact('departments'));
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
            "Menambahkan departemen baru: {$department->name}",
            $validated
        );
        
        return redirect()->back()->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function deleteDepartment($id)
    {
        $department = Department::findOrFail($id);
        $departmentName = $department->name;
        
        Department::destroy($id);
        
        // Log aktivitas admin
        ActivityLogService::logDelete(
            'departments', 
            "Menghapus departemen: {$departmentName}",
            ['id' => $id, 'name' => $departmentName]
        );
        
        return redirect()->back()->with('success', 'Departemen berhasil dihapus.');
    }

    public function editDepartment($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
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
            "Memperbarui departemen dari {$oldName} menjadi {$department->name}",
            [
                'old_name' => $oldName,
                'new_name' => $department->name
            ]
        );

        // Determine redirect route based on user role
        $routeName = 'admin.departments';
        if (Auth::check()) {
            if (Auth::user()->role === 'superadmin') {
                $routeName = 'superadmin.departments';
            } elseif (Auth::user()->role === 'admin_bas') {
                $routeName = 'bas.departments';
            }
        }

        return redirect()->route($routeName)->with('success', 'Departemen berhasil diperbarui.');
    }

    /**
     * Menampilkan daftar user admin
     */
    public function users()
    {
        $users = User::whereIn('role', ['admin', 'admin_bas'])->orderBy('name')->paginate(10);
        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Tampilkan form untuk membuat User baru.
     */
    public function createUser()
    {
        return view('superadmin.users.create');
    }

    /**
     * Simpan User baru dan redirect ke daftar users.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,admin_bas,superadmin',
        ]);

        // Buat user baru dengan role sesuai input
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => $validated['role'], 
        ]);
        
        // Log aktivitas admin
        ActivityLogService::logCreate(
            'users', 
            "Menambahkan user baru: {$user->name} dengan role {$user->role}",
            $validated
        );

        return redirect()->route('superadmin.users')
                       ->with('success', 'User berhasil dibuat!');
    }

    /**
     * Tampilkan form untuk edit User.
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('superadmin.users.edit', compact('user'));
    }

    /**
     * Update User dan redirect ke daftar users.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'role'     => 'required|in:admin,admin_bas,superadmin',
            'password' => 'nullable|min:6',
        ]);
        
        // Update data user
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        
        // Update password jika diisi
        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        
        $user->save();
        
        // Log aktivitas admin
        ActivityLogService::logUpdate(
            'users', 
            "Mengupdate user: {$user->name}",
            $validated
        );
        
        return redirect()->route('superadmin.users')
                       ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Hapus User dan redirect ke daftar users.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting superadmin users
        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.users')
                             ->with('error', 'Super Admin tidak dapat dihapus!');
        }
        
        $userName = $user->name;
        $userRole = $user->role;
        
        $user->delete();
        
        // Log aktivitas admin
        ActivityLogService::logDelete(
            'users', 
            "Menghapus user: {$userName} dengan role {$userRole}",
            ['id' => $id, 'name' => $userName, 'role' => $userRole]
        );
        
        return redirect()->route('superadmin.users')
                         ->with('success', 'User berhasil dihapus!');
    }

    /**
     * Show activity reports page
     */
    public function activityReports()
    {
        if (Auth::check() && Auth::user()->role === 'admin_bas') {
            return view('admin_bas.activity-reports.index');
        }
        
        return view('admin.activity-reports.index');
    }

    /**
     * Export activity reports
     */
    public function exportActivityReports(Request $request)
    {
        $reportType = $request->input('report_type', 'employee_activity');
        $timePeriod = $request->input('time_period', 'monthly');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $quarter = $request->input('quarter', ceil(now()->month / 3));
        $format = $request->input('format', 'xlsx');
        
        // Delegate to ActivityReportController's export method
        $controller = app()->make(ActivityReportController::class);
        return $controller->export($request);
    }

    /**
     * Print activity reports
     */
    public function printActivityReports(Request $request)
    {
        $reportType = $request->input('report_type', 'employee_activity');
        $timePeriod = $request->input('time_period', 'monthly');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $quarter = $request->input('quarter', ceil(now()->month / 3));
        
        // Get data from ActivityReportController
        $controller = app()->make(ActivityReportController::class);
        $request->merge([
            'report_type' => $reportType,
            'time_period' => $timePeriod,
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter
        ]);
        
        $response = $controller->getData($request);
        $data = $response->getData(true);
        
        // Determine view based on role
        $viewPrefix = Auth::check() && Auth::user()->role === 'admin_bas' ? 'admin_bas' : 'admin';
        
        return view($viewPrefix . '.activity-reports.print', compact('data', 'reportType', 'timePeriod', 'year', 'month', 'quarter'));
    }

    public function getBookings(Request $request)
    {
        $query = Booking::with(['meetingRoom'])
            ->select('bookings.*')
            ->distinct();

        // Filter berdasarkan waktu
        if ($request->filter === 'today') {
            $query->whereDate('date', Carbon::today());
        } elseif ($request->filter === 'week') {
            $query->whereBetween('date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        } elseif ($request->filter === 'month') {
            $query->whereYear('date', Carbon::now()->year)
                ->whereMonth('date', Carbon::now()->month);
        } elseif ($request->filter === 'custom' && $request->date) {
            $query->whereDate('date', Carbon::parse($request->date));
        }

        $bookings = $query->orderBy('date', 'desc')->orderBy('start_time', 'desc')->get();

        // Hitung statistik
        $comparisonData = $this->calculateBookingComparison($bookings, $request);
        $stats = [
            'total_bookings' => $bookings->count(),
            'booking_comparison' => $comparisonData,
            'booking_trend' => $this->calculateBookingTrend($bookings),
            'room_usage' => $this->calculateRoomUsage($bookings),
            'department_stats' => $this->calculateDepartmentStats($bookings)
        ];

        return response()->json([
            'bookings' => $bookings,
            'stats' => $stats
        ]);
    }

    private function calculateBookingComparison($bookings, Request $request = null)
    {
        // Get current filter type
        $filterType = $request ? $request->filter : 'today';
        $customDate = $request ? $request->date : null;
        
        $totalBookings = $bookings->count();
        $previousBookings = 0;
        $comparisonText = '';
        $percentageChange = 0;
        
        // Calculate previous period bookings based on filter type
        if ($filterType === 'today' || $filterType === null) {
            // Compare with yesterday
            $yesterday = Carbon::yesterday();
            $previousBookings = Booking::whereDate('date', $yesterday)->count();
            $comparisonText = 'compared to yesterday';
        } 
        elseif ($filterType === 'week') {
            // Compare with previous week
            $previousWeekStart = Carbon::now()->startOfWeek()->subWeek();
            $previousWeekEnd = Carbon::now()->startOfWeek()->subDay();
            $previousBookings = Booking::whereBetween('date', [$previousWeekStart, $previousWeekEnd])->count();
            $comparisonText = 'compared to last week';
        } 
        elseif ($filterType === 'month') {
            // Compare with previous month
            $previousMonthStart = Carbon::now()->startOfMonth()->subMonth();
            $previousMonthEnd = Carbon::now()->startOfMonth()->subDay();
            $previousBookings = Booking::whereBetween('date', [$previousMonthStart, $previousMonthEnd])->count();
            $comparisonText = 'compared to last month';
        } 
        elseif ($filterType === 'custom' && $customDate) {
            // Compare with previous day of the selected date
            $selectedDate = Carbon::parse($customDate);
            $previousDay = $selectedDate->copy()->subDay();
            $previousBookings = Booking::whereDate('date', $previousDay)->count();
            $comparisonText = 'compared to previous day';
        }
        
        // Calculate percentage change
        if ($previousBookings > 0) {
            $percentageChange = round((($totalBookings - $previousBookings) / $previousBookings) * 100);
        } elseif ($totalBookings > 0 && $previousBookings == 0) {
            $percentageChange = 100; // If there were no previous bookings but there are now
        } else {
            $percentageChange = 0; // If both are 0
        }
        
        return [
            'previous_count' => $previousBookings,
            'current_count' => $totalBookings,
            'percentage_change' => $percentageChange,
            'comparison_text' => $comparisonText,
            'is_increase' => $totalBookings >= $previousBookings
        ];
    }

    private function calculateBookingTrend($bookings)
    {
        // Implementasi perhitungan tren booking
        // Contoh: Menggunakan perbandingan jumlah booking
        $totalBookings = $bookings->count();
        $previousBookings = Booking::whereDate('date', '<', Carbon::today())->count();
        $trend = ($totalBookings > $previousBookings) ? 'meningkat' : 'menurun';
        return $trend;
    }

    private function calculateRoomUsage($bookings)
    {
        // Implementasi perhitungan penggunaan ruangan
        // Contoh: Menggunakan perbandingan jumlah booking
        $totalBookings = $bookings->count();
        $totalRooms = MeetingRoom::count();
        
        // Calculate usage as a percentage and round to nearest integer
        $usage = ($totalBookings > 0 && $totalRooms > 0) ? round(($totalBookings / $totalRooms) * 100) : 0;
        
        return $usage;
    }

    private function calculateDepartmentStats($bookings)
    {
        // Implementasi perhitungan statistik departemen
        // Contoh: Menggunakan perbandingan jumlah booking
        $totalBookings = $bookings->count();
        $totalDepartments = Department::count();
        $departmentStats = [];
        $departments = Department::all();
        foreach ($departments as $department) {
            $departmentBookings = $bookings->where('department', $department->name)->count();
            $departmentStats[$department->name] = $departmentBookings;
        }
        return $departmentStats;
    }
}
