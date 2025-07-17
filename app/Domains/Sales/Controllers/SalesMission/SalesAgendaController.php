<?php

namespace App\Domains\Sales\Controllers\SalesMission;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Employee;
use App\Models\Activity;
use App\Models\SalesMissionDetail;
use App\Models\FeedbackSurvey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;

class SalesAgendaController extends Controller
{
    public function index()
    {
        $teams = Team::with('members')->get();
        return view('sales_mission.reports.agenda', compact('teams'));
    }

    public function generateAgenda(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $team = Team::with(['members', 'activities' => function($query) use ($validated) {
                $query->whereBetween('start_datetime', [
                    Carbon::parse($validated['start_date'])->startOfDay(),
                    Carbon::parse($validated['end_date'])->endOfDay()
                ])->orderBy('start_datetime', 'asc');
            }, 'activities.salesMissionDetail'])->findOrFail($validated['team_id']);

            // Fetch Sales Blitz surveys for the selected team and date range
            $salesBlitzSurveys = FeedbackSurvey::where('survey_type', 'sales_blitz')
                ->where('blitz_team_id', $validated['team_id'])
                ->whereBetween('blitz_visit_start_datetime', [
                    Carbon::parse($validated['start_date'])->startOfDay(),
                    Carbon::parse($validated['end_date'])->endOfDay()
                ])
                ->get();

            $formattedBlitzActivities = $salesBlitzSurveys->map(function($blitzSurvey) {
                $detail = new \stdClass();
                $detail->company_name = $blitzSurvey->blitz_company_name;
                $detail->company_address = $blitzSurvey->department;
                $detail->company_pic = $blitzSurvey->contact_name;
                $detail->company_position = $blitzSurvey->contact_job_title;
                $detail->company_contact = $blitzSurvey->contact_mobile;

                $activity = new \stdClass();
                $activity->id = 'blitz_' . $blitzSurvey->id;
                $activity->start_datetime = $blitzSurvey->blitz_visit_start_datetime;
                $activity->salesMissionDetail = $detail;
                $activity->is_blitz = true;
                $activity->originalBlitzSurvey = $blitzSurvey;
                return $activity;
            });

            // Merge field visit activities and formatted blitz activities
            $allActivities = $team->activities->toBase()->merge($formattedBlitzActivities);
            
            // Sort all activities by start_datetime
            $sortedActivities = $allActivities->sortBy('start_datetime');

            $groupedActivities = $sortedActivities->groupBy(function($activity) {
                return Carbon::parse($activity->start_datetime)->format('Y-m-d');
            });

            return view('sales_mission.reports.agenda_preview', [
                'team' => $team,
                'groupedActivities' => $groupedActivities
            ]);

        } catch (\Exception $e) {
            Log::error('Error in generateAgenda:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Error generating agenda. Please contact support.');
        }
    }

    public function exportAgenda(Request $request)
    {
        ob_start(); // Add output buffering

        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $team = Team::with(['members', 'activities' => function($query) use ($validated) {
                $query->whereBetween('start_datetime', [
                    Carbon::parse($validated['start_date'])->startOfDay(),
                    Carbon::parse($validated['end_date'])->endOfDay()
                ])->orderBy('start_datetime', 'asc');
            }, 'activities.salesMissionDetail'])->findOrFail($validated['team_id']);

            // Fetch Sales Blitz surveys for the selected team and date range
            $salesBlitzSurveys = FeedbackSurvey::where('survey_type', 'sales_blitz')
                ->where('blitz_team_id', $validated['team_id'])
                ->whereBetween('blitz_visit_start_datetime', [
                    Carbon::parse($validated['start_date'])->startOfDay(),
                    Carbon::parse($validated['end_date'])->endOfDay()
                ])
                ->get();

            $formattedBlitzActivities = $salesBlitzSurveys->map(function($blitzSurvey) {
                $detail = new \stdClass();
                $detail->company_name = $blitzSurvey->blitz_company_name;
                $detail->company_address = $blitzSurvey->department;
                $detail->company_pic = $blitzSurvey->contact_name;
                $detail->company_position = $blitzSurvey->contact_job_title;
                $detail->company_contact = $blitzSurvey->contact_mobile;

                $activity = new \stdClass();
                $activity->id = 'blitz_' . $blitzSurvey->id;
                $activity->start_datetime = $blitzSurvey->blitz_visit_start_datetime;
                $activity->salesMissionDetail = $detail;
                $activity->is_blitz = true;
                $activity->originalBlitzSurvey = $blitzSurvey;
                return $activity;
            });

            // Merge and sort for export
            $allActivities = $team->activities->toBase()->merge($formattedBlitzActivities);
            $sortedActivities = $allActivities->sortBy('start_datetime');

            // Group activities by date for export logic (already done for preview)
            // The export logic iterates through sortedActivities directly, but grouping might be needed if Day numbers are reset
            $groupedActivities = $sortedActivities->groupBy(function($activity) {
                return Carbon::parse($activity->start_datetime)->format('Y-m-d');
            });

            $fileName = 'agenda_sales_mission_' . Str::slug($team->name ?? 'team') . '_' . now()->format('Ymd_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            
            // Set options for writer, including column widths
            $options = new Options();
            // $options->DEFAULT_COLUMN_WIDTH = 12; // Default width for other columns if needed
            // $options->setColumnWidth(30, 5); // NAMA INSTANSI / CORPORATE (column F)
            // $options->setColumnWidth(40, 7); // ALAMAT INSTANSI (column H) - Commenting this out again
            // $options->setColumnWidth(25, 8); // PIC (column I)
            // $options->setColumnWidth(25, 9); // POSITION (column J)
            // $options->setColumnWidth(20, 10); // NO TELPON (column K)
            // $options->setColumnWidth(10, 0); // DAY
            // $options->setColumnWidth(15, 1); // TANGGAL
            // $options->setColumnWidth(15, 2); // HARI
            // $options->setColumnWidth(10, 3); // Waktu
            // $options->setColumnWidth(5, 4);  // No
            $options->setColumnWidth(15, 6); // LINE BISNIS (column G) - Uncommented and set to 15

            $writer = new Writer($options);
            $writer->openToFile('php://output');

            $headerStyle = (new Style())->setFontBold();
            $topAlignStyle = (new Style())->setCellVerticalAlignment(CellVerticalAlignment::TOP);
            $headerAndTopAlignStyle = (new Style())->setFontBold()->setCellVerticalAlignment(CellVerticalAlignment::TOP);

            $writer->addRow(Row::fromValues(['AGENDA DINAS SALES MISSION', '', '', '', '', '', 'Date of Create:', Carbon::now()->format('l, j F Y')], $headerStyle)); 
            $writer->addRow(Row::fromValues(['Employee Information'], $topAlignStyle));

            $writer->addRow(Row::fromValues([
                'Name:', $team->name ?? 'N/A', 
                '', 'Department:', $team->department ?? 'PT Werkudara Nirwana Sakti', 
                '', 'Month:', Carbon::parse($validated['start_date'])->format('F Y')
            ]));

            $memberNames = $team->members && $team->members->isNotEmpty() ? $team->members->pluck('name')->implode(', ') : 'No members assigned';
            $writer->addRow(Row::fromValues([
                'Group:', $memberNames, 
                '', 'Purpose:', 'Sales Call Yogyakarta', 
                '', 'Periode:', Carbon::parse($validated['start_date'])->format('j') . ' - ' . Carbon::parse($validated['end_date'])->format('j F Y')
            ], $topAlignStyle));
            
            $writer->addRow(Row::fromValues(['* Agenda disesuaikan dengan kebutuhan acara'], $topAlignStyle));
            $writer->addRow(Row::fromValues([])); 

            $writer->addRow(Row::fromValues([
                'DAY', 'TANGGAL', 'HARI', 'Waktu', 'No',
                'NAMA INSTANSI / CORPORATE', 'LINE BISNIS', 'ALAMAT INSTANSI',
                'PIC', 'POSITION', 'NO TELPON'
            ], $headerAndTopAlignStyle));

            $loopDay = 0;
            $previousDate = null;

            if ($groupedActivities->isNotEmpty()) {
                foreach ($groupedActivities as $date => $activitiesOnDate) {
                    if ($date !== $previousDate) {
                        $loopDay++;
                        $activityNumberThisDay = 1;
                        $previousDate = $date;
                        $isFirstRowOfDate = true;
                    }

                    foreach ($activitiesOnDate as $activity) {
                        $detail = $activity->salesMissionDetail; 
                        $companyName = optional($detail)->company_name ?? '-';
                        if (isset($activity->is_blitz) && $activity->is_blitz) {
                            $companyName = (optional($activity->originalBlitzSurvey)->blitz_company_name ?? 'N/A');
                        }

                        $rowData = [
                            $isFirstRowOfDate ? $loopDay : '',
                            $isFirstRowOfDate ? Carbon::parse($date)->format('d-M-y') : '',
                            $isFirstRowOfDate ? Carbon::parse($date)->format('l') : '',
                            Carbon::parse($activity->start_datetime ?? now())->format('H:i'),
                            $activityNumberThisDay++,
                            $companyName,
                            isset($activity->is_blitz) && $activity->is_blitz ? '' : (optional($detail)->business_line ?? '-'),
                            optional($detail)->company_address ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->department : '-'),
                            optional($detail)->company_pic ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->contact_name : '-'),
                            optional($detail)->company_position ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->contact_job_title : '-'),
                            optional($detail)->company_contact ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->contact_mobile : '-')
                        ];
                        $writer->addRow(Row::fromValues($rowData, $topAlignStyle));
                        $isFirstRowOfDate = false; 
                    }
                }
            } else {
                $writer->addRow(Row::fromValues(['No activities found for the selected period', '', '', '', '', '', '', '', '', '', ''], $topAlignStyle));
            }
            
            $writer->addRow(Row::fromValues([]));

            $writer->addRow(Row::fromValues(['Kensrie Diah Ayuningtyas', '', '', '', '', '', 'Nofri Eka Sanjaya'], $topAlignStyle));
            $writer->addRow(Row::fromValues(['Field Coordinator', '', '', '', '', '', 'Captain Sales Mission'], $topAlignStyle));

            $writer->close();
            ob_end_flush(); // Flush the output buffer
            exit();

        } catch (\Exception $e) {
            Log::error('Error in exportAgenda:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Error exporting agenda. Please contact support.');
        }
    }
} 