<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FontneService
{
    public $token;
    public $groupId;
    
    public function __construct()
    {
        // Read token and group ID from environment variables
        $this->token = env('FONNTE_API_TOKEN', '1iGkDo5NqN8icMMG4XW4');
        $this->groupId = env('FONNTE_GROUP_ID', '120363420621774077@g.us');
    
    }
    
    /**
     * Send notification when a team is assigned to an activity
     * 
     * @param object $teamAssignment Team assignment model
     * @param object $survey Survey model
     * @return array API response
     */
    public function sendTeamAssignmentNotification($teamAssignment, $survey)
    {
        try {
            // Prepare the appointment data
            $company = $teamAssignment->activity->salesMissionDetail->company_name;
            $contact = $teamAssignment->activity->salesMissionDetail->company_pic;
            $startTime = $teamAssignment->activity->start_datetime->format('d M Y H:i');
            $endTime = $teamAssignment->activity->end_datetime->format('H:i');
            $teamName = $teamAssignment->team->name;
            // Get absolute URL for the feedback form
            $feedbackUrl = route('sales_mission.surveys.public.form', $survey->survey_token, true);
            
            // Compose message
            $message = "🔔 *TEAM ASSIGNMENT NOTIFICATION* 🔔\n\n";
            $message .= "Team *{$teamName}* has been assigned to:\n\n";
            $message .= "📋 *Meeting Details*\n";
            $message .= "Company: *{$company}*\n";
            $message .= "Contact: {$contact}\n";
            $message .= "Schedule: {$startTime} - {$endTime}\n\n";
            $message .= "📝 *Feedback Form*\n";
            // Make sure URL is on its own line with no formatting around it for WhatsApp to make it clickable
            $message .= "{$feedbackUrl}\n";
            
            return $this->sendWhatsAppMessage($this->groupId, $message);
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp notification: ' . $e->getMessage());
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
            $message .= "Team *{$teamName}* has completed the feedback form for:\n";
            $message .= "Company: *{$company}*\n\n";
            $message .= "📊 *Survey Results*\n";
            $message .= "• Contact Person: {$survey->contact_name}\n";
            $message .= "• Sales Call Outcome: {$survey->sales_call_outcome}\n";
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
            $message .= "• Sales Call Outcome: {$survey->sales_call_outcome}\n";
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
            $message .= "• Sales Call Outcome: {$survey->sales_call_outcome}\n";
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
     * @return array API response
     */
    public function sendWhatsAppMessage($to, $message)
    {
        // Log the target just before sending
        Log::info('[FontneService] sendWhatsAppMessage - Attempting to send to: ' . $to);
        Log::info('[FontneService] sendWhatsAppMessage - Current $this->groupId: ' . $this->groupId);

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
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->token
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        // Log the response
        Log::info('Fonnte API response: ' . $response);
        
        return json_decode($response, true);
    }
} 