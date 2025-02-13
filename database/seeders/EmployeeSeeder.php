<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada departemen dulu
        $department = Department::first();

        if ($department) {
            Employee::create([
                'name' => 'John Doe',
                'gender' => 'L',
                'department_id' => $department->id,
                'position' => 'Staff'
            ]);

            Employee::create([
                'name' => 'Jane Doe',
                'gender' => 'P',
                'department_id' => $department->id,
                'position' => 'Manager'
            ]);
        }
    }
}