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

        // Ambil jenis aktivitas dari database
        $dbActivityTypes = \App\Models\ActivityType::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
            
        // Konversi nama activity type dari bahasa Inggris ke bahasa Indonesia jika diperlukan
        $activityTypes = [];
        foreach ($dbActivityTypes as $type) {
            if ($type == 'Other') {
                // Skip 'Other' karena kita akan menambahkan 'Lainnya' di akhir
                continue;
            }
            $activityTypes[] = $type;
        }
        
        // Tambahkan 'Lainnya' di akhir array
        $activityTypes[] = 'Lainnya';

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

        return view('public.activity.index', compact('departments', 'employees', 'provinces', 'cities', 'activityTypes'));
    }

    /**
     * Menyimpan data kegiatan baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validationRules = [
            'name'           => 'required|exists:employees,name',
            'department_id'  => 'required|exists:departments,id',
            'activity_type'  => 'required|string',
            'description'    => 'required|string',
            'province'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime'   => 'required|date_format:Y-m-d H:i|after:start_datetime',
        ];
        
        // Tambahkan validasi untuk tipe kegiatan "Lainnya"
        if ($request->activity_type === 'Lainnya') {
            $validationRules['activity_type_other'] = 'required|string|max:100';
        }
        
        $request->validate($validationRules);

        // Menyiapkan data untuk disimpan
        $activityData = [
            'name'          => $request->input('name'),
            'department_id' => $request->input('department_id'),
            'description'   => $request->input('description'),
            'city'          => $request->input('city'),
            'province'      => $request->input('province'),
            'start_datetime'=> $request->input('start_datetime'),
            'end_datetime'  => $request->input('end_datetime'),
        ];
        
        // Jika tipe kegiatan adalah "Lainnya", gunakan nilai dari activity_type_other
        if ($request->activity_type === 'Lainnya') {
            $activityData['activity_type'] = 'Lainnya: ' . $request->input('activity_type_other');
        } else {
            $activityData['activity_type'] = $request->input('activity_type');
        }

        // Simpan data kegiatan
        Activity::create($activityData);

        return redirect()->route('activity.create')->with('success', 'Kegiatan berhasil dibuat!');
    }

    /**
     * Menampilkan kalender aktivitas.
     */
    public function calendar()
    {
        // Ambil semua departemen
        $departments = Department::all();
        
        // Ambil jenis aktivitas dari database
        $dbActivityTypes = \App\Models\ActivityType::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
            
        // Konversi nama activity type dari bahasa Inggris ke bahasa Indonesia jika diperlukan
        $activityTypes = [];
        foreach ($dbActivityTypes as $type) {
            if ($type == 'Other') {
                // Skip 'Other' karena kita akan menambahkan 'Lainnya' di akhir
                continue;
            }
            $activityTypes[] = $type;
        }
        
        // Tambahkan 'Lainnya' di akhir array
        $activityTypes[] = 'Lainnya';
        
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
            // Jika filter adalah "Lainnya", cari semua tipe yang dimulai dengan "Lainnya:"
            if ($request->activity_type === 'Lainnya') {
                $query->where('activity_type', 'like', 'Lainnya:%');
            } else {
                $query->where('activity_type', $request->activity_type);
            }
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
            // Tentukan tipe aktivitas yang ditampilkan
            $displayActivityType = $activity->activity_type;

            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $activity->start_datetime,
                'end' => $activity->end_datetime,
                'extendedProps' => [
                    'department' => $activity->department->name,
                    'activity_type' => $displayActivityType,
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
    
    /**
     * Menampilkan daftar semua kegiatan untuk admin.
     */
    public function index()
    {
        $activities = Activity::with('department')
                        ->orderBy('start_datetime', 'desc')
                        ->paginate(10);
                        
        return view('superadmin.activities.index', compact('activities'));
    }
    
    /**
     * Menampilkan form untuk menambahkan kegiatan (admin).
     */
    public function createAdmin()
    {
        // Ambil data departemen dan karyawan untuk dropdown
        $departments = Department::all();
        $employees = Employee::with('department')
                             ->orderBy('name', 'asc')
                             ->get();

        // Ambil jenis aktivitas dari database
        $dbActivityTypes = \App\Models\ActivityType::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
            
        // Konversi nama activity type dari bahasa Inggris ke bahasa Indonesia jika diperlukan
        $activityTypes = [];
        foreach ($dbActivityTypes as $type) {
            if ($type == 'Other') {
                // Skip 'Other' karena kita akan menambahkan 'Lainnya' di akhir
                continue;
            }
            $activityTypes[] = $type;
        }
        
        // Tambahkan 'Lainnya' di akhir array
        $activityTypes[] = 'Lainnya';

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
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur',
            'Bandung', 'Bekasi', 'Tangerang', 'Depok', 'Bogor', 'Semarang', 'Yogyakarta',
            'Surabaya', 'Malang', 'Medan', 'Palembang', 'Makassar', 'Balikpapan', 'Banjarmasin',
            'Pontianak', 'Padang', 'Pekanbaru', 'Denpasar', 'Manado'
        ];

        return view('superadmin.activities.create', compact('departments', 'employees', 'provinces', 'cities', 'activityTypes'));
    }
    
    /**
     * Menyimpan data kegiatan baru (admin).
     */
    public function storeAdmin(Request $request)
    {
        // Validasi input
        $validationRules = [
            'name'           => 'required|exists:employees,name',
            'department_id'  => 'required|exists:departments,id',
            'activity_type'  => 'required|string',
            'description'    => 'required|string',
            'province'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime'   => 'required|date_format:Y-m-d H:i|after:start_datetime',
        ];
        
        // Tambahkan validasi untuk tipe kegiatan "Lainnya"
        if ($request->activity_type === 'Lainnya') {
            $validationRules['activity_type_other'] = 'required|string|max:100';
        }
        
        $request->validate($validationRules);

        // Menyiapkan data untuk disimpan
        $activityData = [
            'name'          => $request->input('name'),
            'department_id' => $request->input('department_id'),
            'description'   => $request->input('description'),
            'city'          => $request->input('city'),
            'province'      => $request->input('province'),
            'start_datetime'=> $request->input('start_datetime'),
            'end_datetime'  => $request->input('end_datetime'),
        ];
        
        // Jika tipe kegiatan adalah "Lainnya", gunakan nilai dari activity_type_other
        if ($request->activity_type === 'Lainnya') {
            $activityData['activity_type'] = 'Lainnya: ' . $request->input('activity_type_other');
        } else {
            $activityData['activity_type'] = $request->input('activity_type');
        }

        // Simpan data kegiatan
        Activity::create($activityData);

        return redirect()->route('superadmin.activities.index')->with('success', 'Kegiatan berhasil dibuat!');
    }
    
    /**
     * Menampilkan form untuk mengedit kegiatan.
     */
    public function edit($id)
    {
        $activity = Activity::findOrFail($id);
        $departments = Department::all();
        $employees = Employee::with('department')
                             ->orderBy('name', 'asc')
                             ->get();
                             
        // Ambil jenis aktivitas dari database
        $dbActivityTypes = \App\Models\ActivityType::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
            
        // Konversi nama activity type dari bahasa Inggris ke bahasa Indonesia jika diperlukan
        $activityTypes = [];
        foreach ($dbActivityTypes as $type) {
            if ($type == 'Other') {
                // Skip 'Other' karena kita akan menambahkan 'Lainnya' di akhir
                continue;
            }
            $activityTypes[] = $type;
        }
        
        // Tambahkan 'Lainnya' di akhir array
        $activityTypes[] = 'Lainnya';

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
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur',
            'Bandung', 'Bekasi', 'Tangerang', 'Depok', 'Bogor', 'Semarang', 'Yogyakarta',
            'Surabaya', 'Malang', 'Medan', 'Palembang', 'Makassar', 'Balikpapan', 'Banjarmasin',
            'Pontianak', 'Padang', 'Pekanbaru', 'Denpasar', 'Manado'
        ];
        
        // Cek apakah activity_type dimulai dengan "Lainnya:"
        $activityTypeOther = '';
        $selectedActivityType = $activity->activity_type;
        
        if (strpos($activity->activity_type, 'Lainnya:') === 0) {
            $selectedActivityType = 'Lainnya';
            $activityTypeOther = substr($activity->activity_type, 9); // Ambil bagian setelah "Lainnya: "
        }
        
        return view('superadmin.activities.edit', compact(
            'activity', 
            'departments', 
            'employees', 
            'provinces', 
            'cities', 
            'activityTypes', 
            'selectedActivityType', 
            'activityTypeOther'
        ));
    }
    
    /**
     * Memperbarui data kegiatan.
     */
    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        
        // Validasi input
        $validationRules = [
            'name'           => 'required|exists:employees,name',
            'department_id'  => 'required|exists:departments,id',
            'activity_type'  => 'required|string',
            'description'    => 'required|string',
            'province'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime'   => 'required|date_format:Y-m-d H:i|after:start_datetime',
        ];
        
        // Tambahkan validasi untuk tipe kegiatan "Lainnya"
        if ($request->activity_type === 'Lainnya') {
            $validationRules['activity_type_other'] = 'required|string|max:100';
        }
        
        $request->validate($validationRules);
        
        // Menyiapkan data untuk disimpan
        $activityData = [
            'name'          => $request->input('name'),
            'department_id' => $request->input('department_id'),
            'description'   => $request->input('description'),
            'city'          => $request->input('city'),
            'province'      => $request->input('province'),
            'start_datetime'=> $request->input('start_datetime'),
            'end_datetime'  => $request->input('end_datetime'),
        ];
        
        // Jika tipe kegiatan adalah "Lainnya", gunakan nilai dari activity_type_other
        if ($request->activity_type === 'Lainnya') {
            $activityData['activity_type'] = 'Lainnya: ' . $request->input('activity_type_other');
        } else {
            $activityData['activity_type'] = $request->input('activity_type');
        }
        
        // Update data kegiatan
        $activity->update($activityData);
        
        return redirect()->route('superadmin.activities.index')->with('success', 'Kegiatan berhasil diperbarui!');
    }
    
    /**
     * Menghapus data kegiatan.
     */
    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        
        return redirect()->route('superadmin.activities.index')->with('success', 'Kegiatan berhasil dihapus!');
    }
    
    /**
     * Menampilkan kalender aktivitas (admin).
     */
    public function adminCalendar()
    {
        // Ambil semua departemen
        $departments = Department::all();
        
        // Ambil jenis aktivitas dari database
        $dbActivityTypes = \App\Models\ActivityType::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
            
        // Konversi nama activity type dari bahasa Inggris ke bahasa Indonesia jika diperlukan
        $activityTypes = [];
        foreach ($dbActivityTypes as $type) {
            if ($type == 'Other') {
                // Skip 'Other' karena kita akan menambahkan 'Lainnya' di akhir
                continue;
            }
            $activityTypes[] = $type;
        }
        
        // Tambahkan 'Lainnya' di akhir array
        $activityTypes[] = 'Lainnya';
        
        return view('superadmin.activities.calendar', compact('departments', 'activityTypes'));
    }
    
    /**
     * Mengambil data events untuk kalender aktivitas (admin).
     */
    public function adminCalendarEvents(Request $request)
    {
        // Query dasar dengan eager loading department
        $query = Activity::with('department');
        
        // Filter berdasarkan department jika ada
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter berdasarkan jenis aktivitas jika ada
        if ($request->filled('activity_type')) {
            // Jika filter adalah "Lainnya", cari semua tipe yang dimulai dengan "Lainnya:"
            if ($request->activity_type === 'Lainnya') {
                $query->where('activity_type', 'like', 'Lainnya:%');
            } else {
                $query->where('activity_type', $request->activity_type);
            }
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
            // Tentukan tipe aktivitas yang ditampilkan
            $displayActivityType = $activity->activity_type;

            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $activity->start_datetime,
                'end' => $activity->end_datetime,
                'extendedProps' => [
                    'department' => $activity->department->name,
                    'activity_type' => $displayActivityType,
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
