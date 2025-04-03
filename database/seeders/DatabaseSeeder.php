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
            AdminSeeder::class,      // Create admin users first
            ResetDataSeeder::class,  // Reset and recreate other data
        ]);
    }
}
