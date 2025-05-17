@extends('admin_bas.layout')

@section('title', 'Departments')

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

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Departments</h1>
            <p class="text-gray-500 mt-1">View and manage all departments</p>
        </div>
        <button id="btnTambahDepartemen" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-[#24448c] text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#1c3670] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Department
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Departments Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-4">
                <div class="flex justify-center items-center rounded-full bg-[#24448c] bg-opacity-10 p-3">
                    <svg class="w-6 h-6 text-[#24448c]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 font-medium">Total Departments</h2>
                    <p class="text-2xl font-bold text-dark">{{ $departments->count() }}</p>
                </div>
            </div>
        </div>
        
        <!-- Employees Count Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-4">
                <div class="flex justify-center items-center rounded-full bg-blue-100 p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 font-medium">Total Employees</h2>
                    <p class="text-2xl font-bold text-dark">{{ $departments->sum('employees_count') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Average Employees per Department -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-4">
                <div class="flex justify-center items-center rounded-full bg-green-100 p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 font-medium">Avg. Employees/Dept</h2>
                    <p class="text-2xl font-bold text-dark">
                        {{ $departments->count() > 0 ? round($departments->sum('employees_count') / $departments->count(), 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="flex flex-col gap-6 bg-white rounded-lg p-6 shadow-sm">
        <div class="flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Department List</h2>
            <div class="relative w-64">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" id="searchDepartments" class="block w-full p-2.5 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" placeholder="Search departments...">
            </div>
        </div>
        
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">No</th>
                        <th scope="col" class="px-5 py-3.5">Department Name</th>
                        <th scope="col" class="px-5 py-3.5">Employees</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="departmentsTableBody">
                    @forelse($departments as $index => $department)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 department-row">
                        <td class="px-5 py-4">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 font-medium">{{ $department->name }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">{{ $department->employees_count }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('bas.departments.edit', $department->id) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <button type="button" class="delete-department px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" data-id="{{ $department->id }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noDataRow">
                        <td colspan="4" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                                <p class="text-gray-500 text-sm">Try adding a new department</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div id="addDepartmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <h3 class="text-xl font-bold text-center mb-4">Add Department</h3>
        
        <form action="{{ route('bas.departments.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Department Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" 
                    placeholder="Enter department name"
                    required
                >
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelAddDepartment" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors focus:outline-none focus:ring-2 focus:ring-[#24448c]">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="flex justify-center mb-4 text-red-500">
            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-center mb-2">Confirm Deletion</h3>
        <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this department? This action cannot be undone.</p>
        <div class="flex justify-center gap-3">
            <button id="cancelDelete" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                Cancel
            </button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
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
        // Search functionality
        const searchInput = document.getElementById('searchDepartments');
        if (searchInput) {
            const departmentRows = document.querySelectorAll('.department-row');
            const noDataRow = document.getElementById('noDataRow');
            
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                let visibleCount = 0;
                
                departmentRows.forEach(row => {
                    const departmentName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    
                    if (departmentName.includes(searchText)) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });
                
                // Show/hide "No Data Found" message
                if (visibleCount === 0 && departmentRows.length > 0) {
                    if (noDataRow) {
                        noDataRow.classList.remove('hidden');
                    } else {
                        const tbody = document.getElementById('departmentsTableBody');
                        const noDataHtml = `
                        <tr id="noDataRow">
                            <td colspan="4" class="px-5 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                                    <p class="text-gray-500 text-sm">Try changing your search criteria</p>
                                </div>
                            </td>
                        </tr>`;
                        tbody.insertAdjacentHTML('beforeend', noDataHtml);
                    }
                } else if (noDataRow) {
                    noDataRow.classList.add('hidden');
                }
            });
        }

        // Add Department Modal
        const btnTambahDepartemen = document.getElementById('btnTambahDepartemen');
        const addDepartmentModal = document.getElementById('addDepartmentModal');
        const cancelAddDepartment = document.getElementById('cancelAddDepartment');
        
        btnTambahDepartemen.addEventListener('click', function() {
            addDepartmentModal.classList.remove('hidden');
            addDepartmentModal.classList.add('flex');
        });
        
        cancelAddDepartment.addEventListener('click', function() {
            addDepartmentModal.classList.add('hidden');
            addDepartmentModal.classList.remove('flex');
        });

        // Delete department functionality
        const deleteButtons = document.querySelectorAll('.delete-department');
        const deleteModal = document.getElementById('deleteModal');
        const cancelDelete = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const departmentId = this.dataset.id;
                console.log('Delete clicked for department ID:', departmentId);
                
                deleteForm.action = `{{ route('bas.departments.delete', '') }}/${departmentId}`;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });
        
        cancelDelete.addEventListener('click', function() {
            console.log('Delete cancelled');
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });
    });
</script>
@endpush 