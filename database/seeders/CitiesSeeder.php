<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jakartaCities = [
            'Jakarta Pusat',
            'Jakarta Utara',
            'Jakarta Barat',
            'Jakarta Selatan',
            'Jakarta Timur',
            'Kepulauan Seribu',
        ];
        
        $westJavaCities = [
            'Bandung',
            'Bekasi',
            'Bogor',
            'Depok',
            'Cimahi',
            'Tasikmalaya',
            'Sukabumi',
            'Banjar',
            'Cirebon',
            'Garut',
        ];
        
        $jakartaProvince = Province::where('name', 'DKI Jakarta')->first();
        $westJavaProvince = Province::where('name', 'Jawa Barat')->first();
        
        if ($jakartaProvince) {
            foreach ($jakartaCities as $cityName) {
                City::create([
                    'name' => $cityName,
                    'province_id' => $jakartaProvince->id
                ]);
            }
        }
        
        if ($westJavaProvince) {
            foreach ($westJavaCities as $cityName) {
                City::create([
                    'name' => $cityName,
                    'province_id' => $westJavaProvince->id
                ]);
            }
        }
    }
}
