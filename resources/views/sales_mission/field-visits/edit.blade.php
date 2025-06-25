@extends('sales_mission.layout')

@section('title', 'Edit Field Visit')
@section('header', 'Edit Field Visit')
@section('description', 'Update the team assignment details')

@push('styles')
<style>
    .card-hover:hover {
        @apply bg-gray-50;
    }
    
    .card-selected {
        @apply ring-2 ring-amber-500 bg-amber-50;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('sales_mission.field-visits.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Field Visits
        </a>
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

    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 font-medium text-red-700">Please fix the following errors:</p>
                <ul class="mt-2 text-sm leading-5 text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <form action="{{ route('sales_mission.field-visits.update', $fieldVisit) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Notes -->
                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3"
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm"
                    >{{ old('notes', $fieldVisit->notes) }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Team Selection -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Team</h3>
                        <div class="grid grid-cols-1 gap-4 max-h-[500px] overflow-y-auto pr-2">
                            @foreach($teams as $team)
                                <label for="team_{{ $team->id }}" class="border border-gray-200 rounded-lg p-4 card-hover">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="team_{{ $team->id }}" name="team_id" type="radio" value="{{ $team->id }}" class="h-4 w-4 text-amber-600 border-gray-300 focus:ring-amber-500" {{ old('team_id', $fieldVisit->team_id) == $team->id ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="team_{{ $team->id }}" class="font-medium text-gray-900">{{ $team->name }}</label>
                                            @if($team->description)
                                                <p class="text-gray-500 mt-1">{{ $team->description }}</p>
                                            @endif
                                            @if(is_array($team->members) && count($team->members) > 0)
                                                @php
                                                    $employees = \App\Models\Employee::whereIn('id', $team->members)->get();
                                                    $memberNames = $employees->pluck('name')->toArray();
                                                @endphp
                                                <div class="mt-2 text-xs text-gray-500">
                                                    <span class="font-medium">Team Members:</span> {{ implode(', ', $memberNames) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Activity Selection -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Activity</h3>
                        <div class="grid grid-cols-1 gap-4 max-h-[500px] overflow-y-auto pr-2">
                            @foreach($activities as $activity)
                                <label for="activity_{{ $activity->id }}" class="border border-gray-200 rounded-lg p-4 card-hover">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="activity_{{ $activity->id }}" name="activity_id" type="radio" value="{{ $activity->id }}" class="h-4 w-4 text-amber-600 border-gray-300 focus:ring-amber-500" {{ old('activity_id', $fieldVisit->activity_id) == $activity->id ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="activity_{{ $activity->id }}" class="font-medium text-gray-900">
                                                {{ $activity->salesMissionDetail->company_name }}
                                            </label>
                                            <div class="mt-1 grid grid-cols-1 gap-1">
                                                <div class="text-gray-500">
                                                    Contact: {{ $activity->salesMissionDetail->company_pic }}
                                                    @if($activity->salesMissionDetail->company_position)
                                                        ({{ $activity->salesMissionDetail->company_position }})
                                                    @endif
                                                </div>
                                                <div class="text-gray-500">
                                                    Location: {{ $activity->city }}, {{ $activity->province }}
                                                </div>
                                                <div class="text-gray-500">
                                                    Schedule: {{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y H:i') : 'N/A' }}
                                                    @if($activity->end_datetime)
                                                         - {{ \Carbon\Carbon::parse($activity->end_datetime)->format('H:i') }}
                                                    @endif
                                                </div>
                                                @if($activity->description)
                                                    <div class="text-gray-500 mt-1">
                                                        {{ $activity->description }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('sales_mission.field-visits.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        Update Field Visit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click event to team cards
        const teamCards = document.querySelectorAll('[id^="team_"]');
        teamCards.forEach(radio => {
            const card = radio.closest('.card-hover');
            
            // Set initial state
            if (radio.checked) {
                card.classList.add('card-selected');
            }
            
            // Add click event to the card
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                document.querySelectorAll('[id^="team_"]').forEach(r => {
                    r.closest('.card-hover').classList.remove('card-selected');
                });
                
                // Check the radio and add selected class
                radio.checked = true;
                card.classList.add('card-selected');
            });
        });
        
        // Add click event to activity cards
        const activityCards = document.querySelectorAll('[id^="activity_"]');
        activityCards.forEach(radio => {
            const card = radio.closest('.card-hover');
            
            // Set initial state
            if (radio.checked) {
                card.classList.add('card-selected');
            }
            
            // Add click event to the card
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                document.querySelectorAll('[id^="activity_"]').forEach(r => {
                    r.closest('.card-hover').classList.remove('card-selected');
                });
                
                // Check the radio and add selected class
                radio.checked = true;
                card.classList.add('card-selected');
            });
        });
    });
</script>
@endpush 