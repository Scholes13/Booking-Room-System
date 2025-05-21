<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactPerson;
use App\Models\SalesOfficerActivity;
use Illuminate\Support\Facades\DB;

class UpdateContactSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating contact sources...');

        // Get all PICs associated with activities
        $activityPics = SalesOfficerActivity::whereNotNull('pic_id')
            ->pluck('pic_id')
            ->unique()
            ->toArray();
            
        // Update the source for these PICs to 'Activity'
        if (count($activityPics) > 0) {
            ContactPerson::whereIn('id', $activityPics)
                ->update(['source' => 'Activity']);
            
            $this->command->info(count($activityPics) . ' contacts updated to "Activity" source.');
        } else {
            $this->command->info('No activity-related contacts found.');
        }
        
        // Update contacts from imported sources
        $importedCount = DB::table('contact_people')
            ->join('sales_officer_contacts', 'contact_people.contact_id', '=', 'sales_officer_contacts.id')
            ->whereNotNull('sales_officer_contacts.sales_mission_detail_id')
            ->update(['contact_people.source' => 'Imported']);
            
        $this->command->info($importedCount . ' contacts updated to "Imported" source.');
        
        $this->command->info('Contact source update completed.');
    }
}
