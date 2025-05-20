<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSalesOfficerUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:sales-officer-user {name?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Sales Officer user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name') ?? $this->ask('Enter user name');
        $email = $this->argument('email') ?? $this->ask('Enter user email');
        $password = $this->argument('password') ?? $this->secret('Enter user password');

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->error("User with email '{$email}' already exists!");
            return 1;
        }

        // Create the Sales Officer user
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'sales_officer',
        ]);

        $this->info("Sales Officer user '{$name}' created successfully!");
        return 0;
    }
} 