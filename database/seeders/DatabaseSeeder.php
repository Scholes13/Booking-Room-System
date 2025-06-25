<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class, // Create superadmin user
            AdminSeeder::class,      // Create admin users
            CountriesSeeder::class,  // Add countries
            ProvincesSeeder::class,  // Add provinces
            CitiesSeeder::class,     // Add cities
            ResetDataSeeder::class,  // Reset and recreate other data
            ActivityTypeSeeder::class, // Add activity types
            SalesMissionSeeder::class,
            LeadUserSeeder::class,
        ]);
    }
}
