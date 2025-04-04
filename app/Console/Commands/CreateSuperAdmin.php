<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\SuperAdminSeeder;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a SuperAdmin user with email it@werkudara.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating SuperAdmin user...');
        
        $seeder = new SuperAdminSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('SuperAdmin user created successfully!');
        $this->info('Email: it@werkudara.com');
        $this->info('Password: Werkudara@2025');
        
        return Command::SUCCESS;
    }
} 