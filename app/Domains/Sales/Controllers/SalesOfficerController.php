<?php

namespace App\Domains\Sales\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\SalesMissionDetail;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use App\Http\Requests\SalesOfficerActivityRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\ActivityLogService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActivitiesExport;
use App\Models\SalesOfficerActivity;
use App\Models\SalesOfficerContact;
use App\Models\CompanyDivision;
use App\Models\ContactPerson;
use Illuminate\Support\Facades\Schema;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Division;
use App\Models\PIC;
use Illuminate\Support\Facades\Auth;

class SalesOfficerController extends Controller
{
    /**
     * Display the Sales Officer Dashboard.
     */
    public function dashboard()
    {
        // Total activities for the current month - Using dedicated Sales Officer table
        $currentMonthActivities = SalesOfficerActivity::whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->where('user_id', auth()->id())
            ->count();
            
        // Total sales missions - For reference only
        $totalSalesMissions = Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->count();
            
        // Recent activities - Using dedicated Sales Officer table
        $recentActivities = SalesOfficerActivity::where('user_id', auth()->id())
            ->orderBy('start_datetime', 'desc')
            ->limit(5)
            ->get();
            
        // Activities by month (for chart) - Using dedicated Sales Officer table
        $activitiesByMonth = SalesOfficerActivity::select(DB::raw('MONTH(start_datetime) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('start_datetime', now()->year)
            ->where('user_id', auth()->id())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $chartData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            $count = 0;
            
            foreach ($activitiesByMonth as $data) {
                if ($data->month == $monthNumber) {
                    $count = $data->count;
                    break;
                }
            }
            
            $chartData[] = [
                'month' => $month,
                'count' => $count
            ];
        }
        
        return view('sales_officer.dashboard.index', compact(
            'currentMonthActivities',
            'totalSalesMissions',
            'recentActivities',
            'chartData'
        ));
    }

    /**
     * Display a listing of activities for Sales Officer.
     */
    public function activitiesIndex(Request $request)
    {
        $query = SalesOfficerActivity::with(['department', 'contact'])
            ->where('user_id', auth()->id()); // Only show logged in user's activities
            
        // Filter by search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('contact', function($sq) use ($searchTerm) {
                    $sq->where('company_name', 'like', "%{$searchTerm}%")
                       ->orWhere('contact_name', 'like', "%{$searchTerm}%")
                       ->orWhere('phone_number', 'like', "%{$searchTerm}%");
                })
                ->orWhere('description', 'like', "%{$searchTerm}%")
                ->orWhere('title', 'like', "%{$searchTerm}%")
                ->orWhere('city', 'like', "%{$searchTerm}%")
                ->orWhere('province', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_datetime', [$request->start_date, $request->end_date]);
        }
        
        $activities = $query->orderBy('start_datetime', 'desc')->paginate(15);
        
