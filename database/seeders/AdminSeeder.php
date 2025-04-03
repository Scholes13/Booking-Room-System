<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@werkudara.com',
            'password' => Hash::make('werkudara88'),
            'role' => 'admin',
        ]);

        // Create Super Admin User
        User::create([
            'name' => 'Super Admin',
            'email' => 'user@werkudara.com',
            'password' => Hash::make('werkudara88'),
            'role' => 'superadmin',
        ]);
    }
} 