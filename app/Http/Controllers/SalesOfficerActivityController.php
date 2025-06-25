<?php

namespace App\Http\Controllers;

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

class SalesOfficerActivityController extends Controller
{
    /**
     * Display a listing of activities for Sales Officer.
     */
    public function index(Request $request)
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
    public function create()
    {
        // Get departments and employees for dropdown
        $departments = Department::all();
        $contacts = SalesOfficerContact::where('user_id', auth()->id())
                      ->orWhereNotNull('sales_mission_detail_id')
                      ->orderBy('company_name', 'asc')
                            ->get();

        // Activity types for Sales Officer
        $activityTypes = ActivityType::orderBy('name')->pluck('name');

        // Data of provinces and cities (example data)
        $provinces = \App\Models\Province::orderBy('name')->get();

        return view('sales_officer.activities.create', compact('departments', 'contacts', 'activityTypes', 'provinces'));
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(SalesOfficerActivityRequest $request)
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
                
                if ($request->division_selector === 'new') {
                    // Create a new division
                    if ($request->filled('division_name')) {
                        $division = CompanyDivision::create([
                            'sales_officer_contact_id' => $contact_id,
                            'name' => $request->division_name,
                            'visit_count' => 1,
                        ]);
                        $division_id = $division->id;
                    }
                } else {
                    // Use existing division
                    $division_id = $request->division_selector;
                    $division = CompanyDivision::find($division_id);
                    if ($division) {
                        $division->increment('visit_count');
                        $division_visit_count = $division->visit_count;
                    }
                }
                
                // STEP 3: Process the PIC
                $pic_id = null;
                $pic_visit_count = 1;

                if ($request->pic_selector === 'new') {
                    // Create a new PIC
                    if ($request->filled('pic_name')) {
                        $pic = ContactPerson::create([
                            'sales_officer_contact_id' => $contact_id,
                            'company_division_id' => $division_id,
                            'name' => $request->pic_name,
                            'position' => $request->pic_position,
                            'phone_number' => $request->pic_phone_number,
                            'email' => $request->pic_email,
                            'visit_count' => 1,
                        ]);
                        $pic_id = $pic->id;
                    }
                } else {
                    // Use existing PIC
                    $pic_id = $request->pic_selector;
                    $pic = ContactPerson::find($pic_id);
                    if ($pic) {
                        $pic->increment('visit_count');
                        $pic_visit_count = $pic->visit_count;
                    }
                }
                
                // STEP 4: Create the main activity
                $activity = SalesOfficerActivity::create([
                    'user_id' => auth()->id(),
                    'sales_officer_contact_id' => $contact_id,
                    'company_division_id' => $division_id,
                    'contact_person_id' => $pic_id,
                    'department_id' => $request->department_id,
                    'title' => $request->title,
                    'activity_type' => $request->activity_type,
                    'description' => $request->description,
                    'start_datetime' => $startDatetime,
                    'end_datetime' => $endDatetime,
                    'status' => $request->status,
                    'visit_count' => $visit_count,
                    'division_visit_count' => $division_visit_count,
                    'pic_visit_count' => $pic_visit_count,
                    'province' => $contact->province,
                    'city' => $contact->city
                ]);

                return redirect()->route('sales-officer.activities.index')->with('success', 'Activity created successfully!');
            });
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating activity', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Error creating activity: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $activity = SalesOfficerActivity::with('contact', 'division', 'pic')->findOrFail($id);
        
        // Authorization check - only the owner can edit
        if ($activity->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $contacts = SalesOfficerContact::where('user_id', auth()->id())
                                       ->orWhereNotNull('sales_mission_detail_id')
                                       ->orderBy('company_name', 'asc')
                                       ->get();

        $activityTypes = [
            'Event Networking', 'Meeting', 'Negotiation', 'Presentation - Introduction & Compro',
            'Presentation - Pitching', 'Sales Call', 'Telemarketing', 'Telemarketing - Email',
            'Telemarketing - LinkedIn', 'Telemarketing - Phone/WhatsApp', 'Werkudara Client Event'
        ];
        
        $provinces = \App\Models\Province::orderBy('name')->get();
        
        $divisions = [];
        if ($activity->sales_officer_contact_id) {
            $divisions = CompanyDivision::where('sales_officer_contact_id', $activity->sales_officer_contact_id)->get();
        }

        $pics = [];
        if ($activity->company_division_id) {
            $pics = ContactPerson::where('company_division_id', $activity->company_division_id)->get();
        } elseif ($activity->sales_officer_contact_id) {
            $pics = ContactPerson::where('sales_officer_contact_id', $activity->sales_officer_contact_id)
                                 ->whereNull('company_division_id')
                                 ->get();
        }

        return view('sales_officer.activities.edit', compact(
            'activity', 'departments', 'contacts', 'activityTypes',
            'provinces', 'divisions', 'pics'
        ));
    }

    public function update(SalesOfficerActivityRequest $request, $id)
    {
        try {
            $activity = SalesOfficerActivity::findOrFail($id);

            // Authorization check
            if ($activity->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            // Use transaction for data consistency
            return \DB::transaction(function() use ($request, $activity) {
                // STEP 1: Process Company
                $contact_id = $request->company_selector;
                $contact = SalesOfficerContact::findOrFail($contact_id);

                // Update company details if they are provided
                $contact->update([
                    'line_of_business' => $request->line_of_business,
                    'company_address' => $request->company_address,
                    'country' => $request->country,
                    'province' => $request->province,
                    'city' => $request->city,
                ]);

                // STEP 2: Process Division
                $division_id = $request->division_selector;
                if ($division_id === 'new' && $request->filled('division_name')) {
                    $division = CompanyDivision::create([
                        'sales_officer_contact_id' => $contact_id,
                        'name' => $request->division_name,
                    ]);
                    $division_id = $division->id;
                }

                // STEP 3: Process PIC
                $pic_id = $request->pic_selector;
                if ($pic_id === 'new' && $request->filled('pic_name')) {
                    $pic = ContactPerson::create([
                        'sales_officer_contact_id' => $contact_id,
                        'company_division_id' => $division_id,
                        'name' => $request->pic_name,
                        'position' => $request->pic_position,
                        'phone_number' => $request->pic_phone_number,
                        'email' => $request->pic_email,
                    ]);
                    $pic_id = $pic->id;
                }

                // STEP 4: Update the Activity
                $activity->update([
                    'department_id' => $request->department_id,
                    'title' => $request->title,
                    'activity_type' => $request->activity_type,
                    'description' => $request->description,
                    'start_datetime' => $request->start_datetime,
                    'end_datetime' => $request->end_datetime,
                    'status' => $request->status,
                    'sales_officer_contact_id' => $contact_id,
                    'company_division_id' => $division_id,
                    'contact_person_id' => $pic_id,
                    'province' => $contact->province,
                    'city' => $contact->city
                ]);

                return redirect()->route('sales-officer.activities.index')->with('success', 'Activity updated successfully!');
            });

        } catch (\Exception $e) {
            \Log::error('Error updating activity: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating activity.')->withInput();
        }
    }

    public function destroy($id)
    {
        $activity = SalesOfficerActivity::findOrFail($id);

        // Authorization check
        if ($activity->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $activity->delete();

        return redirect()->route('sales-officer.activities.index')->with('success', 'Activity deleted successfully.');
    }

    public function calendar()
    {
        return view('sales_officer.calendar.index');
    }

    public function calendarEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $events = SalesOfficerActivity::where('user_id', auth()->id())
            ->whereBetween('start_datetime', [$start, $end])
            ->get();

        $eventData = $events->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->start_datetime,
                'end' => $event->end_datetime,
                'url' => route('sales-officer.activities.edit', $event->id),
                'description' => $event->description,
                'status' => $event->status,
                'activity_type' => $event->activity_type,
                'contact' => optional($event->contact)->company_name,
            ];
        });

        return response()->json($eventData);
    }
} 