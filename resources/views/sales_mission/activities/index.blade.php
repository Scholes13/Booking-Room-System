@extends('sales_mission.layout')

@section('title', 'Sales Mission List')
@section('header', 'Sales Mission List')
@section('description', 'View and manage all your sales mission activities')

@push('styles')
<style>
    /* Scrollbar styling */
    .scrollbar-thin {
        scrollbar-width: thin;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Status badges */
    .badge {
        @apply inline-flex text-xs font-semibold px-2.5 py-0.5 rounded-full;
    }
    
    .badge-blue {
        @apply bg-blue-100 text-blue-800;
    }
    
    .badge-green {
        @apply bg-green-100 text-green-800;
    }
    
    .badge-indigo {
        @apply bg-indigo-100 text-indigo-800;
    }
    
    .badge-red {
        @apply bg-red-100 text-red-800;
    }
    
    .badge-gray {
        @apply bg-gray-100 text-gray-800;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-6 h-full">
    <!-- Filter Section -->
    <div class="flex flex-col gap-6 bg-white rounded-lg p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-700">Filter Sales Missions</h2>
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" id="searchInput" class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500" placeholder="Search company, PIC...">
            </div>
            
            <div class="relative flex-1 min-w-[200px]">
                <select id="statusFilter" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500">
                    <option selected value="">All Status</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <input type="text" id="startDateFilter" class="flatpickr-date block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500" placeholder="Start date">
            </div>
            
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <input type="text" id="endDateFilter" class="flatpickr-date block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500" placeholder="End date">
            </div>
            
            {{-- Filter by City --}}
            <div class="relative flex-1 min-w-[200px]">
                <select id="locationFilter" name="filter_location" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Cities</option>
                    @if(isset($cities) && $cities->count() > 0)
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ (isset($filterLocationValue) && $filterLocationValue == $city) ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>No cities available</option>
                    @endif
                </select>
            </div>
            
            <form id="filterForm" action="{{ route('sales_mission.activities.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="search" id="searchParam">
                <input type="hidden" name="status" id="statusParam">
                <input type="hidden" name="start_date" id="startDateParam">
                <input type="hidden" name="end_date" id="endDateParam">
                <input type="hidden" name="filter_location" id="locationParam">
            </form>
            
            <a href="{{ route('sales_mission.activities.index') }}" class="flex items-center justify-center py-2.5 px-4 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset
            </a>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="flex flex-col gap-6 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Sales Mission List</h2>
            <div class="text-sm text-gray-500">Total: {{ $activities->total() }} sales missions</div>
        </div>
        
        <div class="overflow-x-auto rounded-lg scrollbar-thin scrollbar-thumb-gray-300">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Company</th>
                        <th scope="col" class="px-5 py-3.5">PIC</th>
                        <th scope="col" class="px-5 py-3.5">Location</th>
                        <th scope="col" class="px-5 py-3.5">Date</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5">Description</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900 break-words">{{ $activity->salesMissionDetail->company_name }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $activity->salesMissionDetail->company_pic }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->salesMissionDetail->company_position }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->salesMissionDetail->company_contact }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $activity->city }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->province }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-900">{{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') : 'N/A' }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('H:i') : '' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $statusClass = 'badge-gray';
                                if($activity->status === 'scheduled') $statusClass = 'badge-blue';
                                elseif($activity->status === 'ongoing') $statusClass = 'badge-green';
                                elseif($activity->status === 'completed') $statusClass = 'badge-indigo';
                                elseif($activity->status === 'cancelled') $statusClass = 'badge-red';
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($activity->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-500 line-clamp-2">{{ $activity->description ?? 'No description available' }}</div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" class="assign-team px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center"
                                    data-id="{{ $activity->id }}"
                                    data-company="{{ $activity->salesMissionDetail->company_name }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Assign Team
                                </button>
                                <button type="button" class="edit-activity px-3 py-1.5 text-xs font-medium bg-amber-50 text-amber-700 rounded-md hover:bg-amber-100 transition-colors inline-flex items-center" 
                                    data-id="{{ $activity->id }}" 
                                    data-company="{{ $activity->salesMissionDetail->company_name }}"
                                    data-pic="{{ $activity->salesMissionDetail->company_pic }}"
                                    data-position="{{ $activity->salesMissionDetail->company_position }}"
                                    data-contact="{{ $activity->salesMissionDetail->company_contact }}"
                                    data-email="{{ $activity->salesMissionDetail->company_email }}"
                                    data-address="{{ $activity->salesMissionDetail->company_address }}"
                                    data-department="{{ $activity->department_id }}"
                                    data-status="{{ $activity->status }}"
                                    data-city="{{ $activity->city }}"
                                    data-province="{{ $activity->province }}"
                                    data-description="{{ $activity->description }}"
                                    data-start="{{ \Carbon\Carbon::parse($activity->start_datetime)->format('Y-m-d H:i') }}"
                                    data-end="{{ \Carbon\Carbon::parse($activity->end_datetime)->format('Y-m-d H:i') }}"
                                    data-employee="{{ $activity->name }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" class="delete-activity px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center"
                                    data-id="{{ $activity->id }}"
                                    data-company="{{ $activity->salesMissionDetail->company_name }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Sales Missions Found</h3>
                                <p class="text-gray-500 text-sm">Try changing your search criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center px-6 py-4 gap-4">
            <div class="text-sm text-gray-600 flex items-center bg-gray-50 px-4 py-2 rounded-lg">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span>Showing {{ $activities->firstItem() ?? 0 }} to {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} entries</span>
            </div>
            
            <div>
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Edit Activity Modal -->
<div id="editActivityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-4xl w-full mx-4 transform transition-transform duration-300 scale-100 overflow-y-auto max-h-[90vh]">
        <h3 class="text-xl font-bold text-center mb-4 text-amber-700">Edit Sales Mission</h3>
        
        <form id="editActivityForm" action="" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Name field -->
                <div class="space-y-2">
                    <label for="edit-name" class="block text-sm font-medium text-gray-700">Employee Name</label>
                    <select 
                        id="edit-name" 
                        name="name" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm"
                        required
                    >
                        @foreach(App\Models\Employee::orderBy('name')->get() as $employee)
                            <option value="{{ $employee->name }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label for="edit-company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input 
                        type="text" 
                        id="edit-company_name" 
                        name="company_name" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>
                
                <div class="space-y-2">
                    <label for="edit-company_pic" class="block text-sm font-medium text-gray-700">Company PIC</label>
                    <input 
                        type="text" 
                        id="edit-company_pic" 
                        name="company_pic" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label for="edit-company_position" class="block text-sm font-medium text-gray-700">Jabatan PIC</label>
                    <input 
                        type="text" 
                        id="edit-company_position" 
                        name="company_position" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label for="edit-company_contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input 
                        type="text" 
                        id="edit-company_contact" 
                        name="company_contact" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label for="edit-company_email" class="block text-sm font-medium text-gray-700">Company Email</label>
                    <input 
                        type="email" 
                        id="edit-company_email" 
                        name="company_email" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label for="edit-department_id" class="block text-sm font-medium text-gray-700">Department</label>
                    <select 
                        id="edit-department_id" 
                        name="department_id" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm"
                        required
                    >
                        @foreach(App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="edit-status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select 
                        id="edit-status" 
                        name="status" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm"
                        required
                    >
                        <option value="scheduled">Scheduled</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="edit-province" class="block text-sm font-medium text-gray-700">Province</label>
                    <input 
                        type="text" 
                        id="edit-province" 
                        name="province" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label for="edit-city" class="block text-sm font-medium text-gray-700">City</label>
                    <input 
                        type="text" 
                        id="edit-city" 
                        name="city" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label for="edit-start_datetime" class="block text-sm font-medium text-gray-700">Start Date & Time</label>
                    <input 
                        type="text" 
                        id="edit-start_datetime" 
                        name="start_datetime" 
                        class="flatpickr-datetime w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>

                <div class="space-y-2">
                    <label for="edit-end_datetime" class="block text-sm font-medium text-gray-700">End Date & Time</label>
                    <input 
                        type="text" 
                        id="edit-end_datetime" 
                        name="end_datetime" 
                        class="flatpickr-datetime w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                        required
                    />
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="edit-company_address" class="block text-sm font-medium text-gray-700">Company Address</label>
                <textarea 
                    id="edit-company_address" 
                    name="company_address" 
                    rows="2" 
                    class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                ></textarea>
            </div>

            <div class="space-y-2">
                <label for="edit-description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea 
                    id="edit-description" 
                    name="description" 
                    rows="4" 
                    class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                    required
                ></textarea>
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelEditActivity" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500">
                    Update Sales Mission
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteActivityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="text-center mb-6">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Delete Sales Mission</h3>
            <p class="text-gray-500" id="delete-mission-text">Are you sure you want to delete this sales mission?</p>
        </div>
        
        <form id="deleteActivityForm" action="" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelDeleteActivity" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Team Modal -->
<div id="assignTeamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-3xl w-full mx-4 transform transition-transform duration-300 scale-100 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-amber-700">Assign Team</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="assignTeamForm" action="{{ route('sales_mission.field-visits.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" id="activity_id" name="activity_id">
            
            <div class="mb-4">
                <p class="text-lg font-medium text-gray-800" id="company-name-display"></p>
                <p class="text-sm text-gray-600">The appointment date/time shown in the table is when the sales mission is scheduled</p>
            </div>
            
            <!-- Team Selection -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Select Team <span class="text-red-500">*</span></label>
                <div id="teamsContainer" class="grid grid-cols-1 gap-3 max-h-64 overflow-y-auto p-2 border border-gray-200 rounded-lg">
                    <div class="text-center p-4 text-gray-500">Loading teams...</div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" class="close-modal px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    Assign Team
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize flatpickr for date inputs in the filter section
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            locale: "id",
            allowInput: true,
            altInput: true,
            altFormat: "d M Y",
            disableMobile: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (instance.input.id === 'startDateFilter') {
                    document.getElementById('startDateParam').value = dateStr;
                } else if (instance.input.id === 'endDateFilter') {
                    document.getElementById('endDateParam').value = dateStr;
                }
                submitFilterForm();
            }
        });
        
        // Edit Activity Modal
        const editActivityModal = document.getElementById('editActivityModal');
        const editActivityForm = document.getElementById('editActivityForm');
        const editButtons = document.querySelectorAll('.edit-activity');
        const cancelEditActivity = document.getElementById('cancelEditActivity');
        
        // Delete Activity Modal
        const deleteActivityModal = document.getElementById('deleteActivityModal');
        const deleteActivityForm = document.getElementById('deleteActivityForm');
        const deleteButtons = document.querySelectorAll('.delete-activity');
        const cancelDeleteActivity = document.getElementById('cancelDeleteActivity');
        const deleteMissionText = document.getElementById('delete-mission-text');
        
        // Edit Activity Button Event Listeners
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const company = this.getAttribute('data-company');
                const pic = this.getAttribute('data-pic');
                const position = this.getAttribute('data-position');
                const contact = this.getAttribute('data-contact');
                const address = this.getAttribute('data-address');
                const department = this.getAttribute('data-department');
                const status = this.getAttribute('data-status');
                const city = this.getAttribute('data-city');
                const province = this.getAttribute('data-province');
                const description = this.getAttribute('data-description');
                const start = this.getAttribute('data-start');
                const end = this.getAttribute('data-end');
                const employee = this.getAttribute('data-employee');
                
                // Set form action
                editActivityForm.action = `{{ route('sales_mission.activities.index') }}/${id}`;
                
                // Set form values
                document.getElementById('edit-company_name').value = company;
                document.getElementById('edit-company_pic').value = pic;
                document.getElementById('edit-company_position').value = position;
                document.getElementById('edit-company_contact').value = contact;
                document.getElementById('edit-company_email').value = this.getAttribute('data-email');
                document.getElementById('edit-company_address').value = address;
                document.getElementById('edit-department_id').value = department;
                document.getElementById('edit-status').value = status;
                document.getElementById('edit-city').value = city;
                document.getElementById('edit-province').value = province;
                document.getElementById('edit-description').value = description;
                
                // Set datetime values and initialize flatpickr
                const startDateInput = document.getElementById('edit-start_datetime');
                const endDateInput = document.getElementById('edit-end_datetime');
                
                startDateInput.value = start;
                endDateInput.value = end;
                
                flatpickr(startDateInput, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    locale: "id"
                });
                
                flatpickr(endDateInput, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    locale: "id"
                });
                
                // Set employee name
                document.getElementById('edit-name').value = employee;
                
                // Show modal
                editActivityModal.classList.remove('hidden');
                editActivityModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });
        });
        
        // Cancel Edit Activity
        cancelEditActivity.addEventListener('click', function() {
            editActivityModal.classList.remove('flex');
            editActivityModal.classList.add('hidden');
            document.body.style.overflow = '';
        });
        
        // Delete Activity Button Event Listeners
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const company = this.getAttribute('data-company');
                
                // Set form action
                deleteActivityForm.action = `{{ route('sales_mission.activities.index') }}/${id}`;
                
                // Set confirmation message
                deleteMissionText.textContent = `Are you sure you want to delete the sales mission to "${company}"?`;
                
                // Show modal
                deleteActivityModal.classList.remove('hidden');
                deleteActivityModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });
        });
        
        // Cancel Delete Activity
        cancelDeleteActivity.addEventListener('click', function() {
            deleteActivityModal.classList.remove('flex');
            deleteActivityModal.classList.add('hidden');
            document.body.style.overflow = '';
        });
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editActivityModal) {
                editActivityModal.classList.remove('flex');
                editActivityModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            
            if (event.target === deleteActivityModal) {
                deleteActivityModal.classList.remove('flex');
                deleteActivityModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // Filter functionality
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const startDateFilter = document.getElementById('startDateFilter');
        const endDateFilter = document.getElementById('endDateFilter');
        const locationFilter = document.getElementById('locationFilter');
        const filterForm = document.getElementById('filterForm');
        const searchParam = document.getElementById('searchParam');
        const statusParam = document.getElementById('statusParam');
        const startDateParam = document.getElementById('startDateParam');
        const endDateParam = document.getElementById('endDateParam');
        const locationParam = document.getElementById('locationParam');

        // Set initial values from URL parameters if they exist
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) {
            searchInput.value = urlParams.get('search');
            searchParam.value = urlParams.get('search');
        }
        if (urlParams.has('status')) {
            statusFilter.value = urlParams.get('status');
            statusParam.value = urlParams.get('status');
        }
        if (urlParams.has('start_date') && startDateFilter._flatpickr) {
            startDateFilter._flatpickr.setDate(urlParams.get('start_date'));
            startDateParam.value = urlParams.get('start_date');
        }
        if (urlParams.has('end_date') && endDateFilter._flatpickr) {
            endDateFilter._flatpickr.setDate(urlParams.get('end_date'));
            endDateParam.value = urlParams.get('end_date');
        }
        if (urlParams.has('filter_location')) {
            locationFilter.value = urlParams.get('filter_location');
            locationParam.value = urlParams.get('filter_location');
        }

        // Function to submit the form
        const submitFilterForm = () => {
            // Update hidden inputs with current values
            searchParam.value = searchInput.value;
            statusParam.value = statusFilter.value;
            startDateParam.value = startDateFilter.value;
            endDateParam.value = endDateFilter.value;
            locationParam.value = locationFilter.value;
            filterForm.submit();
        };

        // Auto submit when search input changes (with small delay for typing)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFilterForm();
            }, 500); // 500ms delay to allow typing
        });
        
        // Auto submit when status changes
        statusFilter.addEventListener('change', function() {
            submitFilterForm();
        });

        // Setup flatpickr date input events
        if (startDateFilter._flatpickr) {
            startDateFilter._flatpickr.config.onClose = function(selectedDates, dateStr) {
                startDateParam.value = dateStr;
                submitFilterForm();
            };
        } else {
            startDateFilter.addEventListener('change', function() {
                submitFilterForm();
            });
        }
        
        if (endDateFilter._flatpickr) {
            endDateFilter._flatpickr.config.onClose = function(selectedDates, dateStr) {
                endDateParam.value = dateStr;
                submitFilterForm();
            };
        } else {
            endDateFilter.addEventListener('change', function() {
                submitFilterForm();
            });
        }

        // Still keep the form submit handler for the filter button
        filterForm.addEventListener('submit', function(e) {
            // Update hidden inputs with current values
            searchParam.value = searchInput.value;
            statusParam.value = statusFilter.value;
            startDateParam.value = startDateFilter.value;
            endDateParam.value = endDateFilter.value;
            locationParam.value = locationFilter.value;
        });

        // Allow pressing Enter in search field to submit form
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                submitFilterForm();
            }
        });

        // Assign Team functionality
        const assignTeamModal = document.getElementById('assignTeamModal');
        const assignTeamButtons = document.querySelectorAll('.assign-team');
        const closeModalButtons = document.querySelectorAll('.close-modal');
        const assignTeamForm = document.getElementById('assignTeamForm');
        
        // Fetch teams data
        async function fetchTeams() {
            try {
                const response = await fetch('{{ route("sales_mission.teams.json") }}');
                const data = await response.json();
                
                if (data.success) {
                    return data.teams;
                } else {
                    console.error('Error fetching teams:', data.message || 'Unknown error');
                    return [];
                }
            } catch (error) {
                console.error('Error fetching teams:', error);
                return [];
            }
        }
        
        // Populate teams in the modal
        async function populateTeams() {
            const teamsContainer = document.getElementById('teamsContainer');
            const teams = await fetchTeams();
            
            if (teams.length === 0) {
                teamsContainer.innerHTML = `
                    <div class="text-center p-4 text-gray-500">
                        No teams available. <a href="{{ route('sales_mission.teams.create') }}" class="text-amber-600 hover:underline">Create a team</a> first.
                    </div>
                `;
                return;
            }
            
            let html = '';
            teams.forEach(team => {
                html += `
                    <div class="border border-gray-200 rounded-lg p-3 team-card hover:bg-gray-50 transition-all">
                        <div class="flex items-start">
                            <input type="radio" id="team_${team.id}" name="team_id" value="${team.id}" 
                                class="mt-1 w-4 h-4 text-amber-600 focus:ring-amber-500" required>
                            <label for="team_${team.id}" class="ml-3 flex-1 cursor-pointer">
                                <div class="font-medium text-gray-900">${team.name}</div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                        ${team.members} members
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>
                `;
            });
            
            teamsContainer.innerHTML = html;
            
            // Add click event to team cards
            document.querySelectorAll('.team-card').forEach(card => {
                card.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Remove selected class from all cards
                    document.querySelectorAll('.team-card').forEach(c => {
                        c.classList.remove('ring-2', 'ring-amber-500', 'bg-amber-50');
                    });
                    
                    // Add selected class to this card
                    this.classList.add('ring-2', 'ring-amber-500', 'bg-amber-50');
                });
            });
        }
        
        // Show modal when assign team button is clicked
        assignTeamButtons.forEach(button => {
            button.addEventListener('click', function() {
                const activityId = this.dataset.id;
                const companyName = this.dataset.company;
                
                document.getElementById('activity_id').value = activityId;
                document.getElementById('company-name-display').textContent = companyName;
                
                // Populate teams
                populateTeams();
                
                // Show modal
                assignTeamModal.classList.remove('hidden');
                assignTeamModal.classList.add('flex');
            });
        });
        
        // Close modal when close button is clicked
        closeModalButtons.forEach(button => {
            button.addEventListener('click', function() {
                assignTeamModal.classList.remove('flex');
                assignTeamModal.classList.add('hidden');
            });
        });
        
        // Close modal when clicking outside
        assignTeamModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('flex');
                this.classList.add('hidden');
            }
        });
        
        // Handle form submission
        assignTeamForm.addEventListener('submit', function(e) {
            const teamSelected = document.querySelector('input[name="team_id"]:checked');
            
            if (!teamSelected) {
                e.preventDefault();
                alert('Please select a team to assign.');
            }
        });
    });
</script>
@endpush
@endsection 