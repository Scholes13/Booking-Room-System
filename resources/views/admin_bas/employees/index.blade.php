@extends('admin_bas.layout')

@section('title', 'Karyawan')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Karyawan</h1>
        <a href="{{ route('bas.employees.create') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
            </svg>
            Tambah Karyawan
        </a>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Karyawan Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-primary shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-primary bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-primary" viewBox="0 0 256 256">
                    <path d="M234.38,210a123.36,123.36,0,0,0-60.78-53.23,76,76,0,1,0-91.2,0A123.36,123.36,0,0,0,21.62,210a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.53-52s70.69,19.43,89.53,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Total Karyawan</h2>
                <p class="text-2xl font-bold text-dark">{{ $employees->total() }}</p>
            </div>
        </div>

        <!-- Karyawan Laki-laki Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-blue-500 shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-blue-500 bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-blue-500" viewBox="0 0 256 256">
                    <path d="M208,31.31a8,8,0,0,0-8.63,1.72L166.45,68.42A80,80,0,1,0,68.42,166.45l-35.39,33.45a8,8,0,0,0,5.47,13.79,8.24,8.24,0,0,0,5.5-2.17l35.38-33.45a80,80,0,0,0,108,0l35.39-33.45a8,8,0,0,0-5.47-13.79,8.24,8.24,0,0,0-5.47,2.17l-35.42,33.48a64,64,0,1,1,0-90.51L208,42.83A8,8,0,0,0,208,31.31Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Laki-laki</h2>
                <p class="text-2xl font-bold text-dark">{{ $maleCount }}</p>
            </div>
        </div>

        <!-- Karyawan Perempuan Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-pink-500 shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-pink-500 bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-pink-500" viewBox="0 0 256 256">
                    <path d="M128,104a40,40,0,1,0-40-40A40,40,0,0,0,128,104Zm0-64a24,24,0,1,1-24,24A24,24,0,0,1,128,40Zm48,72a8,8,0,0,0-8,8v56H136V144a8,8,0,0,0-16,0v32H88V120a8,8,0,0,0-16,0v72a8,8,0,0,0,8,8h48v16a8,8,0,0,0,16,0V200h48a8,8,0,0,0,8-8V120A8,8,0,0,0,176,112Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Perempuan</h2>
                <p class="text-2xl font-bold text-dark">{{ $femaleCount }}</p>
            </div>
        </div>

        <!-- Export Button in Card -->
        <div class="bg-white rounded-lg p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex justify-center items-center rounded-full bg-green-500 bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-green-500" viewBox="0 0 256 256">
                        <path d="M216,88H168V40a16,16,0,0,0-16-16H104A16,16,0,0,0,88,40V88H40a16,16,0,0,0-16,16V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V104A16,16,0,0,0,216,88Zm0,16v32H168V104ZM104,40h48V136H104Zm-64,64h48v32H40Zm0,48h48v48H40Zm176,48H104V152H216Z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 font-medium">Data Karyawan</h2>
                    <p class="text-lg font-medium text-dark">Export Excel</p>
                </div>
            </div>
            <a href="{{ route('bas.employees.export') }}" class="bg-green-500 text-white rounded-full py-2 px-4 text-sm hover:bg-green-600 transition-colors">
                Export
            </a>
        </div>
    </div>

    <div class="flex flex-col gap-4">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label for="searchInput" class="block text-sm font-medium text-dark">Cari Nama/Jabatan/HP/Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" id="searchInput" name="search" value="{{ request('search') }}" class="block w-full p-3 ps-10 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary" placeholder="Cari nama, jabatan, nomor HP, atau email...">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="departmentFilter" class="block text-sm font-medium text-dark">Departemen</label>
                    <select id="departmentFilter" name="department_id" class="w-full p-3 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label for="genderFilter" class="block text-sm font-medium text-dark">Jenis Kelamin</label>
                    <select id="genderFilter" name="gender" class="w-full p-3 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary">
                        <option value="">Semua</option>
                        <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Employees Table -->
        <div class="bg-white rounded-lg p-4 shadow-sm" id="employeesTableContainer">
            @include('admin_bas.employees.partials.table')
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 flex">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Konfirmasi Penghapusan</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus karyawan ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Batal</button>
            <form id="deleteForm" method="POST" action="{{ route('bas.employees.delete', '') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete-employee-id" name="id" value="">
                <button type="submit" class="px-4 py-2 bg-danger text-white rounded-lg">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Debounce function to limit how often a function can be called
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Function to initialize delete buttons
    function initializeDeleteButtons() {
        document.querySelectorAll('.delete-employee').forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-id');
                document.getElementById('delete-employee-id').value = employeeId;
                document.getElementById('confirmDeleteModal').classList.remove('hidden');
                document.getElementById('confirmDeleteModal').classList.add('flex');
                
                // Set action URL dengan employee ID
                const formAction = document.getElementById('deleteForm').getAttribute('action');
                document.getElementById('deleteForm').action = formAction.replace('', '/' + employeeId);
            });
        });
    }

    // Initialize when the page first loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeDeleteButtons();
        
        // Close modal button
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmDeleteModal').classList.add('hidden');
            document.getElementById('confirmDeleteModal').classList.remove('flex');
        });

        // Search input event
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', debounce(updateEmployeesList, 500));

        // Department and gender select events
        const departmentSelect = document.getElementById('departmentFilter');
        if (departmentSelect) {
            departmentSelect.addEventListener('change', updateEmployeesList);
        }

        const genderSelect = document.getElementById('genderFilter');
        if (genderSelect) {
            genderSelect.addEventListener('change', updateEmployeesList);
        }
    });

    // Function to update employees list via AJAX
    function updateEmployeesList() {
        const searchValue = document.getElementById('searchInput').value;
        const departmentValue = document.getElementById('departmentFilter')?.value || '';
        const genderValue = document.getElementById('genderFilter')?.value || '';
        
        // Show loading indicator
        const tableContainer = document.getElementById('employeesTableContainer');
        tableContainer.innerHTML = '<div class="text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div><p class="mt-2 text-gray-500">Loading...</p></div>';
        
        // Build query parameters
        const params = new URLSearchParams();
        if (searchValue) params.append('search', searchValue);
        if (departmentValue) params.append('department_id', departmentValue);
        if (genderValue) params.append('gender', genderValue);
        
        // Update the URL without reloading the page
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({ path: newUrl }, '', newUrl);
        
        // Fetch updated data
        fetch(`${window.location.pathname}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            tableContainer.innerHTML = html;
            // Reinitialize delete buttons after content update
            initializeDeleteButtons();
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            tableContainer.innerHTML = `<div class="text-center py-8 text-danger">Error loading data: ${error.message}</div>`;
        });
    }
</script>
@endpush 