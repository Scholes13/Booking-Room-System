@extends('superadmin.layout')

@section('title', 'Activity Types')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Header Section with Responsive Layout -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Activity Types</h1>
            <p class="text-gray-500 mt-1">View and manage all activity types</p>
        </div>
        <button id="btnAddActivityType" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-[#24448c] text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#1c3670] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Activity Type
        </button>
    </div>

    <!-- Stats Cards - Responsive Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Total Activity Types Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 flex justify-center items-center rounded-full bg-[#24448c] bg-opacity-10 p-3">
                    <svg class="w-6 h-6 text-[#24448c]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 font-medium">Total Activity Types</h2>
                    <p class="text-2xl font-bold text-dark">{{ count($activityTypes) }}</p>
                </div>
            </div>
        </div>

        <!-- Active Activity Types Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 flex justify-center items-center rounded-full bg-green-100 p-3">
                    <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 font-medium">Active Types</h2>
                    <p class="text-2xl font-bold text-dark">{{ $activityTypes->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Inactive Activity Types Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 flex justify-center items-center rounded-full bg-red-100 p-3">
                    <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 font-medium">Inactive Types</h2>
                    <p class="text-2xl font-bold text-dark">{{ $activityTypes->where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden bg-white rounded-lg p-6 shadow-sm">
        <div class="flex flex-col items-center justify-center py-4">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-[#24448c]"></div>
            <p class="mt-2 text-gray-600">Loading data...</p>
        </div>
    </div>
    
    <!-- Activity Types Table Container -->
    <div class="flex flex-col gap-6 bg-white rounded-lg p-6 shadow-sm" id="activityTypesTableContainer">
        <div class="flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Activity Type List</h2>
            <div class="relative w-64">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" id="searchActivityTypes" class="block w-full p-2.5 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" placeholder="Search activity types...">
            </div>
        </div>
        
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <!-- Desktop and larger tablet version (hidden on smaller screens) -->
            @if(count($activityTypes) > 0)
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">No</th>
                        <th scope="col" class="px-5 py-3.5">Name</th>
                        <th scope="col" class="px-5 py-3.5">Description</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
                <tbody id="activityTypesTableBody">
                    @foreach($activityTypes as $index => $activityType)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 activity-type-row">
                        <td class="px-5 py-4">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $activityType->name }}</td>
                        <td class="px-5 py-4 text-gray-500">{{ $activityType->description ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full 
                                @if($activityType->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ $activityType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                    </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" class="edit-activity-type px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center"
                                    data-id="{{ $activityType->id }}" 
                                    data-name="{{ $activityType->name }}" 
                                    data-description="{{ $activityType->description }}" 
                                    data-is-active="{{ $activityType->is_active }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" class="delete-activity-type px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" data-id="{{ $activityType->id }}" data-name="{{ $activityType->name }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
            
            <!-- Mobile Card View (displayed only on mobile screens) -->
            <div class="md:hidden divide-y divide-gray-200">
                @foreach($activityTypes as $index => $activityType)
                <div class="p-4 bg-white hover:bg-gray-50 activity-type-card">
                    <div class="flex flex-col gap-3">
                        <!-- Status and type number -->
                        <div class="flex justify-between items-center flex-wrap gap-2">
                            <span class="text-xs text-gray-500">Type #{{ $index + 1 }}</span>
                            <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full 
                                @if($activityType->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ $activityType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <!-- Name -->
                        <div class="font-medium text-gray-900 text-lg break-words">{{ $activityType->name }}</div>
                        
                        <!-- Description -->
                        @if($activityType->description)
                        <div class="text-sm text-gray-500 break-words">{{ $activityType->description }}</div>
                        @endif
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-2 mt-2">
                            <button type="button" class="edit-activity-type px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center"
                                data-id="{{ $activityType->id }}" 
                                data-name="{{ $activityType->name }}" 
                                data-description="{{ $activityType->description }}" 
                                data-is-active="{{ $activityType->is_active }}">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                            <button type="button" class="delete-activity-type px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" data-id="{{ $activityType->id }}" data-name="{{ $activityType->name }}">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                    <p class="text-gray-500 text-sm">Try adding a new activity type</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Activity Type Modal -->
<div id="addActivityTypeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <h3 class="text-xl font-bold text-center mb-4">Add Activity Type</h3>
        
        <form action="{{ route('superadmin.activity-types.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Name Field -->
            <div>
                <label for="add-name" class="block text-sm font-medium text-gray-700 mb-1">Activity Type Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="add-name" 
                    name="name" 
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#24448c] focus:border-[#24448c] text-gray-900"
                    placeholder="Enter activity type name"
                    required
                >
            </div>
            
            <!-- Description Field -->
            <div>
                <label for="add-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea 
                    id="add-description" 
                    name="description" 
                    rows="4" 
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#24448c] focus:border-[#24448c] text-gray-900"
                    placeholder="Enter description (optional)"
                >{{ old('description') }}</textarea>
            </div>
            
            <!-- Active Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1" class="text-[#24448c] border-gray-300 focus:ring-[#24448c]" checked>
                        <span class="ml-2">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0" class="text-[#24448c] border-gray-300 focus:ring-[#24448c]" {{ old('is_active') === '0' ? 'checked' : '' }}>
                        <span class="ml-2">Inactive</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelAddActivityType" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors focus:outline-none focus:ring-2 focus:ring-[#24448c]">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Activity Type Modal -->
<div id="editActivityTypeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <h3 class="text-xl font-bold text-center mb-4">Edit Activity Type</h3>
        
        <form id="editActivityTypeForm" action="" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Name Field -->
            <div>
                <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Activity Type Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="edit-name" 
                    name="name" 
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#24448c] focus:border-[#24448c] text-gray-900"
                    placeholder="Enter activity type name"
                    required
                >
            </div>
            
            <!-- Description Field -->
            <div>
                <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea 
                    id="edit-description" 
                    name="description" 
                    rows="4" 
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#24448c] focus:border-[#24448c] text-gray-900"
                    placeholder="Enter description (optional)"
                ></textarea>
            </div>
            
            <!-- Active Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" id="edit-is-active-1" name="is_active" value="1" class="text-[#24448c] border-gray-300 focus:ring-[#24448c]">
                        <span class="ml-2">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" id="edit-is-active-0" name="is_active" value="0" class="text-[#24448c] border-gray-300 focus:ring-[#24448c]">
                        <span class="ml-2">Inactive</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelEditActivityType" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors focus:outline-none focus:ring-2 focus:ring-[#24448c]">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="flex justify-center mb-4 text-red-500">
            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-center mb-2">Confirm Deletion</h3>
        <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this activity type? This action cannot be undone.</p>
        <div class="flex justify-center gap-3">
            <button id="cancelDelete" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                Cancel
            </button>
            <form id="deleteForm" method="POST" action="" class="flex-1">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete-activity-type-id" name="id" value="">
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Activity Type Modal
        const btnAddActivityType = document.getElementById('btnAddActivityType');
        const addActivityTypeModal = document.getElementById('addActivityTypeModal');
        const cancelAddActivityType = document.getElementById('cancelAddActivityType');
        
        btnAddActivityType.addEventListener('click', function() {
            addActivityTypeModal.classList.remove('hidden');
            addActivityTypeModal.classList.add('flex');
        });
        
        cancelAddActivityType.addEventListener('click', function() {
            addActivityTypeModal.classList.add('hidden');
            addActivityTypeModal.classList.remove('flex');
        });
        
        // Edit Activity Type Modal
        const editButtons = document.querySelectorAll('.edit-activity-type');
        const editActivityTypeModal = document.getElementById('editActivityTypeModal');
        const cancelEditActivityType = document.getElementById('cancelEditActivityType');
        const editActivityTypeForm = document.getElementById('editActivityTypeForm');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description') || '';
                const isActive = this.getAttribute('data-is-active');
                
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-description').value = description;
                
                if (isActive === '1') {
                    document.getElementById('edit-is-active-1').checked = true;
                } else {
                    document.getElementById('edit-is-active-0').checked = true;
                }
                
                editActivityTypeForm.action = `{{ route('superadmin.activity-types.update', '') }}/${id}`;
                editActivityTypeModal.classList.remove('hidden');
                editActivityTypeModal.classList.add('flex');
            });
        });
        
        cancelEditActivityType.addEventListener('click', function() {
            editActivityTypeModal.classList.add('hidden');
            editActivityTypeModal.classList.remove('flex');
        });

        // Delete functionality
        const deleteButtons = document.querySelectorAll('.delete-activity-type');
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const cancelDelete = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const typeId = this.getAttribute('data-id');
                document.getElementById('delete-activity-type-id').value = typeId;
                confirmDeleteModal.classList.remove('hidden');
                confirmDeleteModal.classList.add('flex');
                
                // Set the form action URL directly with the correct route
                deleteForm.action = '{{ url("/superadmin/activity-types") }}/' + typeId;
            });
        });
        
        cancelDelete.addEventListener('click', function() {
            confirmDeleteModal.classList.add('hidden');
            confirmDeleteModal.classList.remove('flex');
        });

        // Search functionality
        const searchInput = document.getElementById('searchActivityTypes');
        if (searchInput) {
            const activityTypeRows = document.querySelectorAll('.activity-type-row');
            const activityTypeCards = document.querySelectorAll('.activity-type-card');
            
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                let visibleCount = 0;
                
                // Search in desktop view
                activityTypeRows.forEach(row => {
                    const typeName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const typeDesc = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    
                    if (typeName.includes(searchText) || typeDesc.includes(searchText)) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });
                
                // Search in mobile view
                activityTypeCards.forEach(card => {
                    const typeName = card.querySelector('.text-lg').textContent.toLowerCase();
                    const typeDesc = card.querySelector('.text-sm')?.textContent.toLowerCase() || '';
                    
                    if (typeName.includes(searchText) || typeDesc.includes(searchText)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
                
                // Show/hide "No Data Found" message
                const noDataMessage = document.querySelector('#activityTypesTableBody + div');
                if (visibleCount === 0 && activityTypeRows.length > 0) {
                    if (noDataMessage) {
                        noDataMessage.classList.remove('hidden');
                    } else {
                        const tableContainer = document.getElementById('activityTypesTableContainer');
                        if (tableContainer) {
                            const noDataHtml = `
                            <div class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No Results Found</h3>
                                    <p class="text-gray-500 text-sm">Try with different search term</p>
                                </div>
                            </div>`;
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = noDataHtml;
                            tableContainer.appendChild(tempDiv.firstElementChild);
                        }
                    }
                } else if (noDataMessage) {
                    noDataMessage.classList.add('hidden');
                }
            });
        }

        // Function to show loading indicator
        function showLoading() {
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('activityTypesTableContainer').classList.add('opacity-50');
        }
        
        // Function to hide loading indicator
        function hideLoading() {
            document.getElementById('loadingIndicator').classList.add('hidden');
            document.getElementById('activityTypesTableContainer').classList.remove('opacity-50');
        }

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 1s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 1000);
            }, 5000);
        });
    });
</script>
@endpush
