@extends('sales_officer.layout')

@section('title', 'Contacts')
@section('header', 'Contacts')
@section('description', 'Manage your business contacts')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div class="flex flex-wrap justify-between items-center mb-6">
        <div class="flex flex-col md:flex-row md:items-center gap-4 w-full md:w-auto">
            <h4 class="text-base font-semibold text-gray-800">Business Contacts</h4>
            <div class="flex-grow md:max-w-md">
                <form action="{{ route('sales_officer.contacts.index') }}" method="GET" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..." 
                        class="w-full rounded-l-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-r-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- View Options -->
        <div class="flex items-center gap-3 mt-3 md:mt-0 w-full md:w-auto">
            <select id="viewType" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <option value="table">Table View</option>
                <option value="group">Group by Company</option>
                <option value="multiple">Companies with Multiple Contacts</option>
            </select>
            
            <select id="sourceFilter" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <option value="all">All Sources</option>
                <option value="sales_officer">Sales Officer</option>
                <option value="sales_mission">Sales Mission</option>
            </select>
            
            <a href="{{ route('sales_officer.contacts.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add New Contact
            </a>
        </div>
    </div>
    
    <!-- Grouped View -->
    <div id="groupedView" class="space-y-4 hidden">
        @php
            $groupedContacts = $contacts->groupBy('company_name');
        @endphp
        
        @foreach($groupedContacts as $companyName => $companyContacts)
            <div class="border rounded-lg overflow-hidden shadow-sm">
                <!-- Company Header -->
                <div class="bg-gray-50 p-4 flex justify-between items-center cursor-pointer company-header" data-company="{{ $companyName }}">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform chevron-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <h3 class="font-semibold text-gray-800">{{ $companyName }}</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $companyContacts->count() }} {{ $companyContacts->count() > 1 ? 'contacts' : 'contact' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2">
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
        @endforeach
    </div>
    
    <!-- Original Table View -->
    <div id="tableView" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
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
                        <td class="px-4 py-4 whitespace-nowrap">
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
    
    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View type switcher
        const viewType = document.getElementById('viewType');
        const groupedView = document.getElementById('groupedView');
        const tableView = document.getElementById('tableView');
        const sourceFilter = document.getElementById('sourceFilter');
        
        // Initialize views
        function updateView() {
            if (viewType.value === 'table') {
                groupedView.classList.add('hidden');
                tableView.classList.remove('hidden');
            } else {
                groupedView.classList.remove('hidden');
                tableView.classList.add('hidden');
                
                // For "multiple" view, hide companies with only one contact
                if (viewType.value === 'multiple') {
                    document.querySelectorAll('#groupedView > div').forEach(companyDiv => {
                        const contactCount = companyDiv.querySelector('.rounded-full').textContent.trim().split(' ')[0];
                        
                        if (parseInt(contactCount) <= 1) {
                            companyDiv.classList.add('hidden');
                        } else {
                            companyDiv.classList.remove('hidden');
                        }
                    });
                } else {
                    // Show all companies for grouped view
                    document.querySelectorAll('#groupedView > div').forEach(companyDiv => {
                        companyDiv.classList.remove('hidden');
                    });
                }
            }
            
            // Apply source filter
            applySourceFilter();
        }
        
        viewType.addEventListener('change', updateView);
        
        // Toggle company contacts visibility
        document.querySelectorAll('.company-header').forEach(header => {
            header.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.closest('a')) return; // Don't toggle when clicking the View Company link
                
                const container = this.nextElementSibling;
                const chevron = this.querySelector('.chevron-icon');
                
                container.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });
        });
        
        // Source filter
        function applySourceFilter() {
            const source = sourceFilter.value;
            
            if (viewType.value === 'table') {
                // Apply to table rows
                document.querySelectorAll('#tableView tbody tr').forEach(row => {
                    if (source === 'all') {
                        row.classList.remove('hidden');
                    } else if (source === row.getAttribute('data-source')) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            } else {
                // Apply to grouped view
                document.querySelectorAll('#groupedView > div').forEach(companyDiv => {
                    const companySource = companyDiv.querySelector('.company-header').querySelector('.rounded-full').nextElementSibling.innerText.includes('Sales Mission') 
                        ? 'sales_mission' 
                        : 'sales_officer';
                    
                    if (source === 'all' || source === companySource) {
                        // Only show if also matches multiple filter
                        if (viewType.value !== 'multiple' || parseInt(companyDiv.querySelector('.rounded-full').textContent.trim().split(' ')[0]) > 1) {
                            companyDiv.classList.remove('hidden');
                        }
                    } else {
                        companyDiv.classList.add('hidden');
                    }
                });
            }
        }
        
        sourceFilter.addEventListener('change', applySourceFilter);
        
        // Initialize view
        updateView();
        
        // Auto expand first company in group view if none expanded
        if (document.querySelectorAll('#groupedView > div').length > 0 && 
            document.querySelectorAll('#groupedView .contacts-container:not(.hidden)').length === 0) {
            
            const firstHeader = document.querySelector('#groupedView .company-header');
            const firstContainer = firstHeader.nextElementSibling;
            const firstChevron = firstHeader.querySelector('.chevron-icon');
            
            firstContainer.classList.remove('hidden');
            firstChevron.classList.add('rotate-180');
        }
    });
</script>
@endpush
@endsection 