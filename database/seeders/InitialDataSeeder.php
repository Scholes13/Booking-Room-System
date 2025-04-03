<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MeetingRoom;
use App\Models\Department;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat Ruang Meeting
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

        // Membuat Departemen
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
    }
} 