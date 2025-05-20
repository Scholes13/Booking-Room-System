@extends('sales_officer.layout')

@section('title', 'Contact Details')
@section('header', 'Contact Details')
@section('description', 'Detailed view of contact information')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-lg font-semibold text-gray-800">{{ $contact->company_name }}</h4>
        <div class="space-x-2">
            <a href="{{ route('sales_officer.contacts.edit', $contact->id) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium">
                Edit Contact
            </a>
            <a href="{{ route('sales_officer.contacts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Back to Contacts
            </a>
        </div>
    </div>
    
    <!-- Company Information -->
    <div class="mb-8">
        <h4 class="text-base font-semibold text-gray-800 mb-4 border-b pb-2">Company Information</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Company Name</div>
                <div class="text-base text-gray-900">{{ $contact->company_name }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Line of Business</div>
                <div class="text-base text-gray-900">{{ $contact->line_of_business ?: 'Not specified' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Address</div>
                <div class="text-base text-gray-900">{{ $contact->company_address ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Location</div>
                <div class="text-base text-gray-900">
                    @if($contact->city || $contact->province || $contact->country)
                        {{ collect([$contact->city, $contact->province, $contact->country])->filter()->join(', ') }}
                    @else
                        Not provided
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Visit Count</div>
                <div class="text-base text-gray-900">{{ $contact->visit_count }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Source</div>
                <div class="text-base text-gray-900">
                    @if($contact->sales_mission_detail_id)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Sales Mission
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Sales Officer
                        </span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Notes</div>
                <div class="text-base text-gray-900">{{ $contact->notes ?: 'No notes' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Business Information -->
    <div class="mb-8">
        <h4 class="text-base font-semibold text-gray-800 mb-4 border-b pb-2">Business Information</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">General Information</div>
                <div class="text-base text-gray-900">{{ $contact->general_information ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Current Event</div>
                <div class="text-base text-gray-900">{{ $contact->current_event ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Target Business</div>
                <div class="text-base text-gray-900">{{ $contact->target_business ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Project Type</div>
                <div class="text-base text-gray-900">{{ $contact->project_type ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Project / Tender Estimation</div>
                <div class="text-base text-gray-900">{{ $contact->project_estimation ?: 'Not provided' }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Potential Revenue</div>
                <div class="text-base text-gray-900 font-medium">
                    @if($contact->potential_revenue)
                        <span class="text-green-600">Rp {{ number_format($contact->potential_revenue, 0, ',', '.') }}</span>
                    @else
                        <span class="text-gray-500">Not provided</span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Potential Projects / Partnerships</div>
                <div class="text-base text-gray-900">{{ $contact->potential_project_count ?: 'Not provided' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Divisions -->
    <div class="mb-8">
        <h4 class="text-base font-semibold text-gray-800 mb-4 border-b pb-2">Divisions ({{ $contact->divisions->count() }})</h4>
        
        @if($contact->divisions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division Name</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Count</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contact->divisions as $division)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $division->name }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $division->visit_count }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $division->notes ?: '-' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-gray-500 text-sm">No divisions found for this company.</div>
        @endif
    </div>
    
    <!-- Contact People (PICs) -->
    <div>
        <h4 class="text-base font-semibold text-gray-800 mb-4 border-b pb-2">Contact People ({{ $contact->contactPeople->count() }})</h4>
        
        @if($contact->contactPeople->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contact->contactPeople as $pic)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $pic->title }} {{ $pic->name }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $pic->position ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $pic->division ? $pic->division->name : 'General' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($pic->phone_number)
                                        <a href="tel:{{ $pic->phone_number }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $pic->phone_number }}</a>
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($pic->email)
                                        <a href="mailto:{{ $pic->email }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $pic->email }}</a>
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($pic->is_primary)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Primary
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Secondary
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-gray-500 text-sm">No contact people found for this company.</div>
        @endif
    </div>
</div>
@endsection 