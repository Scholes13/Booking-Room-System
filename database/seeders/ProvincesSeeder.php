<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvincesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indonesia = Country::where('code', 'IDN')->first();
        
        if ($indonesia) {
            $provinces = [
                'Aceh',
                'Sumatera Utara',
                'Sumatera Barat',
                'Riau',
                'Jambi',
                'Sumatera Selatan',
                'Bengkulu',
                'Lampung',
                'Kepulauan Bangka Belitung',
                'Kepulauan Riau',
                'DKI Jakarta',
                'Jawa Barat',
                'Jawa Tengah',
                'DI Yogyakarta',
                'Jawa Timur',
                'Banten',
                'Bali',
                'Nusa Tenggara Barat',
                'Nusa Tenggara Timur',
                'Kalimantan Barat',
                'Kalimantan Tengah',
                'Kalimantan Selatan',
                'Kalimantan Timur',
                'Kalimantan Utara',
                'Sulawesi Utara',
                'Sulawesi Tengah',
                'Sulawesi Selatan',
                'Sulawesi Tenggara',
                'Gorontalo',
                'Sulawesi Barat',
                'Maluku',
                'Maluku Utara',
                'Papua',
                'Papua Barat',
            ];

            foreach ($provinces as $provinceName) {
                Province::create([
                    'name' => $provinceName,
                    'country_id' => $indonesia->id
                ]);
            }
        }
    }
}
