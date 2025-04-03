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
            ['name' => 'Ani Wijaya', 'department_id' => 1],
            ['name' => 'Budi Santoso', 'department_id' => 2],
            ['name' => 'Citra Dewi', 'department_id' => 3],
            ['name' => 'Dian Pratama', 'department_id' => 4],
            ['name' => 'Eko Nugroho', 'department_id' => 5],
            ['name' => 'Fitri Handayani', 'department_id' => 6],
            ['name' => 'Gunawan', 'department_id' => 7],
            ['name' => 'Hesti Putri', 'department_id' => 1],
            ['name' => 'Irfan Malik', 'department_id' => 2],
            ['name' => 'Joko Widodo', 'department_id' => 3]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
} 