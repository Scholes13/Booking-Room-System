@extends('sales_officer.layout')

@section('title', 'Add Contact')
@section('header', 'Add New Contact')
@section('description', 'Add a new business contact to your list')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <form action="{{ route('sales_officer.contacts.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Company Information -->
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Company Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('company_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="line_of_business" class="block text-sm font-medium text-gray-700 mb-1">Line of Business <span class="text-red-500">*</span></label>
                    <select id="line_of_business" name="line_of_business" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="" selected disabled>Select Line of Business</option>
                        <option value="Advertising" {{ old('line_of_business') == 'Advertising' ? 'selected' : '' }}>Advertising</option>
                        <option value="Agriculture" {{ old('line_of_business') == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                        <option value="Association" {{ old('line_of_business') == 'Association' ? 'selected' : '' }}>Association</option>
                        <option value="Automotive" {{ old('line_of_business') == 'Automotive' ? 'selected' : '' }}>Automotive</option>
                        <option value="Banking" {{ old('line_of_business') == 'Banking' ? 'selected' : '' }}>Banking</option>
                        <option value="Beauty Companies" {{ old('line_of_business') == 'Beauty Companies' ? 'selected' : '' }}>Beauty Companies</option>
                        <option value="Chemical" {{ old('line_of_business') == 'Chemical' ? 'selected' : '' }}>Chemical</option>
                        <option value="Construction" {{ old('line_of_business') == 'Construction' ? 'selected' : '' }}>Construction</option>
                        <option value="Consultant" {{ old('line_of_business') == 'Consultant' ? 'selected' : '' }}>Consultant</option>
                        <option value="Consumer Goods" {{ old('line_of_business') == 'Consumer Goods' ? 'selected' : '' }}>Consumer Goods</option>
                        <option value="Contractor" {{ old('line_of_business') == 'Contractor' ? 'selected' : '' }}>Contractor</option>
                        <option value="Cooperatives" {{ old('line_of_business') == 'Cooperatives' ? 'selected' : '' }}>Cooperatives</option>
                        <option value="Digital Advertising/Agency" {{ old('line_of_business') == 'Digital Advertising/Agency' ? 'selected' : '' }}>Digital Advertising/Agency</option>
                        <option value="Distribution" {{ old('line_of_business') == 'Distribution' ? 'selected' : '' }}>Distribution</option>
                        <option value="E-Commerce" {{ old('line_of_business') == 'E-Commerce' ? 'selected' : '' }}>E-Commerce</option>
                        <option value="Education" {{ old('line_of_business') == 'Education' ? 'selected' : '' }}>Education</option>
                        <option value="Electronic" {{ old('line_of_business') == 'Electronic' ? 'selected' : '' }}>Electronic</option>
                        <option value="Embassy" {{ old('line_of_business') == 'Embassy' ? 'selected' : '' }}>Embassy</option>
                        <option value="Event Organizer" {{ old('line_of_business') == 'Event Organizer' ? 'selected' : '' }}>Event Organizer</option>
                        <option value="F&B Companies" {{ old('line_of_business') == 'F&B Companies' ? 'selected' : '' }}>F&B Companies</option>
                        <option value="Film & Media Creative" {{ old('line_of_business') == 'Film & Media Creative' ? 'selected' : '' }}>Film & Media Creative</option>
                        <option value="Finance" {{ old('line_of_business') == 'Finance' ? 'selected' : '' }}>Finance</option>
                        <option value="Financial Institution" {{ old('line_of_business') == 'Financial Institution' ? 'selected' : '' }}>Financial Institution</option>
                        <option value="Financial Technologies" {{ old('line_of_business') == 'Financial Technologies' ? 'selected' : '' }}>Financial Technologies</option>
                        <option value="Government" {{ old('line_of_business') == 'Government' ? 'selected' : '' }}>Government</option>
                        <option value="Healthcare" {{ old('line_of_business') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                        <option value="Heavy Industries" {{ old('line_of_business') == 'Heavy Industries' ? 'selected' : '' }}>Heavy Industries</option>
                        <option value="Hospital" {{ old('line_of_business') == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                        <option value="Hotel/Hospitality" {{ old('line_of_business') == 'Hotel/Hospitality' ? 'selected' : '' }}>Hotel/Hospitality</option>
                        <option value="HR Services" {{ old('line_of_business') == 'HR Services' ? 'selected' : '' }}>HR Services</option>
                        <option value="Individual Guest" {{ old('line_of_business') == 'Individual Guest' ? 'selected' : '' }}>Individual Guest</option>
                        <option value="Infrastructure" {{ old('line_of_business') == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                        <option value="Insurance" {{ old('line_of_business') == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                        <option value="IT" {{ old('line_of_business') == 'IT' ? 'selected' : '' }}>IT</option>
                        <option value="Law Firm" {{ old('line_of_business') == 'Law Firm' ? 'selected' : '' }}>Law Firm</option>
                        <option value="Manufacture" {{ old('line_of_business') == 'Manufacture' ? 'selected' : '' }}>Manufacture</option>
                        <option value="Media" {{ old('line_of_business') == 'Media' ? 'selected' : '' }}>Media</option>
                        <option value="Mining" {{ old('line_of_business') == 'Mining' ? 'selected' : '' }}>Mining</option>
                        <option value="MLM" {{ old('line_of_business') == 'MLM' ? 'selected' : '' }}>MLM</option>
                        <option value="NGO" {{ old('line_of_business') == 'NGO' ? 'selected' : '' }}>NGO</option>
                        <option value="Oil, Gas & Energy Companies" {{ old('line_of_business') == 'Oil, Gas & Energy Companies' ? 'selected' : '' }}>Oil, Gas & Energy Companies</option>
                        <option value="Organization" {{ old('line_of_business') == 'Organization' ? 'selected' : '' }}>Organization</option>
                        <option value="Outsourcing" {{ old('line_of_business') == 'Outsourcing' ? 'selected' : '' }}>Outsourcing</option>
                        <option value="Pharmaceutical" {{ old('line_of_business') == 'Pharmaceutical' ? 'selected' : '' }}>Pharmaceutical</option>
                        <option value="Property" {{ old('line_of_business') == 'Property' ? 'selected' : '' }}>Property</option>
                        <option value="Retail" {{ old('line_of_business') == 'Retail' ? 'selected' : '' }}>Retail</option>
                        <option value="Telecommunication" {{ old('line_of_business') == 'Telecommunication' ? 'selected' : '' }}>Telecommunication</option>
                        <option value="Tour Operator/Travel Agent" {{ old('line_of_business') == 'Tour Operator/Travel Agent' ? 'selected' : '' }}>Tour Operator/Travel Agent</option>
                        <option value="Trading" {{ old('line_of_business') == 'Trading' ? 'selected' : '' }}>Trading</option>
                        <option value="Transportation/Logistics" {{ old('line_of_business') == 'Transportation/Logistics' ? 'selected' : '' }}>Transportation/Logistics</option>
                        <option value="Other" {{ old('line_of_business') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('line_of_business')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                    <input type="text" id="company_address" name="company_address" value="{{ old('company_address') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('company_address')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        
        <!-- Contact Person Information -->
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Contact Person Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-1">Contact Person Name <span class="text-red-500">*</span></label>
                    <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('contact_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" id="position" name="position" value="{{ old('position') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('position')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('phone_number')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        
        <!-- Import from Sales Mission -->
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Import from Sales Mission</h4>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="sales_mission_detail_id" class="block text-sm font-medium text-gray-700 mb-1">Select Sales Mission Contact (optional)</label>
                    <select id="sales_mission_detail_id" name="sales_mission_detail_id" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">-- Select a contact to import --</option>
                        @foreach($salesMissionDetails as $detail)
                            <option value="{{ $detail->id }}">
                                {{ $detail->company_name }} - {{ $detail->company_pic }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">If you select a Sales Mission contact, it will auto-fill the form fields.</p>
                </div>
            </div>
        </div>
        
        <!-- Additional Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
            <textarea id="notes" name="notes" rows="4" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('notes') }}</textarea>
            @error('notes')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        
        <!-- Business Details -->
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Business Details</h4>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="general_information" class="block text-sm font-medium text-gray-700 mb-1">General Information</label>
                    <textarea id="general_information" name="general_information" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('general_information') }}</textarea>
                    @error('general_information')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="current_event" class="block text-sm font-medium text-gray-700 mb-1">Current Event</label>
                    <textarea id="current_event" name="current_event" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('current_event') }}</textarea>
                    @error('current_event')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="target_business" class="block text-sm font-medium text-gray-700 mb-1">Potential / Target Business</label>
                    <textarea id="target_business" name="target_business" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('target_business') }}</textarea>
                    @error('target_business')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="project_type" class="block text-sm font-medium text-gray-700 mb-1">Type of Project / Partnership</label>
                    <textarea id="project_type" name="project_type" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('project_type') }}</textarea>
                    @error('project_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div>
                    <label for="project_estimation" class="block text-sm font-medium text-gray-700 mb-1">Estimation of Project / Tender</label>
                    <textarea id="project_estimation" name="project_estimation" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('project_estimation') }}</textarea>
                    @error('project_estimation')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="potential_revenue" class="block text-sm font-medium text-gray-700 mb-1">Potential Revenue (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="text" id="potential_revenue" name="potential_revenue" value="{{ old('potential_revenue') }}" class="pl-10 w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 currency-input" placeholder="1,000,000">
                            @error('potential_revenue')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="potential_project_count" class="block text-sm font-medium text-gray-700 mb-1">Potential Number of Projects / Partnerships</label>
                        <input type="number" id="potential_project_count" name="potential_project_count" value="{{ old('potential_project_count') }}" min="0" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('potential_project_count')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <a href="{{ route('sales_officer.contacts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-3">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Save Contact
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const salesMissionSelect = document.getElementById('sales_mission_detail_id');
        
        salesMissionSelect.addEventListener('change', function() {
            if (this.value) {
                // Make an Ajax request to get the contact details
                fetch(`/officer/contacts/mission/${this.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('company_name').value = data.company_name;
                        
                        // Set a default line of business or leave empty
                        const lineOfBusinessField = document.getElementById('line_of_business');
                        if (data.line_of_business) {
                            lineOfBusinessField.value = data.line_of_business;
                        } else {
                            // Default to "Other" if no line of business is specified
                            lineOfBusinessField.value = 'Other';
                        }
                        
                        document.getElementById('company_address').value = data.company_address;
                        document.getElementById('contact_name').value = data.company_pic;
                        document.getElementById('position').value = data.company_position;
                        document.getElementById('phone_number').value = data.company_contact;
                        document.getElementById('email').value = data.company_email;
                    }
                });
            }
        });
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
</script>
@endpush
@endsection 