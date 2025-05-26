<!-- Assignment Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Team Information -->
    <div class="bg-gray-50 rounded-lg overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-100">
            <h2 class="text-base font-semibold text-gray-700">Team Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ $fieldVisit->team->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $fieldVisit->team->description ?? 'No description available' }}</p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Team Members</h4>
                @php
                    $memberNames = [];
                    if(is_array($fieldVisit->team->members) && count($fieldVisit->team->members) > 0) {
                        $employees = \App\Models\Employee::whereIn('id', $fieldVisit->team->members)->get();
                        $memberNames = $employees->pluck('name')->toArray();
                    }
                @endphp
                @if(count($memberNames) > 0)
                    <div class="bg-white p-3 rounded-lg border border-gray-100">
                        {{ implode(', ', $memberNames) }}
                    </div>
                @else
                    <p class="text-sm text-gray-500">No team members listed</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Activity Information -->
    <div class="bg-gray-50 rounded-lg overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-100">
            <h2 class="text-base font-semibold text-gray-700">Activity Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ $fieldVisit->activity->salesMissionDetail->company_name }}</h3>
                <div class="text-sm text-gray-500 mt-1">{{ $fieldVisit->activity->description }}</div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Company Contact</h4>
                    <div class="text-sm mt-1">
                        <div class="font-medium">{{ $fieldVisit->activity->salesMissionDetail->company_pic }}</div>
                        @if($fieldVisit->activity->salesMissionDetail->company_position)
                            <div class="text-gray-500">{{ $fieldVisit->activity->salesMissionDetail->company_position }}</div>
                        @endif
                        @if($fieldVisit->activity->salesMissionDetail->company_contact)
                            <div class="text-gray-500">{{ $fieldVisit->activity->salesMissionDetail->company_contact }}</div>
                        @endif
                        @if($fieldVisit->activity->salesMissionDetail->company_email)
                            <div class="text-gray-500">{{ $fieldVisit->activity->salesMissionDetail->company_email }}</div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Location</h4>
                    <div class="text-sm mt-1">
                        <div>{{ $fieldVisit->activity->city }}, {{ $fieldVisit->activity->province }}</div>
                        <div class="text-gray-500">{{ $fieldVisit->activity->salesMissionDetail->company_address ?? 'No address provided' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Schedule</h4>
                    <div class="text-sm mt-1">
                        <div>{{ optional($fieldVisit->activity->start_datetime)->format('d M Y') }}</div>
                        <div class="text-gray-500">
                            {{ optional($fieldVisit->activity->start_datetime)->format('H:i') }} - 
                            {{ optional($fieldVisit->activity->end_datetime)->format('H:i') }}
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Status</h4>
                    <div class="text-sm mt-1">
                        @php
                            $statusClass = 'badge-gray';
                            $status = $fieldVisit->activity->status;
                            
                            if($status === 'scheduled') $statusClass = 'badge-blue';
                            elseif($status === 'ongoing') $statusClass = 'badge-green';
                            elseif($status === 'completed') $statusClass = 'badge-indigo';
                            elseif($status === 'cancelled') $statusClass = 'badge-red';
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Information -->
<div class="bg-gray-50 rounded-lg overflow-hidden mt-6 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-100">
        <h2 class="text-base font-semibold text-gray-700">Assignment Information</h2>
    </div>
    <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h4 class="text-sm font-medium text-gray-700">Assignment Date</h4>
                <div class="text-sm mt-1">{{ $fieldVisit->assignment_date ? \Carbon\Carbon::parse($fieldVisit->assignment_date)->format('d M Y') : 'N/A' }}</div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-700">Assigned By</h4>
                <div class="text-sm mt-1">{{ $fieldVisit->assigner->name ?? 'N/A' }}</div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-700">Created On</h4>
                <div class="text-sm mt-1">{{ $fieldVisit->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
        
        @if($fieldVisit->notes)
        <div>
            <h4 class="text-sm font-medium text-gray-700">Notes</h4>
            <div class="mt-2 p-4 bg-white rounded-lg text-sm border border-gray-100">
                {{ $fieldVisit->notes }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Feedback Survey Information -->
@if($fieldVisit->feedbackSurvey)
<div class="bg-gray-50 rounded-lg overflow-hidden mt-6 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-100 flex justify-between items-center">
        <h2 class="text-base font-semibold text-gray-700">Feedback Survey</h2>
        <a href="{{ route('sales_mission.surveys.show', $fieldVisit->feedbackSurvey->id) }}" target="_blank" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            View Full Survey
        </a>
    </div>
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $fieldVisit->feedbackSurvey->getStatusClasses() }}">
                    {{ $fieldVisit->feedbackSurvey->getStatus() }}
                </span>
                @if($fieldVisit->feedbackSurvey->is_completed)
                    <div class="text-xs text-gray-500 mt-1">Completed on: {{ $fieldVisit->feedbackSurvey->completed_at->format('d M Y H:i') }}</div>
                @elseif($fieldVisit->feedbackSurvey->isViewed())
                    <div class="text-xs text-gray-500 mt-1">Last viewed: {{ $fieldVisit->feedbackSurvey->last_viewed_at->format('d M Y H:i') }}</div>
                @endif
            </div>
            
            @if(!$fieldVisit->feedbackSurvey->is_completed)
            <a href="{{ route('sales_mission.surveys.public.form', $fieldVisit->feedbackSurvey->survey_token) }}" target="_blank" class="px-3 py-1.5 text-xs font-medium bg-amber-50 text-amber-700 rounded-md hover:bg-amber-100 transition-colors inline-flex items-center">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
                Report Form
            </a>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Modal Footer with Actions -->
<div class="flex justify-between mt-6 pt-4 border-t border-gray-100">
    <div>
        <form action="{{ route('sales_mission.field-visits.destroy', $fieldVisit) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2 shadow-sm" onclick="return confirm('Are you sure you want to delete this assignment?')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete
            </button>
        </form>
    </div>
    <div class="flex gap-2">
        <button type="button" onclick="closeModal('viewModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            Close
        </button>
        <button type="button" onclick="editFieldVisit({{ $fieldVisit->id }})" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
        </button>
    </div>
</div> 