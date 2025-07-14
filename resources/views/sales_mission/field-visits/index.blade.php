@extends('sales_mission.layout')

@section('title', 'Field Visits')
@section('header', 'Field Visits')
@section('description', 'Manage team assignments to sales mission activities')

@push('styles')
<style>
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
    
    .badge-amber {
        @apply bg-amber-100 text-amber-800;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header with Create Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Field Visits List</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('sales_mission.daily_schedule') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Daily Schedule
            </a>
            <a href="{{ route('public.field-visits.index') }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Public View
            </a>
            <a href="{{ route('sales_mission.field-visits.create') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Assign Team to Activity
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="space-y-4">
        <form id="filter-form" action="{{ route('sales_mission.field-visits.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Team Filter -->
            <div class="flex flex-col">
                <label for="team_id" class="mb-2 flex items-center text-sm font-medium text-gray-700">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Filter by Team
                </label>
                <select id="team_id" name="team_id" class="w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                    <option value="">All Teams</option>
                    @foreach(\App\Models\Team::orderBy('name')->get() as $team)
                        <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Location Filter -->
            <div class="flex flex-col">
                <label for="location" class="mb-2 flex items-center text-sm font-medium text-gray-700">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Filter by Location
                </label>
                <select id="location" name="location" class="w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                    <option value="">All Locations</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('location') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div class="flex flex-col">
                <label for="date" class="mb-2 flex items-center text-sm font-medium text-gray-700">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Filter by Date
                </label>
                <input type="date" id="date" name="date" value="{{ request('date') }}" class="w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
            </div>
        </form>
        
        <div class="flex justify-end gap-3">
            <a href="{{ route('sales_mission.field-visits.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                Reset Filters
            </a>
            <button type="submit" form="filter-form" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Results Counter -->
    <div class="flex items-center text-gray-600 text-sm">
        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <span>Showing {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }} results</span>
        @if(request()->hasAny(['team_id', 'location', 'date']))
            <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-xs">Filtered</span>
        @endif
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Assignments Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Team</th>
                        <th scope="col" class="px-5 py-3.5">Company</th>
                        <th scope="col" class="px-5 py-3.5">Location</th>
                        <th scope="col" class="px-5 py-3.5">Activity Date</th>
                        <th scope="col" class="px-5 py-3.5">Assignment Date</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $assignment->team->name }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $assignment->activity->salesMissionDetail->company_name }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $assignment->activity->salesMissionDetail->company_pic }} 
                                @if($assignment->activity->salesMissionDetail->company_position)
                                    ({{ $assignment->activity->salesMissionDetail->company_position }})
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $assignment->activity->city }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $assignment->activity->province }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-900">{{ $assignment->activity->start_datetime ? \Carbon\Carbon::parse($assignment->activity->start_datetime)->format('d M Y') : 'N/A' }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $assignment->activity->start_datetime ? \Carbon\Carbon::parse($assignment->activity->start_datetime)->format('H:i') : '' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-900">{{ $assignment->assignment_date ? \Carbon\Carbon::parse($assignment->assignment_date)->format('d M Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('sales_mission.field-visits.show', $assignment) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('sales_mission.field-visits.edit', $assignment) }}" class="px-3 py-1.5 text-xs font-medium bg-amber-50 text-amber-700 rounded-md hover:bg-amber-100 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <button type="button" class="delete-field-visit px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center"
                                    data-id="{{ $assignment->id }}"
                                    data-company="{{ $assignment->activity->salesMissionDetail->company_name }}"
                                    data-team="{{ $assignment->team->name }}">
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
                        <td colspan="6" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Field Visits Found</h3>
                                <p class="text-gray-500 text-sm mb-4">Start by assigning a team to an activity</p>
                                <a href="{{ route('sales_mission.field-visits.create') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Assign Team to Activity
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-5 py-4">
            {{ $assignments->links() }}
        </div>
    </div>
</div>
@endsection

<!-- Delete Confirmation Modal -->
<div id="deleteFieldVisitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="text-center mb-6">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Delete Field Visit</h3>
            <p class="text-gray-500" id="delete-field-visit-text">Are you sure you want to delete this field visit?</p>
        </div>
        
        <form id="deleteFieldVisitForm" action="" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelDeleteFieldVisit" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete Field Visit Modal
        const deleteFieldVisitModal = document.getElementById('deleteFieldVisitModal');
        const deleteFieldVisitForm = document.getElementById('deleteFieldVisitForm');
        const deleteButtons = document.querySelectorAll('.delete-field-visit');
        const cancelDeleteFieldVisit = document.getElementById('cancelDeleteFieldVisit');
        const deleteFieldVisitText = document.getElementById('delete-field-visit-text');
        
        // Delete Field Visit Button Event Listeners
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const company = this.getAttribute('data-company');
                const team = this.getAttribute('data-team');
                
                // Set form action
                deleteFieldVisitForm.action = `{{ url('sales/field-visits') }}/${id}`;
                
                // Set confirmation message
                deleteFieldVisitText.textContent = `Are you sure you want to delete the assignment of team "${team}" to "${company}"?`;
                
                // Show modal
                deleteFieldVisitModal.classList.remove('hidden');
                deleteFieldVisitModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });
        });
        
        // Cancel Delete Field Visit
        cancelDeleteFieldVisit.addEventListener('click', function() {
            deleteFieldVisitModal.classList.remove('flex');
            deleteFieldVisitModal.classList.add('hidden');
            document.body.style.overflow = '';
        });
        
        // Close modal when clicking outside
        deleteFieldVisitModal.addEventListener('click', function(e) {
            if (e.target === deleteFieldVisitModal) {
                deleteFieldVisitModal.classList.remove('flex');
                deleteFieldVisitModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
        
        // Handle Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !deleteFieldVisitModal.classList.contains('hidden')) {
                deleteFieldVisitModal.classList.remove('flex');
                deleteFieldVisitModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // Filter functionality
        const filterForm = document.getElementById('filter-form');
        const dateInput = document.getElementById('date');
        
        // Auto-submit on select/input change for better UX
        document.getElementById('team_id').addEventListener('change', function() {
            filterForm.submit();
        });
        
        document.getElementById('location').addEventListener('change', function() {
            filterForm.submit();
        });
        
        document.getElementById('date').addEventListener('change', function() {
            filterForm.submit();
        });
        
        // Reset button functionality
        document.querySelector('a[href="{{ route("sales_mission.field-visits.index") }}"]').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Clear all inputs
            document.getElementById('team_id').value = '';
            document.getElementById('location').value = '';
            document.getElementById('date').value = '';
            
            // Submit the form
            filterForm.submit();
        });
    });
</script>
@endpush 