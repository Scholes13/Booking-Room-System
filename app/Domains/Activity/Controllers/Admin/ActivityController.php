<?php

namespace App\Domains\Activity\Controllers\Admin;

use App\Domains\Activity\Controllers\BaseActivityController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityController extends BaseActivityController
{
    /**
     * Get the index route name for redirects
     *
     * @return string
     */
    protected function getIndexRouteName(): string
    {
        return 'admin.activities.index';
    }

    /**
     * Get the view path prefix
     *
     * @return string
     */
    protected function getViewPath(): string
    {
        return 'admin.activities';
    }

    /**
     * Display a listing of activities with admin-specific features
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $filters = $request->only([
            'start_date', 'end_date', 'status', 'type', 'search', 'per_page'
        ]);
        
        $activities = $this->activityService->getFilteredActivities($filters);
        $statistics = $this->activityService->getStatistics();
        $activityTypes = $this->getActivityTypes();
        $activityStatuses = $this->getActivityStatuses();
        
        return view('admin.activities.index', compact(
            'activities', 'statistics', 'filters', 'activityTypes', 'activityStatuses'
        ));
    }

    /**
     * Show the form for creating a new activity
     *
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $activityTypes = $this->getActivityTypes();
        $activityStatuses = $this->getActivityStatuses();
        
        return view('admin.activities.create', compact('activityTypes', 'activityStatuses'));
    }

    /**
     * Store a newly created activity
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $this->validateActivityData($request);
        
        try {
            $activity = $this->activityService->create($validated);
            
            $this->activityService->logActivity(
                'create',
                'activities',
                "Admin created activity: {$activity->name}",
                ['activity_id' => $activity->id, 'created_by' => 'admin']
            );
            
            return redirect()
                ->route('admin.activities.index')
                ->with('success', 'Activity created successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified activity
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id): \Illuminate\View\View
    {
        $activity = $this->findActivityOrFail($id);
        return view('admin.activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified activity
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): \Illuminate\View\View
    {
        $activity = $this->findActivityOrFail($id);
        $activityTypes = $this->getActivityTypes();
        $activityStatuses = $this->getActivityStatuses();
        
        return view('admin.activities.edit', compact('activity', 'activityTypes', 'activityStatuses'));
    }

    /**
     * Update the specified activity
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $activity = $this->findActivityOrFail($id);
        $validated = $this->validateActivityData($request);
        
        try {
            $updatedActivity = $this->activityService->update($activity, $validated);
            
            $this->activityService->logActivity(
                'update',
                'activities',
                "Admin updated activity: {$updatedActivity->name}",
                ['activity_id' => $updatedActivity->id, 'updated_by' => 'admin']
            );
            
            return redirect()
                ->route('admin.activities.index')
                ->with('success', 'Activity updated successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified activity
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $activity = $this->findActivityOrFail($id);
        
        try {
            $this->activityService->delete($activity);
            
            $this->activityService->logActivity(
                'delete',
                'activities',
                "Admin deleted activity: {$activity->name}",
                ['deleted_activity_id' => $id, 'deleted_by' => 'admin']
            );
            
            return redirect()
                ->route('admin.activities.index')
                ->with('success', 'Activity deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Export activities to Excel (Admin feature)
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $filters = $request->only([
            'start_date', 'end_date', 'status', 'type'
        ]);
        
        $this->activityService->logActivity(
            'export',
            'activities',
            'Admin exported activity data',
            ['filters' => $filters, 'exported_by' => 'admin']
        );
        
        // Implementation would depend on your Excel export library
        // For example, using Laravel Excel:
        // return Excel::download(new ActivitiesExport($filters), 'activities.xlsx');
        
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Get activity statistics for dashboard
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->activityService->getStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update activity status
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'activity_ids' => 'required|array',
            'activity_ids.*' => 'exists:activities,id',
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);
        
        try {
            $updatedCount = 0;
            foreach ($request->activity_ids as $activityId) {
                $activity = $this->findActivityOrFail($activityId);
                $this->activityService->updateStatus($activity, $request->status);
                $updatedCount++;
            }
            
            $this->activityService->logActivity(
                'bulk_status_update',
                'activities',
                "Admin bulk updated {$updatedCount} activities to status: {$request->status}",
                ['activity_ids' => $request->activity_ids, 'new_status' => $request->status, 'updated_by' => 'admin']
            );
            
            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} activities updated successfully."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activities by date range (AJAX)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getActivitiesByDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);
        
        try {
            $activities = $this->activityService->getActivitiesInDateRange(
                $request->start_date,
                $request->end_date
            );
            
            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activities by status (AJAX)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getActivitiesByStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);
        
        try {
            $activities = $this->activityService->getActivitiesByStatus($request->status);
            
            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company suggestions for Sales Mission form (AJAX)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanySuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255'
        ]);

        try {
            $suggestions = $this->activityService->getCompanySuggestions($request->query);
            
            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search companies for autocomplete (AJAX)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCompanies(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        try {
            $companies = $this->activityService->searchCompanies(
                $request->query,
                $request->input('limit', 10)
            );
            
            return response()->json([
                'success' => true,
                'data' => $companies->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'address' => $company->address,
                        'city' => $company->city,
                        'province' => $company->province,
                        'status' => $company->status,
                        'total_visits' => $company->salesMissionDetails->count(),
                        'last_visit' => $company->salesMissionDetails->max('created_at'),
                        'primary_contact' => $company->contacts->where('is_primary', true)->first()
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company visit history (AJAX)
     *
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function getCompanyVisitHistory(Request $request, int $companyId): JsonResponse
    {
        try {
            $history = $this->activityService->getCompanyVisitHistory($companyId);
            
            return response()->json([
                'success' => true,
                'data' => $history
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}