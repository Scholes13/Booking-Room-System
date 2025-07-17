<?php

namespace App\Domains\Sales\Services;

use App\Shared\Services\BaseService;
use App\Shared\Enums\UserRole;
use App\Models\SalesMission;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesMissionService extends BaseService
{
    /**
     * Create a new sales mission
     *
     * @param array $data
     * @return SalesMission
     */
    public function create(array $data): SalesMission
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $mission = SalesMission::create($data);
        
        $this->logActivity(
            'create', 
            'sales_missions', 
            "Created sales mission: {$mission->title}",
            ['mission_id' => $mission->id]
        );
        
        return $mission;
    }
    
    /**
     * Update existing sales mission
     *
     * @param SalesMission $mission
     * @param array $data
     * @return SalesMission
     */
    public function update(SalesMission $mission, array $data): SalesMission
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $oldData = $mission->toArray();
        $mission->update($data);
        
        $this->logActivity(
            'update', 
            'sales_missions', 
            "Updated sales mission: {$mission->title}",
            ['mission_id' => $mission->id, 'old_data' => $oldData, 'new_data' => $data]
        );
        
        return $mission;
    }
    
    /**
     * Delete sales mission
     *
     * @param SalesMission $mission
     * @return bool
     */
    public function delete(SalesMission $mission): bool
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $missionData = $mission->toArray();
        $result = $mission->delete();
        
        if ($result) {
            $this->logActivity(
                'delete', 
                'sales_missions', 
                "Deleted sales mission: {$missionData['title']}",
                ['deleted_mission' => $missionData]
            );
        }
        
        return $result;
    }
    
    /**
     * Get filtered sales missions based on request parameters
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredMissions(array $filters = [])
    {
        $query = SalesMission::with(['user', 'client']);
        
        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by priority
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        
        // Filter by assigned user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        // Filter by client
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        
        // Search by title or description
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('start_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Get sales mission statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_missions' => SalesMission::count(),
            'active_missions' => SalesMission::where('status', 'active')->count(),
            'completed_missions' => SalesMission::where('status', 'completed')->count(),
            'pending_missions' => SalesMission::where('status', 'pending')->count(),
            'this_month_missions' => SalesMission::where('created_at', '>=', $thisMonth)->count(),
            'missions_by_status' => $this->getMissionsByStatus(),
            'missions_by_priority' => $this->getMissionsByPriority(),
            'upcoming_missions' => $this->getUpcomingMissions(),
            'overdue_missions' => $this->getOverdueMissions(),
            'success_rate' => $this->calculateSuccessRate()
        ];
    }
    
    /**
     * Get missions grouped by status
     *
     * @return array
     */
    private function getMissionsByStatus(): array
    {
        return SalesMission::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
    
    /**
     * Get missions grouped by priority
     *
     * @return array
     */
    private function getMissionsByPriority(): array
    {
        return SalesMission::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
    }
    
    /**
     * Get upcoming missions
     *
     * @param int $limit
     * @return Collection
     */
    private function getUpcomingMissions(int $limit = 5): Collection
    {
        return SalesMission::with(['user', 'client'])
            ->where('start_date', '>=', Carbon::today())
            ->where('status', '!=', 'completed')
            ->orderBy('start_date')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get overdue missions
     *
     * @param int $limit
     * @return Collection
     */
    private function getOverdueMissions(int $limit = 5): Collection
    {
        return SalesMission::with(['user', 'client'])
            ->where('end_date', '<', Carbon::today())
            ->where('status', '!=', 'completed')
            ->orderBy('end_date')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Calculate success rate
     *
     * @return float
     */
    private function calculateSuccessRate(): float
    {
        $totalMissions = SalesMission::count();
        $completedMissions = SalesMission::where('status', 'completed')->count();
        
        if ($totalMissions === 0) {
            return 0;
        }
        
        return round(($completedMissions / $totalMissions) * 100, 2);
    }
    
    /**
     * Update mission status
     *
     * @param SalesMission $mission
     * @param string $status
     * @return SalesMission
     */
    public function updateStatus(SalesMission $mission, string $status): SalesMission
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $oldStatus = $mission->status;
        $mission->update(['status' => $status]);
        
        $this->logActivity(
            'status_update', 
            'sales_missions', 
            "Changed mission status from {$oldStatus} to {$status}: {$mission->title}",
            ['mission_id' => $mission->id, 'old_status' => $oldStatus, 'new_status' => $status]
        );
        
        return $mission;
    }
    
    /**
     * Assign mission to user
     *
     * @param SalesMission $mission
     * @param int $userId
     * @return SalesMission
     */
    public function assignToUser(SalesMission $mission, int $userId): SalesMission
    {
        $this->validatePermission(UserRole::getSalesManagerRoles());
        
        $oldUserId = $mission->user_id;
        $mission->update(['user_id' => $userId]);
        
        $this->logActivity(
            'assign', 
            'sales_missions', 
            "Assigned mission '{$mission->title}' to user ID: {$userId}",
            ['mission_id' => $mission->id, 'old_user_id' => $oldUserId, 'new_user_id' => $userId]
        );
        
        return $mission;
    }
    
    /**
     * Get missions for a specific user
     *
     * @param int $userId
     * @return Collection
     */
    public function getMissionsForUser(int $userId): Collection
    {
        return SalesMission::with(['client'])
            ->where('user_id', $userId)
            ->orderBy('start_date', 'desc')
            ->get();
    }
    
    /**
     * Get missions by status
     *
     * @param string $status
     * @return Collection
     */
    public function getMissionsByStatusCollection(string $status): Collection
    {
        return SalesMission::with(['user', 'client'])
            ->where('status', $status)
            ->orderBy('start_date', 'desc')
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