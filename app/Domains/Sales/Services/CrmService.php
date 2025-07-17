<?php

namespace App\Domains\Sales\Services;

use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\SalesMissionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CrmService
{
    /**
     * Find or create a company based on name and address
     */
    public function findOrCreateCompany(array $companyData): Company
    {
        $name = trim($companyData['name']);
        $address = trim($companyData['address'] ?? '');
        
        // First, try to find exact match
        $company = Company::where('name', $name)->first();
        
        if (!$company) {
            // Try fuzzy matching
            $company = $this->findSimilarCompany($name, $address);
        }
        
        if (!$company) {
            // Create new company
            $company = Company::create([
                'name' => $name,
                'address' => $address,
                'city' => $companyData['city'] ?? null,
                'province' => $companyData['province'] ?? null,
                'industry' => $companyData['industry'] ?? null,
                'company_size' => $companyData['company_size'] ?? null,
                'status' => 'prospect',
                'website' => $companyData['website'] ?? null,
                'description' => $companyData['description'] ?? null,
            ]);
        }
        
        return $company;
    }
    
    /**
     * Find or create a company contact
     */
    public function findOrCreateCompanyContact(Company $company, array $contactData): CompanyContact
    {
        $name = trim($contactData['name']);
        
        // Try to find existing contact for this company
        $contact = CompanyContact::where('company_id', $company->id)
                                ->where('name', $name)
                                ->first();
        
        if (!$contact) {
            // Check if this is the first contact for the company
            $isFirstContact = $company->contacts()->count() === 0;
            
            $contact = CompanyContact::create([
                'company_id' => $company->id,
                'name' => $name,
                'position' => $contactData['position'] ?? null,
                'department' => $contactData['department'] ?? null,
                'phone' => $contactData['phone'] ?? null,
                'email' => $contactData['email'] ?? null,
                'is_primary' => $isFirstContact, // First contact becomes primary
                'notes' => $contactData['notes'] ?? null,
                'status' => 'active',
            ]);
        }
        
        return $contact;
    }
    
    /**
     * Create a sales mission detail with proper CRM relationships
     */
    public function createSalesMissionDetail(int $activityId, array $companyData, array $contactData): SalesMissionDetail
    {
        return DB::transaction(function () use ($activityId, $companyData, $contactData) {
            // Find or create company
            $company = $this->findOrCreateCompany($companyData);
            
            // Find or create contact
            $contact = $this->findOrCreateCompanyContact($company, $contactData);
            
            // Determine visit type and sequence
            $visitSequence = $this->getNextVisitSequence($company->id);
            $visitType = $visitSequence === 1 ? 'initial' : 'follow_up';
            
            // Create sales mission detail
            $salesMissionDetail = SalesMissionDetail::create([
                'activity_id' => $activityId,
                'company_id' => $company->id,
                'company_contact_id' => $contact->id,
                'visit_type' => $visitType,
                'visit_sequence' => $visitSequence,
                // Keep legacy fields for backward compatibility
                'company_name' => $company->name,
                'company_pic' => $contact->name,
                'company_position' => $contact->position,
                'company_contact' => $contact->phone,
                'company_email' => $contact->email,
                'company_address' => $company->address,
            ]);
            
            return $salesMissionDetail;
        });
    }
    
    /**
     * Get company visit history with analytics
     */
    public function getCompanyVisitHistory(int $companyId): array
    {
        $company = Company::with(['salesMissionDetails.activity', 'contacts'])->find($companyId);
        
        if (!$company) {
            return [];
        }
        
        $visits = $company->salesMissionDetails()
                         ->with(['activity', 'companyContact'])
                         ->orderBy('created_at', 'desc')
                         ->get();
        
        return [
            'company' => $company,
            'total_visits' => $visits->count(),
            'initial_visits' => $visits->where('visit_type', 'initial')->count(),
            'follow_up_visits' => $visits->where('visit_type', 'follow_up')->count(),
            'first_visit_date' => $visits->min('created_at'),
            'last_visit_date' => $visits->max('created_at'),
            'visits' => $visits->toArray(),
            'contacts' => $company->contacts->toArray(),
        ];
    }
    
    /**
     * Get CRM dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'total_companies' => Company::count(),
            'new_prospects' => Company::newProspects()->count(),
            'active_companies' => Company::byStatus('active')->count(),
            'companies_with_multiple_visits' => Company::withMultipleVisits()->count(),
            'total_visits' => SalesMissionDetail::count(),
            'initial_visits' => SalesMissionDetail::initialVisits()->count(),
            'follow_up_visits' => SalesMissionDetail::followUpVisits()->count(),
            'total_contacts' => CompanyContact::count(),
            'recent_companies' => Company::latest()->limit(5)->get(),
            'top_visited_companies' => $this->getTopVisitedCompanies(5),
        ];
    }
    
    /**
     * Search companies with fuzzy matching
     */
    public function searchCompanies(string $query, int $limit = 10): Collection
    {
        return Company::where('name', 'LIKE', "%{$query}%")
                     ->orWhere('address', 'LIKE', "%{$query}%")
                     ->orWhere('city', 'LIKE', "%{$query}%")
                     ->with(['primaryContact', 'salesMissionDetails'])
                     ->limit($limit)
                     ->get();
    }
    
    /**
     * Suggest visit type based on company history
     */
    public function suggestVisitType(string $companyName): array
    {
        $suggestions = $this->findSimilarCompanies($companyName);
        
        $result = [
            'suggested_type' => 'initial',
            'confidence' => 'low',
            'similar_companies' => [],
            'message' => 'This appears to be a new company visit.',
        ];
        
        if ($suggestions->isNotEmpty()) {
            $exactMatch = $suggestions->where('similarity_score', '>=', 0.9)->first();
            
            if ($exactMatch) {
                $result['suggested_type'] = 'follow_up';
                $result['confidence'] = 'high';
                $result['similar_companies'] = [$exactMatch];
                $result['message'] = "Found existing company: {$exactMatch['name']}. This should be a follow-up visit.";
            } else {
                $result['confidence'] = 'medium';
                $result['similar_companies'] = $suggestions->take(3)->toArray();
                $result['message'] = 'Found similar companies. Please verify if this is a new company or follow-up visit.';
            }
        }
        
        return $result;
    }
    
    /**
     * Find similar company using fuzzy matching
     */
    private function findSimilarCompany(string $name, string $address = ''): ?Company
    {
        $companies = Company::all();
        $bestMatch = null;
        $bestScore = 0;
        
        foreach ($companies as $company) {
            $nameScore = $this->calculateSimilarity($name, $company->name);
            $addressScore = $address && $company->address ? 
                          $this->calculateSimilarity($address, $company->address) : 0;
            
            $totalScore = ($nameScore * 0.8) + ($addressScore * 0.2);
            
            if ($totalScore > $bestScore && $totalScore >= 0.8) {
                $bestScore = $totalScore;
                $bestMatch = $company;
            }
        }
        
        return $bestMatch;
    }
    
    /**
     * Find similar companies for suggestion
     */
    private function findSimilarCompanies(string $name): Collection
    {
        $companies = Company::with(['primaryContact', 'salesMissionDetails'])->get();
        $similarities = collect();
        
        foreach ($companies as $company) {
            $score = $this->calculateSimilarity($name, $company->name);
            
            if ($score >= 0.6) {
                $similarities->push([
                    'id' => $company->id,
                    'name' => $company->name,
                    'address' => $company->address,
                    'total_visits' => $company->total_visits,
                    'last_visit' => $company->latest_visit?->created_at,
                    'similarity_score' => $score,
                ]);
            }
        }
        
        return $similarities->sortByDesc('similarity_score');
    }
    
    /**
     * Calculate string similarity using Levenshtein distance
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));
        
        if ($str1 === $str2) {
            return 1.0;
        }
        
        $maxLen = max(strlen($str1), strlen($str2));
        if ($maxLen === 0) {
            return 0.0;
        }
        
        $distance = levenshtein($str1, $str2);
        return 1 - ($distance / $maxLen);
    }
    
    /**
     * Get next visit sequence number for a company
     */
    private function getNextVisitSequence(int $companyId): int
    {
        $lastSequence = SalesMissionDetail::where('company_id', $companyId)
                                         ->max('visit_sequence');
        
        return ($lastSequence ?? 0) + 1;
    }
    
    /**
     * Get top visited companies
     */
    private function getTopVisitedCompanies(int $limit): Collection
    {
        return Company::withCount('salesMissionDetails')
                     ->orderBy('sales_mission_details_count', 'desc')
                     ->limit($limit)
                     ->get();
    }
}