@extends('sales_officer.layout')

@section('title', 'Contact Details')
@section('header', 'Contact Details')
@section('description', 'Detailed view of contact information')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100">
    <!-- Company Header with Quick Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-3">
            <h4 class="text-lg font-semibold text-gray-800">{{ $contact->company_name }}</h4>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $contact->sales_mission_detail_id ? 'green' : 'blue' }}-100 text-{{ $contact->sales_mission_detail_id ? 'green' : 'blue' }}-800">
                {{ $contact->sales_mission_detail_id ? 'Sales Mission' : 'Sales Officer' }}
            </span>
        </div>
        <div class="w-full sm:w-auto flex flex-wrap gap-2">
            <a href="{{ route('sales_officer.contacts.edit', $contact->id) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium">
                Edit Company
            </a>
            <button id="addContactBtn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm font-medium">
                Add Contact
            </button>
            <a href="{{ route('sales_officer.contacts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Back to Contacts
            </a>
        </div>
    </div>
    
    <!-- Mobile Hamburger Menu -->
    <div class="sm:hidden mb-4">
        <button id="mobileTabsToggle" class="w-full flex items-center justify-between bg-gray-100 p-3 rounded-md">
            <span id="currentTabLabel" class="font-medium text-gray-800">Company Info</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="mobileTabsMenu" class="hidden mt-2 bg-white border border-gray-200 rounded-md shadow-lg">
            <button class="mobile-tab-btn w-full text-left p-3 hover:bg-gray-100 active text-primary border-l-2 border-primary" data-tab="company-info">Company Info</button>
            <button class="mobile-tab-btn w-full text-left p-3 hover:bg-gray-100" data-tab="business-info">Business Details</button>
            <button class="mobile-tab-btn w-full text-left p-3 hover:bg-gray-100" data-tab="divisions">Divisions ({{ $contact->divisions->count() }})</button>
            <button class="mobile-tab-btn w-full text-left p-3 hover:bg-gray-100" data-tab="contacts">Contact People ({{ $contact->contactPeople->count() }})</button>
            <button class="mobile-tab-btn w-full text-left p-3 hover:bg-gray-100" data-tab="communication">Communication History</button>
        </div>
    </div>
    
    <!-- Desktop Tab Navigation -->
    <div class="hidden sm:block border-b border-gray-200 mb-6 overflow-x-auto">
        <nav class="flex space-x-8 whitespace-nowrap">
            <button class="tab-btn py-4 px-1 border-b-2 border-primary font-medium text-sm text-primary" 
                    data-tab="company-info">Company Info</button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                    data-tab="business-info">Business Details</button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                    data-tab="divisions">Divisions ({{ $contact->divisions->count() }})</button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                    data-tab="contacts">Contact People ({{ $contact->contactPeople->count() }})</button>
            <button class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                    data-tab="communication">Communication History</button>
        </nav>
    </div>
    
    <!-- Tab Contents -->
    <!-- Company Information Tab -->
    <div class="tab-content" id="company-info">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Company Name</div>
                <div class="text-base text-gray-900">{{ $contact->company_name }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Line of Business</div>
                <div class="text-base text-gray-900">{{ $contact->line_of_business ?: 'Not specified' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Address</div>
                <div class="text-base text-gray-900">{{ $contact->company_address ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Location</div>
                <div class="text-base text-gray-900">
                    @if($contact->city || $contact->province || $contact->country)
                        {{ collect([$contact->city, $contact->province, $contact->country])->filter()->join(', ') }}
                    @else
                        Not provided
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Visit Count</div>
                <div class="text-base text-gray-900">{{ $contact->visit_count }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Source</div>
                <div class="text-base text-gray-900">
                    @if($contact->sales_mission_detail_id)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Sales Mission
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Sales Officer
                        </span>
                    @endif
                    @if($contact->source)
                        <span class="ml-2 text-sm text-gray-500">
                            Added as: {{ $contact->source }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Notes</div>
                <div class="text-base text-gray-900">{{ $contact->notes ?: 'No notes' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Business Information Tab -->
    <div class="tab-content hidden" id="business-info">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">General Information</div>
                <div class="text-base text-gray-900">{{ $contact->general_information ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Current Event</div>
                <div class="text-base text-gray-900">{{ $contact->current_event ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Target Business</div>
                <div class="text-base text-gray-900">{{ $contact->target_business ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Project Type</div>
                <div class="text-base text-gray-900">{{ $contact->project_type ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Project / Tender Estimation</div>
                <div class="text-base text-gray-900">{{ $contact->project_estimation ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Potential Revenue</div>
                <div class="text-base text-gray-900 font-medium">
                    @if($contact->potential_revenue)
                        <span class="text-green-600">Rp {{ number_format($contact->potential_revenue, 0, ',', '.') }}</span>
                    @else
                        <span class="text-gray-500">Not provided</span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Potential Projects / Partnerships</div>
                <div class="text-base text-gray-900">{{ $contact->potential_project_count ?: 'Not provided' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Divisions Tab -->
    <div class="tab-content hidden" id="divisions">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <h4 class="text-base font-semibold text-gray-800">Company Divisions</h4>
            <button id="addDivisionBtn" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-medium">
                Add Division
            </button>
        </div>
        
        @if($contact->divisions->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($contact->divisions as $division)
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <h4 class="font-semibold text-gray-800">{{ $division->name }}</h4>
                            <span class="text-sm text-gray-500">Visits: {{ $division->visit_count }}</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div class="text-sm">
                                <span class="font-medium">{{ $division->contactPeople->count() }}</span> contacts in this division
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('sales_officer.contacts.edit_division', $division->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                @if($division->contactPeople->count() == 0)
                                    <form action="{{ route('sales_officer.contacts.destroy_division', $division->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Are you sure you want to delete this division?')">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 text-sm flex flex-col items-center justify-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p>No divisions found for this company.</p>
                <button id="addFirstDivisionBtn" class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium">
                    Add First Division
                </button>
            </div>
        @endif
    </div>
    
    <!-- Contact People Tab -->
    <div class="tab-content hidden" id="contacts">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <h4 class="text-base font-semibold text-gray-800">Contact People</h4>
            <button id="addContactHeaderBtn" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-medium">
                Add Contact
            </button>
        </div>
        
        @if($contact->contactPeople->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($contact->contactPeople as $pic)
                    <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-4 {{ $pic->is_primary ? 'bg-green-50' : 'bg-white' }}">
                            <div class="flex flex-wrap justify-between items-start gap-2 mb-2">
                                <h4 class="font-semibold text-gray-800">{{ $pic->title }} {{ $pic->name }}</h4>
                                <div class="flex flex-wrap gap-1">
                                    @if($pic->is_primary)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Primary
                                        </span>
                                    @endif
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                        {{ $pic->created_at->diffForHumans() }}
                                    </span>
                                    @php
                                        $sourceBg = match($pic->source) {
                                            'Manual' => 'bg-indigo-100 text-indigo-800',
                                            'Activity' => 'bg-green-100 text-green-800',
                                            'Imported' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold {{ $sourceBg }}">
                                        {{ $pic->source }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">{{ $pic->position ?: 'No position' }}</p>
                            <p class="text-sm text-gray-600">{{ $pic->division ? $pic->division->name : 'General' }}</p>
                            
                            <div class="mt-4 space-y-2">
                                @if($pic->phone_number)
                                    <div class="flex items-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <a href="tel:{{ $pic->phone_number }}" class="text-blue-600 hover:underline">{{ $pic->phone_number }}</a>
                                    </div>
                                @endif
                                
                                @if($pic->email)
                                    <div class="flex items-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <a href="mailto:{{ $pic->email }}" class="text-blue-600 hover:underline">{{ $pic->email }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 flex justify-end space-x-2">
                            <button class="edit-contact-btn text-blue-600 hover:text-blue-800 text-sm" data-id="{{ $pic->id }}">Edit</button>
                            <button class="delete-contact-btn text-red-600 hover:text-red-800 text-sm" data-id="{{ $pic->id }}">Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 text-sm flex flex-col items-center justify-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <p>No contact people found for this company.</p>
                <button id="addFirstContactBtn" class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium">
                    Add First Contact
                </button>
            </div>
        @endif
    </div>
    
    <!-- Communication History Tab -->
    <div class="tab-content hidden" id="communication">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h4 class="text-base font-semibold text-gray-800">Communication History</h4>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <select class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="comm-filter">
                    <option value="all">All Types</option>
                    <option value="Meeting">Meetings</option>
                    <option value="Sales Call">Sales Calls</option>
                    <option value="Telemarketing">Telemarketing</option>
                    <option value="Event Networking">Events</option>
                    <option value="Negotiation">Negotiations</option>
                    <option value="Presentation">Presentations</option>
                </select>
                <a href="{{ route('sales_officer.activities.create') }}" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs font-medium">
                    Add Activity
                </a>
            </div>
        </div>
        
        <div class="timeline-container mb-8">
            @if(count($activitiesByDate) > 0)
                @foreach($activitiesByDate as $date => $dateActivities)
                    <div class="date-divider my-4">
                        <div class="flex items-center">
                            <div class="flex-grow h-px bg-gray-200"></div>
                            <span class="mx-4 flex-shrink bg-gray-100 text-gray-600 px-3 py-1 rounded text-sm">
                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                            </span>
                            <div class="flex-grow h-px bg-gray-200"></div>
                        </div>
                    </div>
                    
                    @foreach($dateActivities as $activity)
                        <div class="timeline-item border-l-2 border-gray-300 pl-4 pb-6 relative ml-4 activity-item" data-type="{{ $activity->activity_type }}">
                            <div class="timeline-bullet absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-blue-500"></div>
                            <div class="bg-white rounded-lg border p-4 shadow-sm hover:shadow transition-shadow">
                                <div class="flex flex-wrap justify-between items-start gap-2">
                                    <div>
                                        <span class="text-sm font-medium bg-blue-100 text-blue-800 px-2 py-0.5 rounded">
                                            {{ $activity->activity_type }}
                                        </span>
                                        <h5 class="font-semibold mt-1">{{ $activity->title }}</h5>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $activity->start_datetime->format('H:i') }}</span>
                                </div>
                                
                                @if($activity->status)
                                    <div class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $activity->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                              ($activity->status == 'ongoing' ? 'bg-blue-100 text-blue-800' : 
                                              'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </div>
                                @endif
                                
                                <p class="mt-2 text-sm text-gray-600">{{ $activity->description }}</p>
                                
                                @if($activity->pic)
                                    <div class="mt-3 text-xs text-gray-500">
                                        <span class="font-medium">Contact Person:</span> {{ $activity->pic->title }} {{ $activity->pic->name }}
                                        @if($activity->pic->position)
                                            ({{ $activity->pic->position }})
                                        @endif
                                    </div>
                                @endif
                                
                                @if($activity->jso_lead_status)
                                    <div class="mt-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Lead Status: {{ $activity->jso_lead_status }}
                                        </span>
                                    </div>
                                @endif
                                
                                @if($activity->next_follow_up)
                                    @php
                                        $isValidDate = true;
                                        try {
                                            $followUpDate = \Carbon\Carbon::parse($activity->next_follow_up);
                                        } catch (\Exception $e) {
                                            $isValidDate = false;
                                        }
                                    @endphp
                                    
                                    <div class="mt-3 flex items-center gap-2 bg-indigo-50 p-2 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <span class="text-xs font-medium text-indigo-800">
                                                Follow-up: 
                                                @if($isValidDate)
                                                    {{ $followUpDate->format('d M Y') }}
                                                @else
                                                    {{ $activity->next_follow_up }}
                                                @endif
                                            </span>
                                            @if($activity->follow_up_type)
                                                <span class="ml-1 text-xs text-indigo-600">({{ $activity->follow_up_type }})</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @else
                <div class="text-gray-500 text-sm flex flex-col items-center justify-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p>No communication history found for this company.</p>
                    <a href="{{ route('sales_officer.activities.create') }}" class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium">
                        Add First Activity
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Contact Person Modal -->
<div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Add New Contact Person</h3>
        </div>
        <form id="contactForm" action="{{ route('sales_officer.contacts.store_pic', $contact->id) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="contact_id" value="{{ $contact->id }}">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <select name="title" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="Mr.">Mr.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Dr.">Dr.</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                    <select name="division_id" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">General (No Division)</option>
                        @foreach($contact->divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                <input type="text" name="position" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone_number" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_primary" value="1" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700">Set as primary contact</label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="closeContactModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-sm font-medium">
                    Add Contact
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Division Modal -->
<div id="divisionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Add New Division</h3>
        </div>
        <form id="divisionForm" action="{{ route('sales_officer.contacts.store_division', $contact->id) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="contact_id" value="{{ $contact->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Division Name</label>
                <input type="text" name="name" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="closeDivisionModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-sm font-medium">
                    Add Division
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile tab menu functionality
        const mobileTabsToggle = document.getElementById('mobileTabsToggle');
        const mobileTabsMenu = document.getElementById('mobileTabsMenu');
        const currentTabLabel = document.getElementById('currentTabLabel');
        const mobileTabBtns = document.querySelectorAll('.mobile-tab-btn');
        
        if(mobileTabsToggle) {
            mobileTabsToggle.addEventListener('click', function() {
                mobileTabsMenu.classList.toggle('hidden');
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileTabsToggle.contains(event.target) && 
                    !mobileTabsMenu.contains(event.target) && 
                    !mobileTabsMenu.classList.contains('hidden')) {
                    mobileTabsMenu.classList.add('hidden');
                }
            });
            
            // Mobile tab switching
            mobileTabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    switchTabs(tabId);
                    
                    // Update mobile menu label and hide menu
                    currentTabLabel.textContent = this.textContent.trim();
                    mobileTabsMenu.classList.add('hidden');
                    
                    // Update active state in mobile menu
                    mobileTabBtns.forEach(btn => {
                        btn.classList.remove('active', 'text-primary', 'border-l-2', 'border-primary');
                    });
                    this.classList.add('active', 'text-primary', 'border-l-2', 'border-primary');
                });
            });
        }
        
        // Tab switching functionality (shared between mobile and desktop)
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        function switchTabs(tabId) {
            // Hide all tabs and remove active class
            tabContents.forEach(content => content.classList.add('hidden'));
            tabBtns.forEach(btn => {
                btn.classList.remove('border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab and add active class
            document.getElementById(tabId).classList.remove('hidden');
            document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.remove('border-transparent', 'text-gray-500');
            document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('border-primary', 'text-primary');
            
            // Also update mobile menu active state
            if(mobileTabBtns) {
                mobileTabBtns.forEach(btn => {
                    if(btn.getAttribute('data-tab') === tabId) {
                        btn.classList.add('active', 'text-primary', 'border-l-2', 'border-primary');
                    } else {
                        btn.classList.remove('active', 'text-primary', 'border-l-2', 'border-primary');
                    }
                });
            }
        }
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                switchTabs(tabId);
                
                // Update mobile menu label
                if(currentTabLabel) {
                    currentTabLabel.textContent = this.textContent.trim();
                }
            });
        });
        
        // Add Division button functionality
        const addDivisionBtn = document.getElementById('addDivisionBtn');
        const addFirstDivisionBtn = document.getElementById('addFirstDivisionBtn');
        
        if(addDivisionBtn) {
            addDivisionBtn.addEventListener('click', function() {
                document.getElementById('divisionModal').classList.remove('hidden');
            });
        }
        
        if(addFirstDivisionBtn) {
            addFirstDivisionBtn.addEventListener('click', function() {
                document.getElementById('divisionModal').classList.remove('hidden');
            });
        }
        
        // Add Contact button functionality
        const addContactBtn = document.getElementById('addContactBtn');
        const addContactHeaderBtn = document.getElementById('addContactHeaderBtn');
        const addFirstContactBtn = document.getElementById('addFirstContactBtn');
        
        if(addContactBtn) {
            addContactBtn.addEventListener('click', function() {
                document.getElementById('contactModal').classList.remove('hidden');
            });
        }
        
        if(addContactHeaderBtn) {
            addContactHeaderBtn.addEventListener('click', function() {
                document.getElementById('contactModal').classList.remove('hidden');
            });
        }
        
        if(addFirstContactBtn) {
            addFirstContactBtn.addEventListener('click', function() {
                document.getElementById('contactModal').classList.remove('hidden');
            });
        }

        // Add event handler for the close buttons
        const closeContactBtn = document.getElementById('closeContactModal');
        const closeDivisionBtn = document.getElementById('closeDivisionModal');
        
        if(closeContactBtn) {
            closeContactBtn.addEventListener('click', function() {
                document.getElementById('contactModal').classList.add('hidden');
            });
        }
        
        if(closeDivisionBtn) {
            closeDivisionBtn.addEventListener('click', function() {
                document.getElementById('divisionModal').classList.add('hidden');
            });
        }
        
        // Filter Communication History by activity type
        const commFilter = document.getElementById('comm-filter');
        if(commFilter) {
            commFilter.addEventListener('change', function() {
                const filterValue = this.value;
                const activityItems = document.querySelectorAll('.activity-item');
                
                activityItems.forEach(item => {
                    if(filterValue === 'all' || item.getAttribute('data-type') === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Check if we need to show "no results" message
                const visibleItems = document.querySelectorAll('.activity-item[style="display: block"]');
                const noResultsMsg = document.getElementById('no-filter-results');
                
                if(visibleItems.length === 0 && filterValue !== 'all') {
                    if(!noResultsMsg) {
                        const msg = document.createElement('div');
                        msg.id = 'no-filter-results';
                        msg.className = 'text-center py-8 text-gray-500';
                        msg.innerText = 'No activities found for the selected type.';
                        document.querySelector('.timeline-container').appendChild(msg);
                    }
                } else if(noResultsMsg) {
                    noResultsMsg.remove();
                }
            });
        }
        
        // Touch-friendly improvements for mobile
        const cards = document.querySelectorAll('.border.rounded-lg');
        cards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.classList.add('bg-gray-50');
            });
            card.addEventListener('touchend', function() {
                this.classList.remove('bg-gray-50');
            });
        });
    });
</script>
@endpush
@endsection 