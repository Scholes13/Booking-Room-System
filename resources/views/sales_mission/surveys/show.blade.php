@extends('sales_mission.layout')

@section('title', 'Sales Visit Report')
@section('header', 'Sales Visit Report Details')
@section('description', 'View detailed information about the sales visit report')

@section('content')
<div class="flex flex-col gap-6 h-full">
    <!-- Activity Information Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Activity Information</h2>
            <div>
                @if(!$survey->is_completed)
                <a href="{{ $survey->public_url }}" target="_blank" class="px-3 py-1.5 text-xs font-medium bg-amber-500 text-white rounded-md hover:bg-amber-600 transition-colors inline-flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Open Report Form
                </a>
                @endif
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Company Details</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Company Name</p>
                            <p class="font-medium">{{ $survey->teamAssignment->activity->salesMissionDetail->company_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Scheduled PIC</p>
                            <p class="font-medium">{{ $survey->teamAssignment->activity->salesMissionDetail->company_pic ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Scheduled Contact</p>
                            <p class="font-medium">{{ $survey->teamAssignment->activity->salesMissionDetail->company_contact ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Scheduled Email</p>
                            <p class="font-medium">{{ $survey->teamAssignment->activity->salesMissionDetail->company_email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Appointment Details</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Scheduled Date & Time</p>
                            <p class="font-medium">
                                {{ optional($survey->teamAssignment->activity->start_datetime)->format('d M Y H:i') ?? 'N/A' }} - 
                                {{ optional($survey->teamAssignment->activity->end_datetime)->format('H:i') ?? '' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Location</p>
                            <p class="font-medium">
                                {{ $survey->teamAssignment->activity->city ?? '' }}, 
                                {{ $survey->teamAssignment->activity->province ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Assigned Team</p>
                            <p class="font-medium">{{ $survey->teamAssignment->team->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Report Status</p>
                            <p class="font-medium">
                                @if($survey->is_completed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sales Visit Report Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-base font-semibold text-gray-700">Sales Visit Report</h2>
        </div>
        
        <div class="p-6">
            @if($survey->is_completed)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-md font-semibold text-gray-800 mb-3">Visit Information</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">VISITED TIME</p>
                                        <p class="font-medium">{{ optional($survey->visited_time)->format('d M Y H:i') ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-md font-semibold text-gray-800 mb-3">Contact Person Information</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">CONTACT PERSON NAME</p>
                                        <p class="font-medium">{{ $survey->contact_salutation ?? '' }} {{ $survey->contact_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">CONTACT PERSON JOB TITTLE & DEPARTMENT</p>
                                        <p class="font-medium">
                                            {{ $survey->contact_job_title ?? 'N/A' }}
                                            {{ $survey->department ? '('.$survey->department.')' : '' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Contact Information</p>
                                        <p class="font-medium">
                                            {{ $survey->contact_mobile ? 'Mobile: '.$survey->contact_mobile : '' }}
                                            {{ $survey->contact_email ? ' | Email: '.$survey->contact_email : '' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">CONTACT PERSON DECISION-MAKER STATUS</p>
                                        <p class="font-medium">
                                            @if($survey->decision_maker_status == 'YES')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    YES
                                                </span>
                                            @elseif($survey->decision_maker_status == 'NO')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    NO
                                                </span>
                                            @elseif($survey->decision_maker_status == 'UNKNOWN')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    UNKNOWN
                                                </span>
                                            @else
                                                {{ $survey->decision_maker_status ?? 'Not specified' }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-md font-semibold text-gray-800 mb-3">Sales Information</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">SALES CALL OUTCOME</p>
                                        <p class="font-medium">
                                            @if($survey->sales_call_outcome == 'SUCCESSFUL')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    SUCCESSFUL
                                                </span>
                                            @elseif($survey->sales_call_outcome == 'NEEDS FOLLOW-UP')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    NEEDS FOLLOW-UP
                                                </span>
                                            @elseif($survey->sales_call_outcome == 'NOT INTERESTED')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    NOT INTERESTED
                                                </span>
                                            @else
                                                {{ $survey->sales_call_outcome ?? 'N/A' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">NEXT FOLLOW UP</p>
                                        <p class="font-medium">
                                            @if($survey->next_follow_up == 'SEND PROPOSAL')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    SEND PROPOSAL
                                                </span>
                                            @elseif($survey->next_follow_up == 'SCHEDULE MEETING')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    SCHEDULE MEETING
                                                </span>
                                            @elseif($survey->next_follow_up == 'SEND COMPANY PROFILE')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    SEND COMPANY PROFILE
                                                </span>
                                            @elseif($survey->next_follow_up == 'OTHER')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    OTHER {{ $survey->next_follow_up_other ? '('.$survey->next_follow_up_other.')' : '' }}
                                                </span>
                                            @else
                                                {{ $survey->next_follow_up ?? 'N/A' }}
                                                {{ $survey->next_follow_up_other ? '('.$survey->next_follow_up_other.')' : '' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">PRODUCT INTERESTED</p>
                                        <p class="font-medium">
                                            @if($survey->product_interested == 'MICE')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-white">
                                                    MICE
                                                </span>
                                            @elseif($survey->product_interested == 'SUSTAINABILITY PRODUCT')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white">
                                                    SUSTAINABILITY PRODUCT
                                                </span>
                                            @elseif($survey->product_interested == 'TRAINING')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-800 text-white">
                                                    TRAINING
                                                </span>
                                            @elseif($survey->product_interested == 'WELLNESS')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900 text-white">
                                                    WELLNESS
                                                </span>
                                            @elseif($survey->product_interested == 'GOOPER')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-600 text-white">
                                                    GOOPER
                                                </span>
                                            @elseif($survey->product_interested == 'RETAIL')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-700 text-white">
                                                    RETAIL
                                                </span>
                                            @elseif($survey->product_interested == 'CREATIVE')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-700 text-white">
                                                    CREATIVE
                                                </span>
                                            @elseif($survey->product_interested == 'TRAVEL')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-200 text-purple-800">
                                                    TRAVEL
                                                </span>
                                            @else
                                                {{ $survey->product_interested ?? 'Not specified' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">STATUS LEAD & POTENTIAL REVENUE</p>
                                        <p class="font-medium">
                                            @if($survey->status_lead == 'POTENTIAL')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    POTENTIAL
                                                </span>
                                            @elseif($survey->status_lead == 'LEAD')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    LEAD
                                                </span>
                                            @elseif($survey->status_lead == 'NOT INTERESTED')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    NOT INTERESTED
                                                </span>
                                            @else
                                                {{ $survey->status_lead ?? 'Unknown' }}
                                            @endif
                                            {{ $survey->potential_revenue ? ' | Revenue: '.$survey->potential_revenue : '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-md font-semibold text-gray-800 mb-3">Documentation</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex gap-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 {{ $survey->has_documentation ? 'text-green-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-2">VISITED DOCUMENTATION</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 {{ $survey->has_business_card ? 'text-green-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-2">BUSINESS CARD</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">KEY DISCUSSION POINTS</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $survey->key_discussion_points ?? 'No discussion points recorded.' }}</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <p>Report completed on: {{ $survey->completed_at ? $survey->completed_at->format('d M Y H:i') : 'N/A' }}</p>
                    </div>
                </div>
                
                <!-- Survey Tracking Information -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-md font-semibold text-gray-800 mb-3">Form Tracking Information</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-gray-500">FORM STATUS</p>
                                <p class="font-medium">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $survey->getStatusClasses() }}">
                                        {{ $survey->getStatus() }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">FIRST VIEWED</p>
                                <p class="font-medium">{{ $survey->viewed_at ? $survey->viewed_at->format('d M Y H:i') : 'Never' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">LAST VIEWED</p>
                                <p class="font-medium">{{ $survey->last_viewed_at ? $survey->last_viewed_at->format('d M Y H:i') : 'Never' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">VIEW COUNT</p>
                                <p class="font-medium">{{ $survey->view_count }} times</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-8 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Pending Sales Visit Report</h3>
                    <p class="text-gray-500 mb-4">The team has not yet completed the sales visit report.</p>
                    <div class="flex justify-center">
                        <a href="{{ $survey->public_url }}" target="_blank" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            Complete Report Form
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('sales_mission.surveys.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Reports
        </a>
    </div>
</div>
@endsection 