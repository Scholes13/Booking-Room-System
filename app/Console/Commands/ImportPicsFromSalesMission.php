<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesOfficerContact;
use App\Models\ContactPerson;
use Illuminate\Support\Facades\DB;

class ImportPicsFromSalesMission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:import-pics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import PICs (Contact People) from existing Sales Mission contacts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing PICs from Sales Mission contacts...');
        
        // Get all Sales Mission contacts that have been imported
        $contacts = SalesOfficerContact::whereNotNull('sales_mission_detail_id')
            ->get();
            
        $this->info('Found ' . $contacts->count() . ' Sales Mission contacts.');
        
        $importedCount = 0;
        $skippedCount = 0;
        
        foreach ($contacts as $contact) {
            // Check if this contact already has any PICs
            $existingPicCount = ContactPerson::where('contact_id', $contact->id)->count();
            
            if ($existingPicCount > 0) {
                $this->line("Skipping {$contact->company_name} - already has {$existingPicCount} PICs");
                $skippedCount++;
                continue; // Skip if there are already PICs for this contact
            }
            
            // Get the related sales mission detail
            $detail = DB::table('sales_mission_details')
                ->where('id', $contact->sales_mission_detail_id)
                ->first();
                
            if (!$detail || empty($detail->company_pic)) {
                $this->line("Skipping {$contact->company_name} - no PIC information available");
                $skippedCount++;
                continue; // Skip if there's no PIC information
            }
            
            // Determine title based on name (simple logic)
            $title = 'Mr'; // Default
            $picName = $detail->company_pic;
            
            // Check for common female titles/prefixes in the name
            $femalePrefixes = ['ibu', 'bu', 'mrs', 'ms', 'miss', 'ny', 'nyonya'];
            $lowercaseName = strtolower($picName);
            foreach ($femalePrefixes as $prefix) {
                if (strpos($lowercaseName, $prefix) === 0) {
                    $title = 'Mrs';
                    // Remove the prefix from the name
                    $picName = trim(substr($picName, strlen($prefix)));
                    break;
                }
            }
            
            try {
                // Create the contact person
                ContactPerson::create([
                    'contact_id' => $contact->id,
                    'division_id' => null, // No division initially
                    'title' => $title,
                    'name' => $picName,
                    'position' => $detail->company_position,
                    'phone_number' => $detail->company_contact,
                    'email' => $detail->company_email,
                    'is_primary' => true, // Set as primary contact
                    'source' => 'Imported', // Set source to identify it was imported
                ]);
                
                $importedCount++;
                $this->info("Created PIC for {$contact->company_name}: {$picName}");
            } catch (\Exception $e) {
                $this->error("Failed to create PIC for {$contact->company_name}: {$e->getMessage()}");
            }
        }
        
        $this->info("Summary: Created {$importedCount} PICs, skipped {$skippedCount} contacts.");
        
        return Command::SUCCESS;
    }
} 