<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Department;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
   // Tampilkan form booking
   public function create()
   {
       $departments = Department::all();
       $meetingRooms = MeetingRoom::orderBy('name', 'asc')->get();
       $employees = Employee::with('department')
                            ->orderBy('name', 'asc')
                            ->get();
   
       return view('public.booking.index', compact('departments', 'meetingRooms', 'employees'));
   }

   // Simpan booking
   public function store(Request $request)
   {
       // Validasi input
       $request->validate([
           'nama' => 'required|exists:employees,name',
           'department' => 'required|exists:departments,name', 
           'meeting_room_id' => 'required|exists:meeting_rooms,id',
           'date' => 'required|date',
           'start_time' => 'required|date_format:H:i',
           'end_time' => 'required|date_format:H:i|after:start_time',
           'booking_type' => 'required|in:internal,external',
           'external_description' => 'nullable|required_if:booking_type,external',
       ]);

       // Ambil semua booking yang sudah ada untuk ruangan dan tanggal yang sama
       $existingBookings = Booking::where('meeting_room_id', $request->meeting_room_id)
           ->where('date', $request->date)
           ->get();

       // Konversi waktu yang dipilih menjadi format Carbon
       $newStartTime = Carbon::createFromFormat('H:i', $request->input('start_time'));
       $newEndTime = Carbon::createFromFormat('H:i', $request->input('end_time'));

       // Cek tumpang tindih booking
       foreach ($existingBookings as $existingBooking) {
           $existingStartTime = Carbon::parse($existingBooking->start_time);
           $existingEndTime = Carbon::parse($existingBooking->end_time);

           if ($newStartTime < $existingEndTime && $newEndTime > $existingStartTime) {
               return redirect()->back()->withErrors([
                   'time_conflict' => 'Waktu yang Anda pilih bertabrakan dengan jadwal yang sudah ada.'
               ])->withInput();
           }
       }

       // Simpan booking jika tidak ada tumpang tindih
       Booking::create([
           'nama' => $request->input('nama'),
           'department' => $request->input('department'),
           'meeting_room_id' => $request->input('meeting_room_id'),
           'date' => $request->input('date'),
           'start_time' => $newStartTime->format('H:i:s'),
           'end_time' => $newEndTime->format('H:i:s'),
           'description' => $request->input('description'),
           'booking_type' => $request->input('booking_type'),
           'external_description' => $request->input('external_description'),
       ]);

       return redirect()->route('booking.create')->with('success', 'Booking berhasil dibuat!');
   }

   // Form edit booking
   public function edit($id)
   {
       // Ambil data booking berdasarkan ID
       $booking = Booking::findOrFail($id);
       
       // Ambil data departments dan meeting rooms untuk dropdown
       $departments = Department::all();
       $meetingRooms = MeetingRoom::orderBy('name', 'asc')->get();
       // Urutkan karyawan berdasarkan nama secara ascending
       $employees = Employee::with('department')
                            ->orderBy('name', 'asc')
                            ->get();
       
       if (Auth::check() && Auth::user()->role === 'admin_bas') {
           return view('admin_bas.bookings.edit', compact('booking', 'departments', 'meetingRooms', 'employees'));
       }
       
       return view('admin.bookings.edit', compact('booking', 'departments', 'meetingRooms', 'employees'));
   }

   // Update booking
   public function update(Request $request, $id)
   {
       // Validasi input
       $request->validate([
           'nama' => 'required|exists:employees,name',
           'department' => 'required|exists:departments,name',
           'meeting_room_id' => 'required|exists:meeting_rooms,id',
           'date' => 'required|date',
           'start_time' => 'required|date_format:H:i',
           'end_time' => 'required|date_format:H:i|after:start_time',
           'booking_type' => 'required|in:internal,external',
           'external_description' => 'nullable|required_if:booking_type,external',
       ]);

       // Get the booking
       $booking = Booking::findOrFail($id);

       // Check for time conflicts
       $existingBookings = Booking::where('meeting_room_id', $request->meeting_room_id)
           ->where('date', $request->date)
           ->where('id', '!=', $id)
           ->get();

       $newStartTime = Carbon::createFromFormat('H:i', $request->input('start_time'));
       $newEndTime = Carbon::createFromFormat('H:i', $request->input('end_time'));

       foreach ($existingBookings as $existingBooking) {
           $existingStartTime = Carbon::parse($existingBooking->start_time);
           $existingEndTime = Carbon::parse($existingBooking->end_time);

           if ($newStartTime < $existingEndTime && $newEndTime > $existingStartTime) {
               return redirect()->back()->withErrors([
                   'time_conflict' => 'Waktu yang Anda pilih bertabrakan dengan jadwal yang sudah ada.'
               ])->withInput();
           }
       }

       // Update the booking
       $booking->update([
           'nama' => $request->input('nama'),
           'department' => $request->input('department'),
           'meeting_room_id' => $request->input('meeting_room_id'),
           'date' => $request->input('date'),
           'start_time' => $newStartTime->format('H:i:s'),
           'end_time' => $newEndTime->format('H:i:s'),
           'description' => $request->input('description'),
           'booking_type' => $request->input('booking_type'),
           'external_description' => $request->input('external_description'),
       ]);

       // Determine the appropriate route prefix based on user role
       $prefix = 'admin.';
       if (Auth::check()) {
           switch (Auth::user()->role) {
               case 'admin_bas':
                   $prefix = 'bas.';
                   break;
               case 'superadmin':
                   $prefix = 'superadmin.';
                   break;
               default:
                   $prefix = 'admin.';
           }
       }
       
       return redirect()->route($prefix . 'bookings.index')->with('success', 'Booking berhasil diperbarui!');
   }

   // Get available times (AJAX)
   public function getAvailableTimes(Request $request)
   {
       $request->validate([
           'date' => 'required|date',
           'meeting_room_id' => 'required|exists:meeting_rooms,id',
       ]);

       $bookings = Booking::where('meeting_room_id', $request->meeting_room_id)
           ->where('date', $request->date)
           ->get()
           ->map(function($booking) {
               return [
                   'id' => $booking->id,
                   'start' => $booking->start_time,
                   'end' => $booking->end_time
               ];
           });

       return response()->json($bookings);
   }

   // Export to Excel
   public function export(Request $request)
   {
       try {
           $writer = new Writer();
           
           // Set header untuk download
           $filename = 'bookings_' . date('Y-m-d_His') . '.xlsx';
           header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
           header('Content-Disposition: attachment; filename="' . $filename . '"');
           
           $writer->openToFile('php://output');
           
           // Tambahkan header
           $writer->addRow(Row::fromValues([
               'Nama',
               'Departemen',
               'Ruang Meeting',
               'Tanggal',
               'Jam Mulai',
               'Jam Selesai',
               'Deskripsi',
               'Dibuat Pada'
           ]));
           
           // Query data
           $bookings = Booking::with('meetingRoom')
               ->when($request->has('start_date'), function($query) use ($request) {
                   return $query->whereDate('date', '>=', $request->start_date);
               })
               ->when($request->has('end_date'), function($query) use ($request) {
                   return $query->whereDate('date', '<=', $request->end_date);
               })
               ->orderBy('date', 'desc')
               ->get();
           
           // Tambahkan data
           foreach ($bookings as $booking) {
               $writer->addRow(Row::fromValues([
                   $booking->nama,
                   $booking->department,
                   $booking->meetingRoom->name,
                   Carbon::parse($booking->date)->format('d/m/Y'),
                   Carbon::parse($booking->start_time)->format('H:i'),
                   Carbon::parse($booking->end_time)->format('H:i'),
                   $booking->description ?? '-',
                   $booking->created_at->format('d/m/Y H:i')
               ]));
           }
           
           // Log aktivitas eksport
           ActivityLogService::logExport(
               'bookings', 
               "Mengekspor data booking " . 
               ($request->has('start_date') ? "dari tanggal " . $request->start_date : "") . 
               ($request->has('end_date') ? " sampai tanggal " . $request->end_date : ""),
               [
                   'total_records' => $bookings->count(),
                   'filters' => $request->only(['start_date', 'end_date'])
               ]
           );
           
           $writer->close();
           
       } catch (\Exception $e) {
           return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
       }
   }

   // Get statistics untuk dashboard
   public function getStatistics()
   {
       $today = Carbon::today();
       
       $statistics = [
           'today_bookings' => Booking::whereDate('date', $today)->count(),
           'upcoming_booking' => Booking::where('date', '>=', $today)
               ->orderBy('date')
               ->orderBy('start_time')
               ->first(),
           'room_usage' => $this->calculateRoomUsage(),
           'most_used_room' => $this->getMostUsedRoom()
       ];
       
       return response()->json($statistics);
   }

   private function calculateRoomUsage()
   {
       $totalRooms = MeetingRoom::count();
       $roomsInUse = Booking::whereDate('date', Carbon::today())
           ->where('start_time', '<=', Carbon::now()->format('H:i:s'))
           ->where('end_time', '>=', Carbon::now()->format('H:i:s'))
           ->distinct('meeting_room_id')
           ->count();
       
       return $totalRooms > 0 ? round(($roomsInUse / $totalRooms) * 100) : 0;
   }

   private function getMostUsedRoom()
   {
       return MeetingRoom::withCount(['bookings' => function($query) {
           $query->whereMonth('date', Carbon::now()->month);
       }])
       ->orderBy('bookings_count', 'desc')
       ->first();
   }

   // Delete booking
   public function delete($id)
   {
       $booking = Booking::findOrFail($id);
       $booking->delete();
       
       // Determine the appropriate route prefix based on user role
       $prefix = 'admin.';
       if (Auth::check()) {
           switch (Auth::user()->role) {
               case 'admin_bas':
                   $prefix = 'bas.';
                   break;
               case 'superadmin':
                   $prefix = 'superadmin.';
                   break;
               default:
                   $prefix = 'admin.';
           }
       }
       
       return redirect()->route($prefix . 'bookings.index')->with('success', 'Booking berhasil dihapus!');
   }

   // Tampilkan halaman daftar booking (admin)
   public function index(Request $request)
   {
       $query = Booking::with('meetingRoom')
           ->orderBy('date', 'desc')
           ->orderBy('start_time', 'asc');
       
       // Filter by search term if provided
       if ($request->has('search')) {
           $search = $request->search;
           $query->where(function($q) use ($search) {
               $q->where('nama', 'like', "%{$search}%")
                 ->orWhere('department', 'like', "%{$search}%")
                 ->orWhere('description', 'like', "%{$search}%")
                 ->orWhereHas('meetingRoom', function($q) use ($search) {
                     $q->where('name', 'like', "%{$search}%");
                 });
           });
       }
       
       // Filter by specific date if provided
       if ($request->has('date')) {
           $query->whereDate('date', $request->date);
       }
       // Apply predefined filters (today, week, month)
       elseif ($request->has('filter')) {
           $today = Carbon::today();
           
           switch ($request->filter) {
               case 'today':
                   $query->whereDate('date', $today);
                   break;
                   
               case 'week':
                   $weekStart = $today->copy()->startOfWeek();
                   $weekEnd = $today->copy()->endOfWeek();
                   $query->whereBetween('date', [$weekStart, $weekEnd]);
                   break;
                   
               case 'month':
                   $monthStart = $today->copy()->startOfMonth();
                   $monthEnd = $today->copy()->endOfMonth();
                   $query->whereBetween('date', [$monthStart, $monthEnd]);
                   break;
           }
       }
       // Filter by date range if provided 
       else if ($request->has('start_date')) {
           $query->whereDate('date', '>=', $request->start_date);
           
           if ($request->has('end_date')) {
               $query->whereDate('date', '<=', $request->end_date);
           }
       }
       else if ($request->has('end_date')) {
           $query->whereDate('date', '<=', $request->end_date);
       }
       
       // Get paginated results
       $bookings = $query->paginate(10);
       
       // Remember to keep any query parameters in pagination links
       $bookings->appends($request->all());
       
       // Get data for modal forms
       $departments = Department::all();
       $meetingRooms = MeetingRoom::orderBy('name', 'asc')->get();
       $employees = Employee::with('department')
                          ->orderBy('name', 'asc')
                          ->get();
       
       if (Auth::check()) {
           switch (Auth::user()->role) {
               case 'admin_bas':
                   return view('admin_bas.bookings.index', compact('bookings', 'departments', 'meetingRooms', 'employees'));
               case 'superadmin':
                   return view('superadmin.bookings.index', compact('bookings', 'departments', 'meetingRooms', 'employees'));
               default:
                   return view('admin.bookings.index', compact('bookings', 'departments', 'meetingRooms', 'employees'));
           }
       }
       
       return view('admin.bookings.index', compact('bookings', 'departments', 'meetingRooms', 'employees'));
   }
}