<?php

namespace App\Domains\Sales\Services;

use App\Shared\Services\BaseService;
use App\Shared\Enums\UserRole;
use App\Models\SalesOfficer;
use App\Models\SalesMission;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesOfficerService extends BaseService
{
    /**
     * Create a new sales officer
     *
     * @param array $data
     * @return SalesOfficer
     */
    public function create(array $data): SalesOfficer
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $officer = SalesOfficer::create($data);
        
        $this->logActivity(
            'create', 
            'sales_officers', 
            "Created sales officer: {$officer->name}",
            ['officer_id' => $officer->id]
        );
        
        return $officer;
    }
    
    /**
     * Update existing sales officer
     *
     * @param SalesOfficer $officer
     * @param array $data
     * @return SalesOfficer
     */
    public function update(SalesOfficer $officer, array $data): SalesOfficer
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $oldData = $officer->toArray();
        $officer->update($data);
        
        $this->logActivity(
            'update', 
            'sales_officers', 
            "Updated sales officer: {$officer->name}",
            ['officer_id' => $officer->id, 'old_data' => $oldData, 'new_data' => $data]
        );
        
        return $officer;
    }
    
    /**
     * Delete sales officer
     *
     * @param SalesOfficer $officer
     * @return bool
     */
    public function delete(SalesOfficer $officer): bool
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $officerData = $officer->toArray();
        $result = $officer->delete();
        
        if ($result) {
            $this->logActivity(
                'delete', 
                'sales_officers', 
                "Deleted sales officer: {$officerData['name']}",
                ['deleted_officer' => $officerData]
            );
        }
        
        return $result;
    }
    
    /**
     * Get filtered sales officers based on request parameters
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredOfficers(array $filters = [])
    {
        $query = SalesOfficer::with(['user', 'missions']);
        
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by department
        if (!empty($filters['department'])) {
            $query->where('department', 'like', '%' . $filters['department'] . '%');
        }
        
        // Filter by region
        if (!empty($filters['region'])) {
            $query->where('region', 'like', '%' . $filters['region'] . '%');
        }
        
        // Search by name, email, or phone
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('name')
                    ->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Get sales officer statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_officers' => SalesOfficer::count(),
            'active_officers' => SalesOfficer::where('status', 'active')->count(),
            'inactive_officers' => SalesOfficer::where('status', 'inactive')->count(),
            'officers_by_region' => $this->getOfficersGroupedByRegion(),
            'officers_by_department' => $this->getOfficersByDepartment(),
            'top_performers' => $this->getTopPerformers(),
            'performance_metrics' => $this->getPerformanceMetrics()
        ];
    }
    
    /**
     * Get officers grouped by region
     *
     * @return array
     */
    private function getOfficersGroupedByRegion(): array
    {
        return SalesOfficer::selectRaw('region, COUNT(*) as count')
            ->groupBy('region')
            ->pluck('count', 'region')
            ->toArray();
    }
    
    /**
     * Get officers grouped by department
     *
     * @return array
     */
    private function getOfficersByDepartment(): array
    {
        return SalesOfficer::selectRaw('department, COUNT(*) as count')
            ->groupBy('department')
            ->pluck('count', 'department')
            ->toArray();
    }
    
    /**
     * Get top performing officers
     *
     * @param int $limit
     * @return Collection
     */
    private function getTopPerformers(int $limit = 5): Collection
    {
        return SalesOfficer::withCount(['missions' => function ($query) {
            $query->where('status', 'completed');
        }])
        ->orderBy('missions_count', 'desc')
        ->limit($limit)
        ->get();
    }
    
    /**
     * Get performance metrics for all officers
     *
     * @return array
     */
    private function getPerformanceMetrics(): array
    {
        $officers = SalesOfficer::withCount([
            'missions',
            'missions as completed_missions_count' => function ($query) {
                $query->where('status', 'completed');
            },
            'missions as active_missions_count' => function ($query) {
                $query->where('status', 'active');
            }
        ])->get();
        
        $totalMissions = $officers->sum('missions_count');
        $totalCompleted = $officers->sum('completed_missions_count');
        $totalActive = $officers->sum('active_missions_count');
        
        return [
            'total_missions_assigned' => $totalMissions,
            'total_missions_completed' => $totalCompleted,
            'total_missions_active' => $totalActive,
            'overall_completion_rate' => $totalMissions > 0 ? round(($totalCompleted / $totalMissions) * 100, 2) : 0,
            'average_missions_per_officer' => $officers->count() > 0 ? round($totalMissions / $officers->count(), 2) : 0
        ];
    }
    
    /**
     * Update officer status
     *
     * @param SalesOfficer $officer
     * @param string $status
     * @return SalesOfficer
     */
    public function updateStatus(SalesOfficer $officer, string $status): SalesOfficer
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $oldStatus = $officer->status;
        $officer->update(['status' => $status]);
        
        $this->logActivity(
            'status_update', 
            'sales_officers', 
            "Changed officer status from {$oldStatus} to {$status}: {$officer->name}",
            ['officer_id' => $officer->id, 'old_status' => $oldStatus, 'new_status' => $status]
        );
        
        return $officer;
    }
    
    /**
     * Assign missions to officer
     *
     * @param SalesOfficer $officer
     * @param array $missionIds
     * @return SalesOfficer
     */
    public function assignMissions(SalesOfficer $officer, array $missionIds): SalesOfficer
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        // Update missions to assign them to this officer
        SalesMission::whereIn('id', $missionIds)->update(['user_id' => $officer->user_id]);
        
        $this->logActivity(
            'assign_missions', 
            'sales_officers', 
            "Assigned " . count($missionIds) . " missions to officer: {$officer->name}",
            ['officer_id' => $officer->id, 'mission_ids' => $missionIds]
        );
        
        return $officer->load('missions');
    }
    
    /**
     * Get officer performance report
     *
     * @param SalesOfficer $officer
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getOfficerPerformance(SalesOfficer $officer, string $startDate = null, string $endDate = null): array
    {
        $query = $officer->missions();
        
        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('end_date', '<=', $endDate);
        }
        
        $missions = $query->get();
        
        $totalMissions = $missions->count();
        $completedMissions = $missions->where('status', 'completed')->count();
        $activeMissions = $missions->where('status', 'active')->count();
        $pendingMissions = $missions->where('status', 'pending')->count();
        
        return [
            'officer' => $officer,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'mission_stats' => [
                'total' => $totalMissions,
                'completed' => $completedMissions,
                'active' => $activeMissions,
                'pending' => $pendingMissions,
                'completion_rate' => $totalMissions > 0 ? round(($completedMissions / $totalMissions) * 100, 2) : 0
            ],
            'missions' => $missions
        ];
    }
    
    /**
     * Get officers by status
     *
     * @param string $status
     * @return Collection
     */
    public function getOfficersByStatus(string $status): Collection
    {
        return SalesOfficer::with(['user', 'missions'])
            ->where('status', $status)
            ->orderBy('name')
            ->get();
    }
    
    /**
     * Get officers by region
     *
     * @param string $region
     * @return Collection
     */
    public function getOfficersInRegion(string $region): Collection
    {
        return SalesOfficer::with(['user', 'missions'])
            ->where('region', 'like', '%' . $region . '%')
            ->orderBy('name')
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
}