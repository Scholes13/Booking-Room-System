@extends('sales_officer.layout')

@section('title', 'Activities')
@section('header', 'Activities')
@section('description', 'Manage your activities')

@section('content')
<div class="bg-white p-2 sm:p-6 rounded-lg shadow-sm border border-gray-100 mx-0">
    <!-- Top section with title and create button -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
        <h4 class="text-xl font-semibold text-gray-800">All Activities</h4>
        
        <!-- Create button - positioned differently on mobile and desktop -->
        <a href="{{ route('sales_officer.activities.create') }}" 
           class="md:w-auto md:ml-auto hidden md:flex bg-primary hover:bg-primary-dark text-white py-2.5 px-4 rounded-lg text-base font-medium items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Create New
        </a>
    </div>
    
    <!-- Mobile optimized create button - full width, prominent placement -->
    <div class="mb-5 md:hidden px-0">
        <a href="{{ route('sales_officer.activities.create') }}" 
           class="w-full flex bg-primary hover:bg-primary-dark text-white py-3.5 px-4 rounded-lg text-base font-medium items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Create New
        </a>
    </div>
    
    <!-- View switcher - simplified for mobile, more options for desktop -->
    <div class="mb-5">
        <div class="relative w-full md:w-64">
            <button id="viewSwitcherBtn" class="w-full flex justify-between items-center bg-gray-100 text-gray-700 px-4 py-3 rounded-lg border border-gray-200">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    <span id="currentView">Card View</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="viewOptions" class="absolute mt-1 bg-white shadow-lg rounded-lg py-2 z-10 w-full hidden">
                <button data-view="card" class="w-full text-left px-4 py-2 hover:bg-gray-100 view-option">Card View</button>
                <button data-view="table" class="w-full text-left px-4 py-2 hover:bg-gray-100 view-option">Table View</button>
            </div>
        </div>
    </div>
    
    <!-- Card View - optimized mobile cards with better spacing -->
    <div id="cardView" class="space-y-4 px-0">
        @forelse($activities as $activity)
            <div class="border rounded-lg overflow-hidden shadow-sm">
                <div class="p-4">
                    <!-- Date and Lead Status Header -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm font-medium text-gray-700 bg-gray-100 px-3 py-1.5 rounded">
                            {{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}
                        </div>
                        <span class="px-3 py-1.5 text-sm font-medium rounded-full 
                            @if(str_contains($activity->jso_lead_status, 'Hot'))
                                bg-red-100 text-red-800
                            @elseif(str_contains($activity->jso_lead_status, 'Cold'))
                                bg-blue-100 text-blue-800
                            @elseif(str_contains($activity->jso_lead_status, 'Handed'))
                                bg-green-100 text-green-800
                            @elseif(str_contains($activity->jso_lead_status, 'progress'))
                                bg-yellow-100 text-yellow-800
                            @elseif(str_contains($activity->jso_lead_status, 'Lost'))
                                bg-gray-100 text-gray-800
                            @elseif(str_contains($activity->jso_lead_status, 'Closed'))
                                bg-purple-100 text-purple-800
                            @else
                                bg-purple-100 text-purple-800
                            @endif
                        ">
                            {{ $activity->jso_lead_status }}
                        </span>
                    </div>
                    
                    <!-- Activity Type as Title -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-5">{{ $activity->activity_type }}</h3>
                    
                    <!-- Company and Account Status with better spacing -->
                    <div class="space-y-3 mb-5">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <div>
                                <span class="text-sm text-gray-500">Company:</span>
                                <div class="text-sm font-medium text-gray-900">{{ $activity->contact ? $activity->contact->company_name : 'N/A' }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <span class="text-sm text-gray-500">Client Type:</span>
                                <div class="text-sm font-medium text-gray-900">{{ $activity->account_status ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons with cleaner design - better centered on mobile -->
                    <div class="pt-4 border-t border-gray-200 flex justify-center">
                        <div class="flex w-full justify-around">
                            <a href="{{ route('sales_officer.activities.edit', $activity->id) }}" class="text-blue-600 font-medium text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            
                            <form action="{{ route('sales_officer.activities.destroy', $activity->id) }}" method="POST" class="text-center">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-medium mx-auto" onclick="return confirm('Are you sure you want to delete this activity?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-gray-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500 mb-4">No activities found</p>
                <a href="{{ route('sales_officer.activities.create') }}" class="inline-block bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Create Your First Activity
                </a>
            </div>
        @endforelse
        
        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </div>
    
    <!-- Table View -->
    <div id="tableView" class="overflow-x-auto hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Client Type</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead Status</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->activity_type }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->contact ? $activity->contact->company_name : 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-sm text-gray-900">{{ $activity->account_status ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if(str_contains($activity->jso_lead_status, 'Hot'))
                                    bg-red-100 text-red-800
                                @elseif(str_contains($activity->jso_lead_status, 'Cold'))
                                    bg-blue-100 text-blue-800
                                @elseif(str_contains($activity->jso_lead_status, 'Handed'))
                                    bg-green-100 text-green-800
                                @elseif(str_contains($activity->jso_lead_status, 'progress'))
                                    bg-yellow-100 text-yellow-800
                                @elseif(str_contains($activity->jso_lead_status, 'Lost'))
                                    bg-gray-100 text-gray-800
                                @else
                                    bg-purple-100 text-purple-800
                                @endif
                            ">
                                {{ $activity->jso_lead_status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('sales_officer.activities.edit', $activity->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('sales_officer.activities.destroy', $activity->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this activity?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-sm text-center text-gray-500">No activities found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View switching functionality
        const viewSwitcherBtn = document.getElementById('viewSwitcherBtn');
        const viewOptions = document.getElementById('viewOptions');
        const currentViewText = document.getElementById('currentView');
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        
        // Detect if user is on mobile or desktop
        function isMobile() {
            return window.innerWidth < 768;
        }
        
        // Set initial view based on device type
        function setInitialView() {
            if (isMobile()) {
                // Mobile - use card view
                cardView.classList.remove('hidden');
                tableView.classList.add('hidden');
                currentViewText.textContent = 'Card View';
            } else {
                // Desktop - use table view
                cardView.classList.add('hidden');
                tableView.classList.remove('hidden');
                currentViewText.textContent = 'Table View';
            }
        }
        
        // Initialize default view based on device
        setInitialView();
        
        // Toggle view options dropdown
        if (viewSwitcherBtn) {
            viewSwitcherBtn.addEventListener('click', function() {
                viewOptions.classList.toggle('hidden');
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (viewOptions && !viewOptions.classList.contains('hidden') && 
                !viewSwitcherBtn.contains(event.target) && 
                !viewOptions.contains(event.target)) {
                viewOptions.classList.add('hidden');
            }
        });
        
        // Handle view selection
        document.querySelectorAll('.view-option').forEach(option => {
            option.addEventListener('click', function() {
                const selectedView = this.dataset.view;
                currentViewText.textContent = this.textContent;
                viewOptions.classList.add('hidden');
                
                // Update view
                if (selectedView === 'card') {
                    cardView.classList.remove('hidden');
                    tableView.classList.add('hidden');
                } else {
                    cardView.classList.add('hidden');
                    tableView.classList.remove('hidden');
                }
            });
        });
        
        // Update view on resize
        window.addEventListener('resize', function() {
            // Only change views automatically if they haven't manually selected one
            // You could implement a user preference storage here too (localStorage, etc)
            setInitialView();
        });
    });
</script>
@endpush

@endsection 