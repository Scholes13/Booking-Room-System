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
            ResetDataSeeder::class,  // Reset and recreate other data
            ActivityTypeSeeder::class, // Add activity types
        ]);
    }
}
