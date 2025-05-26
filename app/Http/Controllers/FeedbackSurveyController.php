<?php

namespace App\Http\Controllers;

use App\Models\FeedbackSurvey;
use App\Models\TeamAssignment;
use App\Models\Team;
use App\Services\FontneService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedbackSurveyController extends Controller
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
     * Display a listing of feedback surveys.
     */
    public function index(Request $request)
    {
        $query = FeedbackSurvey::with([
            'teamAssignment.team', 
            'teamAssignment.activity.salesMissionDetail', 
            'blitzTeam' // Eager load blitzTeam for sales_blitz surveys
        ]);
        
        // Apply Team filter
        if ($request->filled('team_id')) {
            $teamId = $request->team_id;
            $query->where(function($q) use ($teamId) {
                $q->whereHas('teamAssignment', function($subQuery) use ($teamId) {
                    $subQuery->where('team_id', $teamId);
                })
                ->orWhere('blitz_team_id', $teamId);
            });
        }
        
        // Apply Location filter (Currently only applies to non-blitz)
        // TODO: Consider if location filter should apply to blitz if blitz has location data
        if ($request->filled('location')) {
            $query->whereHas('teamAssignment.activity', function($q) use ($request) {
                $q->where('city', $request->location);
            });
        }
        
        // Apply Status filter
        if ($request->filled('status')) {
            if ($request->status === 'answered') {
                $query->where('is_completed', true);
            } elseif ($request->status === 'viewed') {
                $query->where('is_completed', false)
                      ->whereNotNull('viewed_at');
            } elseif ($request->status === 'pending') {
                $query->where('is_completed', false)
                      ->whereNull('viewed_at');
            }
        }
        
        // Apply Date filter (Currently only applies to non-blitz visit_date)
        // TODO: Consider if date filter should apply to blitz_visit_start_datetime
        if ($request->filled('date')) {
            $query->whereHas('teamAssignment.activity', function($q) use ($request) {
                $q->whereDate('start_datetime', '=', $request->date);
            });
        }
        
        $surveys = $query->latest()
            ->paginate(10)
            ->withQueryString();
            
        // Get distinct cities for the location dropdown (This might need adjustment if blitz has locations)
        $cities = \App\Models\Activity::join('team_assignments', 'activities.id', '=', 'team_assignments.activity_id')
            ->join('feedback_surveys', 'team_assignments.id', '=', 'feedback_surveys.team_assignment_id')
            ->select('activities.city')
            ->distinct()
            ->whereNotNull('activities.city')
            ->where('activities.city', '!=', '')
            ->orderBy('activities.city')
            ->pluck('activities.city');
            
        $teams = \App\Models\Team::orderBy('name')->get();
            
        return view('sales_mission.surveys.index', compact('surveys', 'cities', 'teams'));
    }

    /**
     * Create a new feedback survey for a team assignment.
     */
    public function generateSurvey(TeamAssignment $teamAssignment)
    {
        // Check if survey already exists
        if ($teamAssignment->feedbackSurvey) {
            return redirect()->route('sales_mission.surveys.show', $teamAssignment->feedbackSurvey->id)
                ->with('info', 'Survey already exists for this assignment.');
        }
        
        // Generate a unique token
        $token = Str::uuid();
        
        // Create the survey
        $survey = FeedbackSurvey::create([
            'team_assignment_id' => $teamAssignment->id,
            'survey_token' => $token,
            'is_completed' => false
        ]);
        
        return redirect()->route('sales_mission.surveys.show', $survey->id)
            ->with('success', 'Feedback survey created successfully.');
    }

    /**
     * Display the survey in admin view.
     */
    public function show(FeedbackSurvey $survey)
    {
        $survey->load(['teamAssignment.team', 'teamAssignment.activity.salesMissionDetail']);
        
        return view('sales_mission.surveys.show', compact('survey'));
    }

    /**
     * Display the public survey form for customers.
     */
    public function publicSurvey($token)
    {
        $survey = FeedbackSurvey::where('survey_token', $token)->firstOrFail();
        
        // If already completed, redirect to the public view page
        if ($survey->is_completed) {
            return redirect()->route('sales_mission.surveys.public.view_feedback', ['token' => $survey->survey_token]);
        }
        
        // Track the form view
        $survey->trackView();
        
        $survey->load(['teamAssignment.team', 'teamAssignment.activity.salesMissionDetail']);
        
        return view('sales_mission.surveys.public-form', compact('survey'));
    }

    /**
     * Submit feedback from the public form.
     */
    public function submitFeedback(Request $request, $token)
    {
        $survey = FeedbackSurvey::where('survey_token', $token)->firstOrFail();
        
        if ($survey->is_completed) {
            return redirect()->route('sales_mission.surveys.public.thank-you');
        }
        
        $validated = $request->validate([
            'visited_time' => 'required|date',
            'contact_salutation' => 'nullable|string|max:10',
            'contact_name' => 'required|string|max:255',
            'contact_job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_mobile' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'decision_maker_status' => 'nullable|string|max:50',
            'sales_call_outcome' => 'required|string|max:255',
            'next_follow_up' => 'required|string|max:50',
            'next_follow_up_other' => 'nullable|string|max:255',
            'product_interested' => 'nullable|string|max:255',
            'status_lead' => 'required|string|max:50',
            'potential_revenue' => 'nullable|string|max:50',
            'key_discussion_points' => 'required|string|max:1000',
            'has_documentation' => 'nullable',
            'has_business_card' => 'nullable',
        ]);
        
        // dd('Validation passed. Validated data:', $validated); // Langkah 1: Cek setelah validasi

        // dd('Attempting to update survey:', $survey, 'With data:', $validated); // Langkah 2: Cek sebelum update
        
        $survey->update([
            'visited_time' => $validated['visited_time'],
            'contact_salutation' => $validated['contact_salutation'],
            'contact_name' => $validated['contact_name'],
            'contact_job_title' => $validated['contact_job_title'],
            'department' => $validated['department'],
            'contact_mobile' => $validated['contact_mobile'],
            'contact_email' => $validated['contact_email'],
            'decision_maker_status' => $validated['decision_maker_status'],
            'sales_call_outcome' => $validated['sales_call_outcome'],
            'next_follow_up' => $validated['next_follow_up'],
            'next_follow_up_other' => $validated['next_follow_up_other'],
            'product_interested' => $validated['product_interested'],
            'status_lead' => $validated['status_lead'],
            'potential_revenue' => $validated['potential_revenue'],
            'key_discussion_points' => $validated['key_discussion_points'],
            'has_documentation' => $request->has('has_documentation'),
            'has_business_card' => $request->has('has_business_card'),
            'is_completed' => true,
            'completed_at' => now()
        ]);
        
        // dd('Survey updated successfully. Survey data after update:', $survey->refresh()); // Langkah 3: Cek setelah update
        
        // Send notification based on survey type
        if ($survey->survey_type === 'sales_blitz') {
            $this->fontneService->sendSalesBlitzSurveyCompletedNotification($survey);
        } else {
            // Default to standard field visit notification
            $this->fontneService->sendSurveyCompletedNotification($survey);
        }
        
        // dd('Notification sent (or attempted). Attempting redirect to thank you page.'); // Langkah 4: Cek sebelum redirect
        
        return redirect()->route('sales_mission.surveys.public.thank-you');
    }
    
    /**
     * Show thank you page after submission.
     */
    public function thankYou()
    {
        return view('sales_mission.surveys.thank-you');
    }

    /**
     * Send a notification when a survey is viewed for the first time
     */
    private function sendSurveyViewedNotification($survey)
    {
        try {
            // Get team assignment details
            $teamAssignment = $survey->teamAssignment;
            $company = $teamAssignment->activity->salesMissionDetail->company_name;
            $teamName = $teamAssignment->team->name;
            
            // Compose message
            $message = "👁️ *SURVEY FORM OPENED* 👁️\n\n";
            $message .= "The feedback form for team *{$teamName}*'s visit to *{$company}* has been opened.\n\n";
            $message .= "Time: " . now()->format('d M Y H:i:s') . "\n";
            $message .= "Status: Not yet submitted";
            
            // Send the notification
            $this->fontneService->sendWhatsAppMessage($this->fontneService->groupId, $message);
        } catch (\Exception $e) {
            // Log error but don't interrupt the process
            \Illuminate\Support\Facades\Log::error('Error sending survey viewed notification: ' . $e->getMessage());
        }
    }

    public function showSalesBlitzForm()
    {
        $teams = Team::orderBy('name')->get();
        return view('sales_mission.surveys.public-sales-blitz-form', compact('teams'));
    }

    public function submitSalesBlitzForm(Request $request)
    {
        $validated = $request->validate([
            'blitz_team_id' => 'required|exists:teams,id',
            'blitz_company_name' => 'required|string|max:255',
            'blitz_visit_start_datetime' => 'required|date',
            'blitz_visit_end_datetime' => 'required|date|after_or_equal:blitz_visit_start_datetime',
            'visited_time' => 'required|date',
            'contact_salutation' => 'nullable|string|max:10',
            'contact_name' => 'required|string|max:255',
            'contact_job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_mobile' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'decision_maker_status' => 'nullable|string|max:50',
            'sales_call_outcome' => 'required|string|max:255',
            'next_follow_up' => 'required|string|max:50',
            'next_follow_up_other' => 'nullable|string|max:255',
            'product_interested' => 'nullable|string|max:255',
            'status_lead' => 'required|string|max:50',
            'potential_revenue' => 'nullable|string|max:50',
            'key_discussion_points' => 'required|string|max:1000',
            'has_documentation' => 'nullable',
            'has_business_card' => 'nullable',
        ]);

        $token = Str::uuid();

        $survey = FeedbackSurvey::create([
            'survey_token' => $token,
            'survey_type' => 'sales_blitz',
            'team_assignment_id' => null,
            'blitz_team_id' => $validated['blitz_team_id'],
            'blitz_company_name' => $validated['blitz_company_name'],
            'blitz_visit_start_datetime' => $validated['blitz_visit_start_datetime'],
            'blitz_visit_end_datetime' => $validated['blitz_visit_end_datetime'],
            'visited_time' => $validated['visited_time'],
            'contact_salutation' => $validated['contact_salutation'],
            'contact_name' => $validated['contact_name'],
            'contact_job_title' => $validated['contact_job_title'],
            'department' => $validated['department'],
            'contact_mobile' => $validated['contact_mobile'],
            'contact_email' => $validated['contact_email'],
            'decision_maker_status' => $validated['decision_maker_status'],
            'sales_call_outcome' => $validated['sales_call_outcome'],
            'next_follow_up' => $validated['next_follow_up'],
            'next_follow_up_other' => $validated['next_follow_up_other'],
            'product_interested' => $validated['product_interested'],
            'status_lead' => $validated['status_lead'],
            'potential_revenue' => $validated['potential_revenue'],
            'key_discussion_points' => $validated['key_discussion_points'],
            'has_documentation' => $request->has('has_documentation'),
            'has_business_card' => $request->has('has_business_card'),
            'is_completed' => true,
            'completed_at' => now()
        ]);

        // $this->fontneService->sendSalesBlitzSurveyCompletedNotification($survey); 

        return redirect()->route('sales_mission.surveys.public.thank-you')
                         ->with('success', 'Sales blitz report submitted successfully!');
    }
}
