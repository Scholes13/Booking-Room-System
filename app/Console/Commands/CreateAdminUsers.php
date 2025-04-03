<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin and superadmin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@werkudara.com',
            'password' => Hash::make('werkudara88'),
            'role' => 'admin',
        ]);

        $this->info('Admin user created successfully!');

        // Create superadmin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'user@werkudara.com',
            'password' => Hash::make('werkudara88'),
            'role' => 'superadmin',
        ]);

        $this->info('Superadmin user created successfully!');

        return Command::SUCCESS;
    }
} 