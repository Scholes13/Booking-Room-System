<?php

namespace App\Domains\Activity\Services;

use App\Shared\Services\BaseService;
use App\Shared\Enums\UserRole;
use App\Models\Activity;
use App\Models\SalesMissionDetail;
use App\Domains\Sales\Services\CrmService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActivityService extends BaseService
{
    private $crmService;

    public function __construct(CrmService $crmService)
    {
        $this->crmService = $crmService;
    }

    /**
     * Create a new activity with CRM integration for Sales Mission
     *
     * @param array $data
     * @return Activity
     */
    public function create(array $data): Activity
    {
        $this->validatePermission(UserRole::getActivityManagerRoles());
        
        $activity = Activity::create($data);
        
        // If this is a Sales Mission activity, create CRM integration
        if ($this->isSalesMissionActivity($data)) {
            $this->createSalesMissionWithCrm($activity, $data);
        }
        
        $this->logActivity(
            'create',
            'activities',
            "Created activity: {$activity->name}",
            ['activity_id' => $activity->id]
        );
        
        return $activity;
    }
    
    /**
     * Update existing activity
     *
     * @param Activity $activity
     * @param array $data
     * @return Activity
     */
    public function update(Activity $activity, array $data): Activity
    {
        $this->validatePermission(UserRole::getActivityManagerRoles());
        
        $oldData = $activity->toArray();
        $activity->update($data);
        
        $this->logActivity(
            'update', 
            'activities', 
            "Updated activity: {$activity->name}",
            ['activity_id' => $activity->id, 'old_data' => $oldData, 'new_data' => $data]
        );
        
        return $activity;
    }
    
    /**
     * Delete activity
     *
     * @param Activity $activity
     * @return bool
     */
    public function delete(Activity $activity): bool
    {
        $this->validatePermission(UserRole::getActivityManagerRoles());
        
        $activityData = $activity->toArray();
        $result = $activity->delete();
        
        if ($result) {
            $this->logActivity(
                'delete', 
                'activities', 
                "Deleted activity: {$activityData['name']}",
                ['deleted_activity' => $activityData]
            );
        }
        
        return $result;
    }
    
    /**
     * Get filtered activities based on request parameters
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredActivities(array $filters = [])
    {
        $query = Activity::query();
        
        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->where('date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('date', '<=', $filters['end_date']);
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Search by name or description
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Get activity statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_activities' => Activity::count(),
            'today_activities' => Activity::whereDate('date', $today)->count(),
            'this_month_activities' => Activity::where('date', '>=', $thisMonth)->count(),
            'pending_activities' => Activity::where('status', 'pending')->count(),
            'completed_activities' => Activity::where('status', 'completed')->count(),
            'activities_by_type' => $this->getActivitiesByType(),
            'upcoming_activities' => $this->getUpcomingActivities(),
            'recent_activities' => $this->getRecentActivities()
        ];
    }
    
    /**
     * Get activities grouped by type
     *
     * @return array
     */
    private function getActivitiesByType(): array
    {
        return Activity::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }
    
    /**
     * Get upcoming activities
     *
     * @param int $limit
     * @return Collection
     */
    private function getUpcomingActivities(int $limit = 5): Collection
    {
        return Activity::where('date', '>=', Carbon::today())
            ->where('status', '!=', 'completed')
            ->orderBy('date')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get recent activities
     *
     * @param int $limit
     * @return Collection
     */
    private function getRecentActivities(int $limit = 10): Collection
    {
        return Activity::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Update activity status
     *
     * @param Activity $activity
     * @param string $status
     * @return Activity
     */
    public function updateStatus(Activity $activity, string $status): Activity
    {
        $this->validatePermission(UserRole::getActivityManagerRoles());
        
        $oldStatus = $activity->status;
        $activity->update(['status' => $status]);
        
        $this->logActivity(
            'status_update', 
            'activities', 
            "Changed activity status from {$oldStatus} to {$status}: {$activity->name}",
            ['activity_id' => $activity->id, 'old_status' => $oldStatus, 'new_status' => $status]
        );
        
        return $activity;
    }
    
    /**
     * Get activities for a specific date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getActivitiesInDateRange(string $startDate, string $endDate): Collection
    {
        return Activity::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('created_at')
            ->get();
    }
    
    /**
     * Get activities by status
     *
     * @param string $status
     * @return Collection
     */
    public function getActivitiesByStatus(string $status): Collection
    {
        return Activity::where('status', $status)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Public method to log activity (accessible from controllers)
     *
     * @param string $action
     * @param string $module
     * @param string $description
     * @param array|null $properties
     * @return mixed
     */
    public function logActivity($action, $module, $description, $properties = null)
    {
        return parent::logActivity($action, $module, $description, $properties);
    }

    /**
     * Check if activity is a Sales Mission type
     *
     * @param array $data
     * @return bool
     */
    private function isSalesMissionActivity(array $data): bool
    {
        return isset($data['activity_type']) &&
               strtolower($data['activity_type']) === 'sales mission' &&
               isset($data['sales_mission_data']);
    }

    /**
     * Create Sales Mission Detail with CRM integration
     *
     * @param Activity $activity
     * @param array $data
     * @return SalesMissionDetail|null
     */
    private function createSalesMissionWithCrm(Activity $activity, array $data): ?SalesMissionDetail
    {
        if (!isset($data['sales_mission_data'])) {
            return null;
        }

        $salesData = $data['sales_mission_data'];

        // Prepare company data
        $companyData = [
            'name' => $salesData['company_name'],
            'address' => $salesData['company_address'] ?? '',
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
        ];

        // Prepare contact data
        $contactData = [
            'name' => $salesData['company_pic'],
            'position' => $salesData['company_position'] ?? null,
            'phone' => $salesData['company_contact'] ?? null,
            'email' => $salesData['company_email'] ?? null,
        ];

        try {
            // Use CRM Service to create sales mission with proper company/contact relationships
            $salesMissionDetail = $this->crmService->createSalesMissionDetail(
                $activity->id,
                $companyData,
                $contactData
            );

            $this->logActivity(
                'crm_integration',
                'sales_mission',
                "Created Sales Mission with CRM integration for company: {$companyData['name']}",
                [
                    'activity_id' => $activity->id,
                    'company_name' => $companyData['name'],
                    'visit_type' => $salesMissionDetail->visit_type,
                    'visit_sequence' => $salesMissionDetail->visit_sequence
                ]
            );

            return $salesMissionDetail;

        } catch (\Exception $e) {
            // Fallback to old method if CRM integration fails
            $this->logActivity(
                'crm_fallback',
                'sales_mission',
                "CRM integration failed, using fallback method: " . $e->getMessage(),
                ['activity_id' => $activity->id, 'error' => $e->getMessage()]
            );

            return $this->createSalesMissionFallback($activity, $salesData);
        }
    }

    /**
     * Fallback method to create sales mission detail without CRM
     *
     * @param Activity $activity
     * @param array $salesData
     * @return SalesMissionDetail
     */
    private function createSalesMissionFallback(Activity $activity, array $salesData): SalesMissionDetail
    {
        return SalesMissionDetail::create([
            'activity_id' => $activity->id,
            'company_name' => $salesData['company_name'],
            'company_pic' => $salesData['company_pic'],
            'company_position' => $salesData['company_position'] ?? null,
            'company_contact' => $salesData['company_contact'] ?? null,
            'company_email' => $salesData['company_email'] ?? null,
            'company_address' => $salesData['company_address'] ?? '',
            'visit_type' => 'initial', // Default fallback
            'visit_sequence' => 1, // Default fallback
        ]);
    }

    /**
     * Get company suggestions for Sales Mission form
     *
     * @param string $query
     * @return array
     */
    public function getCompanySuggestions(string $query): array
    {
        return $this->crmService->suggestVisitType($query);
    }

    /**
     * Search companies for autocomplete
     *
     * @param string $query
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function searchCompanies(string $query, int $limit = 10): Collection
    {
        return $this->crmService->searchCompanies($query, $limit);
    }

    /**
     * Get company visit history
     *
     * @param int $companyId
     * @return array
     */
    public function getCompanyVisitHistory(int $companyId): array
    {
        return $this->crmService->getCompanyVisitHistory($companyId);
    }
}