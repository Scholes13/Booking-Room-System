<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Indonesia', 'code' => 'IDN', 'flag' => 'id.png'],
            ['name' => 'Malaysia', 'code' => 'MYS', 'flag' => 'my.png'],
            ['name' => 'Singapore', 'code' => 'SGP', 'flag' => 'sg.png'],
            ['name' => 'Thailand', 'code' => 'THA', 'flag' => 'th.png'],
            ['name' => 'Vietnam', 'code' => 'VNM', 'flag' => 'vn.png'],
            ['name' => 'Philippines', 'code' => 'PHL', 'flag' => 'ph.png'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
