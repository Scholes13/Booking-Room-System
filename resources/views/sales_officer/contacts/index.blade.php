@extends('sales_officer.layout')

@section('title', 'Contacts')
@section('header', 'Contacts')
@section('description', 'Manage your business contacts')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100">
    <!-- Mobile-optimized top section with floating action button -->
    <div class="flex flex-col gap-4 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h4 class="text-base font-semibold text-gray-800">Business Contacts</h4>
            
            <form action="{{ route('sales_officer.contacts.index') }}" method="GET" class="flex w-full sm:w-auto max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..." 
                    class="w-full rounded-l-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-r-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>
        
        <!-- Mobile Optimized View Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full">
            <!-- Bottom sheet trigger for mobile filters -->
            <button id="mobileFilterButton" class="md:hidden w-full flex items-center justify-between bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    View & Filters
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <!-- Desktop filters -->
            <div class="hidden md:flex items-center gap-2 w-full sm:w-auto">
                <select id="viewType" class="w-full sm:w-auto rounded-md border-gray-300 text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="card">Card View</option>
                    <option value="table">Table View</option>
                    <option value="group">Group by Company</option>
                    <option value="multiple">Companies with Multiple Contacts</option>
                </select>
                
                <select id="sourceFilter" class="w-full sm:w-auto rounded-md border-gray-300 text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="all">All Sources</option>
                    <option value="sales_officer">Sales Officer</option>
                    <option value="sales_mission">Sales Mission</option>
                </select>
            </div>
            
            <!-- Mobile Bottom Sheet for Filters - Hidden by default -->
            <div id="mobileFilterSheet" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden">
                <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-xl p-4 transform transition-transform duration-300 ease-in-out">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">View & Filters</h3>
                        <button id="closeFilterSheet" class="p-2 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">View Type</label>
                            <select id="mobileViewType" class="w-full rounded-md border-gray-300 text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="card">Card View</option>
                                <option value="table">Table View</option>
                                <option value="group">Group by Company</option>
                                <option value="multiple">Multiple Contacts</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                            <select id="mobileSourceFilter" class="w-full rounded-md border-gray-300 text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="all">All Sources</option>
                                <option value="sales_officer">Sales Officer</option>
                                <option value="sales_mission">Sales Mission</option>
                            </select>
                        </div>
                        
                        <button id="applyFilters" class="w-full bg-primary text-white py-3 px-4 rounded-lg font-medium">
                            Apply Filters
                        </button>
                    </div>
                    
                    <!-- Handle for bottom sheet -->
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-12 h-1 bg-gray-300 rounded-full"></div>
                </div>
            </div>
            
            <!-- Floating Action Button for mobile -->
            <a href="{{ route('sales_officer.contacts.create') }}" class="w-full sm:w-auto bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center sm:justify-start gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add New Contact
            </a>
        </div>
    </div>
    
    <!-- Mobile-optimized Card View - Shown by default on mobile -->
    <div id="cardView" class="space-y-4 block md:hidden">
        @forelse($contacts as $contact)
            <div class="border rounded-lg overflow-hidden shadow-sm contact-card"
                 data-company="{{ $contact->company_name }}" 
                 data-source="{{ $contact->sales_mission_detail_id ? 'sales_mission' : 'sales_officer' }}">
                <!-- Mobile-optimized card layout -->
                <div class="p-4 {{ $contact->sales_mission_detail_id ? 'bg-green-50' : 'bg-white' }}">
                    <!-- Company & Source with touch-friendly tap areas -->
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-gray-800 text-base">{{ $contact->company_name }}</h3>
                        @if($contact->sales_mission_detail_id)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Sales Mission
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Sales Officer
                            </span>
                        @endif
                    </div>
                    
                    <!-- Contact Details -->
                    <div class="space-y-3">
                        <!-- Contact Person with clickable phone/email -->
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    @if($contact->contact_name)
                                        {{ $contact->contact_name }}
                                    @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                        @php
                                            $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                            $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                        @endphp
                                        @if($firstValidPic)
                                            {{ $firstValidPic->name }}
                                            @if($firstValidPic->is_primary)
                                                <span class="ml-1 text-xs text-green-600">(Primary)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">No contact person</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No contact person</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($contact->position)
                                        {{ $contact->position }}
                                    @elseif($contact->contactPeople && $contact->contactPeople->count() > 0 && $firstValidPic)
                                        {{ $firstValidPic->position }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Phone with tap to call -->
                        @php
                            $phoneNumber = $contact->phone_number;
                            if(!$phoneNumber && isset($firstValidPic) && $firstValidPic->phone_number && $firstValidPic->phone_number != 'N/A') {
                                $phoneNumber = $firstValidPic->phone_number;
                            }
                        @endphp
                        @if($phoneNumber)
                        <div class="flex items-start">
                            <a href="tel:{{ $phoneNumber }}" class="flex items-start w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div class="text-sm text-gray-900">{{ $phoneNumber }}</div>
                            </a>
                        </div>
                        @endif
                        
                        <!-- Email with tap to email -->
                        @php
                            $email = $contact->email;
                            if(!$email && isset($firstValidPic) && $firstValidPic->email && $firstValidPic->email != 'N/A') {
                                $email = $firstValidPic->email;
                            }
                        @endphp
                        @if($email)
                        <div class="flex items-start">
                            <a href="mailto:{{ $email }}" class="flex items-start w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div class="text-sm text-blue-600 hover:text-blue-800 truncate">{{ $email }}</div>
                            </a>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Mobile-friendly touch actions with larger tap areas -->
                    <div class="mt-4 flex justify-between border-t pt-3">
                        <a href="{{ route('sales_officer.contacts.show', $contact->id) }}" class="flex-1 text-center py-2 text-green-600">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span class="text-xs mt-1">View</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('sales_officer.contacts.edit', $contact->id) }}" class="flex-1 text-center py-2 text-blue-600">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="text-xs mt-1">Edit</span>
                            </div>
                        </a>
                        
                        @unless($contact->sales_mission_detail_id)
                            <form action="{{ route('sales_officer.contacts.destroy', $contact->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex flex-col items-center py-2 text-red-600" onclick="return confirm('Are you sure you want to delete this contact?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="text-xs mt-1">Delete</span>
                                </button>
                            </form>
                        @endunless
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <p class="mt-2 text-gray-500">No contacts found</p>
                <a href="{{ route('sales_officer.contacts.create') }}" class="mt-3 inline-block bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Add Your First Contact
                </a>
            </div>
        @endforelse
        
        <div class="mt-4">
            {{ $contacts->links() }}
        </div>
    </div>
    
    <!-- Keep the existing views but hide them on mobile -->
    <div id="tableView" class="hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Position</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Contact</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Source</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contacts as $contact)
                    <tr class="{{ $contact->sales_mission_detail_id ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50' }} contact-row" 
                        data-company="{{ $contact->company_name }}" 
                        data-source="{{ $contact->sales_mission_detail_id ? 'sales_mission' : 'sales_officer' }}">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $contact->company_name }}
                                @php
                                    $contactCount = $contacts->where('company_name', $contact->company_name)->count();
                                @endphp
                                @if($contactCount > 1)
                                    <span class="ml-1 text-xs text-blue-600">({{ $contactCount }} contacts)</span>
                                @endif
                            </div>
                            <!-- Mobile-only additional info -->
                            <div class="sm:hidden mt-1 space-y-1">
                                <div class="text-xs">
                                    <span class="font-medium text-gray-500">Contact:</span>
                                    @if($contact->contactPeople && $contact->contactPeople->count() > 0)
                                        @php
                                            $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                            $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                        @endphp
                                        @if($firstValidPic)
                                            {{ $firstValidPic->name }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                                @if($contact->sales_mission_detail_id)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Sales Mission
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Sales Officer
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($contact->contact_name)
                                    {{ $contact->contact_name }}
                                @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                    @php
                                        $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                        $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                    @endphp
                                    @if($firstValidPic)
                                        {{ $firstValidPic->name }}
                                        <span class="text-xs text-gray-500">({{ $contact->contactPeople->where('name', '!=', 'N/A')->count() }} contacts)</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-sm text-gray-900">
                                @if($contact->position)
                                    {{ $contact->position }}
                                @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                    @php
                                        $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                        $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                    @endphp
                                    @if($firstValidPic)
                                        {{ $firstValidPic->position }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-sm text-gray-900">
                                @if($contact->phone_number)
                                    {{ $contact->phone_number }}
                                @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                    @php
                                        $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                        $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                    @endphp
                                    @if($firstValidPic && $firstValidPic->phone_number && $firstValidPic->phone_number != 'N/A')
                                        {{ $firstValidPic->phone_number }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell">
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $contact->email }}</a>
                            @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                @php
                                    $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                    $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                @endphp
                                @if($firstValidPic && $firstValidPic->email && $firstValidPic->email != 'N/A')
                                    <a href="mailto:{{ $firstValidPic->email }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $firstValidPic->email }}</a>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                            @if($contact->sales_mission_detail_id)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Sales Mission
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Sales Officer
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('sales_officer.contacts.show', $contact->id) }}" class="text-green-600 hover:text-green-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('sales_officer.contacts.edit', $contact->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @unless($contact->sales_mission_detail_id)
                                    <form action="{{ route('sales_officer.contacts.destroy', $contact->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this contact?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-sm text-center text-gray-500">No contacts found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div id="groupedView" class="space-y-4 hidden">
        @php
            $groupedContacts = $contacts->groupBy('company_name');
        @endphp
        
        @foreach($groupedContacts as $companyName => $companyContacts)
            <div class="border rounded-lg overflow-hidden shadow-sm">
                <!-- Company Header -->
                <div class="bg-gray-50 p-4 flex flex-wrap justify-between items-center cursor-pointer company-header" data-company="{{ $companyName }}">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform chevron-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <h3 class="font-semibold text-gray-800">{{ $companyName }}</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $companyContacts->count() }} {{ $companyContacts->count() > 1 ? 'contacts' : 'contact' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                        @if($companyContacts->first()->sales_mission_detail_id)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Sales Mission
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Sales Officer
                            </span>
                        @endif
                        
                        <a href="{{ route('sales_officer.contacts.show', $companyContacts->first()->id) }}" class="text-blue-600 hover:text-blue-800 ml-2">
                            View Company
                        </a>
                    </div>
                </div>
                
                <!-- Company Contacts (Collapsible) -->
                <div class="contacts-container hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($companyContacts as $contact)
                                    <tr class="{{ $contact->sales_mission_detail_id ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50' }}">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($contact->contact_name)
                                                    {{ $contact->contact_name }}
                                                @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                                    @php
                                                        $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                                        $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                                    @endphp
                                                    @if($firstValidPic)
                                                        {{ $firstValidPic->name }}
                                                        @if($firstValidPic->is_primary)
                                                            <span class="ml-1 text-xs text-green-600">(Primary)</span>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($contact->position)
                                                    {{ $contact->position }}
                                                @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                                    @php
                                                        $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                                        $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                                    @endphp
                                                    @if($firstValidPic)
                                                        {{ $firstValidPic->position }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($contact->phone_number)
                                                    {{ $contact->phone_number }}
                                                @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                                    @php
                                                        $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                                        $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                                    @endphp
                                                    @if($firstValidPic && $firstValidPic->phone_number && $firstValidPic->phone_number != 'N/A')
                                                        {{ $firstValidPic->phone_number }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($contact->email)
                                                <a href="mailto:{{ $contact->email }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $contact->email }}</a>
                                            @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                                @php
                                                    $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                                    $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                                @endphp
                                                @if($firstValidPic && $firstValidPic->email && $firstValidPic->email != 'N/A')
                                                    <a href="mailto:{{ $firstValidPic->email }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $firstValidPic->email }}</a>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('sales_officer.contacts.show', $contact->id) }}" class="text-green-600 hover:text-green-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('sales_officer.contacts.edit', $contact->id) }}" class="text-blue-600 hover:text-blue-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                @unless($contact->sales_mission_detail_id)
                                                    <form action="{{ route('sales_officer.contacts.destroy', $contact->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this contact?')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endunless
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Desktop Card View - Shown on larger screens -->
    <div id="desktopCardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 hidden">
        @foreach($contacts as $contact)
            <div class="border rounded-lg overflow-hidden shadow-sm contact-card"
                 data-company="{{ $contact->company_name }}" 
                 data-source="{{ $contact->sales_mission_detail_id ? 'sales_mission' : 'sales_officer' }}">
                <div class="p-4 {{ $contact->sales_mission_detail_id ? 'bg-green-50' : 'bg-white' }}">
                    <!-- Company & Source -->
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-gray-800">{{ $contact->company_name }}</h3>
                        @if($contact->sales_mission_detail_id)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Sales Mission
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Sales Officer
                            </span>
                        @endif
                    </div>
                    
                    <!-- Contact Details -->
                    <div class="space-y-2">
                        <!-- Contact Person -->
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>
                                <span class="text-xs text-gray-500">Contact Person:</span>
                                <div class="text-sm text-gray-900">
                                    @if($contact->contact_name)
                                        {{ $contact->contact_name }}
                                    @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                        @php
                                            $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                            $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                        @endphp
                                        @if($firstValidPic)
                                            {{ $firstValidPic->name }}
                                            @if($firstValidPic->is_primary)
                                                <span class="ml-1 text-xs text-green-600">(Primary)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Position -->
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <span class="text-xs text-gray-500">Position:</span>
                                <div class="text-sm text-gray-900">
                                    @if($contact->position)
                                        {{ $contact->position }}
                                    @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                        @php
                                            $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                            $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                        @endphp
                                        @if($firstValidPic)
                                            {{ $firstValidPic->position }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <div>
                                <span class="text-xs text-gray-500">Phone:</span>
                                <div class="text-sm text-gray-900">
                                    @if($contact->phone_number)
                                        <a href="tel:{{ $contact->phone_number }}" class="text-blue-600 hover:underline">{{ $contact->phone_number }}</a>
                                    @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                        @php
                                            $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                            $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                        @endphp
                                        @if($firstValidPic && $firstValidPic->phone_number && $firstValidPic->phone_number != 'N/A')
                                            <a href="tel:{{ $firstValidPic->phone_number }}" class="text-blue-600 hover:underline">{{ $firstValidPic->phone_number }}</a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <span class="text-xs text-gray-500">Email:</span>
                                <div class="text-sm text-gray-900">
                                    @if($contact->email)
                                        <a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:underline">{{ $contact->email }}</a>
                                    @elseif($contact->contactPeople && $contact->contactPeople->count() > 0)
                                        @php
                                            $primaryPic = $contact->contactPeople->where('is_primary', true)->where('name', '!=', 'N/A')->first();
                                            $firstValidPic = $primaryPic ?? $contact->contactPeople->where('name', '!=', 'N/A')->first();
                                        @endphp
                                        @if($firstValidPic && $firstValidPic->email && $firstValidPic->email != 'N/A')
                                            <a href="mailto:{{ $firstValidPic->email }}" class="text-blue-600 hover:underline">{{ $firstValidPic->email }}</a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions Footer -->
                <div class="p-3 bg-gray-50 flex justify-between items-center">
                    <span class="text-xs text-gray-500">
                        @php
                            $contactCount = $contacts->where('company_name', $contact->company_name)->count();
                        @endphp
                        @if($contactCount > 1)
                            {{ $contactCount }} contacts at this company
                        @endif
                    </span>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('sales_officer.contacts.show', $contact->id) }}" class="text-green-600 hover:text-green-800 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('sales_officer.contacts.edit', $contact->id) }}" class="text-blue-600 hover:text-blue-800 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        @unless($contact->sales_mission_detail_id)
                            <form action="{{ route('sales_officer.contacts.destroy', $contact->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" onclick="return confirm('Are you sure you want to delete this contact?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endunless
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile bottom sheet functionality
        const mobileFilterButton = document.getElementById('mobileFilterButton');
        const mobileFilterSheet = document.getElementById('mobileFilterSheet');
        const closeFilterSheet = document.getElementById('closeFilterSheet');
        const applyFilters = document.getElementById('applyFilters');
        
        // Desktop filters
        const viewType = document.getElementById('viewType');
        const sourceFilter = document.getElementById('sourceFilter');
        
        // Mobile filters
        const mobileViewType = document.getElementById('mobileViewType');
        const mobileSourceFilter = document.getElementById('mobileSourceFilter');
        
        // View containers
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        const groupedView = document.getElementById('groupedView');
        const desktopCardView = document.getElementById('desktopCardView');
        
        // Initialize default view based on screen size
        function initializeView() {
            const isMobile = window.innerWidth < 768;
            
            if (isMobile) {
                // Default to card view on mobile
                cardView.classList.remove('hidden');
                cardView.classList.add('block');
                tableView.classList.add('hidden');
                groupedView.classList.add('hidden');
                desktopCardView.classList.add('hidden');
                
                // Set mobile select value
                mobileViewType.value = 'card';
            } else {
                // Default to card view on desktop too
                cardView.classList.add('hidden');
                tableView.classList.add('hidden');
                groupedView.classList.add('hidden');
                desktopCardView.classList.remove('hidden');
                
                // Set desktop select value
                viewType.value = 'card';
            }
            
            // Apply source filters
            applySourceFilter('all');
        }
        
        // Handle changing views
        function changeView(view) {
            const isMobile = window.innerWidth < 768;
            
            // Hide all views first
            cardView.classList.add('hidden');
            tableView.classList.add('hidden');
            groupedView.classList.add('hidden');
            desktopCardView.classList.add('hidden');
            
            // Show the selected view
            if (view === 'card') {
                if (isMobile) {
                    cardView.classList.remove('hidden');
                } else {
                    desktopCardView.classList.remove('hidden');
                }
            } else if (view === 'table') {
                tableView.classList.remove('hidden');
            } else if (view === 'group') {
                groupedView.classList.remove('hidden');
            }
        }
        
        // Filter contacts by source
        function applySourceFilter(source) {
            const contactCards = document.querySelectorAll('.contact-card');
            
            contactCards.forEach(card => {
                const cardSource = card.getAttribute('data-source');
                
                if (source === 'all' || cardSource === source) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }
        
        // Mobile bottom sheet events
        if (mobileFilterButton) {
            mobileFilterButton.addEventListener('click', function() {
                mobileFilterSheet.classList.remove('hidden');
                // Add animation class to slide up
                setTimeout(() => {
                    mobileFilterSheet.querySelector('div').classList.add('translate-y-0');
                }, 10);
            });
        }
        
        if (closeFilterSheet) {
            closeFilterSheet.addEventListener('click', function() {
                // Add animation class to slide down
                mobileFilterSheet.querySelector('div').classList.remove('translate-y-0');
                mobileFilterSheet.querySelector('div').classList.add('translate-y-full');
                
                // Hide after animation completes
                setTimeout(() => {
                    mobileFilterSheet.classList.add('hidden');
                    mobileFilterSheet.querySelector('div').classList.remove('translate-y-full');
                }, 300);
            });
        }
        
        if (applyFilters) {
            applyFilters.addEventListener('click', function() {
                const selectedView = mobileViewType.value;
                const selectedSource = mobileSourceFilter.value;
                
                changeView(selectedView);
                applySourceFilter(selectedSource);
                
                // Close the bottom sheet
                closeFilterSheet.click();
            });
        }
        
        // Desktop filter events
        if (viewType) {
            viewType.addEventListener('change', function() {
                changeView(this.value);
            });
        }
        
        if (sourceFilter) {
            sourceFilter.addEventListener('change', function() {
                applySourceFilter(this.value);
            });
        }
        
        // Sync mobile and desktop filters
        if (mobileViewType && viewType) {
            mobileViewType.addEventListener('change', function() {
                viewType.value = this.value;
            });
            
            viewType.addEventListener('change', function() {
                mobileViewType.value = this.value;
            });
        }
        
        if (mobileSourceFilter && sourceFilter) {
            mobileSourceFilter.addEventListener('change', function() {
                sourceFilter.value = this.value;
            });
            
            sourceFilter.addEventListener('change', function() {
                mobileSourceFilter.value = this.value;
            });
        }
        
        // Initialize default view
        initializeView();
        
        // Company header click event for grouped view (existing functionality)
        const companyHeaders = document.querySelectorAll('.company-header');
        companyHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const container = this.nextElementSibling;
                const chevron = this.querySelector('.chevron-icon');
                
                container.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            initializeView();
        });
    });
</script>
@endpush

@endsection 