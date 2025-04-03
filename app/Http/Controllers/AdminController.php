<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Department;
use App\Models\User; // Tambahkan import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            // Pisahkan admin & superadmin
            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Jika role tidak sesuai, logout dan kembalikan error
            Auth::logout();
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke area admin.');
        }

        return redirect()->back()->with('error', 'Login gagal. Periksa kembali login dan password anda.');
    }

    /**
     * Logout admin/superadmin.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    /**
     * Dashboard untuk admin.
     * Misalnya: Tampilkan daftar booking.
     */
    public function dashboard()
    {
        $bookings = Booking::with('meetingRoom')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.dashboard.index', compact('bookings'));
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

    public function meetingRooms()
    {
        $rooms = MeetingRoom::orderBy('name', 'asc')->get();
        return view('admin.meeting-rooms.index', compact('rooms'));
    }

    public function createMeetingRoom()
    {
        return view('admin.meeting-rooms.create');
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

        MeetingRoom::create($validated);
        
        return redirect()->route('admin.meeting_rooms')
                         ->with('success', 'Ruang meeting berhasil ditambahkan.');
    }

    public function deleteMeetingRoom($id)
    {
        $room = MeetingRoom::findOrFail($id);
        $room->delete();
    
        return redirect()->route('admin.meeting_rooms')
                         ->with('success', 'Meeting room berhasil dihapus.');
    }

    public function editMeetingRoom($id)
    {
        $room = MeetingRoom::findOrFail($id);
        return view('admin.meeting-rooms.edit', compact('room'));
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
        $room->update($validated);
        
        return redirect()->route('admin.meeting_rooms')
                         ->with('success', 'Ruang meeting berhasil diperbarui.');
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
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    //                    K E L O L A   D E P A R T E M E N
    // ----------------------------------------------------------------

    public function departments()
    {
        $departments = Department::withCount('employees')
                        ->orderBy('name', 'asc')
                        ->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Department::create($validated);
        return redirect()->back()->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function deleteDepartment($id)
    {
        Department::destroy($id);
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
        $department->update($validated);

        return redirect()->route('admin.departments')->with('success', 'Departemen berhasil diperbarui.');
    }
}
