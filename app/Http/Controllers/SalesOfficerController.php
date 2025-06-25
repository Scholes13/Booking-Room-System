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
use App\Services\SalesOfficerDashboardService;

class SalesOfficerController extends Controller
{
    /**
     * Display the Sales Officer Dashboard.
     */
    public function dashboard(SalesOfficerDashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        
        return view('sales_officer.dashboard.index', $data);
    }

    /**
     * Display a listing of contacts for Sales Officer.
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
