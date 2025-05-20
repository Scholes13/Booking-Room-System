@extends('superadmin.layout')

@section('title', 'Sales Mission Management')

@section('content')
<div class="flex flex-col gap-6 h-full">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Sales Mission Activities</h1>
            <p class="text-gray-500 mt-1">View and manage all sales mission activities</p>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="flex flex-col gap-6 bg-white rounded-lg p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-700">Filter Sales Missions</h2>
        <form id="filterForm" action="{{ route('superadmin.activities.sales_mission') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" name="search" value="{{ request('search') }}" id="searchInput" class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c]" placeholder="Search company, PIC...">
            </div>
            
            <div class="relative flex-1 min-w-[200px]">
                <select name="status" id="statusFilter" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c]">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div class="relative flex-1 min-w-[200px]">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c]" placeholder="Start Date">
            </div>
            
            <div class="relative flex-1 min-w-[200px]">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c]" placeholder="End Date">
            </div>
            
            <div class="flex-0">
                <button type="submit" class="w-full px-4 py-2.5 bg-[#24448c] text-white rounded-lg hover:bg-[#1c3670] focus:ring-2 focus:ring-[#24448c]/50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
            </div>
            
            <div class="flex-0">
                <a href="{{ route('superadmin.activities.sales_mission') }}" class="inline-block w-full px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </a>
            </div>
        </form>
    </div>
    
    <!-- Activities Table -->
    <div class="flex flex-col gap-6 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Sales Mission List</h2>
            <div class="text-sm text-gray-500">Total: {{ $activities->total() }} sales missions</div>
        </div>
        
        <div class="overflow-x-auto rounded-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Company</th>
                        <th scope="col" class="px-5 py-3.5">PIC</th>
                        <th scope="col" class="px-5 py-3.5">Sales Person</th>
                        <th scope="col" class="px-5 py-3.5">Location</th>
                        <th scope="col" class="px-5 py-3.5">Date</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5">Actions</th>
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
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->salesMissionDetail->company_contact }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $activity->name }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->department->name ?? 'N/A' }}</div>
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
                                $now = \Carbon\Carbon::now();
                                $start = \Carbon\Carbon::parse($activity->start_datetime);
                                $end = \Carbon\Carbon::parse($activity->end_datetime);
                                
                                if ($now < $start) {
                                    $status = 'scheduled';
                                    $badge = 'badge-blue';
                                    $statusText = 'Scheduled';
                                } elseif ($now >= $start && $now <= $end) {
                                    $status = 'ongoing';
                                    $badge = 'badge-green';
                                    $statusText = 'Ongoing';
                                } elseif ($now > $end) {
                                    $status = 'completed';
                                    $badge = 'badge-indigo';
                                    $statusText = 'Completed';
                                } else {
                                    $status = 'unknown';
                                    $badge = 'badge-gray';
                                    $statusText = 'Unknown';
                                }
                            @endphp
                            <span class="badge {{ $badge }}">{{ $statusText }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex space-x-2">
                                <a href="#" 
                                   class="view-detail px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center"
                                   data-id="{{ $activity->id }}"
                                   data-company="{{ $activity->salesMissionDetail->company_name }}"
                                   data-company-pic="{{ $activity->salesMissionDetail->company_pic }}"
                                   data-company-contact="{{ $activity->salesMissionDetail->company_contact }}"
                                   data-company-address="{{ $activity->salesMissionDetail->company_address }}"
                                   data-employee-name="{{ $activity->name }}"
                                   data-department="{{ $activity->department->name ?? 'N/A' }}"
                                   data-city="{{ $activity->city }}"
                                   data-province="{{ $activity->province }}"
                                   data-start="{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y H:i') }}"
                                   data-end="{{ \Carbon\Carbon::parse($activity->end_datetime)->format('d M Y H:i') }}"
                                   data-description="{{ $activity->description }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Detail
                                </a>
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
        
        <div class="px-6 py-3">
            {{ $activities->links() }}
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-4xl w-full mx-4 transform transition-transform duration-300 scale-100 overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-center mb-4 border-b pb-3">
            <h3 class="text-xl font-bold text-[#24448c]">Sales Mission Details</h3>
            <button id="closeDetailModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Company Information</h4>
                    <p id="company-name" class="text-lg font-semibold text-gray-900"></p>
                    <p id="company-address" class="text-sm text-gray-600 mt-1"></p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Contact Person</h4>
                    <p id="company-pic" class="text-lg font-semibold text-gray-900"></p>
                    <p id="company-contact" class="text-sm text-gray-600 mt-1"></p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Location</h4>
                    <p id="location" class="text-lg font-semibold text-gray-900"></p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Sales Person</h4>
                    <p id="employee-name" class="text-lg font-semibold text-gray-900"></p>
                    <p id="department" class="text-sm text-gray-600 mt-1"></p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Date & Time</h4>
                    <p id="start-date" class="text-sm text-gray-600">Start: </p>
                    <p id="end-date" class="text-sm text-gray-600">End: </p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500">Description</h4>
            <p id="description" class="text-sm text-gray-600 mt-1 whitespace-pre-line"></p>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-sm transition-colors" id="closeDetailModalBtn">
                Close
            </button>
        </div>
    </div>
</div>

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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when filters change
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        let timer;
        searchInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
        
        statusFilter.addEventListener('change', function() {
            filterForm.submit();
        });
        
        dateInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
        
        // Detail modal functionality
        const detailModal = document.getElementById('detailModal');
        const closeButtons = [
            document.getElementById('closeDetailModal'),
            document.getElementById('closeDetailModalBtn')
        ];
        
        const viewDetailButtons = document.querySelectorAll('.view-detail');
        
        viewDetailButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Fill in modal data
                document.getElementById('company-name').textContent = this.dataset.company;
                document.getElementById('company-address').textContent = this.dataset.companyAddress;
                document.getElementById('company-pic').textContent = this.dataset.companyPic;
                document.getElementById('company-contact').textContent = this.dataset.companyContact;
                document.getElementById('location').textContent = `${this.dataset.city}, ${this.dataset.province}`;
                document.getElementById('employee-name').textContent = this.dataset.employeeName;
                document.getElementById('department').textContent = this.dataset.department;
                document.getElementById('start-date').textContent = `Start: ${this.dataset.start}`;
                document.getElementById('end-date').textContent = `End: ${this.dataset.end}`;
                document.getElementById('description').textContent = this.dataset.description;
                
                // Show modal
                detailModal.classList.remove('hidden');
                detailModal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            });
        });
        
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                detailModal.classList.add('hidden');
                detailModal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            });
        });
        
        // Close modal when clicking outside
        detailModal.addEventListener('click', function(e) {
            if (e.target === detailModal) {
                detailModal.classList.add('hidden');
                detailModal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });
</script>
@endpush
@endsection 