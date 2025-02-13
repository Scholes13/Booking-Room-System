<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Tampilkan halaman login admin
    public function showLogin()
    {
        return view('admin.login');
    }

    // Proses login admin sederhana (contoh menggunakan session)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin') {
            session(['admin' => true]);
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->with('error', 'Username atau password salah');
    }

    // Logout admin
    public function logout()
    {
        session()->forget('admin');
        return redirect()->route('admin.login');
    }

    // Dashboard admin: Tampilkan report booking
    public function dashboard()
    {
        $bookings = Booking::with('meetingRoom')
            ->orderBy('date', 'asc') // Urutkan berdasarkan tanggal terdekat (terlama ke terbaru)
            ->orderBy('start_time', 'asc') // Urutkan berdasarkan jam mulai (paling pagi lebih dulu)
            ->get();
    
        return view('admin.dashboard', compact('bookings'));
    }

    // Tampilkan halaman pengelolaan meeting room
    public function meetingRooms()
    {
        $rooms = MeetingRoom::all();
        return view('admin.meeting_rooms', compact('rooms'));
    }

    // Simpan meeting room baru
    public function storeMeetingRoom(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        MeetingRoom::create($validated);
        return redirect()->back()->with('success', 'Ruang meeting berhasil ditambahkan.');
    }

    // Hapus meeting room
    public function deleteMeetingRoom($id)
    {
        MeetingRoom::destroy($id);
        return redirect()->back()->with('success', 'Ruang meeting berhasil dihapus.');
    }
    
    // =============================================
    // Fitur Tambahan: Kelola Booking (Edit/Update/Hapus)
    // =============================================

    // Tampilkan formulir edit booking
    public function editBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $meetingRooms = MeetingRoom::all();
        return view('admin.edit_booking', compact('booking', 'meetingRooms'));
    }

    // Proses update booking
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
            return redirect()->back()->withInput()->with('error', 'Maaf, ruangan sudah dibooking pada waktu tersebut.');
        }

        $booking->update($validated);
        return redirect()->route('admin.dashboard')->with('success', 'Booking berhasil diperbarui.');
    }

    // Hapus booking
    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Booking berhasil dihapus.');
    }

    public function departments()
    {
        $departments = Department::all();
        return view('admin.departments', compact('departments'));
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

    
    
}
