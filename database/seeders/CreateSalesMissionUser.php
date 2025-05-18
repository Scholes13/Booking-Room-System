<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSalesMissionUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sales Mission User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role' => 'sales_mission',
        ]);
        
        $this->command->info('Sales Mission User created successfully!');
    }
} 