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

        return view('activity.activity', compact('departments', 'employees'));
    }

    /**
     * Menyimpan data kegiatan baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        // UBAH 'Invititation' => 'Invitation'
        $request->validate([
            'nama'           => 'required|exists:employees,name',
            'department'     => 'required|exists:departments,name',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime'   => 'required|date_format:Y-m-d H:i|after:start_datetime',
            // Perbaikan di sini:
            'activity_type'  => 'required|in:Meeting,Invitation,Survey',
            'description'    => 'required|string',
        ]);

        // Simpan data kegiatan
        Activity::create([
            'nama'           => $request->input('nama'),
            'department'     => $request->input('department'),
            'start_datetime' => Carbon::createFromFormat('Y-m-d H:i', $request->input('start_datetime'))
                                      ->format('Y-m-d H:i:s'),
            'end_datetime'   => Carbon::createFromFormat('Y-m-d H:i', $request->input('end_datetime'))
                                      ->format('Y-m-d H:i:s'),
            'activity_type'  => $request->input('activity_type'),
            'description'    => $request->input('description'),
        ]);

        return redirect()->route('activity.create')->with('success', 'Kegiatan berhasil dibuat!');
    }
}
