<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Activity;
use App\Models\TeamAssignment;
use App\Models\FeedbackSurvey;
use App\Services\FontneService;
use App\Services\FeedbackSurveySlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\SalesOfficerActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\SalesMissionDetail;

class TeamAssignmentController extends Controller
{
    /**
     * The Fonnte WhatsApp notification service
     */
    protected $fontneService;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(FontneService $fontneService)
    {
        $this->fontneService = $fontneService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TeamAssignment::with(['team', 'activity', 'activity.salesMissionDetail'])
                        ->join('activities', 'team_assignments.activity_id', '=', 'activities.id')
                        ->select('team_assignments.*');
        
        // Apply Team filter
        if ($request->filled('team_id')) {
            $query->where('team_assignments.team_id', $request->team_id);
        }
        
        // Apply Location filter
        if ($request->filled('location')) {
            $query->where('activities.city', $request->location);
        }
        
        // Apply Date filter (single date)
        if ($request->filled('date')) {
            $query->whereDate('activities.start_datetime', '=', $request->date);
        }
        
        $assignments = $query->orderBy('activities.start_datetime', 'asc')
            ->paginate(10)
            ->withQueryString();
        
        // Get distinct cities for the location dropdown
        $cities = Activity::whereHas('teamAssignments')
            ->select('city')
            ->distinct()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->orderBy('city')
            ->pluck('city');
            
        return view('sales_mission.field-visits.index', compact('assignments', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Log::info('TeamAssignmentController@create accessed.');
        Log::info('Request headers:', $request->headers->all());
        Log::info('Request query params:', $request->query());
        Log::info('Request format param:' . $request->input('format'));
        Log::info('Is AJAX request: ' . ($request->ajax() ? 'Yes' : 'No'));
        Log::info('Wants JSON: ' . ($request->wantsJson() ? 'Yes' : 'No'));

        // Get unique cities from Activities (Sales Missions) for the filter dropdown
        $cities = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->select('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city', 'asc')
            ->pluck('city');

        $teamQuery = Team::with('members')->orderBy('name');
        $activityQuery = Activity::whereHas('salesMissionDetail')
            ->with([
                'salesMissionDetail', 
                'teamAssignments', 
                'teamAssignments.team' 
            ]);

        // Filter teams by name
        if ($request->filled('search_team')) {
            $teamQuery->where('name', 'like', '%' . $request->input('search_team') . '%');
        }

        // Filter activities by company name in salesMissionDetail or activity name
        if ($request->filled('search_activity')) {
            $searchActivity = $request->input('search_activity');
            $activityQuery->where(function ($query) use ($searchActivity) {
                $query->whereHas('salesMissionDetail', function ($subQuery) use ($searchActivity) {
                    $subQuery->where('company_name', 'like', '%' . $searchActivity . '%');
                })
                ->orWhere('name', 'like', '%' . $searchActivity . '%');
            });
        }

        // Filter by Location
        $filterLocationValue = $request->input('filter_location');
        if ($filterLocationValue) {
            // Exact match since it's from a dropdown
            $activityQuery->where('city', $filterLocationValue);
        }

        $filterDateValue = $request->input('filter_date');
        if ($filterDateValue) {
            $activityQuery->whereDate('start_datetime', '=', $filterDateValue);
            $activityQuery->orderBy('start_datetime', 'asc');
        } else {
            $today = Carbon::today()->toDateString();
            $activityQuery->orderByRaw("CASE 
                                            WHEN DATE(start_datetime) >= '{$today}' THEN 0 
                                            ELSE 1 
                                        END ASC")
                          ->orderByRaw("CASE 
                                            WHEN DATE(start_datetime) >= '{$today}' THEN start_datetime 
                                            ELSE NULL 
                                        END ASC")
                          ->orderByRaw("CASE 
                                            WHEN DATE(start_datetime) < '{$today}' THEN start_datetime 
                                            ELSE NULL 
                                        END DESC");
        }

        $teams = $teamQuery->get();
        $activities = $activityQuery->get();
        
        $searchTeamValue = $request->input('search_team');
        $searchActivityValue = $request->input('search_activity');

        // Prioritaskan format=json dari URL untuk respons AJAX
        if ($request->input('format') === 'json') {
            Log::info('Responding with JSON because format=json was found.');
            $teamsHtml = view('sales_mission.field-visits.partials._team-list', compact('teams'))->render();
            $activitiesHtml = view('sales_mission.field-visits.partials._activity-list', compact('activities'))->render();
            
            return response()->json([
                'teams_html' => $teamsHtml,
                'activities_html' => $activitiesHtml,
                // Kita tidak mengirim ulang cities via JSON karena dropdown kota tidak perlu di-refresh via AJAX
                // Jika diperlukan, bisa ditambahkan di sini dan dihandle di JS.
            ]);
        }

        Log::info('Not responding with JSON, proceeding to render HTML view.');
        // Check if request is for modal format
        if ($request->has('format') && $request->format === 'modal') {
            Log::info('Responding with modal HTML view.');
            return view('sales_mission.field-visits.partials.modal-create', compact('teams', 'activities', 'searchTeamValue', 'searchActivityValue', 'filterDateValue', 'filterLocationValue', 'cities'));
        }
            
        Log::info('Responding with full HTML view.');
        return view('sales_mission.field-visits.create', compact('teams', 'activities', 'searchTeamValue', 'searchActivityValue', 'filterDateValue', 'filterLocationValue', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'activity_id' => 'required|exists:activities,id',
            'notes' => 'nullable|string'
        ]);
        
        // Check if assignment already exists
        $exists = TeamAssignment::where('team_id', $validated['team_id'])
            ->where('activity_id', $validated['activity_id'])
            ->exists();
            
        if ($exists) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This team is already assigned to this activity.'
                ], 422);
            }
            
            return back()->with('error', 'This team is already assigned to this activity.');
        }
        
        // Get the activity to use its start and end dates for conflict checking
        $newActivity = Activity::findOrFail($validated['activity_id']);
        
        // Check for time conflicts with existing assignments
        $conflict = $this->checkTimeConflicts($validated['team_id'], $newActivity, null);
        
        if ($conflict) {
            $conflictMessage = "Schedule conflict detected: Team is already assigned to {$conflict['company']} on " . 
                date('d M Y', strtotime($conflict['start'])) . " from " . 
                date('H:i', strtotime($conflict['start'])) . " to " . 
                date('H:i', strtotime($conflict['end'])) . ".";
                
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $conflictMessage
                ], 422);
            }
            
