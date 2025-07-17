<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    /**
     * Get employee by name with department information
     */
    public function getByName(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $employee = Employee::with('department')
            ->where('name', $request->name)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee found',
            'data' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'department' => [
                    'id' => $employee->department->id ?? null,
                    'name' => $employee->department->name ?? null
                ],
                'position' => $employee->position,
                'email' => $employee->email,
                'phone' => $employee->phone
            ]
        ]);
    }

    /**
     * Search employees by name (for autocomplete)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255'
        ]);

        $employees = Employee::with('department')
            ->where('name', 'LIKE', '%' . $request->query . '%')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'department' => [
                        'id' => $employee->department->id ?? null,
                        'name' => $employee->department->name ?? null
                    ],
                    'position' => $employee->position
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Employees found',
            'data' => $employees
        ]);
    }

    /**
     * Get all employees with departments (for dropdowns)
     */
    public function index(): JsonResponse
    {
        $employees = Employee::with('department')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'department' => [
                        'id' => $employee->department->id ?? null,
                        'name' => $employee->department->name ?? null
                    ],
                    'position' => $employee->position,
                    'email' => $employee->email,
                    'phone' => $employee->phone
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Employees retrieved successfully',
            'data' => $employees
        ]);
    }
}