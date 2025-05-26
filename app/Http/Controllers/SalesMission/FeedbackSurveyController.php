<?php

namespace App\Http\Controllers\SalesMission;

use App\Http\Controllers\Controller;
use App\Models\FeedbackSurvey;
use App\Models\Team;
use App\Services\FontneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FeedbackSurveyController extends Controller
{
    protected $fontneService;

    public function __construct(FontneService $fontneService)
    {
        $this->fontneService = $fontneService;
    }

    public function publicSurvey($token)
    {
        $survey = FeedbackSurvey::where('survey_token', $token)->firstOrFail();

        if ($survey->is_completed) {
            return redirect()->route('sales_mission.surveys.public.view_feedback', ['token' => $token]);
        }

        // Track the view 
        $survey->trackView(); // This will set viewed_at if it's the first time AND always update last_viewed_at

        return view('sales_mission.surveys.public-form', compact('survey'));
    }

    public function submitFeedback(Request $request, $token)
    {
        $survey = FeedbackSurvey::where('survey_token', $token)->firstOrFail();

        if ($survey->is_completed) {
            return redirect()->route('sales_mission.surveys.public.thank_you')->with('info', 'This survey has already been submitted.');
        }

        $validator = Validator::make($request->all(), [
            'contact_salutation' => 'nullable|string|max:10',
            'contact_name' => 'required|string|max:255',
            'contact_job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_mobile' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'decision_maker_status' => 'nullable|string|in:Yes,No,Partial,Unknown',
            'sales_call_outcome' => 'required|string',
            'next_follow_up' => 'required|string',
            'next_follow_up_other' => 'nullable|string|required_if:next_follow_up,Other|max:255',
            'product_interested' => 'nullable|string|max:255',
            'status_lead' => 'required|string',
            'potential_revenue' => 'nullable|string|max:255',
            'key_discussion_points' => 'required|string',
            'has_documentation' => 'nullable|boolean',
            'has_business_card' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $validated = $validator->validated();

        $survey->update([
            'contact_salutation' => $validated['contact_salutation'] ?? $survey->contact_salutation,
            'contact_name' => $validated['contact_name'] ?? $survey->contact_name,
            'contact_job_title' => $validated['contact_job_title'] ?? $survey->contact_job_title,
            'department' => $validated['department'] ?? $survey->department,
            'contact_mobile' => $validated['contact_mobile'] ?? $survey->contact_mobile,
            'contact_email' => $validated['contact_email'] ?? $survey->contact_email,
            'decision_maker_status' => $validated['decision_maker_status'] ?? $survey->decision_maker_status,
            'sales_call_outcome' => $validated['sales_call_outcome'] ?? $survey->sales_call_outcome,
            'next_follow_up' => $validated['next_follow_up'] ?? $survey->next_follow_up,
            'next_follow_up_other' => $validated['next_follow_up_other'] ?? $survey->next_follow_up_other,
            'product_interested' => $validated['product_interested'] ?? $survey->product_interested,
            'status_lead' => $validated['status_lead'] ?? $survey->status_lead,
            'potential_revenue' => $validated['potential_revenue'] ?? $survey->potential_revenue,
            'key_discussion_points' => $validated['key_discussion_points'] ?? $survey->key_discussion_points,
            'has_documentation' => $request->has('has_documentation'),
            'has_business_card' => $request->has('has_business_card'),
            'status' => 'submitted', 
            'submitted_at' => now(),
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // Send notification based on survey type
        if ($survey->survey_type === 'sales_blitz') {
            $this->fontneService->sendSalesBlitzSurveyCompletedNotification($survey);
        } else {
            $this->fontneService->sendFieldVisitSurveyCompletedNotification($survey);
        }

        return redirect()->route('sales_mission.surveys.public.thank_you')->with('success', 'Feedback submitted successfully!');
    }

    public function showSalesBlitzForm()
    {
        $teams = Team::orderBy('name')->get();
        return view('sales_mission.surveys.public-sales-blitz-form', compact('teams'));
    }

    public function submitSalesBlitzForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blitz_team_id' => 'required|exists:teams,id',
            'blitz_company_name' => 'required|string|max:255',
            'blitz_visit_start_datetime' => 'required|date',
            'blitz_visit_end_datetime' => 'required|date|after_or_equal:blitz_visit_start_datetime',
            'contact_salutation' => 'nullable|string|max:10',
            'contact_name' => 'required|string|max:255',
            'contact_job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_mobile' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'decision_maker_status' => 'nullable|string|in:Yes,No,Partial,Unknown',
            'sales_call_outcome' => 'required|string',
            'next_follow_up' => 'required|string',
            'next_follow_up_other' => 'nullable|string|required_if:next_follow_up,Other|max:255',
            'product_interested' => 'nullable|string|max:255',
            'status_lead' => 'required|string',
            'potential_revenue' => 'nullable|string|max:255',
            'key_discussion_points' => 'required|string',
            'has_documentation' => 'nullable|boolean',
            'has_business_card' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $validated = $validator->validated();

        $survey = FeedbackSurvey::create([
            'survey_token' => Str::uuid(),
            'survey_type' => 'sales_blitz',
            'team_assignment_id' => null, 
            'blitz_team_id' => $validated['blitz_team_id'],
            'blitz_company_name' => $validated['blitz_company_name'],
            'blitz_visit_start_datetime' => $validated['blitz_visit_start_datetime'],
            'blitz_visit_end_datetime' => $validated['blitz_visit_end_datetime'],
            'contact_salutation' => $validated['contact_salutation'],
            'contact_name' => $validated['contact_name'],
            'contact_job_title' => $validated['contact_job_title'],
            'department' => $validated['department'],
            'contact_mobile' => $validated['contact_mobile'],
            'contact_email' => $validated['contact_email'],
            'decision_maker_status' => $validated['decision_maker_status'],
            'sales_call_outcome' => $validated['sales_call_outcome'],
            'next_follow_up' => $validated['next_follow_up'],
            'next_follow_up_other' => $validated['next_follow_up_other'] ?? null,
            'product_interested' => $validated['product_interested'],
            'status_lead' => $validated['status_lead'],
            'potential_revenue' => $validated['potential_revenue'],
            'key_discussion_points' => $validated['key_discussion_points'],
            'has_documentation' => $request->has('has_documentation'),
            'has_business_card' => $request->has('has_business_card'),
            'status' => 'submitted', 
            'submitted_at' => now(),
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $this->fontneService->sendSalesBlitzSurveyCompletedNotification($survey);

        return redirect()->route('sales_mission.surveys.public.thank_you')->with('success', 'Sales blitz report submitted successfully!');
    }

    public function publicViewFeedback($token)
    {
        $survey = FeedbackSurvey::where('survey_token', $token)
                                ->with([
                                    'teamAssignment.activity.salesMissionDetail',
                                    'teamAssignment.team',
                                    'blitzTeam' // Eager load blitzTeam relation
                                ])
                                ->firstOrFail(); // Abort with 404 if not found

        return view('sales_mission.surveys.public-view-feedback', compact('survey'));
    }

    public function thankYou()
    {
        return view('sales_mission.surveys.thank-you');
    }
} 