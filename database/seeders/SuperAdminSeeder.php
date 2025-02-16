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
    public function run()
    {
        User::create([
            'name'     => 'AdminWG',
            'email'    => 'admin@werkudara.com',
            'password' => 'werkudara88',
            'role'     => 'superadmin',
        ]);
    }
}
