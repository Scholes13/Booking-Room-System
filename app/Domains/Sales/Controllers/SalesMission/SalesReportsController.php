<?php

namespace App\Domains\Sales\Controllers\SalesMission;

use App\Http\Controllers\Controller;
use App\Models\FeedbackSurvey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use Illuminate\Support\Facades\Log;

class SalesReportsController extends Controller
{
    /**
     * Display the survey reports page
     */
    public function surveyReports()
    {
        return view('sales_mission.reports.surveys');
    }

    /**
     * Get survey report data based on filters
     */
    public function getSurveyReportData(Request $request)
    {
        try {
            $query = FeedbackSurvey::with([
                'teamAssignment.team',
                'teamAssignment.activity.salesMissionDetail',
                'blitzTeam'
            ])->where('is_completed', true);

            // Filter by survey type
            if ($request->filled('survey_type')) {
                $query->where('survey_type', $request->survey_type);
            }

            // Filter by date range
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where(function($q) use ($startDate) {
                    $q->where('completed_at', '>=', $startDate)
                      ->orWhere('blitz_visit_start_datetime', '>=', $startDate);
                });
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where(function($q) use ($endDate) {
                    $q->where('completed_at', '<=', $endDate)
                      ->orWhere('blitz_visit_end_datetime', '<=', $endDate);
                });
            }

            // Filter by team
            if ($request->filled('team_id')) {
                $query->where(function($q) use ($request) {
                    $q->where('blitz_team_id', $request->team_id)
                      ->orWhereHas('teamAssignment', function($q) use ($request) {
                          $q->where('team_id', $request->team_id);
                      });
                });
            }

            // Get the data
            $surveys = $query->orderBy('completed_at', 'desc')->get();

            // Prepare statistics
            $statistics = [
                'total_surveys' => $surveys->count(),
                'blitz_surveys' => $surveys->where('survey_type', 'sales_blitz')->count(),
                'token_surveys' => $surveys->where('survey_type', '!=', 'sales_blitz')->count(),
                'status_lead' => $surveys->groupBy('status_lead')->map->count(),
                'decision_maker_status' => $surveys->groupBy('decision_maker_status')->map->count(),
                'next_follow_up' => $surveys->groupBy('next_follow_up')->map->count(),
            ];

            // Format survey data for table
            $surveyData = $surveys->map(function($survey) {
                $companyName = $survey->survey_type === 'sales_blitz' 
                    ? $survey->blitz_company_name 
                    : ($survey->teamAssignment && $survey->teamAssignment->activity && $survey->teamAssignment->activity->salesMissionDetail
                        ? $survey->teamAssignment->activity->salesMissionDetail->company_name 
                        : null);

                $visitDate = $survey->survey_type === 'sales_blitz'
                    ? $survey->blitz_visit_start_datetime
                    : ($survey->teamAssignment && $survey->teamAssignment->activity 
                        ? $survey->teamAssignment->activity->start_datetime 
                        : null);

                return [
                    'id' => $survey->id,
                    'type' => $survey->survey_type,
                    'survey_token' => $survey->survey_token,
                    'company_name' => $companyName,
                    'contact_name' => $survey->contact_name,
                    'contact_job_title' => $survey->contact_job_title,
                    'department' => $survey->department,
                    'contact_mobile' => $survey->contact_mobile,
                    'contact_email' => $survey->contact_email,
                    'team_name' => $survey->survey_type === 'sales_blitz' 
                        ? optional($survey->blitzTeam)->name 
                        : optional(optional($survey->teamAssignment)->team)->name,
                    'visit_date' => $visitDate,
                    'status_lead' => $survey->status_lead,
                    'decision_maker_status' => $survey->decision_maker_status,
                    'next_follow_up' => $survey->next_follow_up,
                    'product_interested' => $survey->product_interested,
                    'potential_revenue' => $survey->potential_revenue,
                    'key_discussion_points' => $survey->key_discussion_points,
                    'submitted_at' => $survey->completed_at,
                ];
            });

            return response()->json([
                'statistics' => $statistics,
                'surveys' => $surveyData
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getSurveyReportData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to fetch report data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export survey report to Excel
     */
    public function exportSurveyReport(Request $request)
    {
        try {
            $query = FeedbackSurvey::with([
                'teamAssignment.team',
                'teamAssignment.activity.salesMissionDetail',
                'blitzTeam'
            ])->where('is_completed', true);

            // Apply filters
            if ($request->filled('survey_type')) {
                $query->where('survey_type', $request->survey_type);
            }

            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where(function($q) use ($startDate) {
                    $q->where('completed_at', '>=', $startDate)
                      ->orWhere('blitz_visit_start_datetime', '>=', $startDate);
                });
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where(function($q) use ($endDate) {
                    $q->where('completed_at', '<=', $endDate)
                      ->orWhere('blitz_visit_end_datetime', '<=', $endDate);
                });
            }

            if ($request->filled('team_id')) {
                $query->where(function($q) use ($request) {
                    $q->where('blitz_team_id', $request->team_id)
                      ->orWhereHas('teamAssignment', function($q) use ($request) {
                          $q->where('team_id', $request->team_id);
                      });
                });
            }

            $surveys = $query->orderBy('completed_at', 'desc')->get();

            // Create new Excel file
            $writer = new Writer();
            
            // Set headers for download
            $fileName = 'survey_report_' . now()->format('Y-m-d_His') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');

            // Create header style
            $headerStyle = (new Style())
                ->setBackgroundColor(Color::BLUE)
                ->setFontColor(Color::WHITE)
                ->setFontBold();

            // Start the file
            $writer->openToBrowser($fileName);

            // Add headers
            $headers = [
                'Team',
                'Visit Date',
                'Survey Type',
                'Company Name',
                'Contact Name',
                'Job Title',
                'Department',
                'Mobile',
                'Email',
                'Point Interest',
                'Status Lead',
                'Decision Maker',
                'Next Follow Up',
                'Product Interest',
                'Potential Revenue',
                'Submitted At'
            ];

            $writer->addRow(Row::fromValues($headers, $headerStyle));

            // Add data rows
            foreach ($surveys as $survey) {
                $companyName = $survey->survey_type === 'sales_blitz'
                    ? $survey->blitz_company_name
                    : ($survey->teamAssignment && $survey->teamAssignment->activity && $survey->teamAssignment->activity->salesMissionDetail
                        ? $survey->teamAssignment->activity->salesMissionDetail->company_name
                        : null);

                $visitDate = null;
                if ($survey->survey_type === 'sales_blitz') {
                    $visitDate = optional($survey->blitz_visit_start_datetime)->format('d M Y');
                } elseif ($survey->teamAssignment && $survey->teamAssignment->activity && $survey->teamAssignment->activity->start_datetime) {
                    $visitDate = Carbon::parse($survey->teamAssignment->activity->start_datetime)->format('d M Y');
                }

                $teamName = $survey->survey_type === 'sales_blitz'
                    ? optional($survey->blitzTeam)->name
                    : optional(optional($survey->teamAssignment)->team)->name;

                $surveyTypeDisplay = $survey->survey_type === 'sales_blitz' ? 'Sales Blitz' : 'Field Visit';

                $writer->addRow(Row::fromValues([
                    $teamName,
                    $visitDate,
                    $surveyTypeDisplay,
                    $companyName,
                    $survey->contact_name,
                    $survey->contact_job_title,
                    $survey->department,
                    $survey->contact_mobile,
                    $survey->contact_email,
                    $survey->sales_call_outcome,
                    $survey->status_lead,
                    $survey->decision_maker_status,
                    $survey->next_follow_up,
                    $survey->product_interested,
                    $survey->potential_revenue,
                    optional($survey->completed_at)->format('d M Y H:i')
                ]));
            }

            $writer->close();
            exit();

        } catch (\Exception $e) {
            Log::error('Error in exportSurveyReport: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Failed to export report: ' . $e->getMessage());
        }
    }
}
