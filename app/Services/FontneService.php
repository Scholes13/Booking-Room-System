<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Activity;
use Carbon\Carbon;

class FontneService
{
    public $token;
    public $groupId;
    
    public function __construct()
    {
        // Read token and group ID from environment variables
        $this->token = env('FONNTE_API_TOKEN', '1iGkDo5NqN8icMMG4XW4');
        $this->groupId = env('FONNTE_GROUP_ID', '120363416512791187@g.us');
    
    }
    
    /**
     * Send notification when a new Sales Mission activity is created.
     *
     * @param Activity $activity The newly created activity.
     * @return array API response
     */
    public function sendNewSalesMissionNotification(Activity $activity)
    {
        try {
            $companyName = $activity->salesMissionDetail ? $activity->salesMissionDetail->company_name : 'N/A';
            $companyAddress = $activity->salesMissionDetail ? $activity->salesMissionDetail->company_address : 'N/A';
            $activityCreatorName = $activity->name; // This is the employee name from the activity model
            $city = $activity->city ?? 'N/A';
            $province = $activity->province ?? 'N/A';
            $description = $activity->description ?? 'N/A';
            
            // New date and time formatting
            $activityDate = Carbon::parse($activity->start_datetime)->format('d M Y');
            $startTime = Carbon::parse($activity->start_datetime)->format('H:i');
            $endTime = Carbon::parse($activity->end_datetime)->format('H:i');
            $timeRange = "{$startTime} - {$endTime}";

            $message = "📣 *SALES MISSION BARU DIBUAT* 📣\n\n";
            $message .= "Sebuah Sales Mission baru telah ditambahkan:\n\n";
            $message .= " Pembuat appointment: *{$activityCreatorName}*\n";
            $message .= " Nama Perusahaan: *{$companyName}*\n";
            $message .= " Alamat Perusahaan: {$companyAddress}\n";
            $message .= " Provinsi: {$province}\n";
            $message .= " Kota: {$city}\n";
            $message .= " Tanggal: {$activityDate}\n"; // Changed to Tanggal
            $message .= " Jam: {$timeRange}\n"; // Changed to Jam with range
            $message .= " Deskripsi: {$description}\n\n";
            $message .= "Terimakasih, semangat semangat semangat!";
            
            $specificGroupId = '120363418930872751@g.us';

            Log::info('Sending new sales mission notification for activity ID: ' . $activity->id . ' to Group ID: ' . $specificGroupId);
            return $this->sendWhatsAppMessage($specificGroupId, $message);
        } catch (\Exception $e) {
            Log::error('Error sending new sales mission notification: ' . $e->getMessage(), ['activity_id' => $activity->id]);
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send notification when a survey/feedback form is completed
     * 
     * @param object $survey Completed survey
     * @return array API response
     */
    public function sendSurveyCompletedNotification($survey)
    {
        try {
            // Get team assignment details
            $teamAssignment = $survey->teamAssignment;
            $company = $teamAssignment->activity->salesMissionDetail->company_name;
            $teamName = $teamAssignment->team->name;
            // Get absolute URL for survey details
            $visitUrl = route('sales_mission.surveys.show', $survey->id, true);
            
            // Compose message
            $message = "✅ *SURVEY COMPLETED* ✅\n\n";
            $message .= "Team *{$teamName}* has completed the feedback form for:\n\n";
            $message .= "Company: *{$company}*\n\n";
            $message .= "📊 *Key Info*\n";
            $message .= "• Contact Person: {$survey->contact_name}\n";
            $message .= "• Job Title: {$survey->contact_job_title}\n";
            $message .= "• Nomor: {$survey->contact_mobile}\n";
            $message .= "• Email: {$survey->contact_email}\n";
            $message .= "• Sales Call Outcome: {$survey->sales_call_outcome}\n";
            $message .= "• Next follow up action: {$survey->next_follow_up}\n";
            $message .= "• Status Lead: {$survey->status_lead}\n\n";
            $message .= "View complete details here:\n";
            // Make sure URL is on its own line with no formatting around it for WhatsApp to make it clickable
            $message .= "{$visitUrl}\n";
            
            return $this->sendWhatsAppMessage($this->groupId, $message);
        } catch (\Exception $e) {
            Log::error('Error sending survey completed notification: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send a test message with a clickable link
     * 
     * @return array API response
     */
    public function sendTestMessageWithLink()
    {
        try {
            $appUrl = config('app.url', 'http://localhost');
            // Replace localhost with your actual server URL in production
            $testUrl = $appUrl . '/test-fonnte-page';
            
            $message = "🧪 *TEST MESSAGE WITH CLICKABLE LINK* 🧪\n\n";
            $message .= "This is a test message from your Booking-Room-System.\n\n";
            $message .= "Here is a test link that should be clickable:\n";
            $message .= "{$testUrl}\n\n";
            $message .= "Time sent: " . now()->format('d M Y H:i:s');
            
            return $this->sendWhatsAppMessage($this->groupId, $message);
        } catch (\Exception $e) {
            Log::error('Error sending test message: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send notification when a survey is viewed
     * 
     * @param object $survey Survey model
     * @return array API response
     */
    public function sendSurveyViewedNotification($survey)
    {
        try {
            // Get team assignment details
            $teamAssignment = $survey->teamAssignment;
            $company = $teamAssignment->activity->salesMissionDetail->company_name;
            $teamName = $teamAssignment->team->name;
            $formUrl = route('sales_mission.surveys.public.form', $survey->survey_token, true);
            
            // Compose message
            $message = "👁️ *SURVEY FORM OPENED* 👁️\n\n";
            $message .= "The feedback form for team *{$teamName}*'s visit to *{$company}* has been opened.\n\n";
            $message .= "Time: " . now()->format('d M Y H:i:s') . "\n";
            $message .= "Status: Not yet submitted\n\n";
            $message .= "Form link:\n";
            $message .= "{$formUrl}\n";
            
            return $this->sendWhatsAppMessage($this->groupId, $message);
        } catch (\Exception $e) {
            Log::error('Error sending survey viewed notification: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send notification when a Sales Blitz survey/feedback form is completed
     *
     * @param object $survey Completed survey (type 'sales_blitz')
     * @return array API response
     */
    public function sendSalesBlitzSurveyCompletedNotification($survey)
    {
        try {
            // Get Sales Blitz specific details
            $teamName = $survey->blitzTeam ? $survey->blitzTeam->name : $survey->blitz_team_name;
            $companyName = $survey->blitz_company_name;
            
            // Get absolute URL for public survey view
            // IMPORTANT: Use the public view route, not the admin one
            $viewUrl = route('sales_mission.surveys.public.view_feedback', ['token' => $survey->survey_token], true);
            
            // Compose message
            $message = "⚡️ *SALES BLITZ REPORT COMPLETED* ⚡️\n\n";
            $message .= "Sales Blitz report submitted by: *{$teamName}*\n";
            $message .= "Company Visited: *{$companyName}*\n\n";
            $message .= "📊 *Key Info*\n";
            $message .= "• Contact Person: {$survey->contact_name}\n";
            $message .= "• Job Title: {$survey->contact_job_title}\n";
            $message .= "• Nomor: {$survey->contact_mobile}\n";
            $message .= "• Email: {$survey->contact_email}\n";
            $message .= "• Sales Call Outcome / point interest: {$survey->sales_call_outcome}\n";
            $message .= "• Next follow up action: {$survey->next_follow_up}\n";
            $message .= "• Status Lead: {$survey->status_lead}\n\n";
            $message .= "View complete report here:\n";
            $message .= "{$viewUrl}\n";
            
            return $this->sendWhatsAppMessage($this->groupId, $message);
        } catch (\Exception $e) {
            Log::error('Error sending Sales Blitz survey completed notification: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send notification when a standard Field Visit survey/feedback form is completed
     *
     * @param object $survey Completed survey (type 'field_visit')
     * @return array API response
     */
    public function sendFieldVisitSurveyCompletedNotification($survey)
    {
        try {
            // Ensure teamAssignment and related data is loaded
            $survey->loadMissing(['teamAssignment.team', 'teamAssignment.activity.salesMissionDetail']);

            if (!$survey->teamAssignment || !$survey->teamAssignment->activity || !$survey->teamAssignment->activity->salesMissionDetail) {
                Log::error('Missing data for field visit survey notification.', ['survey_id' => $survey->id]);
                return [
                    'status' => false,
                    'error' => 'Missing related data for notification.'
                ];
            }

            // Get team assignment details
            $teamAssignment = $survey->teamAssignment;
            $companyName = $teamAssignment->activity->salesMissionDetail->company_name;
            $teamName = $teamAssignment->team->name;
            
            // Get absolute URL for public survey view
            $viewUrl = route('sales_mission.surveys.public.view_feedback', ['token' => $survey->survey_token], true);
            
            // Compose message
            $message = "✅ *FIELD VISIT REPORT COMPLETED* ✅\n\n";
            $message .= "Team *{$teamName}* has completed the feedback form for their visit to:\n";
            $message .= "Company: *{$companyName}*\n\n";
            $message .= "📊 *Key Info*\n";
            $message .= "• Contact Person: {$survey->contact_name}\n";
            $message .= "• Job Title: {$survey->contact_job_title}\n";
            $message .= "• Nomor: {$survey->contact_mobile}\n";
            $message .= "• Email: {$survey->contact_email}\n";
            $message .= "• Sales Call Outcome: {$survey->sales_call_outcome}\n";
            $message .= "• Next follow up action: {$survey->next_follow_up}\n";
            $message .= "• Status Lead: {$survey->status_lead}\n\n";
            $message .= "View complete report here:\n";
            $message .= "{$viewUrl}\n";
            
            return $this->sendWhatsAppMessage($this->groupId, $message);
        } catch (\Exception $e) {
            Log::error('Error sending Field Visit survey completed notification: ' . $e->getMessage(), ['survey_id' => $survey->id, 'exception' => $e]);
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send a WhatsApp message using Fonnte API
     * 
     * @param string $to Target number or group ID
     * @param string $message Message content
     */
    public function sendWhatsAppMessage($to, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.fonnte.com/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
            'target' => $to,
            'message' => $message,
            // 'countryCode' => '62', //optional
          ),
          CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $this->token //change TOKEN to your actual token
          ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            Log::error('Fonnte API cURL Error: ' . $error_msg);
            return ['status' => false, 'error' => 'cURL Error: ' . $error_msg, 'http_code' => $httpcode];
        }

        curl_close($curl);
        Log::info('Fonnte API Response: ', ['response' => $response, 'http_code' => $httpcode]);
        
        $decodedResponse = json_decode($response, true);
        if ($httpcode >= 200 && $httpcode < 300 && isset($decodedResponse['status']) && $decodedResponse['status'] === true) {
            return array_merge(['status' => true], $decodedResponse);
        } else {
            return array_merge(['status' => false, 'error' => 'API Error', 'http_code' => $httpcode], $decodedResponse ?? []);
        }
    }

    /**
     * Send notification when an activity schedule is updated for an assigned team.
     *
     * @param \App\Models\TeamAssignment $teamAssignment
     * @return array API response
     */
    public function sendScheduleUpdateNotification($teamAssignment)
    {
        try {
            // Ensure related data is loaded
            $teamAssignment->loadMissing(['team', 'activity.salesMissionDetail', 'feedbackSurvey']);

            if (!$teamAssignment->team || !$teamAssignment->activity || !$teamAssignment->activity->salesMissionDetail) {
                Log::error('Missing data for schedule update notification.', ['team_assignment_id' => $teamAssignment->id]);
                return [
                    'status' => false,
                    'error' => 'Missing related data for notification.'
                ];
            }

            $teamName = $teamAssignment->team->name;
            $activity = $teamAssignment->activity;
            $salesMissionDetail = $activity->salesMissionDetail;
            
            $companyName = $salesMissionDetail->company_name;
            $contactPic = $salesMissionDetail->company_pic;
            $newStartTime = $activity->start_datetime->format('d M Y H:i');
            $newEndTime = $activity->end_datetime->format('H:i');

            $feedbackUrl = '#'; // Default if no survey
            if ($teamAssignment->feedbackSurvey) {
                $feedbackUrl = route('sales_mission.surveys.public.form', $teamAssignment->feedbackSurvey->survey_token, true);
            }

            $fieldVisitsUrl = route('sales_mission.field-visits.index', [], true);

            $message = "🔔 *UPDATE JADWAL TIM* 🔔\n\n";
            $message .= "Jadwal untuk tim *{$teamName}* pada Sales Mission berikut telah diperbarui:\n\n";
            $message .= "🗓️ *Detail Kunjungan*\n";
            $message .= "Perusahaan: *{$companyName}*\n";
            $message .= "Kontak PIC: {$contactPic}\n";
            $message .= "Jadwal Baru: {$newStartTime} - {$newEndTime}\n\n";
            
            if ($teamAssignment->feedbackSurvey) {
                 $message .= "📝 *Link Form Feedback*\n";
                 $message .= "{$feedbackUrl}\n\n";
            }
           
            $message .= "Untuk detail penugasan dan jadwal tim lainnya, silakan cek:\n";
            $message .= "{$fieldVisitsUrl}";

            return $this->sendWhatsAppMessage($this->groupId, $message);

        } catch (\Exception $e) {
            Log::error('Error sending schedule update notification: ' . $e->getMessage(), [
                'team_assignment_id' => $teamAssignment->id ?? null,
                'exception' => $e
            ]);
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
} 