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
                'position' => 'IT Manager',
                'phone' => '081234567890',
                'email' => 'budi.santoso@example.com'
            ],
            [
                'name' => 'Dewi Lestari',
                'gender' => 'P',
                'department_id' => $departments['IT'],
                'position' => 'Software Developer',
                'phone' => '081234567891',
                'email' => 'dewi.lestari@example.com'
            ],
            [
                'name' => 'Ahmad Hidayat',
                'gender' => 'L',
                'department_id' => $departments['IT'],
                'position' => 'System Administrator',
                'phone' => '081234567892',
                'email' => 'ahmad.hidayat@example.com'
            ],
            
            // Marketing Department
            [
                'name' => 'Siti Rahayu',
                'gender' => 'P',
                'department_id' => $departments['Marketing'],
                'position' => 'Marketing Manager',
                'phone' => '081234567893',
                'email' => 'siti.rahayu@example.com'
            ],
            [
                'name' => 'Joko Widodo',
                'gender' => 'L',
                'department_id' => $departments['Marketing'],
                'position' => 'Digital Marketing Specialist',
                'phone' => '081234567894',
                'email' => 'joko.widodo@example.com'
            ],
            
            // Finance Department
            [
                'name' => 'Maya Sari',
                'gender' => 'P',
                'department_id' => $departments['Finance'],
                'position' => 'Finance Director',
                'phone' => '081234567895',
                'email' => 'maya.sari@example.com'
            ],
            [
                'name' => 'Rudi Hartono',
                'gender' => 'L',
                'department_id' => $departments['Finance'],
                'position' => 'Accountant',
                'phone' => '081234567896',
                'email' => 'rudi.hartono@example.com'
            ],
            
            // HR Department
            [
                'name' => 'Ani Wijaya',
                'gender' => 'P',
                'department_id' => $departments['HR'],
                'position' => 'HR Manager',
                'phone' => '081234567897',
                'email' => 'ani.wijaya@example.com'
            ],
            [
                'name' => 'Bambang Suparno',
                'gender' => 'L',
                'department_id' => $departments['HR'],
                'position' => 'Recruitment Specialist',
                'phone' => '081234567898',
                'email' => 'bambang.suparno@example.com'
            ],
            
            // Operations Department
            [
                'name' => 'Rina Susanti',
                'gender' => 'P',
                'department_id' => $departments['Operations'],
                'position' => 'Operations Manager',
                'phone' => '081234567899',
                'email' => 'rina.susanti@example.com'
            ],
            [
                'name' => 'Agus Setiawan',
                'gender' => 'L',
                'department_id' => $departments['Operations'],
                'position' => 'Supply Chain Analyst',
                'phone' => '081234567800',
                'email' => 'agus.setiawan@example.com'
            ],
            
            // Sales Department
            [
                'name' => 'Adi Nugroho',
                'gender' => 'L',
                'department_id' => $departments['Sales'],
                'position' => 'Sales Director',
                'phone' => '081234567801',
                'email' => 'adi.nugroho@example.com'
            ],
            [
                'name' => 'Putri Indah',
                'gender' => 'P',
                'department_id' => $departments['Sales'],
                'position' => 'Account Executive',
                'phone' => '081234567802',
                'email' => 'putri.indah@example.com'
            ],
            
            // R&D Department
            [
                'name' => 'Eko Prasetyo',
                'gender' => 'L',
                'department_id' => $departments['Research & Development'],
                'position' => 'Research Director',
                'phone' => '081234567803',
                'email' => 'eko.prasetyo@example.com'
            ],
            [
                'name' => 'Ratna Dewi',
                'gender' => 'P',
                'department_id' => $departments['Research & Development'],
                'position' => 'Product Developer',
                'phone' => '081234567804',
                'email' => 'ratna.dewi@example.com'
            ]
        ];
        
        // Tambahkan data karyawan ke database
        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }
    }
}