            return back()->with('error', $conflictMessage);
        }
        
        // Create the team assignment
        $teamAssignment = TeamAssignment::create([
            'team_id' => $validated['team_id'],
            'activity_id' => $validated['activity_id'],
            'assignment_date' => $newActivity->start_datetime,
            'assigned_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null
        ]);
        
        // Generate a feedback survey for this assignment
        $survey = $this->generateFeedbackSurvey($teamAssignment);
        
        // Handle AJAX request (for modal form submission)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Team assigned to activity successfully. A feedback survey has been generated and notification sent.'
            ]);
        }
        
        return redirect()->route('sales_mission.field-visits.index')
            ->with('success', 'Team assigned to activity successfully. A feedback survey has been generated and notification sent.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamAssignment $fieldVisit)
    {
        // Load relasi yang diperlukan
        $fieldVisit->load(['team.members', 'activity.salesMissionDetail', 'assigner']);
        
        // Check if request is for modal format
        if (request()->has('format') && request()->format === 'modal') {
            return view('sales_mission.field-visits.partials.modal-show', compact('fieldVisit'));
        }
        
        return view('sales_mission.field-visits.show', compact('fieldVisit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeamAssignment $fieldVisit)
    {
        $teams = Team::orderBy('name')->get();
        $activities = Activity::whereHas('salesMissionDetail')
            ->with('salesMissionDetail')
            ->orderBy('start_datetime', 'desc')
            ->get();
            
        // Check if request is for modal format
        if (request()->has('format') && request()->format === 'modal') {
            return view('sales_mission.field-visits.partials.modal-edit', compact('fieldVisit', 'teams', 'activities'));
        }
            
        return view('sales_mission.field-visits.edit', compact('fieldVisit', 'teams', 'activities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamAssignment $fieldVisit)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'activity_id' => 'required|exists:activities,id',
            'notes' => 'nullable|string'
        ]);
        
        // Check if assignment already exists (excluding current one)
        $exists = TeamAssignment::where('team_id', $validated['team_id'])
            ->where('activity_id', $validated['activity_id'])
            ->where('id', '!=', $fieldVisit->id)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'This team is already assigned to this activity.');
        }
        
        // Get the activity to use its start and end dates for conflict checking
        $newActivity = Activity::findOrFail($validated['activity_id']);
        
        // Check for time conflicts with existing assignments
        $conflict = $this->checkTimeConflicts($validated['team_id'], $newActivity, $fieldVisit->id);
        
        if ($conflict) {
            return back()->with('error', "Schedule conflict detected: Team is already assigned to {$conflict['company']} on " . 
                date('d M Y', strtotime($conflict['start'])) . " from " . 
                date('H:i', strtotime($conflict['start'])) . " to " . 
                date('H:i', strtotime($conflict['end'])) . ".");
        }
        
        $fieldVisit->update([
            'team_id' => $validated['team_id'],
            'activity_id' => $validated['activity_id'],
            'assignment_date' => $newActivity->start_datetime,
            'notes' => $validated['notes'] ?? null
        ]);
        
        // Check if a feedback survey exists, if not, create one
        if (!$fieldVisit->feedbackSurvey) {
            $this->generateFeedbackSurvey($fieldVisit);
            $surveyMessage = " A feedback survey has been generated.";
        } else {
            $surveyMessage = "";
        }
        
        // Handle AJAX request (for modal form submission)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Field visit updated successfully.' . $surveyMessage,
                'redirect' => route('sales_mission.field-visits.index')
            ]);
        }
        
        return redirect()->route('sales_mission.field-visits.index')
            ->with('success', 'Field visit updated successfully.' . $surveyMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamAssignment $fieldVisit)
    {
        $fieldVisit->delete();
        
        return redirect()->route('sales_mission.field-visits.index')
            ->with('success', 'Field visit deleted successfully.');
    }

    /**
     * Check if the team has any scheduling conflicts with the new activity
     * 
     * @param int $teamId The team ID to check
     * @param Activity $newActivity The new activity to check for conflicts
     * @param int|null $excludeAssignmentId Optional assignment ID to exclude from conflict check
     * @return array|null Conflict details if found, null otherwise
     */
    private function checkTimeConflicts($teamId, $newActivity, $excludeAssignmentId = null)
    {
        // Get all team assignments for this team
        $query = TeamAssignment::where('team_id', $teamId);
        
        // Exclude the current assignment if we're updating
        if ($excludeAssignmentId) {
            $query->where('id', '!=', $excludeAssignmentId);
        }
        
        $teamAssignments = $query->with(['activity', 'activity.salesMissionDetail'])->get();
        
        // Check each assignment for time conflicts
        foreach ($teamAssignments as $assignment) {
            $existingActivity = $assignment->activity;
            
            // Skip if no activity data
            if (!$existingActivity || !$existingActivity->start_datetime || !$existingActivity->end_datetime) {
                continue;
            }
            
            // Check for overlap between the new activity and the existing one
            $newStart = strtotime($newActivity->start_datetime);
            $newEnd = strtotime($newActivity->end_datetime);
            $existingStart = strtotime($existingActivity->start_datetime);
            $existingEnd = strtotime($existingActivity->end_datetime);
            
            // Check if there's an overlap
            if (($newStart >= $existingStart && $newStart < $existingEnd) || // New activity starts during existing activity
                ($newEnd > $existingStart && $newEnd <= $existingEnd) ||     // New activity ends during existing activity
                ($newStart <= $existingStart && $newEnd >= $existingEnd)) {  // New activity completely encompasses existing activity
                
                // Return conflict details
                return [
                    'company' => $existingActivity->salesMissionDetail->company_name ?? 'Unknown company',
                    'start' => $existingActivity->start_datetime,
                    'end' => $existingActivity->end_datetime
                ];
            }
        }
        
        return null; // No conflicts
    }

    /**
     * Generate a feedback survey for a team assignment
     *
     * @param TeamAssignment $teamAssignment
     * @return FeedbackSurvey
     */
    private function generateFeedbackSurvey(TeamAssignment $teamAssignment)
    {
        $slugService = new FeedbackSurveySlugService();
        
        // Create the survey with both UUID token (for backward compatibility) and readable slug
        return FeedbackSurvey::create([
            'team_assignment_id' => $teamAssignment->id,
            'survey_token' => Str::uuid(),
            'url_slug' => $slugService->generateSlug($teamAssignment),
            'is_completed' => false
        ]);
    }

    /**
     * Display the public view of field visits.
     */
    public function publicIndex(Request $request)
    {
        $query = TeamAssignment::with([
            'team',
            'activity.salesMissionDetail'
        ])
        ->join('activities', 'team_assignments.activity_id', '=', 'activities.id')
        ->select('team_assignments.*');

        // Apply Team filter
        if ($request->filled('team_id')) {
            $query->where('team_assignments.team_id', $request->team_id);
        }
        
        // Apply Location filter
        if ($request->filled('location')) {
            $query->where('activities.city', $request->location);
        }
        
        // Apply Company filter using whereHas
        if ($request->filled('company_name')) {
            $query->whereHas('activity.salesMissionDetail', function ($q) use ($request) {
                $q->where('company_name', $request->company_name);
            });
            // If company_name is present, date filter is explicitly ignored for the query.
        } else {
            // No company filter is active. Determine date for query.
            $currentQueryDate = null;
            if ($request->has('date')) {
                // Date parameter is present in URL (could be filled or empty for all dates)
                if ($request->filled('date')) {
                    $currentQueryDate = $request->input('date');
                }
                // If date is present but not filled (e.g., ?date=), $currentQueryDate remains null (no date filter for query)
            } else {
                // Date parameter is NOT in URL. This is an initial load or pagination without explicit date.
                // Default to today for query ONLY IF it's a true initial load (no other filters implying a wider set).
                // For now, let's assume if no date and no company, we default to today. Pagination will carry 'date' if it was set.
                // If only 'page' is present, it implies all dates for other active filters.
                if (!$request->hasAny(['team_id', 'location', 'page'])) { // Simple check for true initial load
                     $currentQueryDate = Carbon::today()->toDateString();
                }
            }

            if ($currentQueryDate) {
                $query->whereDate('activities.start_datetime', '=', $currentQueryDate);
            }
        }

        $assignments = $query->orderBy('activities.start_datetime', 'asc')
            ->paginate(10)
            ->withQueryString();

        $teams = Team::orderBy('name')->get();
        $cities = Activity::whereHas('teamAssignments')
            ->select('city')
            ->distinct()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->orderBy('city')
            ->pluck('city');

        // Get distinct company names for the filter dropdown
        $companies = SalesMissionDetail::whereHas('activity.teamAssignments')
            ->select('company_name')
            ->distinct()
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->orderBy('company_name')
            ->pluck('company_name');

        // Determine the value for the date input field ($filterDate) for the view
        $filterDate = ''; // Default to empty for the view
        if ($request->filled('company_name')) {
            // Company filter is active, so date input should be empty
            $filterDate = ''; 
        } elseif ($request->has('date')) {
            // Date parameter is present in the URL (could be filled or empty string)
            $filterDate = $request->input('date'); // Persist user's explicit date (or empty string) choice
        } elseif (!$request->hasAny(['team_id', 'location', 'company_name', 'page'])) {
            // No company filter, no 'date' in URL, and no other filters/pagination: Initial load default for view
            $filterDate = Carbon::today()->toDateString();
        } // In other cases (e.g. paginating without an explicit date filter), date input remains empty, showing all

        return view('sales_mission.field-visits.public', compact('assignments', 'teams', 'cities', 'companies', 'filterDate'));
    }
    
    /**
     * Get field visits data in calendar format
     */
    public function calendarData(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $teamId = $request->input('team_id');
        $location = $request->input('location');
        
        $query = TeamAssignment::with(['team', 'activity', 'activity.salesMissionDetail'])
            ->whereHas('activity', function($q) use ($start, $end) {
                $q->where(function($q) use ($start, $end) {
                    $q->whereBetween('start_datetime', [$start, $end])
                      ->orWhereBetween('end_datetime', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('start_datetime', '<=', $start)
                            ->where('end_datetime', '>=', $end);
                      });
                });
            });
        
        // Apply filters
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        
        if ($location) {
            $query->whereHas('activity', function($q) use ($location) {
                $q->where('city', $location);
            });
        }
        
        $events = [];
        $fieldVisits = $query->get();
        
        foreach ($fieldVisits as $visit) {
            if (!$visit->activity || !$visit->activity->salesMissionDetail) {
                continue;
            }
            
            $events[] = [
                'id' => $visit->id,
                'title' => $visit->activity->salesMissionDetail->company_name,
                'start' => $visit->activity->start_datetime,
                'end' => $visit->activity->end_datetime,
                'team' => $visit->team->name,
                'location' => $visit->activity->city . ', ' . $visit->activity->province,
                'color' => '#f59e0b', // Amber color
                'url' => route('public.field-visits.detail', $visit->id),
                'extendedProps' => [
                    'team_name' => $visit->team->name,
                    'company_name' => $visit->activity->salesMissionDetail->company_name,
                    'company_pic' => $visit->activity->salesMissionDetail->company_pic,
                    'company_position' => $visit->activity->salesMissionDetail->company_position,
                    'company_address' => $visit->activity->salesMissionDetail->company_address,
                ]
            ];
        }
        
        return response()->json($events);
    }
    
    /**
     * Display the detailed public view of a specific field visit
     */
    public function publicDetail(TeamAssignment $fieldVisit)
    {
        $fieldVisit->load(['team', 'activity', 'activity.salesMissionDetail']);
        
        // Ambil employees secara manual berdasarkan array ID di team->members
        $employees = collect(); // Default ke collection kosong
        if ($fieldVisit->team && is_array($fieldVisit->team->members)) {
            $employees = Employee::whereIn('id', $fieldVisit->team->members ?: [])->get();
        }
        
        return view('sales_mission.field-visits.public-detail', compact('fieldVisit', 'employees'));
    }

    /**
     * Display the daily schedule matrix for administrators.
     */
    public function adminDailySchedule(Request $request)
    {
        Log::info('adminDailySchedule: Method entry.');

        // 1. Get selected date from request (default to today)
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $carbonSelectedDate = Carbon::parse($selectedDate);
        $dayStart = $carbonSelectedDate->copy()->startOfDay();
        $dayEnd = $carbonSelectedDate->copy()->endOfDay();

        // 2. Define time slots (09:00 - 18:00, 30-min interval)
        $timeSlots = [];
        $slotIterator = $carbonSelectedDate->copy()->setTime(9, 0, 0);
        $scheduleEndTime = $carbonSelectedDate->copy()->setTime(18, 0, 0);
        while ($slotIterator < $scheduleEndTime) {
            $timeSlots[] = $slotIterator->format('H:i');
            $slotIterator->addMinutes(30);
        }

        // 3. Get all active teams
        $teams = Team::orderBy('name')->get();

        // 4. Fetch assignments that are active on the selected date
        $assignments = TeamAssignment::with(['team', 'activity', 'activity.salesMissionDetail'])
            ->whereHas('activity', function ($query) use ($dayStart, $dayEnd) {
                $query->where('start_datetime', '<=', $dayEnd) 
                      ->where('end_datetime', '>=', $dayStart);
            })
            ->get();

        // 5. Prepare data matrix for the view
        $scheduleData = [];
        foreach ($timeSlots as $slot) {
            foreach ($teams as $team) {
                $scheduleData[$slot][$team->id] = null;
            }
        }

        foreach ($assignments as $assignment) {
            if (!$assignment->activity || !$assignment->team) {
                continue;
            }

            $activityStartCarbon = Carbon::parse($assignment->activity->start_datetime);
            $activityEndCarbon = Carbon::parse($assignment->activity->end_datetime);
            $teamId = $assignment->team_id;

            Log::info("Processing activity", [
                'activity_id' => $assignment->activity->id,
                'original_start' => $activityStartCarbon->format('Y-m-d H:i:s'),
                'original_end' => $activityEndCarbon->format('Y-m-d H:i:s')
            ]);

            // Find the first slot where this activity should appear
            $firstSlotTime = null;
            $firstSlotIndex = null;
            foreach ($timeSlots as $index => $slot) {
                $slotTime = Carbon::parse($selectedDate . ' ' . $slot);
                $nextSlotTime = $slotTime->copy()->addMinutes(30);

                if ($activityStartCarbon < $nextSlotTime && $activityEndCarbon > $slotTime) {
                    $firstSlotTime = $slot;
                    $firstSlotIndex = $index;
                    Log::info("Found first slot", [
                        'slot' => $slot,
                        'index' => $index,
                        'slot_time' => $slotTime->format('H:i'),
                        'next_slot_time' => $nextSlotTime->format('H:i')
                    ]);
                    break;
                }
            }

            if ($firstSlotTime !== null) {
                // Calculate start and end times within schedule bounds
                $startTime = max($activityStartCarbon, Carbon::parse($selectedDate . ' 09:00'));
                $endTime = min($activityEndCarbon, Carbon::parse($selectedDate . ' 18:00'));

                Log::info("Time bounds", [
                    'adjusted_start' => $startTime->format('H:i'),
                    'adjusted_end' => $endTime->format('H:i')
                ]);

                // Calculate total slots needed by counting 30-minute intervals
                $totalSlots = 0;
                $tempTime = $startTime->copy();
                // Loop selama tempTime masih SEBELUM endTime.
                // Jika aktivitas berakhir jam 11:00, slot jam 11:00 itu sendiri tidak boleh dihitung sebagai bagian dari aktivitas ini.
                while ($tempTime < $endTime) { // kondisi diubah dari <= menjadi <
                    $totalSlots++;
                    $tempTime->addMinutes(30);
                }

                // Ensure we have at least one slot if the activity is very short or exactly on a slot boundary
                // but only if it actually has duration within the schedule.
                if ($startTime < $endTime) {
                    $rowspan = max(1, $totalSlots);
                } else {
                    $rowspan = max(1, $totalSlots); 
                }

                Log::info("Rowspan calculation for view", [
                    'activity_id' => $assignment->activity->id,
                    'company_name' => optional($assignment->activity->salesMissionDetail)->company_name ?? 'N/A',
                    'activity_start' => $activityStartCarbon->format('H:i'),
                    'activity_end' => $activityEndCarbon->format('H:i'),
                    'calculated_totalSlots' => $totalSlots,
                    'final_rowspan_for_view' => $rowspan
                ]);

                // Set the activity data in the first slot
                $scheduleData[$firstSlotTime][$teamId] = [
                    'company_name' => optional($assignment->activity->salesMissionDetail)->company_name ?? 'N/A',
                    'activity_id' => $assignment->activity->id,
                    'assignment_id' => $assignment->id,
                    'start_time' => $activityStartCarbon->format('H:i'),
                    'end_time' => $activityEndCarbon->format('H:i'),
                    'rowspan' => $rowspan,
                ];

                // Mark subsequent slots as 'CONTINUATION'
                for ($i = 1; $i < $rowspan && ($firstSlotIndex + $i) < count($timeSlots); $i++) {
                    $nextSlot = $timeSlots[$firstSlotIndex + $i];
                    $scheduleData[$nextSlot][$teamId] = 'CONTINUATION';
                    Log::info("Marking continuation slot", [
                        'slot' => $nextSlot,
                        'index' => $firstSlotIndex + $i
                    ]);
                }
            }
        }

        return view('sales_mission.schedules.daily-matrix', compact(
            'teams',
            'timeSlots',
            'scheduleData',
            'selectedDate',
            'carbonSelectedDate'
        ));
    }
}
