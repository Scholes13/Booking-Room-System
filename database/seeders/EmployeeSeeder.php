<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan ID departemen untuk referensi
        $departments = Department::all()->pluck('id', 'name')->toArray();
        
        // Data karyawan
        $employees = [
            // IT Department
            [
                'name' => 'Budi Santoso',
                'gender' => 'L',
                'department_id' => $departments['IT'],
                'position' => 'IT Manager'
            ],
            [
                'name' => 'Dewi Lestari',
                'gender' => 'P',
                'department_id' => $departments['IT'],
                'position' => 'Software Developer'
            ],
            [
                'name' => 'Ahmad Hidayat',
                'gender' => 'L',
                'department_id' => $departments['IT'],
                'position' => 'System Administrator'
            ],
            
            // Marketing Department
            [
                'name' => 'Siti Rahayu',
                'gender' => 'P',
                'department_id' => $departments['Marketing'],
                'position' => 'Marketing Manager'
            ],
            [
                'name' => 'Joko Widodo',
                'gender' => 'L',
                'department_id' => $departments['Marketing'],
                'position' => 'Digital Marketing Specialist'
            ],
            
            // Finance Department
            [
                'name' => 'Maya Sari',
                'gender' => 'P',
                'department_id' => $departments['Finance'],
                'position' => 'Finance Director'
            ],
            [
                'name' => 'Rudi Hartono',
                'gender' => 'L',
                'department_id' => $departments['Finance'],
                'position' => 'Accountant'
            ],
            
            // HR Department
            [
                'name' => 'Ani Wijaya',
                'gender' => 'P',
                'department_id' => $departments['HR'],
                'position' => 'HR Manager'
            ],
            [
                'name' => 'Bambang Suparno',
                'gender' => 'L',
                'department_id' => $departments['HR'],
                'position' => 'Recruitment Specialist'
            ],
            
            // Operations Department
            [
                'name' => 'Rina Susanti',
                'gender' => 'P',
                'department_id' => $departments['Operations'],
                'position' => 'Operations Manager'
            ],
            [
                'name' => 'Agus Setiawan',
                'gender' => 'L',
                'department_id' => $departments['Operations'],
                'position' => 'Supply Chain Analyst'
            ],
            
            // Sales Department
            [
                'name' => 'Adi Nugroho',
                'gender' => 'L',
                'department_id' => $departments['Sales'],
                'position' => 'Sales Director'
            ],
            [
                'name' => 'Putri Indah',
                'gender' => 'P',
                'department_id' => $departments['Sales'],
                'position' => 'Account Executive'
            ],
            
            // R&D Department
            [
                'name' => 'Eko Prasetyo',
                'gender' => 'L',
                'department_id' => $departments['Research & Development'],
                'position' => 'Research Director'
            ],
            [
                'name' => 'Ratna Dewi',
                'gender' => 'P',
                'department_id' => $departments['Research & Development'],
                'position' => 'Product Developer'
            ]
        ];
        
        // Tambahkan data karyawan ke database
        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }
    }
}