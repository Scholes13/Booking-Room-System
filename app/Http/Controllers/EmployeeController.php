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
        // Determine if we're in superadmin context based on the route
        $isSuperAdmin = $request->route()->getName() === 'superadmin.employees';
        
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
            
        // Use a CASE statement to sort by position hierarchy
        $employeesQuery->orderByRaw("
            CASE 
                WHEN position LIKE '%CEO%' THEN 1
                WHEN position LIKE '%Managing Director%' THEN 2
                WHEN position LIKE '%HOD%' THEN 3
                WHEN position LIKE '%Coordinator%' THEN 4
                WHEN position LIKE '%Staff%' THEN 5
                ELSE 6
            END ASC, 
            name ASC
        ");

        // Count employees by gender - menggunakan query terpisah untuk menghindari masalah dengan filter
        $maleCount = Employee::where('gender', 'L')->count();
        $femaleCount = Employee::where('gender', 'P')->count();

        // Standard pagination instead of manual pagination
        $employees = $employeesQuery->paginate(10);

        $departments = Department::all();

        if ($request->ajax()) {
            return view('admin.employees.partials.table', compact('employees', 'departments', 'maleCount', 'femaleCount', 'isSuperAdmin'));
        }

        return view('admin.employees.index', compact('employees', 'departments', 'maleCount', 'femaleCount', 'isSuperAdmin'));
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

        // Determine the correct route based on the current request
        $routeName = strpos($request->route()->getName(), 'superadmin') !== false 
            ? 'superadmin.employees' 
            : 'admin.employees';

        return redirect()->route($routeName)
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

        // Determine the correct route based on the current request
        $routeName = strpos($request->route()->getName(), 'superadmin') !== false 
            ? 'superadmin.employees' 
            : 'admin.employees';

        return redirect()->route($routeName)
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
            
            // Determine the correct route based on the current request
            $routeName = strpos(request()->route()->getName(), 'superadmin') !== false 
                ? 'superadmin.employees' 
                : 'admin.employees';

            return redirect()->route($routeName)
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