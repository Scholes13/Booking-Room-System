<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    /**
     * Display a listing of the activity types.
     */
    public function index(Request $request)
    {
        $activityTypes = ActivityType::orderBy('name')->get();
        
        // Detect if request is coming from superadmin or admin_bas
        if (str_contains($request->route()->getName(), 'superadmin')) {
            return view('superadmin.activity_types.index', compact('activityTypes'));
        } else {
            return view('admin_bas.activity_types.index', compact('activityTypes'));
        }
    }

    /**
     * Show the form for creating a new activity type.
     */
    public function create(Request $request)
    {
        // Detect if request is coming from superadmin or admin_bas
        if (str_contains($request->route()->getName(), 'superadmin')) {
            return view('superadmin.activity_types.create');
        } else {
            return view('admin_bas.activity_types.create');
        }
    }

    /**
     * Store a newly created activity type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:activity_types',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle checkbox - explicitly set is_active based on presence of checkbox value
        $validated['is_active'] = $request->has('is_active');

        ActivityType::create($validated);

        // Redirect based on which route was used
        if (str_contains($request->route()->getName(), 'superadmin')) {
            return redirect()->route('superadmin.activity-types.index')
                ->with('success', 'Jenis aktivitas berhasil ditambahkan');
        } else {
            return redirect()->route('bas.activity-types.index')
                ->with('success', 'Jenis aktivitas berhasil ditambahkan');
        }
    }

    /**
     * Show the form for editing the specified activity type.
     */
    public function edit(Request $request, ActivityType $activityType)
    {
        // Detect if request is coming from superadmin or admin_bas
        if (str_contains($request->route()->getName(), 'superadmin')) {
            return view('superadmin.activity_types.edit', compact('activityType'));
        } else {
            return view('admin_bas.activity_types.edit', compact('activityType'));
        }
    }

    /**
     * Update the specified activity type in storage.
     */
    public function update(Request $request, ActivityType $activityType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:activity_types,name,' . $activityType->id,
            'description' => 'nullable|string',
        ]);
        
        // Handle checkbox - explicitly set is_active based on presence of checkbox value
        $validated['is_active'] = $request->has('is_active');

        $activityType->update($validated);

        // Redirect based on which route was used
        if (str_contains($request->route()->getName(), 'superadmin')) {
            return redirect()->route('superadmin.activity-types.index')
                ->with('success', 'Jenis aktivitas berhasil diperbarui');
        } else {
            return redirect()->route('bas.activity-types.index')
                ->with('success', 'Jenis aktivitas berhasil diperbarui');
        }
    }

    /**
     * Remove the specified activity type from storage.
     */
    public function destroy(Request $request, ActivityType $activityType)
    {
        // Check if this activity type is being used by any activities
        $activitiesCount = $activityType->activities()->count();
        
        if ($activitiesCount > 0) {
            // Redirect based on which route was used
            if (str_contains($request->route()->getName(), 'superadmin')) {
                return redirect()->route('superadmin.activity-types.index')
                    ->with('error', 'Jenis aktivitas tidak dapat dihapus karena sedang digunakan oleh ' . $activitiesCount . ' aktivitas.');
            } else {
                return redirect()->route('bas.activity-types.index')
                    ->with('error', 'Jenis aktivitas tidak dapat dihapus karena sedang digunakan oleh ' . $activitiesCount . ' aktivitas.');
            }
        }
        
        $activityType->delete();

        // Redirect based on which route was used
        if (str_contains($request->route()->getName(), 'superadmin')) {
            return redirect()->route('superadmin.activity-types.index')
                ->with('success', 'Jenis aktivitas berhasil dihapus');
        } else {
            return redirect()->route('bas.activity-types.index')
                ->with('success', 'Jenis aktivitas berhasil dihapus');
        }
    }
}
