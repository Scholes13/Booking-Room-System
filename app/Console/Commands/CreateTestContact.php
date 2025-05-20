<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesOfficerContact;
use App\Models\User;

class CreateTestContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-contact {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test contact for a sales officer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get user ID argument or prompt for it
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            // List all sales officers
            $salesOfficers = User::where('role', 'sales_officer')->get(['id', 'name', 'email']);
            
            $this->info('Available Sales Officers:');
            foreach ($salesOfficers as $officer) {
                $this->line("ID: {$officer->id}, Name: {$officer->name}, Email: {$officer->email}");
            }
            
            $userId = $this->ask('Enter the ID of the sales officer to create a contact for:');
        }
        
        // Create a test contact
        try {
            $contact = SalesOfficerContact::create([
                'user_id' => $userId,
                'company_name' => 'Test Company ' . rand(1000, 9999),
                'line_of_business' => 'Test Business',
                'company_address' => 'Test Address, Jakarta',
                'contact_name' => 'Test Contact',
                'position' => 'Test Position',
                'phone_number' => '+62812' . rand(10000000, 99999999),
                'email' => 'test' . rand(100, 999) . '@example.com',
                'status' => 'active',
                'visit_count' => rand(1, 5)
            ]);
            
            $this->info('Test contact created successfully:');
            $this->line("ID: {$contact->id}");
            $this->line("Company: {$contact->company_name}");
            $this->line("User ID: {$contact->user_id}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create test contact: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 