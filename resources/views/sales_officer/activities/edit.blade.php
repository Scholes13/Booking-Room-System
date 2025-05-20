@extends('sales_officer.layout')

@section('title', 'Edit Activity')
@section('header', 'Edit Activity')
@section('description', 'Update your sales activity details')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100">
    <form action="{{ route('sales_officer.activities.update', $activity->id) }}" method="POST" class="space-y-4 sm:space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Company Information -->
        <div>
            <h4 class="text-sm sm:text-base font-semibold text-gray-800 mb-3 sm:mb-4">Company Information</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                    <input type="text" id="company_name" value="{{ $activity->contact->company_name }}" readonly class="w-full rounded-md border-gray-300 bg-gray-50 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="division_name" class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                    <input type="text" id="division_name" value="{{ $activity->division ? $activity->division->name : 'N/A' }}" readonly class="w-full rounded-md border-gray-300 bg-gray-50 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">PIC <span class="text-red-500">*</span></label>
                    <input type="text" id="pic_name" value="{{ $activity->pic ? $activity->pic->name : 'N/A' }}" readonly class="w-full rounded-md border-gray-300 bg-gray-50 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <div class="text-xs text-gray-500 mt-1">To change PIC, create a new activity</div>
                </div>
                
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position <span class="text-red-500">*</span></label>
                    <input type="text" id="position" name="position" value="{{ old('position', $activity->pic ? $activity->pic->position : '') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('position')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="pic_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" id="pic_phone" name="pic_phone" value="{{ old('pic_phone', $activity->pic && $activity->pic->phone_number !== 'N/A' ? $activity->pic->phone_number : '') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="e.g. 081234567890">
                    @error('pic_phone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    <div class="text-xs text-gray-500 mt-1">If empty, will be filled with "0"</div>
                </div>
                
                <div>
                    <label for="pic_email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="pic_email" name="pic_email" value="{{ old('pic_email', $activity->pic && $activity->pic->email !== 'N/A' ? $activity->pic->email : '') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="email@example.com">
                    @error('pic_email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    <div class="text-xs text-gray-500 mt-1">If empty, will be filled with "blank@werkudara.com"</div>
                </div>
                
                <div>
                    <label for="account_status" class="block text-sm font-medium text-gray-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                    <select id="account_status" name="account_status" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="New" {{ old('account_status', $activity->account_status) == 'New' ? 'selected' : '' }}>New</option>
                        <option value="Contracted" {{ old('account_status', $activity->account_status) == 'Contracted' ? 'selected' : '' }}>Contracted</option>
                        <option value="Existing" {{ old('account_status', $activity->account_status) == 'Existing' ? 'selected' : '' }}>Existing</option>
                    </select>
                    @error('account_status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div class="sm:col-span-2">
                    <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address <span class="text-red-500">*</span></label>
                    <textarea id="company_address" name="company_address" rows="2" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('company_address', $activity->contact->company_address) }}</textarea>
                    @error('company_address')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <!-- Location Information -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country <span class="text-red-500">*</span></label>
                    <select id="country" name="country" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select Country</option>
                        <option value="Indonesia" {{ old('country', $activity->country) == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                        <option value="Malaysia" {{ old('country', $activity->country) == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                        <option value="Singapore" {{ old('country', $activity->country) == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                        <option value="Thailand" {{ old('country', $activity->country) == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                        <option value="Philippines" {{ old('country', $activity->country) == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                    </select>
                    @error('country')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Province <span class="text-red-500">*</span></label>
                    <select id="province" name="province" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select Province</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ old('province', $activity->province) == $province ? 'selected' : '' }}>{{ $province }}</option>
                        @endforeach
                    </select>
                    @error('province')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" id="city" name="city" value="{{ old('city', $activity->city) }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('city')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        
        <!-- Activity Details -->
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Activity Details</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type <span class="text-red-500">*</span></label>
                    <select id="activity_type" name="activity_type" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select Type</option>
                        <option value="Event Networking" {{ old('activity_type', $activity->activity_type) == 'Event Networking' ? 'selected' : '' }}>Event Networking</option>
                        <option value="Meeting" {{ old('activity_type', $activity->activity_type) == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="Negotiation" {{ old('activity_type', $activity->activity_type) == 'Negotiation' ? 'selected' : '' }}>Negotiation</option>
                        <option value="Presentation - Introduction & Compro" {{ old('activity_type', $activity->activity_type) == 'Presentation - Introduction & Compro' ? 'selected' : '' }}>Presentation - Introduction & Compro</option>
                        <option value="Presentation - Pitching" {{ old('activity_type', $activity->activity_type) == 'Presentation - Pitching' ? 'selected' : '' }}>Presentation - Pitching</option>
                        <option value="Sales Call" {{ old('activity_type', $activity->activity_type) == 'Sales Call' ? 'selected' : '' }}>Sales Call</option>
                        <option value="Telemarketing" {{ old('activity_type', $activity->activity_type) == 'Telemarketing' ? 'selected' : '' }}>Telemarketing</option>
                        <option value="Telemarketing - Email" {{ old('activity_type', $activity->activity_type) == 'Telemarketing - Email' ? 'selected' : '' }}>Telemarketing - Email</option>
                        <option value="Telemarketing - LinkedIn" {{ old('activity_type', $activity->activity_type) == 'Telemarketing - LinkedIn' ? 'selected' : '' }}>Telemarketing - LinkedIn</option>
                        <option value="Telemarketing - Phone/WhatsApp" {{ old('activity_type', $activity->activity_type) == 'Telemarketing - Phone/WhatsApp' ? 'selected' : '' }}>Telemarketing - Phone/WhatsApp</option>
                        <option value="Werkudara Client Event" {{ old('activity_type', $activity->activity_type) == 'Werkudara Client Event' ? 'selected' : '' }}>Werkudara Client Event</option>
                    </select>
                    @error('activity_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="meeting_type" class="block text-sm font-medium text-gray-700 mb-1">Meeting Type <span class="text-red-500">*</span></label>
                    <select id="meeting_type" name="meeting_type" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select Type</option>
                        <option value="Online" {{ old('meeting_type', $activity->meeting_type) == 'Online' ? 'selected' : '' }}>Online</option>
                        <option value="Offline" {{ old('meeting_type', $activity->meeting_type) == 'Offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                    @error('meeting_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="products_discussed" class="block text-sm font-medium text-gray-700 mb-1">Number of Products Discussed</label>
                    <input type="number" id="products_discussed" name="products_discussed" min="1" max="50" value="{{ old('products_discussed', $activity->products_discussed) }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('products_discussed')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        
        <!-- Business Details -->
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Business Details</h4>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="jso_lead_status" class="block text-sm font-medium text-gray-700 mb-1">Lead Status <span class="text-red-500">*</span></label>
                    <select id="jso_lead_status" name="jso_lead_status" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select Lead Status</option>
                        <option value="Closed / Cold Lead" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Closed / Cold Lead' ? 'selected' : '' }}>Closed / Cold Lead</option>
                        <option value="Closed / Handed Over" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Closed / Handed Over' ? 'selected' : '' }}>Closed / Handed Over</option>
                        <option value="Closed / No Prospect" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Closed / No Prospect' ? 'selected' : '' }}>Closed / No Prospect</option>
                        <option value="Cold Lead" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Cold Lead' ? 'selected' : '' }}>Cold Lead</option>
                        <option value="Handed Over" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Handed Over' ? 'selected' : '' }}>Handed Over</option>
                        <option value="Hot Lead" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Hot Lead' ? 'selected' : '' }}>Hot Lead</option>
                        <option value="Lost Lead" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Lost Lead' ? 'selected' : '' }}>Lost Lead</option>
                        <option value="On progress" {{ old('jso_lead_status', $activity->jso_lead_status) == 'On progress' ? 'selected' : '' }}>On progress</option>
                        <option value="Open / Cold Lead" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Open / Cold Lead' ? 'selected' : '' }}>Open / Cold Lead</option>
                        <option value="Open / Hot Lead" {{ old('jso_lead_status', $activity->jso_lead_status) == 'Open / Hot Lead' ? 'selected' : '' }}>Open / Hot Lead</option>
                    </select>
                    @error('jso_lead_status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="general_information" class="block text-sm font-medium text-gray-700 mb-1">General Information</label>
                    <textarea id="general_information" name="general_information" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('general_information', $activity->contact->general_information) }}</textarea>
                    @error('general_information')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="current_event" class="block text-sm font-medium text-gray-700 mb-1">Current Event</label>
                    <textarea id="current_event" name="current_event" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('current_event', $activity->contact->current_event) }}</textarea>
                    @error('current_event')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="target_business" class="block text-sm font-medium text-gray-700 mb-1">Potential / Target Business</label>
                    <textarea id="target_business" name="target_business" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('target_business', $activity->contact->target_business) }}</textarea>
                    @error('target_business')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="project_type" class="block text-sm font-medium text-gray-700 mb-1">Type of Project / Partnership</label>
                    <textarea id="project_type" name="project_type" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('project_type', $activity->contact->project_type) }}</textarea>
                    @error('project_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="project_estimation" class="block text-sm font-medium text-gray-700 mb-1">Estimation of Project / Tender</label>
                    <textarea id="project_estimation" name="project_estimation" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('project_estimation', $activity->contact->project_estimation) }}</textarea>
                    @error('project_estimation')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="potential_revenue" class="block text-sm font-medium text-gray-700 mb-1">Potential Revenue (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="text" id="potential_revenue" name="potential_revenue" value="{{ old('potential_revenue', isset($activity->contact->potential_revenue) ? number_format($activity->contact->potential_revenue, 0, ',', '.') : '') }}" class="pl-10 w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 currency-input" placeholder="1,000,000">
                            @error('potential_revenue')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="potential_project_count" class="block text-sm font-medium text-gray-700 mb-1">Potential Number of Projects / Partnerships</label>
                        <input type="number" id="potential_project_count" name="potential_project_count" value="{{ old('potential_project_count', $activity->contact->potential_project_count) }}" min="0" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('potential_project_count')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="next_follow_up" class="block text-sm font-medium text-gray-700 mb-1">Next Follow Up <span class="text-red-500">*</span></label>
                        <input type="text" id="next_follow_up" name="next_follow_up" 
                            value="{{ old('next_follow_up', 
                                    $activity->next_follow_up instanceof \Carbon\Carbon 
                                    ? $activity->next_follow_up->format('Y-m-d H:i') 
                                    : $activity->next_follow_up) }}" 
                            class="datetimepicker w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('next_follow_up')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <input type="text" id="status" name="status" value="{{ old('status', $activity->status) }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="follow_up_type" class="block text-sm font-medium text-gray-700 mb-1">Type of Follow Up <span class="text-red-500">*</span></label>
                        <input type="text" id="follow_up_type" name="follow_up_type" value="{{ old('follow_up_type', $activity->follow_up_type) }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('follow_up_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="follow_up_frequency" class="block text-sm font-medium text-gray-700 mb-1">Frequency Follow Up <span class="text-red-500">*</span></label>
                        <select id="follow_up_frequency" name="follow_up_frequency" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Frequency</option>
                            <option value="Weekly" {{ old('follow_up_frequency', $activity->follow_up_frequency) == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="Monthly" {{ old('follow_up_frequency', $activity->follow_up_frequency) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="Bi-Weekly" {{ old('follow_up_frequency', $activity->follow_up_frequency) == 'Bi-Weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="Quarterly" {{ old('follow_up_frequency', $activity->follow_up_frequency) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="Semester" {{ old('follow_up_frequency', $activity->follow_up_frequency) == 'Semester' ? 'selected' : '' }}>Semester</option>
                            <option value="Yearly" {{ old('follow_up_frequency', $activity->follow_up_frequency) == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                            <option value="As Requested" {{ old('follow_up_frequency', $activity->follow_up_frequency) == 'As Requested' ? 'selected' : '' }}>As Requested</option>
                        </select>
                        @error('follow_up_frequency')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Date -->
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Date</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                    <input type="text" id="start_datetime" name="start_datetime" value="{{ old('start_datetime', $activity->start_datetime->format('Y-m-d')) }}" required class="datepicker w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('start_datetime')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <input type="hidden" id="end_datetime" name="end_datetime" value="{{ old('end_datetime', $activity->end_datetime->format('Y-m-d')) }}">
                
                <div>
                    <label for="month_number" class="block text-sm font-medium text-gray-700 mb-1">Month <span class="text-red-500">*</span></label>
                    <select id="month_number" name="month_number" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select Month</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('month_number', $activity->month_number) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                    @error('month_number')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="week_number" class="block text-sm font-medium text-gray-700 mb-1">Week <span class="text-red-500">*</span></label>
                    <select id="week_number" name="week_number" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Select Week</option>
                        <option value="1" {{ old('week_number', $activity->week_number) == 1 ? 'selected' : '' }}>Week 1</option>
                        <option value="2" {{ old('week_number', $activity->week_number) == 2 ? 'selected' : '' }}>Week 2</option>
                        <option value="3" {{ old('week_number', $activity->week_number) == 3 ? 'selected' : '' }}>Week 3</option>
                        <option value="4" {{ old('week_number', $activity->week_number) == 4 ? 'selected' : '' }}>Week 4</option>
                        <option value="5" {{ old('week_number', $activity->week_number) == 5 ? 'selected' : '' }}>Week 5</option>
                    </select>
                    @error('week_number')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <a href="{{ route('sales_officer.activities.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-3">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Update Activity
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize datepicker
        flatpickr('.datepicker', {
            enableTime: false,
            dateFormat: "Y-m-d"
        });
        
        // Auto-populate month and week based on selected date
        const startDatetime = document.getElementById('start_datetime');
        startDatetime.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                
                // Set month
                const month = date.getMonth() + 1; // JavaScript months are 0-indexed
                document.getElementById('month_number').value = month;
                
                // Calculate week of month
                const day = date.getDate();
                let weekOfMonth = Math.ceil(day / 7);
                if (weekOfMonth > 5) weekOfMonth = 5;
                
                document.getElementById('week_number').value = weekOfMonth;
                
                // Set end_datetime to the same date
                document.getElementById('end_datetime').value = this.value;
            }
        });
        
        // Location data - this would typically come from your backend
        const locationData = {
            'Indonesia': {
                'DKI Jakarta': ['Jakarta Pusat', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Utara', 'Kepulauan Seribu'],
                'Jawa Barat': ['Bandung', 'Bekasi', 'Bogor', 'Cimahi', 'Cirebon', 'Depok', 'Sukabumi', 'Tasikmalaya'],
                'Jawa Tengah': ['Semarang', 'Solo', 'Magelang', 'Salatiga', 'Pekalongan', 'Tegal'],
                'Jawa Timur': ['Surabaya', 'Malang', 'Kediri', 'Blitar', 'Madiun', 'Mojokerto'],
                'Bali': ['Denpasar', 'Badung', 'Gianyar', 'Tabanan']
            },
            'Malaysia': {
                'Kuala Lumpur': ['Kuala Lumpur'],
                'Selangor': ['Shah Alam', 'Petaling Jaya', 'Subang Jaya'],
                'Johor': ['Johor Bahru', 'Iskandar Puteri', 'Muar']
            },
            'Singapore': {
                'Singapore': ['Singapore City', 'Jurong East', 'Tampines', 'Woodlands']
            },
            'Thailand': {
                'Bangkok': ['Bangkok City'],
                'Chiang Mai': ['Chiang Mai City'],
                'Phuket': ['Phuket City', 'Patong']
            },
            'Philippines': {
                'Metro Manila': ['Manila', 'Quezon City', 'Makati', 'Pasig'],
                'Cebu': ['Cebu City', 'Mandaue', 'Lapu-Lapu']
            }
        };
        
        // Handle form submission with loading indicator
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Prevent default form submission
            e.preventDefault();
            
            // Show loading indicator
            Swal.fire({
                title: 'Updating Activity...',
                text: 'Please wait while we update your activity data',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit the form
            this.submit();
        });

        // Currency formatting for Rupiah input
        document.querySelectorAll('.currency-input').forEach(function(input) {
            input.addEventListener('input', function(e) {
                // Remove non-digits and leading zeros
                let value = e.target.value.replace(/[^\d]/g, '').replace(/^0+/, '');
                
                // Format with thousand separators
                if (value.length > 0) {
                    value = new Intl.NumberFormat('id-ID').format(value);
                }
                
                // Set the formatted value back
                e.target.value = value;
            });
        });
    });
</script>
@endpush
@endsection 