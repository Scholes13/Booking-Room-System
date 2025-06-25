@extends('sales_mission.layout')

@section('title', 'Edit Team')
@section('header', 'Edit Team')
@section('description', 'Edit team details and members')

@push('styles')
<style>
    /* Custom checkbox styles */
    .custom-checkbox {
        @apply w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500;
    }
    
    /* Hide employees that don't match search */
    .employee-item.hidden {
        display: none;
    }
    
    /* Selected employees styling */
    .selected-employee {
        @apply bg-amber-50 border-amber-300;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-6">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
        <form action="{{ route('sales_mission.teams.update', $team) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                <div class="font-medium">Please fix the following errors:</div>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Team Name -->
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Team Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $team->name) }}"
                    class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm" 
                    required
                />
            </div>
            
            <!-- Team Members -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Team Members <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-500 mb-2">Select employees to include in this team</p>
                
                <!-- Search Box -->
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input 
                        type="search" 
                        id="employee-search" 
                        class="block w-full p-3 pl-10 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500" 
                        placeholder="Search employees by name..." 
                    />
                </div>
                
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Available Employees -->
                    <div class="w-full md:w-1/2">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="font-medium text-gray-900 mb-2">Available Employees</h3>
                            <div class="max-h-96 overflow-y-auto">
                                @foreach($employees as $employee)
                                <div class="employee-item p-2 mb-2 bg-white rounded border border-gray-200 hover:bg-gray-50 cursor-pointer transition-all" 
                                     data-name="{{ strtolower($employee->name) }}" 
                                     data-email="{{ strtolower($employee->email ?? '') }}" 
                                     data-id="{{ $employee->id }}">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            id="employee_{{ $employee->id }}" 
                                            name="members[]" 
                                            value="{{ $employee->id }}" 
                                            class="employee-checkbox custom-checkbox mr-3"
                                            {{ in_array($employee->id, old('members', ($team->members ? $team->members->pluck('id')->toArray() : []))) ? 'checked' : '' }}
                                        >
                                        <label for="employee_{{ $employee->id }}" class="flex-1">
                                            <div class="font-medium text-gray-800">{{ $employee->name }}</div>
                                            @if($employee->email || $employee->phone)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $employee->email ?? '' }}
                                                {{ $employee->email && $employee->phone ? ' • ' : '' }}
                                                {{ $employee->phone ?? '' }}
                                            </div>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected Employees -->
                    <div class="w-full md:w-1/2">
                        <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                            <h3 class="font-medium text-amber-800 mb-2">Selected Team Members</h3>
                            <div id="selected-employees" class="min-h-[12rem] max-h-96 overflow-y-auto p-2">
                                <div id="no-selected" class="text-center text-gray-500 py-8">
                                    No employees selected yet
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('sales_mission.teams.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    Update Team
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSearch = document.getElementById('employee-search');
        const employeeItems = document.querySelectorAll('.employee-item');
        const selectedEmployeesContainer = document.getElementById('selected-employees');
        const noSelectedMessage = document.getElementById('no-selected');
        
        // Handle search functionality
        employeeSearch.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            
            employeeItems.forEach(item => {
                const name = item.dataset.name;
                const email = item.dataset.email;
                
                if (name.includes(searchValue) || email.includes(searchValue)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
        
        // Handle checkbox changes for visual feedback
        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            const employeeItem = checkbox.closest('.employee-item');
            const employeeId = employeeItem.dataset.id;
            
            // Initialize the selected state
            updateSelectedState(checkbox, employeeId);
            
            checkbox.addEventListener('change', function() {
                updateSelectedState(this, employeeId);
            });
        });
        
        // Function to update the selected employees visual display
        function updateSelectedState(checkbox, employeeId) {
            const employeeItem = checkbox.closest('.employee-item');
            const employeeName = employeeItem.querySelector('label div:first-child').textContent;
            const employeeDetails = employeeItem.querySelector('label div:last-child')?.textContent || '';
            
            if (checkbox.checked) {
                employeeItem.classList.add('selected-employee');
                
                // Add to selected list if not already there
                if (!document.getElementById(`selected-${employeeId}`)) {
                    const selectedItem = document.createElement('div');
                    selectedItem.id = `selected-${employeeId}`;
                    selectedItem.className = 'p-2 mb-2 bg-white rounded border border-amber-200';
                    selectedItem.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800">${employeeName}</div>
                                <div class="text-xs text-gray-500 mt-1">${employeeDetails}</div>
                            </div>
                            <button type="button" class="remove-employee text-amber-700 hover:text-amber-900" data-id="${employeeId}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    `;
                    selectedEmployeesContainer.appendChild(selectedItem);
                    
                    // Set up remove button
                    selectedItem.querySelector('.remove-employee').addEventListener('click', function() {
                        const employeeId = this.dataset.id;
                        document.getElementById(`employee_${employeeId}`).checked = false;
                        document.querySelector(`.employee-item[data-id="${employeeId}"]`).classList.remove('selected-employee');
                        selectedItem.remove();
                        updateNoSelectedMessage();
                    });
                    
                    updateNoSelectedMessage();
                }
            } else {
                employeeItem.classList.remove('selected-employee');
                const selectedItem = document.getElementById(`selected-${employeeId}`);
                if (selectedItem) {
                    selectedItem.remove();
                    updateNoSelectedMessage();
                }
            }
        }
        
        // Function to update the 'no selected' message visibility
        function updateNoSelectedMessage() {
            if (selectedEmployeesContainer.children.length <= 1) {
                noSelectedMessage.style.display = 'block';
            } else {
                noSelectedMessage.style.display = 'none';
            }
        }
        
        // Initialize the no selected message
        updateNoSelectedMessage();
    });
</script>
@endpush
@endsection 