<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('department');

        // Search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('position', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Department Filter
        if ($request->has('department') && $request->department !== '') {
            $query->where('department_id', $request->department);
        }

        // Gender Filter
        if ($request->has('gender') && $request->gender !== '') {
            $query->where('gender', $request->gender);
        }

        $employees = $query->paginate(10);
        $departments = Department::all();

        // Get counts for dashboard
        $totalEmployees = Employee::count();
        $maleEmployees = Employee::where('gender', 'L')->count();
        $femaleEmployees = Employee::where('gender', 'P')->count();

        return view('admin.employees.index', compact(
            'employees', 
            'departments', 
            'totalEmployees', 
            'maleEmployees', 
            'femaleEmployees'
        ));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
        ]);

        Employee::create($request->all());

        return redirect()->route('admin.employees')
            ->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($request->all());

        return redirect()->route('admin.employees')
            ->with('success', 'Data karyawan berhasil diupdate!');
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            
            return redirect()->route('admin.employees')
                ->with('success', 'Karyawan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            $writer = new Writer();
            
            $filename = 'employees_' . date('Y-m-d_His') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $writer->openToFile('php://output');
            
            // Add header row
            $writer->addRow(Row::fromValues([
                'ID',
                'Nama',
                'Jenis Kelamin',
                'Departemen',
                'Jabatan',
                'Tanggal Dibuat'
            ]));
            
            // Query with filters
            $query = Employee::with('department');
            
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('position', 'LIKE', "%{$searchTerm}%");
                });
            }

            if ($request->has('department') && $request->department !== '') {
                $query->where('department_id', $request->department);
            }

            if ($request->has('gender') && $request->gender !== '') {
                $query->where('gender', $request->gender);
            }
            
            $employees = $query->get();
            
            // Add data rows
            foreach ($employees as $employee) {
                $writer->addRow(Row::fromValues([
                    $employee->id,
                    $employee->name,
                    $employee->gender == 'L' ? 'Laki-laki' : 'Perempuan',
                    $employee->department->name,
                    $employee->position ?? '-',
                    $employee->created_at->format('d/m/Y H:i')
                ]));
            }
            
            $writer->close();
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
}