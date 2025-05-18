<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class UpdateEmployeeIdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all employees without employee_id
        $employees = Employee::whereNull('employee_id')->get();
        
        if ($employees->count() === 0) {
            $this->command->info('Tidak ada karyawan yang perlu diupdate!');
            return;
        }
        
        $this->command->info('Memulai update employee_id untuk ' . $employees->count() . ' karyawan...');
        
        $counter = 1;
        
        foreach ($employees as $employee) {
            // Get department code
            $department = Department::find($employee->department_id);
            $departmentCode = $department ? substr(strtoupper($department->name), 0, 3) : 'EMP';
            
            // Generate timestamp - use created_at if available, otherwise use current date
            $timestamp = $employee->created_at 
                ? $employee->created_at->format('ymd') 
                : now()->format('ymd');
            
            // Generate a unique sequence number
            $sequence = str_pad($counter, 3, '0', STR_PAD_LEFT);
            
            // Create employee_id
            $employeeId = "{$departmentCode}{$timestamp}{$sequence}";
            
            // Check if ID already exists (unlikely but just to be safe)
            while (Employee::where('employee_id', $employeeId)->exists()) {
                $counter++;
                $sequence = str_pad($counter, 3, '0', STR_PAD_LEFT);
                $employeeId = "{$departmentCode}{$timestamp}{$sequence}";
            }
            
            // Update employee
            $employee->employee_id = $employeeId;
            $employee->save();
            
            $this->command->info("Updated karyawan: {$employee->name} dengan ID: {$employeeId}");
            
            $counter++;
        }
        
        $this->command->info('Berhasil mengupdate semua employee_id!');
    }
} 