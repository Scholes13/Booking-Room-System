@extends('sales_mission.layout')

@section('title', 'Edit Sales Mission')
@section('header', 'Edit Sales Mission')
@section('description', 'Update sales mission appointment details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <form action="{{ route('sales_mission.activities.update', $activity->id) }}" method="POST" id="editSalesMissionForm">
        @csrf
        @method('PUT')
        
        <!-- Appointment Creator Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                <i class="fas fa-user-tie text-primary mr-2"></i>
                Pembuat Appointment
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Employee Name <span class="text-red-500">*</span>
                    </label>
                    <select name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->name }}" 
                                data-department-id="{{ $employee->department_id }}"
                                {{ $activity->name == $employee->name ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $activity->department_id == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Appointment Details Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                <i class="fas fa-calendar-alt text-primary mr-2"></i>
                Data Appointment
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" id="company_name" 
                           value="{{ old('company_name', $activity->salesMissionDetail->company_name ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                           required>
                    @error('company_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company PIC -->
                <div>
                    <label for="company_pic" class="block text-sm font-medium text-gray-700 mb-2">
                        Company PIC <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_pic" id="company_pic" 
                           value="{{ old('company_pic', $activity->salesMissionDetail->company_pic ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                           required>
                    @error('company_pic')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Position -->
                <div>
                    <label for="company_position" class="block text-sm font-medium text-gray-700 mb-2">
                        Jabatan PIC <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_position" id="company_position" 
                           value="{{ old('company_position', $activity->salesMissionDetail->company_position ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                           required>
                    @error('company_position')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="company_contact" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_contact" id="company_contact" 
                           value="{{ old('company_contact', $activity->salesMissionDetail->company_contact ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                           required>
                    @error('company_contact')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Email -->
                <div>
                    <label for="company_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="company_email" id="company_email" 
                           value="{{ old('company_email', $activity->salesMissionDetail->company_email ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                           required>
                    @error('company_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="scheduled" {{ old('status', $activity->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ old('status', $activity->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $activity->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Province -->
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                        Province <span class="text-red-500">*</span>
                    </label>
                    <select name="province" id="province" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select Province</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ old('province', $activity->province) == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                    @error('province')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <select name="city" id="city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('city', $activity->city) == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Date & Time Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Start Date & Time -->
                <div>
                    <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" 
                           value="{{ old('start_datetime', $activity->start_datetime ? date('Y-m-d\TH:i', strtotime($activity->start_datetime)) : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent flatpickr-datetime" 
                           required>
                    @error('start_datetime')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date & Time -->
                <div>
                    <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" 
                           value="{{ old('end_datetime', $activity->end_datetime ? date('Y-m-d\TH:i', strtotime($activity->end_datetime)) : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent flatpickr-datetime" 
                           required>
                    @error('end_datetime')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Company Address -->
            <div class="mt-6">
                <label for="company_address" class="block text-sm font-medium text-gray-700 mb-2">
                    Company Address <span class="text-red-500">*</span>
                </label>
                <textarea name="company_address" id="company_address" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                          required>{{ old('company_address', $activity->salesMissionDetail->company_address ?? '') }}</textarea>
                @error('company_address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                          required>{{ old('description', $activity->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('sales_mission.activities.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-primary text-white rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors">
                <i class="fas fa-save mr-2"></i>
                Update Sales Mission
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Employee to Department mapping
    const employeeDepartmentMap = {
        @foreach($employees as $employee)
            '{{ $employee->name }}': {{ $employee->department_id }},
        @endforeach
    };
    
    // Auto-populate department when employee is selected
    const employeeSelect = document.getElementById('name');
    const departmentSelect = document.getElementById('department_id');
    
    employeeSelect.addEventListener('change', function() {
        const selectedEmployee = this.value;
        const departmentId = employeeDepartmentMap[selectedEmployee];
        
        if (departmentId) {
            departmentSelect.value = departmentId;
        } else {
            departmentSelect.value = '';
        }
    });
    
    // Form validation
    const form = document.getElementById('editSalesMissionForm');
    form.addEventListener('submit', function(e) {
        const startDateTime = new Date(document.getElementById('start_datetime').value);
        const endDateTime = new Date(document.getElementById('end_datetime').value);
        
        if (endDateTime <= startDateTime) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date Range',
                text: 'End date and time must be after start date and time.',
                confirmButtonColor: '#f59e0b'
            });
            return false;
        }
    });
});
</script>
@endpush