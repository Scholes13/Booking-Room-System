@extends('sales_officer.layout')

@section('title', 'Create Activity')
@section('header', 'Create New Activity')
@section('description', 'Add a new sales activity to your schedule')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100">
    <!-- Display general errors -->
    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan pada saat menyimpan data:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Draft autosave notification -->
    <div id="autosave-notification" class="hidden mb-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-md p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-blue-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <span id="draft-status">Draft tersimpan</span>
        </div>
    </div>

    <form id="activity-form" action="{{ route('sales_officer.activities.store') }}" method="POST" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- Date & Schedule Section -->
        <div class="form-section bg-white border-l-4 border-primary pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="schedule">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">Date & Schedule Information</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="schedule-section-content">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                    <div>
                        <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="text" id="start_datetime" name="start_datetime" value="{{ old('start_datetime') }}" required class="datepicker w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="YYYY-MM-DD">
                        <div class="text-xs text-gray-500 mt-1">Format: YYYY-MM-DD (contoh: 2023-12-31)</div>
                        <div id="date-error" class="text-red-500 text-xs mt-1"></div>
                        @error('start_datetime')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <input type="hidden" id="end_datetime" name="end_datetime" value="{{ old('end_datetime') }}">
                    
                    <div>
                        <label for="month_number" class="block text-sm font-medium text-gray-700 mb-1">Month <span class="text-red-500">*</span></label>
                        <select id="month_number" name="month_number" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Month</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('month_number') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                        <div id="month-error" class="text-red-500 text-xs mt-1"></div>
                        @error('month_number')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="week_number" class="block text-sm font-medium text-gray-700 mb-1">Week <span class="text-red-500">*</span></label>
                        <select id="week_number" name="week_number" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Week</option>
                            <option value="1" {{ old('week_number') == 1 ? 'selected' : '' }}>Week 1</option>
                            <option value="2" {{ old('week_number') == 2 ? 'selected' : '' }}>Week 2</option>
                            <option value="3" {{ old('week_number') == 3 ? 'selected' : '' }}>Week 3</option>
                            <option value="4" {{ old('week_number') == 4 ? 'selected' : '' }}>Week 4</option>
                            <option value="5" {{ old('week_number') == 5 ? 'selected' : '' }}>Week 5</option>
                        </select>
                        <div id="week-error" class="text-red-500 text-xs mt-1"></div>
                        @error('week_number')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Company Information Section -->
        <div class="form-section bg-white border-l-4 border-blue-400 pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="company">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">Company Information</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="company-section-content">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="company_selector" class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="company_selector" name="company_selector" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="" disabled selected>Select a company</option>
                                <option value="new" class="text-primary font-medium">+ Add New Company</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}" data-visit-count="{{ $contact->activities->count() }}" data-company-name="{{ $contact->company_name }}">
                                        {{ $contact->company_name }} ({{ $contact->activities->count() > 0 ? $contact->activities->count() . ' visits' : 'No visits yet' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="company-error" class="text-red-500 text-xs mt-1"></div>
                        <div id="visit_count_display" class="text-sm mt-1 text-primary font-medium hidden">
                            Visit count: <span id="visit_count_number">0</span>
                        </div>
                    </div>
                    
                    <div id="new_company_container" class="hidden">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">New Company Name <span class="text-red-500">*</span></label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('company_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div id="line_of_business_container">
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
                        <div id="business-error" class="text-red-500 text-xs mt-1"></div>
                        @error('line_of_business')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="division_selector" class="block text-sm font-medium text-gray-700 mb-1">Division <span class="text-gray-500">(optional)</span></label>
                        <div class="relative">
                            <select id="division_selector" name="division_selector" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="" disabled selected>Select company first</option>
                            </select>
                        </div>
                        <div id="division_visit_count_display" class="text-sm mt-1 text-primary font-medium hidden">
                            Division visit count: <span id="division_visit_count_number">0</span>
                        </div>
                    </div>
                    
                    <div id="new_division_container" class="hidden">
                        <label for="division_name" class="block text-sm font-medium text-gray-700 mb-1">New Division Name</label>
                        <input type="text" id="division_name" name="division_name" value="{{ old('division_name') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('division_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="account_status" class="block text-sm font-medium text-gray-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                        <select id="account_status" name="account_status" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Status</option>
                            <option value="New" {{ old('account_status') == 'New' ? 'selected' : '' }}>New</option>
                            <option value="Contracted" {{ old('account_status') == 'Contracted' ? 'selected' : '' }}>Contracted</option>
                            <option value="Existing" {{ old('account_status') == 'Existing' ? 'selected' : '' }}>Existing</option>
                        </select>
                        <div id="status-error" class="text-red-500 text-xs mt-1"></div>
                        @error('account_status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- PIC Information Section -->
        <div class="form-section bg-white border-l-4 border-green-400 pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="pic">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">PIC Information</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="pic-section-content">
                <!-- PIC Selection or Creation -->
                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pic_selector" class="block text-sm font-medium text-gray-700 mb-1">PIC <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="pic_selector" name="pic_selector" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="" disabled selected>Select company first</option>
                                </select>
                            </div>
                            <div id="pic-error" class="text-red-500 text-xs mt-1"></div>
                        </div>
                        
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position <span class="text-red-500">*</span></label>
                            <input type="text" id="position" name="position" value="{{ old('position') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            @error('position')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div id="new_pic_container" class="hidden mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="pic_title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                                <select id="pic_title" name="pic_title" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="Mr" {{ old('pic_title') == 'Mr' ? 'selected' : '' }}>Mr</option>
                                    <option value="Mrs" {{ old('pic_title') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                    <option value="Ms" {{ old('pic_title') == 'Ms' ? 'selected' : '' }}>Ms</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" id="pic_name" name="pic_name" value="{{ old('pic_name') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Full name">
                                @error('pic_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div>
                                <label for="pic_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                <input type="text" id="pic_phone" name="pic_phone" value="{{ old('pic_phone') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="e.g. 081234567890">
                                @error('pic_phone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                                <div class="text-xs text-gray-500 mt-1">If empty, will be filled with "0"</div>
                            </div>
                            
                            <div>
                                <label for="pic_email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="pic_email" name="pic_email" value="{{ old('pic_email') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="email@example.com">
                                @error('pic_email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                                <div class="text-xs text-gray-500 mt-1">If empty, will be filled with "blank@werkudara.com"</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Address Section -->
        <div class="form-section bg-white border-l-4 border-yellow-400 pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="address">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">Company Address</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="address-section-content">
                <div class="md:col-span-2">
                    <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address <span class="text-red-500">*</span></label>
                    <textarea id="company_address" name="company_address" rows="2" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('company_address') }}</textarea>
                    @error('company_address')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                
                <!-- Location Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country <span class="text-red-500">*</span></label>
                        <select id="country" name="country" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Country</option>
                            <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                            <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                            <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                            <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                            <option value="Philippines" {{ old('country') == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                        </select>
                        @error('country')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Province <span class="text-red-500">*</span></label>
                        <select id="province" name="province" required data-old-value="{{ old('province') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Country First</option>
                        </select>
                        @error('province')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                        <select id="city" name="city" required data-old-value="{{ old('city') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Province First</option>
                        </select>
                        @error('city')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <input type="hidden" id="country_id" name="country_id" value="{{ old('country_id') }}">
                <input type="hidden" id="state_id" name="state_id" value="{{ old('state_id') }}">
                <input type="hidden" id="city_id" name="city_id" value="{{ old('city_id') }}">
                
                <input type="hidden" id="contact_id" name="contact_id" value="">
                <input type="hidden" id="division_id" name="division_id" value="">
                <input type="hidden" id="pic_id" name="pic_id" value="">
                <input type="hidden" id="pic_title_selected" name="pic_title_selected" value="{{ old('pic_title_selected', 'Mr') }}">
                <input type="hidden" id="visit_count" name="visit_count" value="1">
                <input type="hidden" id="division_visit_count" name="division_visit_count" value="1">
            </div>
        </div>
        
        <!-- Activity Details Section -->
        <div class="form-section bg-white border-l-4 border-purple-400 pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="activity">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">Activity Details</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="activity-section-content">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type <span class="text-red-500">*</span></label>
                        <select id="activity_type" name="activity_type" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Type</option>
                            <option value="Event Networking" {{ old('activity_type') == 'Event Networking' ? 'selected' : '' }}>Event Networking</option>
                            <option value="Meeting" {{ old('activity_type') == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                            <option value="Negotiation" {{ old('activity_type') == 'Negotiation' ? 'selected' : '' }}>Negotiation</option>
                            <option value="Presentation - Introduction & Compro" {{ old('activity_type') == 'Presentation - Introduction & Compro' ? 'selected' : '' }}>Presentation - Introduction & Compro</option>
                            <option value="Presentation - Pitching" {{ old('activity_type') == 'Presentation - Pitching' ? 'selected' : '' }}>Presentation - Pitching</option>
                            <option value="Sales Call" {{ old('activity_type') == 'Sales Call' ? 'selected' : '' }}>Sales Call</option>
                            <option value="Telemarketing" {{ old('activity_type') == 'Telemarketing' ? 'selected' : '' }}>Telemarketing</option>
                            <option value="Telemarketing - Email" {{ old('activity_type') == 'Telemarketing - Email' ? 'selected' : '' }}>Telemarketing - Email</option>
                            <option value="Telemarketing - LinkedIn" {{ old('activity_type') == 'Telemarketing - LinkedIn' ? 'selected' : '' }}>Telemarketing - LinkedIn</option>
                            <option value="Telemarketing - Phone/WhatsApp" {{ old('activity_type') == 'Telemarketing - Phone/WhatsApp' ? 'selected' : '' }}>Telemarketing - Phone/WhatsApp</option>
                            <option value="Werkudara Client Event" {{ old('activity_type') == 'Werkudara Client Event' ? 'selected' : '' }}>Werkudara Client Event</option>
                        </select>
                        <div id="activity-type-error" class="text-red-500 text-xs mt-1"></div>
                        @error('activity_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="meeting_type" class="block text-sm font-medium text-gray-700 mb-1">Meeting Type <span class="text-red-500">*</span></label>
                        <select id="meeting_type" name="meeting_type" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Type</option>
                            <option value="Online" {{ old('meeting_type') == 'Online' ? 'selected' : '' }}>Online</option>
                            <option value="Offline" {{ old('meeting_type') == 'Offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                        <div id="meeting-type-error" class="text-red-500 text-xs mt-1"></div>
                        @error('meeting_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="products_discussed" class="block text-sm font-medium text-gray-700 mb-1">Number of Products Discussed</label>
                        <input type="number" id="products_discussed" name="products_discussed" min="1" max="50" value="{{ old('products_discussed', 1) }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('products_discussed')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Business Details Section -->
        <div class="form-section bg-white border-l-4 border-indigo-400 pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="business">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">Business Details</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="business-section-content">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="jso_lead_status" class="block text-sm font-medium text-gray-700 mb-1">Lead Status <span class="text-red-500">*</span></label>
                        <select id="jso_lead_status" name="jso_lead_status" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Lead Status</option>
                            <option value="Closed / Cold Lead" {{ old('jso_lead_status') == 'Closed / Cold Lead' ? 'selected' : '' }}>Closed / Cold Lead</option>
                            <option value="Closed / Handed Over" {{ old('jso_lead_status') == 'Closed / Handed Over' ? 'selected' : '' }}>Closed / Handed Over</option>
                            <option value="Closed / No Prospect" {{ old('jso_lead_status') == 'Closed / No Prospect' ? 'selected' : '' }}>Closed / No Prospect</option>
                            <option value="Cold Lead" {{ old('jso_lead_status') == 'Cold Lead' ? 'selected' : '' }}>Cold Lead</option>
                            <option value="Handed Over" {{ old('jso_lead_status') == 'Handed Over' ? 'selected' : '' }}>Handed Over</option>
                            <option value="Hot Lead" {{ old('jso_lead_status') == 'Hot Lead' ? 'selected' : '' }}>Hot Lead</option>
                            <option value="Lost Lead" {{ old('jso_lead_status') == 'Lost Lead' ? 'selected' : '' }}>Lost Lead</option>
                            <option value="On progress" {{ old('jso_lead_status') == 'On progress' ? 'selected' : '' }}>On progress</option>
                            <option value="Open / Cold Lead" {{ old('jso_lead_status') == 'Open / Cold Lead' ? 'selected' : '' }}>Open / Cold Lead</option>
                            <option value="Open / Hot Lead" {{ old('jso_lead_status') == 'Open / Hot Lead' ? 'selected' : '' }}>Open / Hot Lead</option>
                        </select>
                        <div id="lead-status-error" class="text-red-500 text-xs mt-1"></div>
                        @error('jso_lead_status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
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
        </div>
        
        <!-- Follow Up Section -->
        <div class="form-section bg-white border-l-4 border-pink-400 pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="followup">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">Follow Up Information</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="followup-section-content">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="next_follow_up" class="block text-sm font-medium text-gray-700 mb-1">Next Follow Up <span class="text-red-500">*</span></label>
                        <input type="text" id="next_follow_up" name="next_follow_up" value="{{ old('next_follow_up') }}" required class="datepicker w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <div id="followup-error" class="text-red-500 text-xs mt-1"></div>
                        @error('next_follow_up')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <input type="text" id="status" name="status" value="{{ old('status') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <div id="status-field-error" class="text-red-500 text-xs mt-1"></div>
                        @error('status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="follow_up_type" class="block text-sm font-medium text-gray-700 mb-1">Type of Follow Up <span class="text-red-500">*</span></label>
                        <input type="text" id="follow_up_type" name="follow_up_type" value="{{ old('follow_up_type') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <div id="followup-type-error" class="text-red-500 text-xs mt-1"></div>
                        @error('follow_up_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="follow_up_frequency" class="block text-sm font-medium text-gray-700 mb-1">Frequency Follow Up <span class="text-red-500">*</span></label>
                        <select id="follow_up_frequency" name="follow_up_frequency" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select Frequency</option>
                            <option value="Weekly" {{ old('follow_up_frequency') == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="Monthly" {{ old('follow_up_frequency') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="Bi-Weekly" {{ old('follow_up_frequency') == 'Bi-Weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="Quarterly" {{ old('follow_up_frequency') == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="Semester" {{ old('follow_up_frequency') == 'Semester' ? 'selected' : '' }}>Semester</option>
                            <option value="Yearly" {{ old('follow_up_frequency') == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                            <option value="As Requested" {{ old('follow_up_frequency') == 'As Requested' ? 'selected' : '' }}>As Requested</option>
                        </select>
                        <div id="frequency-error" class="text-red-500 text-xs mt-1"></div>
                        @error('follow_up_frequency')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- History Follow Up Section -->
        <div class="form-section bg-white border-l-4 border-gray-400 pl-4 mb-6">
            <div class="section-header flex justify-between items-center cursor-pointer mb-4" data-section="history">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800">History Follow Up</h4>
                <button type="button" class="section-toggle text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="history-section-content">
                <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                    <div id="follow_up_history" class="text-sm">
                        <p class="text-gray-500 italic">Follow-up history will be displayed here for existing contacts.</p>
                        
                        <div id="follow_up_history_container" class="hidden mt-3">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Follow Up Type</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Follow Up</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="follow_up_history_body">
                                    <!-- Follow-up history items will be dynamically added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <a href="{{ route('sales_officer.activities.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-3">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Create Activity
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="{{ asset('js/location-data.js') }}"></script>
<script src="{{ asset('js/location-dropdowns.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Form script initialized');
        
        // First make sure all sections are visible when the page loads
        document.querySelectorAll('.section-content').forEach(content => {
            content.classList.remove('hidden');
            console.log('Section made visible:', content.id);
        });
        
        // Toggle section visibility - modified to ensure they don't stay hidden
        document.querySelectorAll('.section-header').forEach(header => {
            header.addEventListener('click', function() {
                const sectionName = this.getAttribute('data-section');
                const content = document.getElementById(`${sectionName}-section-content`);
                const toggle = this.querySelector('.section-toggle svg');
                
                // Instead of toggling, just make sure it's always visible
                content.classList.remove('hidden');
                toggle.classList.remove('rotate-180'); // Always pointing down
                
                console.log('Section clicked:', sectionName, 'now visible');
            });
        });
        
        // Special handler for business section toggle
        const businessToggle = document.querySelector('[data-section="business"]');
        if (businessToggle) {
            businessToggle.addEventListener('click', function(e) {
                // Prevent normal click behavior
                e.stopPropagation();
                
                // Always ensure the business section is visible
                const businessContent = document.getElementById('business-section-content');
                businessContent.classList.remove('hidden');
                
                const toggle = this.querySelector('.section-toggle svg');
                toggle.classList.remove('rotate-180');
                
                console.log('Business section toggle clicked - ensuring visibility');
            }, true);
        }

        // Get references to DOM elements
        const companySelector = document.getElementById('company_selector');
        const newCompanyContainer = document.getElementById('new_company_container');
        const companyNameField = document.getElementById('company_name');
        const visitCountDisplay = document.getElementById('visit_count_display');
        const visitCountNumber = document.getElementById('visit_count_number');
        
        // Division elements
        const divisionSelector = document.getElementById('division_selector');
        const newDivisionContainer = document.getElementById('new_division_container');
        const divisionNameField = document.getElementById('division_name');
        const divisionVisitCountDisplay = document.getElementById('division_visit_count_display');
        const divisionVisitCountNumber = document.getElementById('division_visit_count_number');
        
        // PIC elements
        const picSelector = document.getElementById('pic_selector');
        const newPicContainer = document.getElementById('new_pic_container');
        const picNameField = document.getElementById('pic_name');
        const picTitleField = document.getElementById('pic_title');
        const positionField = document.getElementById('position');
        
        // Make sure containers are hidden by default
        newCompanyContainer.classList.add('hidden');
        newDivisionContainer.classList.add('hidden');
        newPicContainer.classList.add('hidden');
        
        // Data storage
        let divisionsData = {};
        let picsData = {};
        let companiesData = {};
        
        // Function to ensure all sections are visible
        function ensureAllSectionsVisible() {
            document.querySelectorAll('.section-content').forEach(content => {
                if (content.classList.contains('hidden')) {
                    console.log('Making section visible:', content.id);
                    content.classList.remove('hidden');
                }
            });
            
            // Extra focus on business section
            const businessSection = document.getElementById('business-section-content');
            if (businessSection) {
                businessSection.classList.remove('hidden');
                console.log('Ensuring business section is visible');
            }
        }

        // Handle company selection
        companySelector.addEventListener('change', function() {
            console.log('Company selector changed to:', this.value);
            const selectedOption = this.options[this.selectedIndex];
            
            // Reset division fields
            divisionSelector.innerHTML = '';
            divisionVisitCountDisplay.classList.add('hidden');
            newDivisionContainer.classList.add('hidden');
            divisionNameField.removeAttribute('required');
            
            // Reset PIC fields
            picSelector.innerHTML = '';
            newPicContainer.classList.add('hidden');
            picNameField.removeAttribute('required');
            
            if (this.value === 'new') {
                // New company selected
                newCompanyContainer.classList.remove('hidden');
                companyNameField.setAttribute('required', 'required');
                companyNameField.focus();
                visitCountDisplay.classList.add('hidden');
                
                // Setup division dropdown for new company
                divisionSelector.innerHTML = '<option value="">No division (optional)</option>';
                divisionSelector.innerHTML += '<option value="new" class="text-primary font-medium">+ Add New Division</option>';
                
                // Setup PIC dropdown for new company
                picSelector.innerHTML = '<option value="" disabled selected>Enter PIC details</option>';
                picSelector.innerHTML += '<option value="new" class="text-primary font-medium">+ Add New PIC</option>';
                
                // Show PIC input fields
                newPicContainer.classList.remove('hidden');
                picNameField.setAttribute('required', 'required');
                
                // Set account status to New for new companies
                document.getElementById('account_status').value = 'New';
                
            } else if (this.value !== '') {
                // Existing company selected
                newCompanyContainer.classList.add('hidden');
                companyNameField.removeAttribute('required');
                
                // Show company visit count
                const visitCount = parseInt(selectedOption.dataset.visitCount) || 0;
                visitCountNumber.textContent = visitCount;
                visitCountDisplay.classList.remove('hidden');
                
                // Set default account status for existing companies
                if (visitCount > 0) {
                    document.getElementById('account_status').value = 'Existing';
                }
                
                // Load line of business for this company if available
                if (companiesData[this.value] && companiesData[this.value].lineOfBusiness) {
                    document.getElementById('line_of_business').value = companiesData[this.value].lineOfBusiness;
                }
                
                // Load divisions for this company
                loadDivisions(this.value);
                
                // Load PICs for this company
                loadPICs(this.value);
                
            } else {
                // No selection
                newCompanyContainer.classList.add('hidden');
                visitCountDisplay.classList.add('hidden');
                
                // Reset dropdown options
                divisionSelector.innerHTML = '<option value="" disabled selected>Select company first</option>';
                picSelector.innerHTML = '<option value="" disabled selected>Select company first</option>';
            }
            
            // Always ensure all sections are visible after company change
            setTimeout(ensureAllSectionsVisible, 100);
        });
        
        // Handle division selection
        divisionSelector.addEventListener('change', function() {
            console.log('Division selector changed to:', this.value);
            
            if (this.value === 'new') {
                // New division selected
                newDivisionContainer.classList.remove('hidden');
                divisionNameField.focus();
                divisionVisitCountDisplay.classList.add('hidden');
                
                // Reset PIC list and load PICs for company only (not division specific)
                if (companySelector.value && companySelector.value !== 'new') {
                    loadPICs(companySelector.value);
                }
                
            } else if (this.value !== '' && this.value !== 'none') {
                // Existing division selected
                newDivisionContainer.classList.add('hidden');
                
                // Get the selected division data
                const selectedDivision = divisionsData[this.value];
                if (selectedDivision) {
                    // Show division visit count
                    const visitCount = parseInt(selectedDivision.visitCount) || 0;
                    divisionVisitCountNumber.textContent = visitCount;
                    divisionVisitCountDisplay.classList.remove('hidden');
                    
                    // Load PICs for this division
                    loadDivisionPICs(this.value);
                }
                
            } else if (this.value === 'none') {
                // No division selected
                newDivisionContainer.classList.add('hidden');
                divisionVisitCountDisplay.classList.add('hidden');
                
                // Load PICs for the company (not division specific)
                if (companySelector.value && companySelector.value !== 'new') {
                    loadPICs(companySelector.value);
                }
                
            } else {
                // No selection
                newDivisionContainer.classList.add('hidden');
                divisionVisitCountDisplay.classList.add('hidden');
            }
            
            // Always ensure all sections are visible after division change
            setTimeout(ensureAllSectionsVisible, 100);
        });
        
        // Handle PIC selection
        picSelector.addEventListener('change', function() {
            console.log('PIC selector changed to:', this.value);
            
            if (this.value === 'new') {
                // New PIC selected
                newPicContainer.classList.remove('hidden');
                picNameField.setAttribute('required', 'required');
                picNameField.focus();
                
                // Clear position field for new PIC
                positionField.value = '';
                // Clear phone and email fields for new PIC
                document.getElementById('pic_phone').value = '';
                document.getElementById('pic_email').value = '';
                
            } else if (this.value !== '') {
                // Existing PIC selected
                newPicContainer.classList.add('hidden');
                picNameField.removeAttribute('required');
                
                // Set position field from selected PIC
                const selectedPic = picsData[this.value];
                if (selectedPic) {
                    if (selectedPic.position) {
                        positionField.value = selectedPic.position;
                    }
                }
                
            } else {
                // No selection
                newPicContainer.classList.add('hidden');
                picNameField.removeAttribute('required');
            }
            
            // Always ensure all sections are visible after PIC change
            setTimeout(ensureAllSectionsVisible, 100);
        });
        
        // ===== Loader Functions =====
        
        // Function to load divisions for a company
        function loadDivisions(companyId) {
            fetch(`/officer/api/company/${companyId}/divisions`)
                .then(response => response.json())
                .then(data => {
                    divisionsData = {};
                    
                    // Clear current division options
                    divisionSelector.innerHTML = '';
                    
                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = 'none';
                    defaultOption.textContent = 'No division (optional)';
                    divisionSelector.appendChild(defaultOption);
                    
                    // Add option to create new division
                    const newOption = document.createElement('option');
                    newOption.value = 'new';
                    newOption.className = 'text-primary font-medium';
                    newOption.textContent = '+ Add New Division';
                    divisionSelector.appendChild(newOption);
                    
                    // Add existing divisions
                    if (data.length > 0) {
                        data.forEach(division => {
                            const option = document.createElement('option');
                            option.value = division.id;
                            option.textContent = `${division.name} (${division.visit_count > 0 ? division.visit_count + ' visits' : 'No visits yet'})`;
                            divisionSelector.appendChild(option);
                            
                            // Store division data for later use
                            divisionsData[division.id] = {
                                id: division.id,
                                name: division.name,
                                visitCount: division.visit_count
                            };
                        });
                    }
                })
                .catch(error => console.error('Error loading divisions:', error));
        }
        
        // Function to load PICs for a company (not division specific)
        function loadPICs(companyId) {
            fetch(`/officer/api/company/${companyId}/pics`)
                .then(response => response.json())
                .then(data => {
                    picsData = {};
                    
                    // Clear current PIC options
                    picSelector.innerHTML = '';
                    
                    // Add option to create new PIC
                    const newOption = document.createElement('option');
                    newOption.value = 'new';
                    newOption.className = 'text-primary font-medium';
                    newOption.textContent = '+ Add New PIC';
                    picSelector.appendChild(newOption);
                    
                    // Add existing PICs
                    if (data.length > 0) {
                        data.forEach(pic => {
                            const option = document.createElement('option');
                            option.value = pic.id;
                            const primaryTag = pic.is_primary ? ' (Primary)' : '';
                            option.textContent = `${pic.title} ${pic.name} - ${pic.position}${primaryTag}`;
                            picSelector.appendChild(option);
                            
                            // Store PIC data for later use
                            picsData[pic.id] = {
                                id: pic.id,
                                title: pic.title,
                                name: pic.name,
                                position: pic.position,
                                phone_number: pic.phone_number,
                                email: pic.email,
                                isPrimary: pic.is_primary
                            };
                        });
                    } else {
                        // No PICs found, automatically show the form to add a new one
                        newPicContainer.classList.remove('hidden');
                        picNameField.setAttribute('required', 'required');
                        
                        // Select the "Add New PIC" option
                        picSelector.value = 'new';
                    }
                })
                .catch(error => console.error('Error loading PICs:', error));
        }
        
        // Function to load PICs for a specific division
        function loadDivisionPICs(divisionId) {
            fetch(`/officer/api/division/${divisionId}/pics`)
                .then(response => response.json())
                .then(data => {
                    picsData = {};
                    
                    // Clear current PIC options
                    picSelector.innerHTML = '';
                    
                    // Add option to create new PIC
                    const newOption = document.createElement('option');
                    newOption.value = 'new';
                    newOption.className = 'text-primary font-medium';
                    newOption.textContent = '+ Add New PIC for Division';
                    picSelector.appendChild(newOption);
                    
                    // Add existing PICs
                    if (data.length > 0) {
                        data.forEach(pic => {
                            const option = document.createElement('option');
                            option.value = pic.id;
                            const primaryTag = pic.is_primary ? ' (Primary)' : '';
                            option.textContent = `${pic.title} ${pic.name} - ${pic.position}${primaryTag}`;
                            picSelector.appendChild(option);
                            
                            // Store PIC data for later use
                            picsData[pic.id] = {
                                id: pic.id,
                                title: pic.title,
                                name: pic.name,
                                position: pic.position,
                                phone_number: pic.phone_number,
                                email: pic.email,
                                isPrimary: pic.is_primary
                            };
                        });
                    } else {
                        // No PICs found, automatically show the form to add a new one
                        newPicContainer.classList.remove('hidden');
                        picNameField.setAttribute('required', 'required');
                        
                        // Select the "Add New PIC" option
                        picSelector.value = 'new';
                    }
                })
                .catch(error => console.error('Error loading division PICs:', error));
        }

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
                
                // Set end_datetime to the same date (since we only need the date part)
                document.getElementById('end_datetime').value = this.value;
            }
        });
        
        // When the page loads, fetch all companies data for reference
        function loadCompaniesData() {
            fetch('/officer/api/companies')
                .then(response => {
                    if (!response.ok) {
                        console.warn('No company data available yet.');
                        return [];
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if we received a setup_required message
                    if (data.setup_required) {
                        console.warn(data.message);
                        return;
                    }

                    // Check if data is an array
                    if (Array.isArray(data)) {
                        data.forEach(company => {
                            if (company && company.id) {
                                companiesData[company.id] = {
                                    id: company.id,
                                    name: company.company_name,
                                    lineOfBusiness: company.line_of_business,
                                    address: company.company_address,
                                    visitCount: company.visit_count
                                };
                            }
                        });
                    }
                })
                .catch(error => {
                    console.warn('Could not load companies. This is normal if you have not created any companies yet or if the database is not set up yet.');
                });
        }
        
        // Call the function to load companies data
        loadCompaniesData();
        
        // Initialize all date/time pickers
        flatpickr(".datepicker", {
            enableTime: false,
            dateFormat: "Y-m-d",
            allowInput: true,
            altFormat: "d F Y",
            altInput: true,
            locale: {
                firstDayOfWeek: 1 // Start with Monday
            }
        });

        // Form validation - dari implementasi yang baru
        const activityForm = document.getElementById('activity-form');
        if (activityForm) {
            activityForm.addEventListener('submit', function(e) {
                let hasErrors = false;
                
                // Clear previous errors
                document.querySelectorAll('.text-red-500:not([data-error-type="server"])').forEach(el => {
                    el.textContent = '';
                });
                
                // Validate date
                if (!startDatetime.value) {
                    document.getElementById('date-error').textContent = 'Tanggal aktivitas harus diisi';
                    hasErrors = true;
                }
                
                // Validate month
                const monthNumber = document.getElementById('month_number');
                if (!monthNumber.value) {
                    document.getElementById('month-error').textContent = 'Bulan harus dipilih';
                    hasErrors = true;
                }
                
                // Validate week
                const weekNumber = document.getElementById('week_number');
                if (!weekNumber.value) {
                    document.getElementById('week-error').textContent = 'Minggu harus dipilih';
                    hasErrors = true;
                }
                
                // Validate company
                if (!companySelector.value) {
                    document.getElementById('company-error').textContent = 'Perusahaan harus dipilih';
                    hasErrors = true;
                } else if (companySelector.value === 'new') {
                    if (!companyNameField.value) {
                        document.getElementById('company-error').textContent = 'Nama perusahaan baru harus diisi';
                        hasErrors = true;
                    }
                }
                
                // Validate line of business
                const lineOfBusiness = document.getElementById('line_of_business');
                if (!lineOfBusiness.value) {
                    document.getElementById('business-error').textContent = 'Lini bisnis harus dipilih';
                    hasErrors = true;
                }
                
                // Validate account status
                const accountStatus = document.getElementById('account_status');
                if (!accountStatus.value) {
                    document.getElementById('status-error').textContent = 'Status akun harus dipilih';
                    hasErrors = true;
                }
                
                // If there are errors, prevent form submission
                if (hasErrors) {
                    e.preventDefault();
                    
                    // Find first error and scroll to it
                    const firstError = document.querySelector('.text-red-500:not(:empty)');
                    if (firstError) {
                        const section = firstError.closest('.form-section');
                        const sectionContent = section.querySelector('.section-content');
                        
                        // Ensure section is expanded
                        if (sectionContent.classList.contains('hidden')) {
                            section.querySelector('.section-header').click();
                        }
                        
                        // Scroll to error
                        setTimeout(() => {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 100);
                    }
                }
            });
        }

        // Autosave draft - fitur baru
        let draftTimer;
        const formInputs = document.querySelectorAll('#activity-form input, #activity-form select, #activity-form textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                clearTimeout(draftTimer);
                draftTimer = setTimeout(saveDraft, 2000);
            });
            
            if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'textarea')) {
                input.addEventListener('keyup', function() {
                    clearTimeout(draftTimer);
                    draftTimer = setTimeout(saveDraft, 2000);
                });
            }
        });
        
        function saveDraft() {
            const formData = new FormData(document.getElementById('activity-form'));
            const notificationEl = document.getElementById('autosave-notification');
            const statusEl = document.getElementById('draft-status');
            
            // Store in localStorage as fallback
            const formObject = {};
            formData.forEach((value, key) => {
                formObject[key] = value;
            });
            
            localStorage.setItem('activity_draft', JSON.stringify(formObject));
            
            // Show notification
            statusEl.textContent = 'Draft tersimpan: ' + new Date().toLocaleTimeString();
            notificationEl.classList.remove('hidden');
            
            // Hide notification after 3 seconds
            setTimeout(() => {
                notificationEl.classList.add('hidden');
            }, 3000);
        }
        
        // Load draft if it exists
        const savedDraft = localStorage.getItem('activity_draft');
        if (savedDraft) {
            try {
                const draftData = JSON.parse(savedDraft);
                
                // Fill form with draft data
                Object.entries(draftData).forEach(([key, value]) => {
                    const field = document.querySelector(`[name="${key}"]`);
                    if (field) {
                        field.value = value;
                    }
                });
                
                // Trigger change events to ensure dependent dropdowns work
                if (draftData.company_selector) {
                    const event = new Event('change');
                    document.getElementById('company_selector').dispatchEvent(event);
                }
            } catch (e) {
                console.error('Error loading draft:', e);
            }
        }
        
        // After any form field change, ensure all sections are visible
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('change', function() {
                setTimeout(ensureAllSectionsVisible, 100);
            });
        });
        
        // Make all form sections visible again after page fully loads
        window.addEventListener('load', function() {
            ensureAllSectionsVisible();
            
            // Check every second that sections remain visible (for 30 seconds)
            let checkCount = 0;
            const visibilityInterval = setInterval(function() {
                ensureAllSectionsVisible();
                checkCount++;
                if (checkCount >= 30) {
                    clearInterval(visibilityInterval);
                }
            }, 1000);
        });
    });
</script>

<style>
    /* Style for Add New options */
    #company_selector option[value="new"],
    #division_selector option[value="new"],
    #pic_selector option[value="new"] {
        font-weight: 600;
        color: #4f46e5;
        background-color: #f3f4f6;
        padding: 8px;
    }
</style>
@endpush
@endsection 