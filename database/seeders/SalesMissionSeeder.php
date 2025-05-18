<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Department;
use App\Models\SalesMissionDetail;
use Carbon\Carbon;

class SalesMissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a department
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'name' => 'Sales Department',
                'code' => 'SALES'
            ]);
        }
        
        // Create a few sales missions for testing
        $salesMissions = [
            [
                'name' => 'John Doe',
                'department_id' => $department->id,
                'activity_type' => 'Sales Mission',
                'description' => 'Meeting with PT ABC for product presentation',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'start_datetime' => Carbon::now()->subDays(5),
                'end_datetime' => Carbon::now()->subDays(5)->addHours(2),
                'company' => [
                    'company_name' => 'PT ABC Technology',
                    'company_pic' => 'Michael Johnson',
                    'company_contact' => '081234567890',
                    'company_address' => 'Jl. Sudirman No. 123, Jakarta Pusat'
                ]
            ],
            [
                'name' => 'Jane Smith',
                'department_id' => $department->id,
                'activity_type' => 'Sales Mission',
                'description' => 'Product demo for PT XYZ Corp',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'start_datetime' => Carbon::now()->subDays(10),
                'end_datetime' => Carbon::now()->subDays(10)->addHours(3),
                'company' => [
                    'company_name' => 'PT XYZ Corporation',
                    'company_pic' => 'Sarah Williams',
                    'company_contact' => '082345678901',
                    'company_address' => 'Jl. Basuki Rahmat No. 45, Surabaya'
                ]
            ],
            [
                'name' => 'Robert Johnson',
                'department_id' => $department->id,
                'activity_type' => 'Sales Mission',
                'description' => 'Contract negotiation with PT DEF Group',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'start_datetime' => Carbon::now()->subDays(2),
                'end_datetime' => Carbon::now()->subDays(2)->addHours(4),
                'company' => [
                    'company_name' => 'PT DEF Group',
                    'company_pic' => 'David Anderson',
                    'company_contact' => '083456789012',
                    'company_address' => 'Jl. Asia Afrika No. 88, Bandung'
                ]
            ],
        ];
        
        foreach ($salesMissions as $data) {
            $companyData = $data['company'];
            unset($data['company']);
            
            // Create the activity
            $activity = Activity::create($data);
            
            // Create the sales mission detail
            SalesMissionDetail::create([
                'activity_id' => $activity->id,
                'company_name' => $companyData['company_name'],
                'company_pic' => $companyData['company_pic'],
                'company_contact' => $companyData['company_contact'],
                'company_address' => $companyData['company_address']
            ]);
        }
    }
} 