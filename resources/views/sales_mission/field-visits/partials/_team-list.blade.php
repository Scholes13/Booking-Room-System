@foreach($teams as $team)
<label for="team_{{ $team->id }}" class="border border-gray-200 rounded-lg p-4 card-hover cursor-pointer transition-all duration-150 ease-in-out hover:shadow-md focus-within:ring-2 focus-within:ring-amber-500 focus-within:border-amber-500">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="team_{{ $team->id }}" 
                   name="team_id" 
                   type="radio" 
                   value="{{ $team->id }}" 
                   class="h-4 w-4 text-amber-600 border-gray-300 focus:ring-amber-500 team-radio"
                   {{ old('team_id') == $team->id ? 'checked' : '' }}>
        </div>
        <div class="ml-3 text-sm flex-grow">
            <span class="font-medium text-gray-900">{{ $team->name }}</span>
            @if($team->description)
                <p class="text-gray-500 mt-1 text-xs">{{ $team->description }}</p>
            @endif
            <div class="mt-2 text-xs text-gray-600">
                <span class="font-semibold">Members:</span>
                @if($team->members instanceof \Illuminate\Support\Collection && $team->members->isNotEmpty())
                    <p class="text-gray-500">
                        {{ $team->members->pluck('name')->implode(', ') }}
                    </p>
                @else
                    <p class="text-gray-500">No members assigned</p>
                @endif
            </div>
        </div>
    </div>
</label>
@endforeach

@if($teams->isEmpty())
<div class="col-span-full text-center py-8">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">No teams found</h3>
    <p class="mt-1 text-sm text-gray-500">No teams match your current search criteria.</p>
</div>
@endif 