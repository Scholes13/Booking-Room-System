<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::with(['creator', 'members'])->paginate(10);
        return view('sales_mission.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('sales_mission.teams.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:employees,id'
        ]);
        
        // Check if any of the selected employees are already in another team
        $conflictingEmployees = $this->findEmployeesInOtherTeams($validated['members']);
        
        if (count($conflictingEmployees) > 0) {
            $errorMessage = 'The following employees are already in other teams: ' . 
                implode(', ', $conflictingEmployees->pluck('name')->toArray());
            
            throw ValidationException::withMessages([
                'members' => [$errorMessage]
            ]);
        }
        
        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'created_by' => Auth::id()
        ]);

        // Attach members using the pivot table
        $team->members()->attach($validated['members']);
        
        return redirect()->route('sales_mission.teams.index')
            ->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['creator', 'activities', 'members']);
        return view('sales_mission.teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $team->load('members');
        $employees = Employee::orderBy('name')->get();
        return view('sales_mission.teams.edit', compact('team', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:employees,id'
        ]);
        
        // Check if any of the selected employees are already in another team
        $conflictingEmployees = $this->findEmployeesInOtherTeams($validated['members'], $team->id);
        
        if (count($conflictingEmployees) > 0) {
            $errorMessage = 'The following employees are already in other teams: ' . 
                implode(', ', $conflictingEmployees->pluck('name')->toArray());
            
            throw ValidationException::withMessages([
                'members' => [$errorMessage]
            ]);
        }
        
        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);

        // Sync members using the pivot table
        $team->members()->sync($validated['members']);
        
        return redirect()->route('sales_mission.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();
        
        return redirect()->route('sales_mission.teams.index')
            ->with('success', 'Team deleted successfully.');
    }
    
    /**
     * Get teams data in JSON format
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeamsJson()
    {
        $teams = Team::orderBy('name')->get()->map(function($team) {
            return [
                'id' => $team->id,
                'name' => $team->name,
                'members' => is_array($team->members) ? count($team->members) : 0
            ];
        });
        
        return response()->json([
            'success' => true,
            'teams' => $teams
        ]);
    }
    
    /**
     * Find employees that are already in other teams.
     *
     * @param array $employeeIds
     * @param int|null $excludeTeamId - Team ID to exclude from check (for updates)
     * @return \Illuminate\Support\Collection
     */
    private function findEmployeesInOtherTeams(array $employeeIds, ?int $excludeTeamId = null)
    {
        return Employee::whereIn('id', $employeeIds)
            ->whereHas('teams', function($query) use ($excludeTeamId) {
                if ($excludeTeamId) {
                    $query->where('teams.id', '!=', $excludeTeamId);
                }
            })->get();
    }
}
