<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesOfficerContact;
use Illuminate\Support\Facades\DB;

class UpdateAllSalesMissionLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:update-all-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all Sales Mission contacts with location data from Activity table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating all Sales Mission contacts with location data from Activity table...');
        
        // Get all Sales Mission contacts with their related detail and activity
        $contacts = SalesOfficerContact::whereNotNull('sales_mission_detail_id')
            ->get();
            
        $this->info('Found ' . $contacts->count() . ' Sales Mission contacts.');
        
        $updatedCount = 0;
        
        foreach ($contacts as $contact) {
            // Get the related sales mission detail and its activity
            $detail = DB::table('sales_mission_details')
                ->where('id', $contact->sales_mission_detail_id)
                ->first();
                
            if (!$detail) {
                continue;
            }
            
            $activity = DB::table('activities')
                ->where('id', $detail->activity_id)
                ->first();
                
            if (!$activity) {
                continue;
            }
            
            // Update contact with location data from activity
            $city = $activity->city;
            $province = $activity->province;
            
            // Only update if we have location data from activity
            if ($city || $province) {
                $contact->update([
                    'city' => $city,
                    'province' => $province,
                    'country' => 'Indonesia', // Default country
                ]);
                
                $updatedCount++;
                $this->info("Updated contact ID {$contact->id}: {$contact->company_name} with location {$city}, {$province}");
            }
        }
        
        $this->info('Successfully updated ' . $updatedCount . ' contacts with location data from activities.');
        
        return Command::SUCCESS;
    }
} 