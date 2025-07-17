<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration will migrate existing sales_mission_details data to the new CRM format
        
        DB::transaction(function () {
            // Get all existing sales mission details
            $salesMissionDetails = DB::table('sales_mission_details')->get();
            
            $companyMap = [];
            $contactMap = [];
            
            foreach ($salesMissionDetails as $detail) {
                // Create or find company
                $companyKey = strtolower(trim($detail->company_name));
                
                if (!isset($companyMap[$companyKey])) {
                    // Check if company already exists
                    $existingCompany = DB::table('companies')
                        ->whereRaw('LOWER(name) = ?', [$companyKey])
                        ->first();
                    
                    if ($existingCompany) {
                        $companyId = $existingCompany->id;
                    } else {
                        // Create new company
                        $companyId = DB::table('companies')->insertGetId([
                            'name' => $detail->company_name,
                            'address' => $detail->company_address,
                            'status' => 'prospect',
                            'created_at' => $detail->created_at,
                            'updated_at' => $detail->updated_at,
                        ]);
                    }
                    
                    $companyMap[$companyKey] = $companyId;
                } else {
                    $companyId = $companyMap[$companyKey];
                }
                
                // Create or find company contact
                $contactKey = $companyId . '_' . strtolower(trim($detail->company_pic));
                
                if (!isset($contactMap[$contactKey])) {
                    // Check if contact already exists for this company
                    $existingContact = DB::table('company_contacts')
                        ->where('company_id', $companyId)
                        ->whereRaw('LOWER(name) = ?', [strtolower(trim($detail->company_pic))])
                        ->first();
                    
                    if ($existingContact) {
                        $contactId = $existingContact->id;
                    } else {
                        // Create new contact
                        $contactId = DB::table('company_contacts')->insertGetId([
                            'company_id' => $companyId,
                            'name' => $detail->company_pic,
                            'position' => $detail->company_position ?? null,
                            'phone' => $detail->company_contact,
                            'email' => $detail->company_email ?? null,
                            'is_primary' => true, // First contact is primary
                            'status' => 'active',
                            'created_at' => $detail->created_at,
                            'updated_at' => $detail->updated_at,
                        ]);
                    }
                    
                    $contactMap[$contactKey] = $contactId;
                } else {
                    $contactId = $contactMap[$contactKey];
                }
                
                // Update sales_mission_details with new foreign keys
                DB::table('sales_mission_details')
                    ->where('id', $detail->id)
                    ->update([
                        'company_id' => $companyId,
                        'company_contact_id' => $contactId,
                    ]);
            }
            
            // Now calculate visit sequences and types
            $this->calculateVisitSequences();
        });
    }
    
    /**
     * Calculate visit sequences and determine visit types
     */
    private function calculateVisitSequences(): void
    {
        // Get all sales mission details grouped by company, ordered by creation date
        $companiesWithVisits = DB::table('sales_mission_details')
            ->select('company_id')
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->get();
        
        foreach ($companiesWithVisits as $company) {
            $visits = DB::table('sales_mission_details')
                ->where('company_id', $company->company_id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $sequence = 1;
            foreach ($visits as $visit) {
                $visitType = $sequence === 1 ? 'initial' : 'follow_up';
                
                DB::table('sales_mission_details')
                    ->where('id', $visit->id)
                    ->update([
                        'visit_sequence' => $sequence,
                        'visit_type' => $visitType,
                    ]);
                
                $sequence++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset the foreign key columns to null
        DB::table('sales_mission_details')->update([
            'company_id' => null,
            'company_contact_id' => null,
            'visit_type' => 'initial',
            'visit_sequence' => 1,
        ]);
        
        // Note: We don't delete companies and contacts as they might be used elsewhere
        // If you want to clean up, you can manually delete them
    }
};