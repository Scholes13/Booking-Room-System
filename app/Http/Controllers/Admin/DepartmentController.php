<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;

class DepartmentController extends Controller
{
    /**
     * Helper method untuk mendapatkan view berdasarkan role user
     */
    private function getViewByRole($adminView)
    {
        if (session('user_role') === 'superadmin') {
            $superadminView = str_replace('admin.', 'superadmin.', $adminView);
            if (view()->exists($superadminView)) {
                return $superadminView;
            }
        }
        return $adminView;
    }

    public function index()
    {
        $departments = Department::orderBy('name', 'asc')->get();
        $view = $this->getViewByRole('admin.departments.index');
        return view($view, compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:50|unique:departments,code',
        ]);

        $department = Department::create($validated);

        ActivityLogService::logCreate(
            'departments',
            'Menambahkan departemen baru: ' . $department->name,
            $validated
        );

        $routeName = 'admin.departments';
        if (session('user_role') === 'superadmin') {
            $routeName = 'superadmin.departments';
        }

        return redirect()->route($routeName)->with('success', 'Departemen berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $view = $this->getViewByRole('admin.departments.edit');
        return view($view, compact('department'));
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'code' => 'required|string|max:50|unique:departments,code,' . $id,
        ]);

        $department->update($validated);

        ActivityLogService::logUpdate(
            'departments',
            'Mengupdate departemen: ' . $department->name,
            $validated
        );

        $routeName = 'admin.departments';
        if (session('user_role') === 'superadmin') {
            $routeName = 'superadmin.departments';
        }

        return redirect()->route($routeName)->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $departmentName = $department->name;
        $department->delete();

        ActivityLogService::logDelete(
            'departments',
            'Menghapus departemen: ' . $departmentName,
            ['id' => $id, 'name' => $departmentName]
        );

        $routeName = 'admin.departments';
        if (session('user_role') === 'superadmin') {
            $routeName = 'superadmin.departments';
        }

        return redirect()->route($routeName)->with('success', 'Departemen berhasil dihapus.');
    }
} 