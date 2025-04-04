<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Services\ActivityLogService;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeExport;

class EmployeeController extends Controller
{
    public function index(Request $request)
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

        // Count employees by gender - menggunakan query terpisah untuk menghindari masalah dengan filter
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
            return view('admin.employees.partials.table', compact('employees', 'departments', 'maleCount', 'femaleCount'));
        }

        return view('admin.employees.index', compact('employees', 'departments', 'maleCount', 'femaleCount'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.employees.create', compact('departments'));
    }

    public function store(EmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());
        
        // Log aktivitas admin
        ActivityLogService::logCreate(
            'employees', 
            "Menambahkan karyawan baru: {$employee->name}",
            $request->validated()
        );

        return redirect()->route('admin.employees')
            ->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    public function update(EmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $oldData = $employee->toArray();
        
        $employee->update($request->validated());
        
        // Log aktivitas admin
        ActivityLogService::logUpdate(
            'employees', 
            "Memperbarui data karyawan: {$employee->name}",
            [
                'old_data' => $oldData,
                'new_data' => $employee->toArray()
            ]
        );

        return redirect()->route('admin.employees')
            ->with('success', 'Data karyawan berhasil diupdate!');
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employeeData = $employee->toArray();
            
            $employee->delete();
            
            // Log aktivitas admin
            ActivityLogService::logDelete(
                'employees', 
                "Menghapus karyawan: {$employee->name}",
                $employeeData
            );
            
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
                'No. HP/WA',
                'Email',
                'Tanggal Dibuat'
            ]));
            
            // Query with filters - improved with case insensitive search
            $query = Employee::with('department')
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
            
            $employees = $query->get();
            
            // Add data rows
            foreach ($employees as $employee) {
                $writer->addRow(Row::fromValues([
                    $employee->id,
                    $employee->name,
                    $employee->gender == 'L' ? 'Laki-laki' : 'Perempuan',
                    $employee->department->name,
                    $employee->position ?? '-',
                    $employee->phone ?? '-',
                    $employee->email ?? '-',
                    $employee->created_at->format('d/m/Y H:i')
                ]));
            }
            
            // Log aktivitas eksport
            ActivityLogService::logExport(
                'employees', 
                "Mengekspor data karyawan" . 
                ($request->filled('search') ? " dengan pencarian: " . $request->search : "") . 
                ($request->filled('department_id') ? " untuk departemen ID: " . $request->department_id : "") . 
                ($request->filled('gender') ? " dengan jenis kelamin: " . ($request->gender == 'L' ? 'Laki-laki' : 'Perempuan') : ""),
                [
                    'total_records' => $employees->count(),
                    'filters' => $request->only(['search', 'department_id', 'gender'])
                ]
            );
            
            $writer->close();
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
}