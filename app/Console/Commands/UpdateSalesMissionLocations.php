<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesOfficerContact;

class UpdateSalesMissionLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:update-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update location fields for Sales Mission imported contacts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating location data for Sales Mission contacts...');
        
        // Get all Sales Mission contacts
        $contacts = SalesOfficerContact::whereNotNull('sales_mission_detail_id')
            ->whereNull('city')
            ->whereNull('province')
            ->get();
            
        $this->info('Found ' . $contacts->count() . ' contacts without location data.');
        
        $updatedCount = 0;
        
        foreach ($contacts as $contact) {
            // Extract location from address if possible
            $location = ['city' => null, 'province' => null, 'country' => 'Indonesia'];
            
            if (!empty($contact->company_address)) {
                // Try to extract location data from address
                $addressParts = explode(',', $contact->company_address);
                $partsCount = count($addressParts);
                
                if ($partsCount >= 1) {
                    // Last part is usually the city/region
                    $cityPart = trim(end($addressParts));
                    
                    // Try to extract city name
                    if (strpos($cityPart, 'Jakarta') !== false) {
                        // Check for Jakarta districts
                        if (strpos($cityPart, 'Jakarta Selatan') !== false) {
                            $location['city'] = 'Jakarta Selatan';
                        } elseif (strpos($cityPart, 'Jakarta Utara') !== false) {
                            $location['city'] = 'Jakarta Utara';
                        } elseif (strpos($cityPart, 'Jakarta Barat') !== false) {
                            $location['city'] = 'Jakarta Barat';
                        } elseif (strpos($cityPart, 'Jakarta Timur') !== false) {
                            $location['city'] = 'Jakarta Timur';
                        } elseif (strpos($cityPart, 'Jakarta Pusat') !== false) {
                            $location['city'] = 'Jakarta Pusat';
                        } else {
                            $location['city'] = 'Jakarta';
                        }
                        $location['province'] = 'DKI Jakarta';
                    } elseif (strpos($cityPart, 'Bandung') !== false) {
                        $location['city'] = 'Bandung';
                        $location['province'] = 'Jawa Barat';
                    } elseif (strpos($cityPart, 'Surabaya') !== false) {
                        $location['city'] = 'Surabaya';
                        $location['province'] = 'Jawa Timur';
                    } elseif (preg_match('/(\w+)(?:\s+\w+)*$/', $cityPart, $matches)) {
                        // Extract last word from address as city if we can't match known cities
                        $location['city'] = $matches[0];
                    }
                }
            }
            
            // Update the contact with location data
            $contact->update([
                'city' => $location['city'],
                'province' => $location['province'],
                'country' => $location['country'],
            ]);
            
            $updatedCount++;
        }
        
        $this->info('Successfully updated ' . $updatedCount . ' contacts with location data.');
        
        return Command::SUCCESS;
    }
}