        return view('sales_officer.activities.index', compact('activities'));
    }

    /**
     * Show form to create new activity.
     */
    public function createActivity()
    {
        // Get departments and employees for dropdown
        $departments = Department::all();
        $contacts = SalesOfficerContact::where('user_id', auth()->id())
                      ->orWhereNotNull('sales_mission_detail_id')
                      ->orderBy('company_name', 'asc')
                            ->get();

        // Activity types for Sales Officer
        $activityTypes = [
            'Event Networking',
            'Meeting',
            'Negotiation',
            'Presentation - Introduction & Compro',
            'Presentation - Pitching',
            'Sales Call',
            'Telemarketing',
            'Telemarketing - Email',
            'Telemarketing - LinkedIn',
            'Telemarketing - Phone/WhatsApp',
            'Werkudara Client Event'
        ];

        // Data of provinces and cities (example data)
        $provinces = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan',
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 'DKI Jakarta',
            'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 'Bali',
            'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
            'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara',
            'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo',
            'Sulawesi Barat', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat'
        ];

        return view('sales_officer.activities.create', compact('departments', 'contacts', 'activityTypes', 'provinces'));
    }

    /**
     * Store a newly created activity in storage.
     */
    public function storeActivity(SalesOfficerActivityRequest $request)
    {
        try {
            // Log the entire request for debugging
            \Illuminate\Support\Facades\Log::info('Activity form submission', [
                'request_data' => $request->all()
            ]);
            
            // Double check datetime values
            $startDatetime = $request->start_datetime;
            $endDatetime = $request->end_datetime;
            
            \Illuminate\Support\Facades\Log::info('Datetime values after FormRequest processing', [
                'start_datetime' => $startDatetime,
                'end_datetime' => $endDatetime
            ]);
            
            // No need to validate here - the form request handles validation
            $validated = $request->validated();
            
            // Menggunakan DB transaction untuk memastikan konsistensi data
            return \DB::transaction(function() use ($request, $startDatetime, $endDatetime) {
                // STEP 1: Process the company
                $contact_id = null;
                $visit_count = 1;
                $contact = null;
                
                if ($request->company_selector === 'new') {
                    // Create a new company contact
                    $contact = SalesOfficerContact::create([
                        'user_id' => auth()->id(),
                        'company_name' => $request->company_name,
                        'line_of_business' => $request->line_of_business,
                        'company_address' => $request->company_address,
                        'country' => $request->country,
                        'province' => $request->province,
                        'city' => $request->city,
                        'general_information' => $request->general_information,
                        'current_event' => $request->current_event,
                        'target_business' => $request->target_business,
                        'project_type' => $request->project_type,
                        'project_estimation' => $request->project_estimation,
                        'potential_revenue' => $request->potential_revenue,
                        'potential_project_count' => $request->potential_project_count,
                        'visit_count' => 1,
                        'status' => 'active',
                    ]);
                    $contact_id = $contact->id;
                    \Illuminate\Support\Facades\Log::info('Created new company successfully', [
                        'contact_id' => $contact_id,
                        'company_name' => $request->company_name
                    ]);
                } else {
                    // Use existing company and increment visit count
                    $contact_id = $request->company_selector;
                    $contact = SalesOfficerContact::findOrFail($contact_id);
                    $contact->incrementVisitCount();
                    $visit_count = $contact->visit_count;
                    
                    // Update company details
                    $contact->update([
                        'line_of_business' => $request->line_of_business,
                        'company_address' => $request->company_address,
                        'country' => $request->country,
                        'province' => $request->province,
                        'city' => $request->city,
                        'general_information' => $request->general_information,
                        'current_event' => $request->current_event,
                        'target_business' => $request->target_business,
                        'project_type' => $request->project_type,
                        'project_estimation' => $request->project_estimation,
                        'potential_revenue' => $request->potential_revenue,
                        'potential_project_count' => $request->potential_project_count,
                    ]);
                }

                // STEP 2: Process the division
                $division_id = null;
                $division_visit_count = 1;
                
                if ($request->filled('division_selector')) {
                    if ($request->division_selector === 'new' && $request->filled('division_name')) {
                        // Create a new division
                        $division = CompanyDivision::create([
                            'contact_id' => $contact_id,
                            'name' => $request->division_name,
                            'visit_count' => 1,
                        ]);
                        $division_id = $division->id;
                    } elseif ($request->division_selector !== 'new' && $request->division_selector !== '') {
                        // Use existing division and increment visit count
                        $division_id = $request->division_selector;
                        $division = CompanyDivision::findOrFail($division_id);
                        $division->incrementVisitCount();
                        $division_visit_count = $division->visit_count;
                    }
                }
                
                // STEP 3: Process the PIC
                $pic_id = null;
                
                if ($request->pic_selector === 'new') {
                    // Ensure PIC name is not empty
                    if (empty($request->pic_name)) {
                        throw new \Exception('PIC name cannot be empty');
                    }
                    
                    // Create a new PIC
                    $pic = ContactPerson::create([
                        'contact_id' => $contact_id,
                        'division_id' => $division_id,
                        'title' => $request->pic_title ?? 'Mr',
                        'name' => $request->pic_name,
                        'position' => $request->position,
                        'phone_number' => $request->filled('pic_phone') ? $request->pic_phone : '0',
                        'email' => $request->filled('pic_email') ? $request->pic_email : 'blank@werkudara.com',
                        // Auto-set as primary if it's the first PIC for this company
                        'is_primary' => ContactPerson::where('contact_id', $contact_id)->count() == 0,
                        'source' => 'Activity'
                    ]);
                    $pic_id = $pic->id;
                } else if ($request->pic_selector && $request->pic_selector !== 'new') {
                    // Use existing PIC
                    $pic_id = $request->pic_selector;
                    // Update position if needed
                    ContactPerson::where('id', $pic_id)->update([
                        'position' => $request->position
                    ]);
                } else {
                    // No PIC selected, create a default one
                    $pic = ContactPerson::create([
                        'contact_id' => $contact_id,
                        'division_id' => $division_id,
                        'title' => 'Mr',
                        'name' => 'Unnamed PIC',
                        'position' => $request->position,
                        'phone_number' => '0',
                        'email' => 'blank@werkudara.com',
                        'is_primary' => ContactPerson::where('contact_id', $contact_id)->count() == 0,
                        'source' => 'Activity'
                    ]);
                    $pic_id = $pic->id;
                }
                
                // STEP 4: Create the activity
                $activity = SalesOfficerActivity::create([
                    'title' => 'Visit to ' . $contact->company_name,
                    'user_id' => auth()->id(),
                    'activity_type' => $request->activity_type,
                    'meeting_type' => $request->meeting_type,
                    'description' => $request->general_information . ' ' . 
                                ($request->current_event ? 'Current Event: ' . $request->current_event . ' ' : '') . 
                                ($request->target_business ? 'Target Business: ' . $request->target_business . ' ' : '') . 
                                ($request->project_type ? 'Project Type: ' . $request->project_type . ' ' : '') . 
                                ($request->project_estimation ? 'Project / Tender: ' . $request->project_estimation . ' ' : '') . 
                                ($request->potential_revenue ? 'Potential Revenue: Rp ' . number_format((float)$request->potential_revenue, 0, ',', '.') . ' ' : '') . 
                                ($request->potential_project_count ? 'Potential Projects: ' . $request->potential_project_count : ''),
                    'country' => $request->country,
                    'province' => $request->province,
                    'city' => $request->city,
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'start_datetime' => $startDatetime,
                    'end_datetime' => $endDatetime,
                    'month_number' => $request->month_number,
                    'week_number' => $request->week_number,
                    'contact_id' => $contact_id,
                    'division_id' => $division_id,
                    'pic_id' => $pic_id,
                    'account_status' => $request->account_status,
                    'products_discussed' => $request->products_discussed ?? 1,
                    'status' => $request->status,
                    'next_follow_up' => $request->next_follow_up,
                    'follow_up_type' => $request->follow_up_type,
                    'follow_up_frequency' => $request->follow_up_frequency,
                    'jso_lead_status' => $request->jso_lead_status,
                ]);
                
                // Log the activity creation
                ActivityLogService::logCreate(
                    'sales_officer_activities',
                    'Created new activity: Visit to ' . $contact->company_name,
                    [
                        'activity_id' => $activity->id,
                        'type' => $request->activity_type,
                        'company' => $contact->company_name,
                        'start_date' => $startDatetime,
                    ]
                );
                
                \Illuminate\Support\Facades\Log::info('Activity created successfully', [
                    'activity_id' => $activity->id,
                    'contact_id' => $contact_id
                ]);
                
                return redirect()->route('sales_officer.activities.index')
                    ->with('success', 'Activity created successfully.');
            });
        } catch (\Exception $e) {
            // Log error and return with error message
            \Illuminate\Support\Facades\Log::error('Error in activity creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->withErrors(['general_error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Show form to edit an activity.
     */
    public function editActivity($id)
    {
        $activity = SalesOfficerActivity::with(['contact', 'division', 'pic'])->findOrFail($id);
        $departments = Department::all();
        
        // Activity types for Sales Officer
        $activityTypes = [
            'Event Networking',
            'Meeting',
            'Negotiation',
            'Presentation - Introduction & Compro',
            'Presentation - Pitching',
            'Sales Call',
            'Telemarketing',
            'Telemarketing - Email',
            'Telemarketing - LinkedIn',
            'Telemarketing - Phone/WhatsApp',
            'Werkudara Client Event'
        ];
        
        // Data of provinces
        $provinces = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan',
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 'DKI Jakarta',
            'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 'Bali',
            'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
            'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara',
            'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo',
            'Sulawesi Barat', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat'
        ];
        
        // Get contacts for dropdown in case user wants to change company
        $contacts = SalesOfficerContact::where('user_id', auth()->id())
                    ->orWhereNotNull('sales_mission_detail_id')
                    ->orderBy('company_name', 'asc')
                    ->get();
        
        return view('sales_officer.activities.edit', compact('activity', 'departments', 'activityTypes', 'provinces', 'contacts'));
    }

    /**
     * Update the specified activity in storage.
     */
    public function updateActivity(SalesOfficerActivityRequest $request, $id)
    {
        try {
            // No need to validate here - the form request handles validation
            $validated = $request->validated();
            
            // Find the activity
            $activity = SalesOfficerActivity::findOrFail($id);
            
            // Process dates
            $next_follow_up = $request->next_follow_up;
            
            // Use transaction to ensure data consistency
            return \DB::transaction(function() use ($request, $activity, $next_follow_up, $id) {
                // Update the PIC's position if needed
                if ($activity->pic_id) {
                    ContactPerson::where('id', $activity->pic_id)->update([
                        'position' => $request->position,
                        'phone_number' => $request->filled('pic_phone') ? $request->pic_phone : '0',
                        'email' => $request->filled('pic_email') ? $request->pic_email : 'blank@werkudara.com'
                    ]);
                }
                
                // Update the contact's business details
                if ($activity->contact_id) {
                    $contact = SalesOfficerContact::findOrFail($activity->contact_id);
                    $contact->update([
                        'company_address' => $request->company_address,
                        'country' => $request->country,
                        'province' => $request->province,
                        'city' => $request->city,
                        'general_information' => $request->general_information,
                        'current_event' => $request->current_event,
                        'target_business' => $request->target_business,
                        'project_type' => $request->project_type,
                        'project_estimation' => $request->project_estimation,
                        'potential_revenue' => $request->potential_revenue,
                        'potential_project_count' => $request->potential_project_count,
                    ]);
                }
                
                // Update the activity
                $activity->update([
                    'title' => 'Visit to ' . $contact->company_name,
                    'activity_type' => $request->activity_type,
                    'meeting_type' => $request->meeting_type,
                    'description' => $request->general_information . ' ' . 
                                   ($request->current_event ? 'Current Event: ' . $request->current_event . ' ' : '') . 
                                   ($request->target_business ? 'Target Business: ' . $request->target_business . ' ' : '') . 
                                   ($request->project_type ? 'Project Type: ' . $request->project_type . ' ' : '') . 
                                   ($request->project_estimation ? 'Project / Tender: ' . $request->project_estimation . ' ' : '') . 
                                   ($request->potential_revenue ? 'Potential Revenue: Rp ' . number_format($request->potential_revenue, 0, ',', '.') . ' ' : '') . 
                                   ($request->potential_project_count ? 'Potential Projects: ' . $request->potential_project_count : ''),
                    'country' => $request->country,
                    'province' => $request->province,
                    'city' => $request->city,
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'start_datetime' => $request->start_datetime,
                    'end_datetime' => $request->end_datetime,
                    'month_number' => $request->month_number,
                    'week_number' => $request->week_number,
                    'account_status' => $request->account_status,
                    'products_discussed' => $request->products_discussed,
                    'status' => $request->status,
                    'next_follow_up' => $next_follow_up,
                    'follow_up_type' => $request->follow_up_type,
                    'follow_up_frequency' => $request->follow_up_frequency,
                    'jso_lead_status' => $request->jso_lead_status,
                ]);
                
                // Log the activity update
                ActivityLogService::logUpdate(
                    'sales_officer_activities',
                    'Updated activity: ' . $activity->title,
                    [
                        'activity_id' => $activity->id,
                        'type' => $request->activity_type,
                        'company' => $activity->contact ? $activity->contact->company_name : 'N/A',
                        'start_date' => $request->start_datetime,
                    ]
                );
                
                return redirect()->route('sales_officer.activities.index')
                    ->with('success', 'Activity updated successfully.');
            });
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Error updating activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'activity_id' => $id
            ]);
            
            return redirect()->back()->withInput()->withErrors(['general_error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroyActivity($id)
    {
        $activity = SalesOfficerActivity::findOrFail($id);
        $activityTitle = $activity->title;
        
        // Log the activity deletion
        ActivityLogService::logDelete(
            'sales_officer_activities',
            'Deleted activity: ' . $activityTitle,
            [
                'activity_id' => $activity->id,
                'type' => $activity->activity_type,
            ]
        );
        
        // Delete the activity
        $activity->delete();
        
        return redirect()->route('sales_officer.activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    /**
     * Display calendar view.
     */
    public function calendar()
    {
        return view('sales_officer.calendar');
    }
    
    /**
     * Get calendar events as JSON.
     */
    public function calendarEvents(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        
        $activities = Activity::whereBetween('start_datetime', [$start, $end])
            ->get();
            
        $events = [];
        
        foreach ($activities as $activity) {
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $activity->start_datetime->format('Y-m-d H:i:s'),
                'end' => $activity->end_datetime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#10b981', // Green color for Sales Officer
                'borderColor' => '#059669',
                'textColor' => '#ffffff',
                'description' => $activity->description,
                'location' => $activity->city . ', ' . $activity->province,
                'classNames' => ['sales-officer-event'],
                'extendedProps' => [
                    'type' => $activity->activity_type
                ]
            ];
        }
        
        return response()->json($events);
    }
    
    /**
     * Display reports page.
     */
    public function reports()
    {
        return view('sales_officer.reports.index');
    }
    
    /**
     * Get report data.
     */
    public function getReportData(Request $request)
    {
        $query = Activity::query();
        
        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_datetime', [$request->start_date, $request->end_date]);
        }
        
        // Filter by activity type
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        
        $activities = $query->get();
        
        return response()->json([
            'activities' => $activities
        ]);
    }
    
    /**
     * Export reports.
     */
    public function exportReport(Request $request)
    {
        // Get parameters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $activityType = $request->input('activity_type');
        
        // Build query based on parameters
        $query = Activity::with(['department', 'salesMissionDetail']);
        
        if ($startDate && $endDate) {
            $query->whereBetween('start_datetime', [$startDate, $endDate]);
        }
        
        if ($activityType) {
            $query->where('activity_type', $activityType);
        }
        
        $activities = $query->orderBy('start_datetime', 'desc')->get();
        
        // Generate Excel file
        $export = new ActivitiesExport($activities);
        return Excel::download($export, 'activities_report_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Display a listing of contacts.
     */
    public function contactsIndex(Request $request)
    {
        // Try to import any new Sales Mission contacts first
        $this->importSalesMissionContacts();
        
        // Query both user's own contacts and shared Sales Mission contacts
        $query = SalesOfficerContact::with(['contactPeople' => function($q) {
                $q->where('name', '!=', 'N/A')->orderBy('is_primary', 'desc');
            }])
            ->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereNotNull('sales_mission_detail_id');
            });
            
        // Filter by search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('company_name', 'like', "%{$searchTerm}%")
                  ->orWhere('contact_name', 'like', "%{$searchTerm}%")
                  ->orWhere('position', 'like', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        
        $contacts = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('sales_officer.contacts.index', compact('contacts'));
    }
    
    /**
     * Import contacts from Sales Mission into Sales Officer Contacts table
     */
    private function importSalesMissionContacts()
    {
        // Find Sales Mission details that haven't been imported yet
        $salesMissionDetails = SalesMissionDetail::whereDoesntHave('salesOfficerContact')
            ->with('activity') // Load the related activity with eager loading
            ->get();
            
        foreach ($salesMissionDetails as $detail) {
            // Default location
            $location = ['city' => null, 'province' => null, 'country' => 'Indonesia'];
            
            // Get location data from the related activity if available
            if ($detail->activity) {
                $location['city'] = $detail->activity->city;
                $location['province'] = $detail->activity->province;
            }
            
            // If location is still not available, try to extract from address as fallback
            if (empty($location['city']) && !empty($detail->company_address)) {
                // Try to extract location data from address
                $addressParts = explode(',', $detail->company_address);
                $partsCount = count($addressParts);
                
                if ($partsCount >= 1) {
                    // Last part is usually the city/region
                    $cityPart = trim(end($addressParts));
                    
                    // Try to extract city name
                    if (strpos($cityPart, 'Jakarta') !== false) {
                        // Check for Jakarta districts
                        if (strpos($cityPart, 'Jakarta Selatan') !== false) {
                            $location['city'] = 'Jakarta Selatan';
                        } elseif (strpos($cityPart, 'Jakarta Utara') !== false) {
                            $location['city'] = 'Jakarta Utara';
                        } elseif (strpos($cityPart, 'Jakarta Barat') !== false) {
                            $location['city'] = 'Jakarta Barat';
                        } elseif (strpos($cityPart, 'Jakarta Timur') !== false) {
                            $location['city'] = 'Jakarta Timur';
                        } elseif (strpos($cityPart, 'Jakarta Pusat') !== false) {
                            $location['city'] = 'Jakarta Pusat';
                        } else {
                            $location['city'] = 'Jakarta';
                        }
                        $location['province'] = 'DKI Jakarta';
                    } elseif (strpos($cityPart, 'Bandung') !== false) {
                        $location['city'] = 'Bandung';
                        $location['province'] = 'Jawa Barat';
                    } elseif (strpos($cityPart, 'Surabaya') !== false) {
                        $location['city'] = 'Surabaya';
                        $location['province'] = 'Jawa Timur';
                    } elseif (preg_match('/(\w+)(?:\s+\w+)*$/', $cityPart, $matches)) {
                        // Extract last word from address as city if we can't match known cities
                        $location['city'] = $matches[0];
                    }
                }
            }
            
            // Create a Sales Officer Contact from Sales Mission Detail
            try {
                $contact = SalesOfficerContact::create([
                    'user_id' => auth()->id(), // Assign to current user
                    'company_name' => $detail->company_name,
                    'line_of_business' => $detail->line_of_business ?? 'Other', // Add line_of_business with default 'Other'
                    'company_address' => $detail->company_address,
                    'city' => $location['city'],
                    'province' => $location['province'],
                    'country' => $location['country'],
                    'contact_name' => $detail->company_pic,
                    'position' => $detail->company_position,
                    'phone_number' => $detail->company_contact,
                    'email' => $detail->company_email,
                    'sales_mission_detail_id' => $detail->id,
                    'status' => 'active',
                    'source' => 'Sales Mission', // Set the source to "Sales Mission"
                    'notes' => 'Imported from Sales Mission'
                ]);
                
                // Also create a PIC (Contact Person) automatically if PIC information is available
                if (!empty($detail->company_pic)) {
                    // Determine title based on name (simple logic)
                    $title = 'Mr'; // Default
                    $picName = $detail->company_pic;
                    
                    // Check for common female titles/prefixes in the name
                    $femalePrefixes = ['ibu', 'bu', 'mrs', 'ms', 'miss', 'ny', 'nyonya'];
                    $lowercaseName = strtolower($picName);
                    foreach ($femalePrefixes as $prefix) {
                        if (strpos($lowercaseName, $prefix) === 0) {
                            $title = 'Mrs';
                            // Remove the prefix from the name
                            $picName = trim(substr($picName, strlen($prefix)));
                            break;
                        }
                    }
                    
                    // Create the contact person
                    ContactPerson::create([
                        'contact_id' => $contact->id,
                        'division_id' => null, // No division initially as requested
                        'title' => $title,
                        'name' => $picName,
                        'position' => $detail->company_position,
                        'phone_number' => $detail->company_contact,
                        'email' => $detail->company_email,
                        'is_primary' => true, // Set as primary contact
                        'source' => 'Imported', // Set source to identify it was imported
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but continue with other imports
                \Log::error('Failed to import sales mission contact: ' . $e->getMessage());
            }
        }
        
        return $salesMissionDetails->count(); // Return number of imported contacts
    }

    /**
     * Show form to create a new contact.
     */
    public function createContact()
    {
        // Get sales mission contacts that aren't already imported
        $salesMissionDetails = SalesMissionDetail::whereDoesntHave('salesOfficerContact')
            ->orderBy('company_name')
            ->get();
            
        return view('sales_officer.contacts.create', compact('salesMissionDetails'));
    }

    /**
     * Store a newly created contact.
     */
    public function storeContact(Request $request)
    {
        // Validate the request
        $request->validate([
            'company_name' => 'required|string|max:255',
            'line_of_business' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'contact_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'sales_mission_detail_id' => 'nullable|exists:sales_mission_details,id',
        ]);
        
        // Create the contact
        $contact = SalesOfficerContact::create([
            'user_id' => auth()->id(),
            'company_name' => $request->company_name,
            'line_of_business' => $request->line_of_business,
            'company_address' => $request->company_address,
            'contact_name' => $request->contact_name,
            'position' => $request->position,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'sales_mission_detail_id' => $request->sales_mission_detail_id,
            'notes' => $request->notes,
            'status' => 'active',
            'source' => 'Contact', // Set the source to "Contact"
        ]);
        
        // Log the contact creation
        ActivityLogService::logCreate(
            'sales_officer_contacts',
            'Created new contact: ' . $request->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $request->company_name,
                'contact_name' => $request->contact_name,
            ]
        );
        
        return redirect()->route('sales_officer.contacts.index')
            ->with('success', 'Contact created successfully.');
    }

    /**
     * Show form to edit a contact.
     */
    public function editContact($id)
    {
        $contact = SalesOfficerContact::findOrFail($id);
        
        // Check if user has permission to edit this contact
        if ($contact->user_id != auth()->id() && !$contact->isFromSalesMission()) {
            return redirect()->route('sales_officer.contacts.index')
                ->with('error', 'You do not have permission to edit this contact.');
        }
        
        return view('sales_officer.contacts.edit', compact('contact'));
    }

    /**
     * Update the specified contact.
     */
    public function updateContact(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'company_name' => 'required|string|max:255',
            'line_of_business' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'contact_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Find the contact
        $contact = SalesOfficerContact::findOrFail($id);
        
        // Check if user has permission to edit this contact
        if ($contact->user_id != auth()->id() && !$contact->isFromSalesMission()) {
            return redirect()->route('sales_officer.contacts.index')
                ->with('error', 'You do not have permission to edit this contact.');
        }
        
        // Update the contact
        $contact->update([
            'company_name' => $request->company_name,
            'line_of_business' => $request->line_of_business,
            'company_address' => $request->company_address,
            'contact_name' => $request->contact_name,
            'position' => $request->position,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'notes' => $request->notes,
        ]);
        
        // Log the contact update
        ActivityLogService::logUpdate(
            'sales_officer_contacts',
            'Updated contact: ' . $request->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $request->company_name,
                'contact_name' => $request->contact_name,
            ]
        );
        
        return redirect()->route('sales_officer.contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified contact.
     */
    public function destroyContact($id)
    {
        $contact = SalesOfficerContact::findOrFail($id);
        
        // Check if user has permission to delete this contact
        if ($contact->user_id != auth()->id()) {
            return redirect()->route('sales_officer.contacts.index')
                ->with('error', 'You do not have permission to delete this contact.');
        }
        
        $contactName = $contact->company_name;
        
        // Log the contact deletion
        ActivityLogService::logDelete(
            'sales_officer_contacts',
            'Deleted contact: ' . $contactName,
            [
                'contact_id' => $contact->id,
                'company' => $contactName,
            ]
        );
        
        // Delete the contact
        $contact->delete();
        
        return redirect()->route('sales_officer.contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    /**
     * View contact details including all associated PICs.
     */
    public function viewContact($id)
    {
        $contact = SalesOfficerContact::with(['contactPeople' => function($query) {
            $query->orderBy('is_primary', 'desc')->orderBy('name');
        }, 'divisions' => function($query) {
            $query->orderBy('name');
        }])->findOrFail($id);
        
        // Check if user has permission to view this contact
        if ($contact->user_id != auth()->id() && !$contact->isFromSalesMission()) {
            return redirect()->route('sales_officer.contacts.index')
                ->with('error', 'You do not have permission to view this contact.');
        }
        
        // Get activities related to this contact for communication history
        $allActivities = SalesOfficerActivity::where('contact_id', $contact->id)
            ->orderBy('start_datetime', 'desc')
            ->get();
            
        // Group activities by date for timeline display
        $activitiesByDate = [];
        foreach ($allActivities as $activity) {
            $dateKey = $activity->start_datetime->format('Y-m-d');
            $activitiesByDate[$dateKey][] = $activity;
        }
        
        return view('sales_officer.contacts.show', compact('contact', 'activitiesByDate'));
    }

    /**
     * Get Sales Mission contact details for AJAX request
     */
    public function getSalesMissionContact($id)
    {
        $detail = SalesMissionDetail::findOrFail($id);
        
        // Extract location from address if possible
        $location = ['city' => null, 'province' => null, 'country' => 'Indonesia'];
        
        if (!empty($detail->company_address)) {
            // Try to extract location data from address
            $addressParts = explode(',', $detail->company_address);
            $partsCount = count($addressParts);
            
            if ($partsCount >= 1) {
                // Last part is usually the city/region
                $cityPart = trim(end($addressParts));
                
                // Try to extract city name
                if (strpos($cityPart, 'Jakarta') !== false) {
                    // Check for Jakarta districts
                    if (strpos($cityPart, 'Jakarta Selatan') !== false) {
                        $location['city'] = 'Jakarta Selatan';
                    } elseif (strpos($cityPart, 'Jakarta Utara') !== false) {
                        $location['city'] = 'Jakarta Utara';
                    } elseif (strpos($cityPart, 'Jakarta Barat') !== false) {
                        $location['city'] = 'Jakarta Barat';
                    } elseif (strpos($cityPart, 'Jakarta Timur') !== false) {
                        $location['city'] = 'Jakarta Timur';
                    } elseif (strpos($cityPart, 'Jakarta Pusat') !== false) {
                        $location['city'] = 'Jakarta Pusat';
                    } else {
                        $location['city'] = 'Jakarta';
                    }
                    $location['province'] = 'DKI Jakarta';
                } elseif (strpos($cityPart, 'Bandung') !== false) {
                    $location['city'] = 'Bandung';
                    $location['province'] = 'Jawa Barat';
                } elseif (strpos($cityPart, 'Surabaya') !== false) {
                    $location['city'] = 'Surabaya';
                    $location['province'] = 'Jawa Timur';
                } elseif (preg_match('/(\w+)(?:\s+\w+)*$/', $cityPart, $matches)) {
                    // Extract last word from address as city if we can't match known cities
                    $location['city'] = $matches[0];
                }
            }
        }
        
        // Add location data to the detail
        $detailWithLocation = $detail->toArray();
        $detailWithLocation['city'] = $location['city'];
        $detailWithLocation['province'] = $location['province'];
        $detailWithLocation['country'] = $location['country'];
        
        return response()->json($detailWithLocation);
    }

    /**
     * Get company divisions for AJAX request
     */
    public function getCompanyDivisions($company_id)
    {
        $divisions = CompanyDivision::where('contact_id', $company_id)
            ->orderBy('name')
            ->get(['id', 'name', 'visit_count']);
            
        return response()->json($divisions);
    }
    
    /**
     * Get company PICs for AJAX request
     */
    public function getCompanyPICs($company_id)
    {
        $pics = ContactPerson::where('contact_id', $company_id)
            ->whereNull('division_id')
            ->orderBy('name')
            ->get(['id', 'title', 'name', 'position', 'phone_number', 'email', 'is_primary']);
            
        return response()->json($pics);
    }
    
    /**
     * Get division PICs for AJAX request
     */
    public function getDivisionPICs($division_id)
    {
        $pics = ContactPerson::where('division_id', $division_id)
            ->orderBy('name')
            ->get(['id', 'title', 'name', 'position', 'phone_number', 'email', 'is_primary']);
            
        return response()->json($pics);
    }

    /**
     * Get all companies for AJAX request
     */
    public function getCompanies()
    {
        try {
            // First check if the table exists to avoid errors
            if (!Schema::hasTable('sales_officer_contacts')) {
                return response()->json([
                    'message' => 'The database setup is not complete yet. Please run the migrations to set up the database tables.',
                    'setup_required' => true
                ], 200);
            }

            // Return real data from database
            $companies = SalesOfficerContact::where(function($q) {
                    $q->where('user_id', auth()->id())
                      ->orWhereNotNull('sales_mission_detail_id');
                })
                ->orderBy('company_name')
                ->get(['id', 'company_name', 'line_of_business', 'company_address', 'visit_count']);
                
            return response()->json($companies);
        } catch (\Exception $e) {
            // On error, return informative message with status 200 to avoid breaking the UI
            return response()->json([
                'message' => 'Could not load companies. Please make sure the database is properly set up.',
                'error' => $e->getMessage(),
                'setup_required' => true
            ], 200);
        }
    }
    
    /**
     * Get follow-up history for a company
     */
    public function getCompanyFollowUpHistory($companyId)
    {
        try {
            // Fetch activities for this company with their follow-up data
            $activities = SalesOfficerActivity::where('contact_id', $companyId)
                ->where(function($query) {
                    $query->whereNotNull('next_follow_up')
                          ->orWhereNotNull('follow_up_type');
                })
                ->orderBy('created_at', 'desc')
                ->get([
                    'id', 
                    'created_at', 
                    'status', 
                    'next_follow_up', 
                    'follow_up_type',
                    'jso_lead_status',
                    'division_id'
                ]);
                
            return response()->json($activities);
        } catch (\Exception $e) {
            // On error, return empty array with status 200 to avoid breaking the UI
            return response()->json([]);
        }
    }
    
    /**
     * Store a new contact person (PIC) for a company.
     */
    public function storePIC(Request $request, $contactId)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:10',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'division_id' => 'nullable|exists:company_divisions,id',
            'is_primary' => 'nullable|boolean',
        ]);
        
        // Find the contact/company
        $contact = SalesOfficerContact::findOrFail($contactId);
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to add contact persons to this company.');
        }
        
        // Create the PIC
        $pic = ContactPerson::create([
            'contact_id' => $contactId,
            'division_id' => $request->division_id ?: null,
            'title' => $request->title,
            'name' => $request->name,
            'position' => $request->position,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'is_primary' => $request->has('is_primary') ? true : false,
            'source' => 'Activity'
        ]);
        
        // If this is marked as primary, update other PICs
        if ($request->has('is_primary')) {
            $pic->markAsPrimary();
        }
        
        // Log the creation
        ActivityLogService::logCreate(
            'contact_people',
            'Added new contact person: ' . $pic->name . ' to ' . $contact->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $contact->company_name,
                'pic_id' => $pic->id,
                'pic_name' => $pic->name,
            ]
        );
        
        return redirect()->route('sales_officer.contacts.show', $contact->id)
            ->with('success', 'Contact person added successfully.');
    }
    
    /**
     * Show form to edit a contact person.
     */
    public function editPIC($id)
    {
        $pic = ContactPerson::with('contact', 'division')->findOrFail($id);
        $contact = $pic->contact;
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to edit this contact person.');
        }
        
        // Get divisions for dropdown
        $divisions = $contact->divisions()->orderBy('name')->get();
        
        return view('sales_officer.contacts.edit_pic', compact('pic', 'contact', 'divisions'));
    }
    
    /**
     * Update the specified contact person.
     */
    public function updatePIC(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:10',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'division_id' => 'nullable|exists:company_divisions,id',
            'is_primary' => 'nullable|boolean',
        ]);
        
        // Find the PIC
        $pic = ContactPerson::with('contact')->findOrFail($id);
        $contact = $pic->contact;
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to edit this contact person.');
        }
        
        // Update the PIC
        $pic->update([
            'title' => $request->title,
            'name' => $request->name,
            'position' => $request->position,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'division_id' => $request->division_id ?: null,
            'is_primary' => $request->has('is_primary') ? true : false,
        ]);
        
        // If this is marked as primary, update other PICs
        if ($request->has('is_primary')) {
            $pic->markAsPrimary();
        }
        
        // Log the update
        ActivityLogService::logUpdate(
            'contact_people',
            'Updated contact person: ' . $pic->name . ' for ' . $contact->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $contact->company_name,
                'pic_id' => $pic->id,
                'pic_name' => $pic->name,
            ]
        );
        
        return redirect()->route('sales_officer.contacts.show', $contact->id)
            ->with('success', 'Contact person updated successfully.');
    }
    
    /**
     * Remove the specified contact person.
     */
    public function destroyPIC($id)
    {
        $pic = ContactPerson::with('contact')->findOrFail($id);
        $contact = $pic->contact;
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to delete this contact person.');
        }
        
        $picName = $pic->name;
        
        // Log the deletion
        ActivityLogService::logDelete(
            'contact_people',
            'Deleted contact person: ' . $picName . ' from ' . $contact->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $contact->company_name,
                'pic_id' => $pic->id,
                'pic_name' => $picName,
            ]
        );
        
        // Delete the PIC
        $pic->delete();
        
        return redirect()->route('sales_officer.contacts.show', $contact->id)
            ->with('success', 'Contact person deleted successfully.');
    }
    
    /**
     * Store a new division for a company.
     */
    public function storeDivision(Request $request, $contactId)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        // Find the contact/company
        $contact = SalesOfficerContact::findOrFail($contactId);
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to add divisions to this company.');
        }
        
        // Create the division
        $division = CompanyDivision::create([
            'contact_id' => $contactId,
            'name' => $request->name,
            'visit_count' => 0,
        ]);
        
        // Log the creation
        ActivityLogService::logCreate(
            'company_divisions',
            'Added new division: ' . $division->name . ' to ' . $contact->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $contact->company_name,
                'division_id' => $division->id,
                'division_name' => $division->name,
            ]
        );
        
        return redirect()->route('sales_officer.contacts.show', $contact->id)
            ->with('success', 'Division added successfully.');
    }
    
    /**
     * Show form to edit a division.
     */
    public function editDivision($id)
    {
        $division = CompanyDivision::with('contact')->findOrFail($id);
        $contact = $division->contact;
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to edit this division.');
        }
        
        return view('sales_officer.contacts.edit_division', compact('division', 'contact'));
    }
    
    /**
     * Update the specified division.
     */
    public function updateDivision(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        // Find the division
        $division = CompanyDivision::with('contact')->findOrFail($id);
        $contact = $division->contact;
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to edit this division.');
        }
        
        // Update the division
        $division->update([
            'name' => $request->name,
        ]);
        
        // Log the update
        ActivityLogService::logUpdate(
            'company_divisions',
            'Updated division: ' . $division->name . ' for ' . $contact->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $contact->company_name,
                'division_id' => $division->id,
                'division_name' => $division->name,
            ]
        );
        
        return redirect()->route('sales_officer.contacts.show', $contact->id)
            ->with('success', 'Division updated successfully.');
    }
    
    /**
     * Remove the specified division.
     */
    public function destroyDivision($id)
    {
        $division = CompanyDivision::with('contact')->findOrFail($id);
        $contact = $division->contact;
        
        // Check if user has permission
        if ($contact->user_id != auth()->id() && !$contact->sales_mission_detail_id) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'You do not have permission to delete this division.');
        }
        
        // Check if there are PICs associated with this division
        if ($division->contactPeople()->count() > 0) {
            return redirect()->route('sales_officer.contacts.show', $contact->id)
                ->with('error', 'Cannot delete division that has contact people. Please reassign or delete the contact people first.');
        }
        
        $divisionName = $division->name;
        
        // Log the deletion
        ActivityLogService::logDelete(
            'company_divisions',
            'Deleted division: ' . $divisionName . ' from ' . $contact->company_name,
            [
                'contact_id' => $contact->id,
                'company' => $contact->company_name,
                'division_id' => $division->id,
                'division_name' => $divisionName,
            ]
        );
        
        // Delete the division
        $division->delete();
        
        return redirect()->route('sales_officer.contacts.show', $contact->id)
            ->with('success', 'Division deleted successfully.');
    }
}
