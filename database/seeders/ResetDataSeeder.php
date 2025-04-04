<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MeetingRoom;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Booking;
use App\Models\Activity;

class ResetDataSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        Activity::truncate();
        Booking::truncate();
        Employee::truncate();
        Department::truncate();
        MeetingRoom::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Seed fresh meeting rooms
        $meetingRooms = [
            'Ruang Meeting A',
            'Ruang Meeting B',
            'Ruang Rapat Direksi',
            'Ruang Konferensi',
            'Ruang Presentasi'
        ];

        foreach ($meetingRooms as $roomName) {
            MeetingRoom::create([
                'name' => $roomName
            ]);
        }

        // Seed fresh departments
        $departments = [
            'IT',
            'Marketing',
            'Finance',
            'HR',
            'Operations',
            'Sales',
            'Research & Development'
        ];

        foreach ($departments as $deptName) {
            Department::create([
                'name' => $deptName
            ]);
        }

        // Seed sample employees
        $departments = Department::all();
        
        $employees = [
            [
                'name' => 'Ani Wijaya', 
                'department_id' => 1, 
                'gender' => 'P', 
                'position' => 'HR Manager',
                'phone' => '081234567001',
                'email' => 'ani.wijaya@example.com'
            ],
            [
                'name' => 'Budi Santoso', 
                'department_id' => 2, 
                'gender' => 'L', 
                'position' => 'Sales Executive',
                'phone' => '081234567002',
                'email' => 'budi.santoso@example.com'
            ],
            [
                'name' => 'Citra Dewi', 
                'department_id' => 3, 
                'gender' => 'P', 
                'position' => 'Marketing Staff',
                'phone' => '081234567003',
                'email' => 'citra.dewi@example.com'
            ],
            [
                'name' => 'Dian Pratama', 
                'department_id' => 4, 
                'gender' => 'L', 
                'position' => 'IT Staff',
                'phone' => '081234567004',
                'email' => 'dian.pratama@example.com'
            ],
            [
                'name' => 'Eko Nugroho', 
                'department_id' => 5, 
                'gender' => 'L', 
                'position' => 'Finance Staff',
                'phone' => '081234567005',
                'email' => 'eko.nugroho@example.com'
            ],
            [
                'name' => 'Fitri Handayani', 
                'department_id' => 6, 
                'gender' => 'P', 
                'position' => 'Operations Manager',
                'phone' => '081234567006',
                'email' => 'fitri.handayani@example.com'
            ],
            [
                'name' => 'Gunawan', 
                'department_id' => 7, 
                'gender' => 'L', 
                'position' => 'R&D Staff',
                'phone' => '081234567007',
                'email' => 'gunawan@example.com'
            ],
            [
                'name' => 'Hesti Putri', 
                'department_id' => 1, 
                'gender' => 'P', 
                'position' => 'HR Staff',
                'phone' => '081234567008',
                'email' => 'hesti.putri@example.com'
            ],
            [
                'name' => 'Irfan Malik', 
                'department_id' => 2, 
                'gender' => 'L', 
                'position' => 'Sales Manager',
                'phone' => '081234567009',
                'email' => 'irfan.malik@example.com'
            ],
            [
                'name' => 'Joko Widodo', 
                'department_id' => 3, 
                'gender' => 'L', 
                'position' => 'Marketing Director',
                'phone' => '081234567010',
                'email' => 'joko.widodo@example.com'
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
} 