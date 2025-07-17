<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\SalesMissionDetail;
use App\Domains\Sales\Services\CrmService;
use Illuminate\Http\Request;

class CrmTestController extends Controller
{
    private $crmService;

    public function __construct(CrmService $crmService)
    {
        $this->crmService = $crmService;
    }

    public function testCrmSystem()
    {
        $results = [];
        
        try {
            // Test 1: Check Companies Table
            $companies = Company::with(['contacts', 'salesMissionDetails'])->get();
            $results['companies'] = [
                'total' => $companies->count(),
                'data' => $companies->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'status' => $company->status,
                        'contacts_count' => $company->contacts->count(),
                        'total_visits' => $company->salesMissionDetails->count(),
                        'initial_visits' => $company->salesMissionDetails->where('visit_type', 'initial')->count(),
                        'follow_up_visits' => $company->salesMissionDetails->where('visit_type', 'follow_up')->count(),
                    ];
                })->toArray()
            ];

            // Test 2: Check Company Contacts
            $contacts = CompanyContact::with('company')->get();
            $results['contacts'] = [
                'total' => $contacts->count(),
                'data' => $contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'position' => $contact->position,
                        'company_name' => $contact->company->name,
                        'is_primary' => $contact->is_primary,
                        'phone' => $contact->phone,
                        'email' => $contact->email,
                    ];
                })->toArray()
            ];

            // Test 3: Check Sales Mission Details
            $salesMissions = SalesMissionDetail::with(['company', 'companyContact', 'activity'])
                                              ->limit(10)
                                              ->get();
            $results['sales_missions'] = [
                'total' => SalesMissionDetail::count(),
                'sample_data' => $salesMissions->map(function ($mission) {
                    return [
                        'id' => $mission->id,
                        'activity_name' => $mission->activity->name,
                        'company_id' => $mission->company_id,
                        'company_name' => $mission->company->name,
                        'contact_id' => $mission->company_contact_id,
                        'contact_name' => $mission->companyContact->name,
                        'visit_type' => $mission->visit_type,
                        'visit_sequence' => $mission->visit_sequence,
                        'created_at' => $mission->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
            ];

            // Test 4: CRM Service Dashboard Stats
            $results['dashboard_stats'] = $this->crmService->getDashboardStats();

            // Test 5: Test Company Search
            $searchResults = $this->crmService->searchCompanies('PT', 5);
            $results['search_test'] = [
                'query' => 'PT',
                'results_count' => $searchResults->count(),
                'results' => $searchResults->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'total_visits' => $company->total_visits,
                    ];
                })->toArray()
            ];

            // Test 6: Visit Type Suggestions
            $visitSuggestion = $this->crmService->suggestVisitType('PT Test Company');
            $results['visit_suggestion_test'] = $visitSuggestion;

            $results['status'] = 'success';
            $results['message'] = 'Mini CRM system is working properly!';

        } catch (\Exception $e) {
            $results['status'] = 'error';
            $results['message'] = 'Error testing CRM system: ' . $e->getMessage();
            $results['trace'] = $e->getTraceAsString();
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }

    public function testCreateSalesMission(Request $request)
    {
        try {
            // Test creating a new sales mission with CRM integration
            $companyData = [
                'name' => $request->get('company_name', 'PT Test Company'),
                'address' => $request->get('company_address', 'Jl. Test No. 123'),
                'city' => $request->get('city', 'Jakarta'),
                'province' => $request->get('province', 'DKI Jakarta'),
            ];

            $contactData = [
                'name' => $request->get('contact_name', 'John Doe'),
                'position' => $request->get('position', 'Manager'),
                'phone' => $request->get('phone', '08123456789'),
                'email' => $request->get('email', 'john@testcompany.com'),
            ];

            // Get first activity for testing
            $activity = \App\Models\Activity::first();
            if (!$activity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No activities found for testing'
                ]);
            }

            $salesMissionDetail = $this->crmService->createSalesMissionDetail(
                $activity->id,
                $companyData,
                $contactData
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Sales mission created successfully with CRM integration',
                'data' => [
                    'sales_mission_id' => $salesMissionDetail->id,
                    'company_id' => $salesMissionDetail->company_id,
                    'company_name' => $salesMissionDetail->company->name,
                    'contact_id' => $salesMissionDetail->company_contact_id,
                    'contact_name' => $salesMissionDetail->companyContact->name,
                    'visit_type' => $salesMissionDetail->visit_type,
                    'visit_sequence' => $salesMissionDetail->visit_sequence,
                ]
            ], 200, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating sales mission: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}