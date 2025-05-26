@foreach($activities as $activity)
@php
    $hasAssignments = $activity->teamAssignments->isNotEmpty();
@endphp
<label for="activity_{{ $activity->id }}" 
       class="border rounded-lg p-4 card-hover cursor-pointer transition-all duration-150 ease-in-out hover:shadow-md focus-within:ring-2 focus-within:ring-amber-500 focus-within:border-amber-500 
              {{ $hasAssignments ? 'border-green-400 bg-green-50' : 'border-gray-200' }}">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="activity_{{ $activity->id }}" 
                   name="activity_id" 
                   type="radio" 
                   value="{{ $activity->id }}" 
                   class="h-4 w-4 text-amber-600 border-gray-300 focus:ring-amber-500 activity-radio"
                   {{ old('activity_id') == $activity->id ? 'checked' : '' }}>
        </div>
        <div class="ml-3 text-sm flex-grow">
            <span class="font-medium text-gray-900">
                {{ $activity->salesMissionDetail->company_name ?? 'N/A' }}
            </span>
            <div class="mt-1 grid grid-cols-1 gap-y-0.5 text-xs">
                <div class="text-gray-600">
                    <span class="font-semibold">Activity:</span> {{ $activity->name ?? 'N/A' }}
                </div>
                <div class="text-gray-600">
                    <span class="font-semibold">PIC:</span> {{ $activity->salesMissionDetail->company_pic ?? 'N/A' }}
                    @if($activity->salesMissionDetail->company_position)
                        ({{ $activity->salesMissionDetail->company_position }})
                    @endif
                </div>
                <div class="text-gray-600">
                    <span class="font-semibold">Location:</span> {{ $activity->city ?? 'N/A' }}, {{ $activity->province ?? 'N/A' }}
                </div>
                <div class="text-gray-600">
                    <span class="font-semibold">Schedule:</span> {{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y H:i') : 'N/A' }}
                    @if($activity->end_datetime)
                         - {{ \Carbon\Carbon::parse($activity->end_datetime)->format('H:i') }}
                    @endif
                </div>
                @if($hasAssignments)
                    <div class="mt-2 text-xs">
                        <span class="font-semibold text-green-700">Assigned Team(s):</span>
                        @foreach($activity->teamAssignments as $assignment)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-1">
                                {{ $assignment->team->name ?? 'N/A' }}
                            </span>
                        @endforeach
                    </div>
                @endif
                @if($activity->description)
                    <div class="text-gray-600 mt-1 {{ $hasAssignments ? 'mt-1' : 'mt-1' }}">
                        <span class="font-semibold">Description:</span> {{ Str::limit($activity->description, 100) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</label>
@endforeach

@if($activities->isEmpty())
<div class="col-span-full text-center py-8">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">No activities found</h3>
    <p class="mt-1 text-sm text-gray-500">No activities match your current search criteria.</p>
</div>
@endif 