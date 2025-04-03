<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Menampilkan form untuk menambahkan kegiatan.
     */
    public function create()
    {
        // Ambil data departemen dan karyawan untuk dropdown
        $departments = Department::all();
        $employees = Employee::with('department')
                             ->orderBy('name', 'asc')
                             ->get();

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
            'Pontianak', 'Padang', 'Pekanbaru', 'Denpasar', 'Manado'
        ];

        return view('public.activity.index', compact('departments', 'employees', 'provinces', 'cities'));
    }

    /**
     * Menyimpan data kegiatan baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name'           => 'required|exists:employees,name',
            'department_id'  => 'required|exists:departments,id',
            'activity_type'  => 'required|in:Meeting,Invitation,Survey',
            'description'    => 'required|string',
            'province'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime'   => 'required|date_format:Y-m-d H:i|after:start_datetime',
        ]);

        // Simpan data kegiatan
        Activity::create([
            'name'          => $request->input('name'),
            'department_id' => $request->input('department_id'),
            'activity_type' => $request->input('activity_type'),
            'description'   => $request->input('description'),
            'city'          => $request->input('city'),
            'province'      => $request->input('province'),
            'start_datetime'=> $request->input('start_datetime'),
            'end_datetime'  => $request->input('end_datetime'),
        ]);

        return redirect()->route('activity.create')->with('success', 'Kegiatan berhasil dibuat!');
    }

    /**
     * Menampilkan kalender aktivitas.
     */
    public function calendar()
    {
        // Ambil semua departemen
        $departments = Department::all();
        
        // Ambil semua jenis aktivitas
        $activityTypes = ['Meeting', 'Invitation', 'Survey'];
        
        return view('public.activity.calendar', compact('departments', 'activityTypes'));
    }

    /**
     * Mengambil data events untuk kalender aktivitas.
     */
    public function calendarEvents(Request $request)
    {
        // Query dasar dengan eager loading department
        $query = Activity::with('department');
        
        // Filter berdasarkan department jika ada
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter berdasarkan jenis aktivitas jika ada
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        
        // Filter berdasarkan range tanggal jika ada
        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('start_datetime', [$request->start, $request->end]);
        }
        
        // Dapatkan hasil query
        $activities = $query->get();
        
        // Format data untuk FullCalendar
        $events = [];
        foreach ($activities as $activity) {
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $activity->start_datetime,
                'end' => $activity->end_datetime,
                'extendedProps' => [
                    'department' => $activity->department->name,
                    'activity_type' => $activity->activity_type,
                    'description' => $activity->description,
                    'city' => $activity->city,
                    'province' => $activity->province,
                    'location' => ($activity->city && $activity->province) 
                                ? $activity->city . ', ' . $activity->province 
                                : ($activity->city ?: $activity->province ?: 'No location')
                ]
            ];
        }
        
        return response()->json($events);
    }
}
