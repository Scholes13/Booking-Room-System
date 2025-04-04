<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        if (User::where('email', 'it@werkudara.com')->exists()) {
            $this->command->info('SuperAdmin user already exists!');
            return;
        }
        
        // Create a new superadmin user
        User::create([
            'name' => 'IT Werkudara',
            'email' => 'it@werkudara.com',
            'password' => Hash::make('Werkudara@2025'),
            'role' => 'superadmin',
        ]);
        
        $this->command->info('SuperAdmin user created successfully!');
    }
}
