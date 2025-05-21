<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesOfficerContact;

class UpdateJakartaLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:update-jakarta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Jakarta contacts to use specific district names';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating Jakarta location data for contacts...');
        
        // Get all contacts with Jakarta as city
        $contacts = SalesOfficerContact::where('city', 'Jakarta')
            ->orWhere('city', 'like', '%Jakarta%')
            ->get();
            
        $this->info('Found ' . $contacts->count() . ' Jakarta contacts.');
        
        $updatedCount = 0;
        
        foreach ($contacts as $contact) {
            $newCity = 'Jakarta'; // Default 
            
            if (!empty($contact->company_address)) {
                if (stripos($contact->company_address, 'Jakarta Selatan') !== false) {
                    $newCity = 'Jakarta Selatan';
                } elseif (stripos($contact->company_address, 'Jakarta Utara') !== false) {
                    $newCity = 'Jakarta Utara';
                } elseif (stripos($contact->company_address, 'Jakarta Barat') !== false) {
                    $newCity = 'Jakarta Barat';
                } elseif (stripos($contact->company_address, 'Jakarta Timur') !== false) {
                    $newCity = 'Jakarta Timur';
                } elseif (stripos($contact->company_address, 'Jakarta Pusat') !== false) {
                    $newCity = 'Jakarta Pusat';
                }
            }
            
            // Only update if we have a more specific district
            if ($newCity != 'Jakarta' || $contact->city != $newCity) {
                $contact->update([
                    'city' => $newCity,
                    'province' => 'DKI Jakarta',
                    'country' => 'Indonesia',
                ]);
                
                $updatedCount++;
            }
        }
        
        $this->info('Successfully updated ' . $updatedCount . ' Jakarta contacts with specific district names.');
        
        return Command::SUCCESS;
    }
}
