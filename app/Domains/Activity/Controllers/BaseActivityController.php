<?php

namespace App\Domains\Activity\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Activity\Services\ActivityService;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

abstract class BaseActivityController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Get the index route name for redirects (must be implemented by child classes)
     *
     * @return string
     */
    abstract protected function getIndexRouteName(): string;

    /**
     * Get the view path prefix (must be implemented by child classes)
     *
     * @return string
     */
    abstract protected function getViewPath(): string;

    /**
     * Display a listing of activities
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'start_date', 'end_date', 'status', 'type', 'search', 'per_page'
        ]);
        
        $activities = $this->activityService->getFilteredActivities($filters);
        $statistics = $this->activityService->getStatistics();
        
        return view($this->getViewPath() . '.index', compact('activities', 'statistics', 'filters'));
    }

    /**
     * Show the form for creating a new activity
     *
     * @return View
     */
    public function create(): View
    {
        return view($this->getViewPath() . '.create');
    }

    /**
     * Store a newly created activity
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateActivityData($request);
        
        try {
            $activity = $this->activityService->create($validated);
            
            return redirect()
                ->route($this->getIndexRouteName())
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
     * @return View
     */
    public function show(int $id): View
    {
        $activity = $this->findActivityOrFail($id);
        return view($this->getViewPath() . '.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified activity
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $activity = $this->findActivityOrFail($id);
        return view($this->getViewPath() . '.edit', compact('activity'));
    }

    /**
     * Update the specified activity
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $activity = $this->findActivityOrFail($id);
        $validated = $this->validateActivityData($request);
        
        try {
            $this->activityService->update($activity, $validated);
            
            return redirect()
                ->route($this->getIndexRouteName())
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
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $activity = $this->findActivityOrFail($id);
        
        try {
            $this->activityService->delete($activity);
            
            return redirect()
                ->route($this->getIndexRouteName())
                ->with('success', 'Activity deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update activity status
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);
        
        $activity = $this->findActivityOrFail($id);
        
        try {
            $this->activityService->updateStatus($activity, $request->status);
            
            return redirect()
                ->route($this->getIndexRouteName())
                ->with('success', 'Activity status updated successfully.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Find activity by ID or fail
     *
     * @param int $id
     * @return Activity
     */
    protected function findActivityOrFail(int $id): Activity
    {
        return Activity::findOrFail($id);
    }

    /**
     * Validate activity data
     *
     * @param Request $request
     * @return array
     */
    protected function validateActivityData(Request $request): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'participants' => 'nullable|integer|min:1',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'activity_type' => 'nullable|string|max:100'
        ];

        // Add Sales Mission specific validation rules
        if ($request->input('activity_type') === 'Sales Mission' || $request->input('type') === 'Sales Mission') {
            $rules = array_merge($rules, [
                'sales_mission_data' => 'required|array',
                'sales_mission_data.company_name' => 'required|string|max:255',
                'sales_mission_data.company_pic' => 'required|string|max:255',
                'sales_mission_data.company_position' => 'nullable|string|max:255',
                'sales_mission_data.company_contact' => 'nullable|string|max:50',
                'sales_mission_data.company_email' => 'nullable|email|max:255',
                'sales_mission_data.company_address' => 'nullable|string|max:500',
            ]);
        }

        return $request->validate($rules);
    }

    /**
     * Get activity types for dropdowns
     *
     * @return array
     */
    protected function getActivityTypes(): array
    {
        return [
            'meeting' => 'Meeting',
            'training' => 'Training',
            'workshop' => 'Workshop',
            'seminar' => 'Seminar',
            'conference' => 'Conference',
            'team_building' => 'Team Building',
            'presentation' => 'Presentation',
            'Sales Mission' => 'Sales Mission',
            'other' => 'Other'
        ];
    }

    /**
     * Get activity statuses for dropdowns
     *
     * @return array
     */
    protected function getActivityStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
    }
}