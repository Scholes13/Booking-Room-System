<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add all activity types that were in the form
        $activityTypes = [
            [
                'name' => 'Meeting',
                'description' => 'Pertemuan formal antara dua atau lebih orang untuk mendiskusikan topik tertentu',
                'is_active' => true
            ],
            [
                'name' => 'Training',
                'description' => 'Kegiatan pelatihan untuk meningkatkan keterampilan atau pengetahuan',
                'is_active' => true
            ],
            [
                'name' => 'Workshop',
                'description' => 'Pertemuan di mana sekelompok orang terlibat dalam diskusi dan aktivitas intensif pada subjek tertentu',
                'is_active' => true
            ],
            [
                'name' => 'Conference',
                'description' => 'Pertemuan formal untuk diskusi, pertukaran informasi, atau pembahasan masalah tertentu',
                'is_active' => true
            ],
            [
                'name' => 'Invitation',
                'description' => 'Kegiatan yang dihadiri berdasarkan undangan',
                'is_active' => true
            ],
            [
                'name' => 'Survey',
                'description' => 'Kegiatan pengumpulan data atau informasi',
                'is_active' => true
            ],
            [
                'name' => 'Courtesy Visit',
                'description' => 'Kunjungan formal sebagai bentuk penghormatan atau kesopanan',
                'is_active' => true
            ],
            [
                'name' => 'External Activities',
                'description' => 'Kegiatan yang dilakukan di luar organisasi',
                'is_active' => true
            ],
            [
                'name' => 'Hosting',
                'description' => 'Menjadi tuan rumah untuk suatu acara atau kegiatan',
                'is_active' => true
            ],
            [
                'name' => 'Internal Activities',
                'description' => 'Kegiatan yang dilakukan di dalam organisasi',
                'is_active' => true
            ],
            [
                'name' => 'Meeting External',
                'description' => 'Pertemuan dengan pihak luar organisasi',
                'is_active' => true
            ],
            [
                'name' => 'Other',
                'description' => 'Jenis kegiatan lainnya yang tidak termasuk dalam kategori di atas',
                'is_active' => true
            ]
        ];

        foreach ($activityTypes as $type) {
            ActivityType::firstOrCreate(
                ['name' => $type['name']],
                [
                    'description' => $type['description'],
                    'is_active' => $type['is_active']
                ]
            );
        }
    }
}
