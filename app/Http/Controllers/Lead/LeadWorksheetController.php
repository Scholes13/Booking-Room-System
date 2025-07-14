<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\FeedbackSurvey;
use App\Models\LeadWorksheet;
use App\Models\LeadStatusLog;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeadWorksheetController extends Controller
{
    private function getStatusOptions(): array
    {
        return [
            'New', 'Hot', 'Warm', 'Cold', 'Follow-up Required',
            'Contacted', 'Meeting Scheduled', 'Negotiation',
            'Closed - Won', 'Closed - Lost', 'On Hold'
        ];
    }

    private function getFollowUpStatusOptions(): array
    {
        return [
            'COSTING', 'RQ PROPOSAL', 'FU CLIENT', 'QUOTATION SENT', 
            'MATERIALIZED', 'LOST', 'FU VENDOR', 'NOT YET', 'BELUM JADI LEAD'
        ];
    }

    /**
     * Display a listing of the resource.
     * This view is now a read-only grid.
     */
    public function index()
    {
        // Get the authenticated user's employee ID
        $authEmployeeId = null;
        if (Auth::check()) {
            $authEmployee = Employee::where('email', Auth::user()->email)->first();
            $authEmployeeId = $authEmployee ? $authEmployee->id : null;
        }

        // Auto-create and backfill worksheets
        $surveys = FeedbackSurvey::where('is_completed', true)->get();
        foreach ($surveys as $survey) {
            $worksheet = LeadWorksheet::firstOrNew(['feedback_survey_id' => $survey->id]);

            if (!$worksheet->exists) {
                // This is a new worksheet, fill all default values
                $worksheet->fill([
                    'current_status' => $survey->status_lead ?? 'New',
                    'pic_employee_id' => $authEmployeeId,
                    'month_receive_lead' => $survey->completed_at,
                    'requirements' => $survey->sales_call_outcome,
                    'estimated_revenue' => is_numeric($survey->potential_revenue) ? (float) $survey->potential_revenue : null,
                ]);
            } else {
                // This worksheet already exists, only backfill empty fields
                if (empty($worksheet->requirements) && !empty($survey->sales_call_outcome)) {
                    $worksheet->requirements = $survey->sales_call_outcome;
                }
                if (empty($worksheet->estimated_revenue) && !empty($survey->potential_revenue) && is_numeric($survey->potential_revenue)) {
                    $worksheet->estimated_revenue = (float) $survey->potential_revenue;
                }
            }
            $worksheet->save();
        }
        
        $worksheetsQuery = LeadWorksheet::with([
            // Load all necessary nested relationships
            'feedbackSurvey.teamAssignment.activity.salesMissionDetail', 
            'pic:id,name',
            'statusLogs' // Eager load all status logs
        ])
        ->orderBy('created_at', 'desc');

        $worksheets = $worksheetsQuery->get()->map(function ($ws) {
            // Correctly implement the fallback logic for company name
            $companyName = $ws->feedbackSurvey->blitz_company_name 
                         ?? $ws->feedbackSurvey->teamAssignment?->activity?->salesMissionDetail?->company_name 
                         ?? 'N/A';
            
            // Get the latest follow-up note from the logs
            $latestNote = $ws->statusLogs->sortByDesc('created_at')->first()->notes ?? '';

            return [
                'id' => $ws->id,
                'company_name' => $companyName,
                'project_name' => $ws->project_name,
                'service_type' => $ws->service_type ? implode(', ', $ws->service_type) : '',
                'line_of_business' => $ws->line_of_business,
                'status_of_lead' => $ws->feedbackSurvey->status_lead ?? 'N/A',
                'pic_lead' => $ws->pic->name ?? '',
                'follow_up_status' => $ws->follow_up_status,
                'follow_up_note' => $latestNote, // Add the new field here
                'requirements' => $ws->requirements,
                'estimated_revenue' => $ws->estimated_revenue,
                'materialized_revenue' => $ws->materialized_revenue,
                'contact_person' => $ws->feedbackSurvey->contact_name,
                'contact_phone' => $ws->feedbackSurvey->contact_mobile,
                'edit_url' => route('lead.worksheets.edit', $ws->id),
            ];
        });

        return view('lead.worksheets.index', compact('worksheets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeadWorksheet $worksheet)
    {
        $worksheet->load('feedbackSurvey.teamAssignment.team', 'pic', 'statusLogs.user');

        $picOptions = Employee::orderBy('name')->get();
        $serviceTypeOptions = ['Corporate Training', 'Creative', 'Mice', 'Retail', 'Tour and Travel', 'Wellness'];
        $followUpStatusOptions = $this->getFollowUpStatusOptions();
        $statusOptions = $this->getStatusOptions(); // Get main status options

        return view('lead.worksheets.edit', compact('worksheet', 'picOptions', 'serviceTypeOptions', 'followUpStatusOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeadWorksheet $worksheet)
    {
        $mainStatusOptions = $this->getStatusOptions();
        $followUpStatusOptions = $this->getFollowUpStatusOptions();

        $validatedData = $request->validate([
            'project_name' => 'nullable|string|max:255',
            'service_type' => 'nullable|array',
            'line_of_business' => 'nullable|string|max:255',
            'pic_employee_id' => 'nullable|exists:employees,id',
            'current_status' => ['required', 'string', Rule::in($mainStatusOptions)],
            'follow_up_status' => ['nullable', 'string', Rule::in($followUpStatusOptions)],
            'requirements' => 'nullable|string',
            'estimated_revenue' => 'nullable|numeric',
            'materialized_revenue' => 'nullable|numeric',
            'update_note' => ['nullable', 'string', 'max:5000', Rule::requiredIf(function () use ($request, $worksheet) {
                return $request->input('current_status') !== $worksheet->current_status ||
                       $request->input('follow_up_status') !== $worksheet->follow_up_status;
            })],
        ]);

        $oldMainStatus = $worksheet->current_status;
        $newMainStatus = $validatedData['current_status'];
        $oldFollowUpStatus = $worksheet->follow_up_status;
        $newFollowUpStatus = $validatedData['follow_up_status'];
        $note = $validatedData['update_note'] ?? null;

        $logMessage = '';
        $mainStatusChanged = $oldMainStatus !== $newMainStatus;
        $followUpStatusChanged = $oldFollowUpStatus !== $newFollowUpStatus;

        if ($mainStatusChanged) {
            $logMessage .= "Status utama diubah dari '{$oldMainStatus}' menjadi '{$newMainStatus}'. ";
        }
        if ($followUpStatusChanged) {
            $logMessage .= "Status tindak lanjut diubah dari '{$oldFollowUpStatus}' menjadi '{$newFollowUpStatus}'. ";
        }
        
        // Create a log if any status changed OR if a note was provided
        if ($mainStatusChanged || $followUpStatusChanged || !empty($note)) {
            LeadStatusLog::create([
                'lead_worksheet_id' => $worksheet->id,
                'user_id' => Auth::id(),
                'status' => $newMainStatus, // Log the new main status
                'notes' => trim($logMessage . "Catatan: " . $note)
            ]);
        }
        
        // Unset the transient note field before updating the worksheet
        unset($validatedData['update_note']);

        $worksheet->update($validatedData);

        return redirect()->route('lead.worksheets.index')->with('success', 'Worksheet updated successfully.');
    }

    /*
    // OBSOLETE METHODS
    
    public function store(Request $request) {}
    public function show(LeadWorksheet $worksheet) {}
    public function addStatusLog(Request $request, LeadWorksheet $worksheet) {}

    */
}
