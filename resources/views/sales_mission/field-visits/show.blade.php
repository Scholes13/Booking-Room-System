@extends('sales_mission.layout')

@section('title', 'Field Visit Details')
@section('header', 'Field Visit Details')
@section('description', 'View detailed information about the field visit')

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
    <!-- Actions Buttons -->
    <div class="flex justify-between items-center">
        <a href="{{ route('sales_mission.field-visits.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to List
        </a>
        <div class="flex gap-2">
            <a href="{{ route('sales_mission.field-visits.edit', $fieldVisit) }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <button type="button" class="delete-field-visit px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2"
                data-id="{{ $fieldVisit->id }}"
                data-company="{{ $fieldVisit->activity->salesMissionDetail->company_name }}"
                data-team="{{ $fieldVisit->team->name }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete
            </button>
        </div>
    </div>

    <!-- Assignment Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Team Information -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-700">Team Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $fieldVisit->team->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $fieldVisit->team->description ?? 'No description available' }}</p>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Team Members</h4>
                    @if($fieldVisit->team->members->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($fieldVisit->team->members as $member)
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $member->name }}</span>
                                    @if($member->position)
                                        <span class="text-xs text-gray-500">({{ $member->position }})</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No team members listed for this assignment.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Activity Information -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
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
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
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
                <div class="mt-2 p-4 bg-gray-50 rounded-lg text-sm">
                    {{ $fieldVisit->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Feedback Survey Information -->
    @if($fieldVisit->feedbackSurvey)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Feedback Survey</h2>
            <a href="{{ route('sales_mission.surveys.show', $fieldVisit->feedbackSurvey->id) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
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
        window.addEventListener('click', function(event) {
            if (event.target === deleteFieldVisitModal) {
                deleteFieldVisitModal.classList.remove('flex');
                deleteFieldVisitModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
        
        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                deleteFieldVisitModal.classList.remove('flex');
                deleteFieldVisitModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    });
</script>
@endpush 