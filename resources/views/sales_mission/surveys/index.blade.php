@extends('sales_mission.layout')

@section('title', 'Sales Visit Reports')
@section('header', 'Sales Visit Reports')
@section('description', 'View and manage sales visit reports')

@section('content')
<div class="flex flex-col gap-6 h-full">
    <!-- Filter Section -->
    <div class="space-y-4">
        <form id="filter-form" action="{{ route('sales_mission.surveys.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
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
                    @foreach($teams as $team)
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

            <!-- Status Filter -->
            <div class="flex flex-col">
                <label for="status" class="mb-2 flex items-center text-sm font-medium text-gray-700">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Filter by Status
                </label>
                <select id="status" name="status" class="w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                    <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>Answered</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div class="flex flex-col">
                <label for="date" class="mb-2 flex items-center text-sm font-medium text-gray-700">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Filter by Visit Date
                </label>
                <input type="date" id="date" name="date" value="{{ request('date') }}" class="w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
            </div>
        </form>
        
        <div class="flex justify-end gap-3">
            <a href="{{ route('sales_mission.surveys.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
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
        <span>Showing {{ $surveys->firstItem() ?? 0 }} - {{ $surveys->lastItem() ?? 0 }} of {{ $surveys->total() }} results</span>
        @if(request()->hasAny(['team_id', 'location', 'status', 'date']))
            <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-xs">Filtered</span>
        @endif
    </div>

    <!-- Report List -->
    <div class="flex flex-col gap-6 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Sales Visit Reports</h2>
            <div class="text-sm text-gray-500">Total: {{ $surveys->total() }} reports</div>
        </div>
        
        <div class="overflow-x-auto rounded-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Company</th>
                        <th scope="col" class="px-5 py-3.5">Team</th>
                        <th scope="col" class="px-5 py-3.5">Date</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5">Submitted At</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surveys as $survey)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">
                                @if ($survey->survey_type === 'sales_blitz')
                                    {{ $survey->blitz_company_name ?? 'N/A' }}
                                    <span class="ml-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium">Blitz</span>
                                @else
                                    {{ $survey->teamAssignment->activity->salesMissionDetail->company_name ?? 'N/A' }}
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">
                                @if ($survey->survey_type === 'sales_blitz')
                                    {{ $survey->blitzTeam->name ?? ($survey->blitz_team_name ?? 'N/A') }}
                                @else
                                    {{ $survey->teamAssignment->team->name ?? 'N/A' }}
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-900">
                                @if ($survey->survey_type === 'sales_blitz')
                                    {{ $survey->blitz_visit_start_datetime ? \Carbon\Carbon::parse($survey->blitz_visit_start_datetime)->format('d M Y') : 'N/A' }}
                                @else
                                    {{ optional($survey->teamAssignment->activity->start_datetime)->format('d M Y') ?? 'N/A' }}
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Created: {{ $survey->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $survey->getStatusClasses() }}">
                                {{ $survey->getStatus() }}
                            </span>
                            @if($survey->getStatus() == \App\Models\FeedbackSurvey::STATUS_ANSWERED)
                                <div class="text-xs text-gray-500 mt-1">{{ $survey->completed_at ? $survey->completed_at->format('d M Y H:i') : '' }}</div>
                            @elseif($survey->getStatus() == \App\Models\FeedbackSurvey::STATUS_VIEWED && $survey->last_viewed_at)
                                <div class="text-xs text-gray-500 mt-1">Last viewed: {{ $survey->last_viewed_at->format('d M Y H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">{{ $survey->submitted_at ? $survey->submitted_at->format('d M Y H:i') : '-' }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('sales_mission.surveys.show', $survey->id) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                @if ($survey->survey_token)
                                    @if (!$survey->is_completed)
                                        <a href="{{ route('sales_mission.surveys.public.form', $survey->survey_token) }}" target="_blank" class="px-3 py-1.5 text-xs font-medium bg-amber-50 text-amber-700 rounded-md hover:bg-amber-100 transition-colors inline-flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            Form Link
                                        </a>
                                    @else
                                        <a href="{{ route('sales_mission.surveys.public.view_feedback', $survey->survey_token) }}" target="_blank" class="px-3 py-1.5 text-xs font-medium bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition-colors inline-flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.242 0 3 3 0 00-4.242 0z"></path></svg>
                                            View Submission
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Sales Visit Reports</h3>
                                <p class="text-gray-500 text-sm">Reports will appear here when teams are assigned to activities</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center px-6 py-4">
            {{ $surveys->links() }}
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter functionality
        const filterForm = document.getElementById('filter-form');
        
        // Auto-submit on select/input change for better UX
        document.getElementById('team_id').addEventListener('change', function() {
            filterForm.submit();
        });
        
        document.getElementById('location').addEventListener('change', function() {
            filterForm.submit();
        });
        
        document.getElementById('status').addEventListener('change', function() {
            filterForm.submit();
        });
        
        document.getElementById('date').addEventListener('change', function() {
            filterForm.submit();
        });
        
        // Reset button functionality
        document.querySelector('a[href="{{ route("sales_mission.surveys.index") }}"]').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Clear all inputs
            document.getElementById('team_id').value = '';
            document.getElementById('location').value = '';
            document.getElementById('status').value = '';
            document.getElementById('date').value = '';
            
            // Submit the form
            filterForm.submit();
        });
    });
</script>
@endpush 