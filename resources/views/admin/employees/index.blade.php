@php
    $layout = isset($isSuperAdmin) && $isSuperAdmin ? 'superadmin.layout' : 'admin.layout';
    $routePrefix = isset($isSuperAdmin) && $isSuperAdmin ? 'superadmin' : 'admin';
@endphp

@extends($layout)

@section('title', 'Karyawan')

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
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Karyawan</h1>
            <p class="text-gray-500 mt-1">Lihat dan kelola semua karyawan</p>
        </div>
        <button id="btnAddEmployee" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-primary text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-colors shadow-sm w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Karyawan
        </button>
    </div>

    <!-- Stats Cards - Responsive Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Total Karyawan Card -->
        <div class="bg-white rounded-lg p-4 md:p-5 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 flex justify-center items-center rounded-full bg-primary bg-opacity-10 p-2 md:p-3">
                    <svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm text-gray-500 font-medium">Total Karyawan</h2>
                    <p class="text-xl md:text-2xl font-bold text-dark">{{ $employees->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Karyawan Laki-laki Card -->
        <div class="bg-white rounded-lg p-4 md:p-5 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 flex justify-center items-center rounded-full bg-blue-100 p-2 md:p-3">
                    <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm text-gray-500 font-medium">Laki-laki</h2>
                    <p class="text-xl md:text-2xl font-bold text-dark">{{ $maleCount }}</p>
                </div>
            </div>
        </div>

        <!-- Karyawan Perempuan Card -->
        <div class="bg-white rounded-lg p-4 md:p-5 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 flex justify-center items-center rounded-full bg-pink-100 p-2 md:p-3">
                    <svg class="w-6 h-6 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm text-gray-500 font-medium">Perempuan</h2>
                    <p class="text-xl md:text-2xl font-bold text-dark">{{ $femaleCount }}</p>
                </div>
            </div>
        </div>

        <!-- Employee Data Export Card -->
        <div class="bg-white rounded-lg p-4 md:p-5 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-300">
            <div class="h-full flex flex-col">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-shrink-0 flex justify-center items-center rounded-full bg-green-100 p-2 md:p-3">
                        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm text-gray-500 font-medium">Data Karyawan</h2>
                        <p class="text-base font-medium text-dark">Export Excel</p>
                    </div>
                </div>
                <a href="{{ route($routePrefix.'.employees.export') }}" class="mt-auto py-2 px-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm text-sm font-medium flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section - Improved Mobile Layout -->
    <div class="flex flex-col gap-4 bg-white rounded-lg p-4 md:p-5 shadow-sm">
        <h2 class="text-base font-semibold text-gray-700">Filter Karyawan</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Search Input with Responsive Design -->
            <div class="relative w-full">
                <form action="{{ route($routePrefix.'.employees') }}" method="GET">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="searchInput" name="search" value="{{ request('search') }}" class="block w-full p-3 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" placeholder="Cari nama, jabatan, nomor HP, atau email...">
                </form>
            </div>
            
            <!-- Department Filter -->
            <div>
                <select id="departmentFilter" name="department_id" class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm appearance-none bg-no-repeat bg-[right_0.5rem_center] bg-[length:1em]" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236B7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27M6 8l4 4 4-4%27/%3e%3c/svg%3e')">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Gender Filter -->
            <div>
                <select id="genderFilter" name="gender" class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm appearance-none bg-no-repeat bg-[right_0.5rem_center] bg-[length:1em]" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236B7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27M6 8l4 4 4-4%27/%3e%3c/svg%3e')">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden bg-white rounded-lg p-6 shadow-sm">
        <div class="flex flex-col items-center justify-center py-4">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-primary"></div>
            <p class="mt-2 text-gray-600">Loading data...</p>
        </div>
    </div>
    
    <!-- Employees Table Container -->
    <div class="flex flex-col gap-6 bg-white rounded-lg p-4 md:p-6 shadow-sm" id="employeesTableContainer">
        @include('admin.employees.partials.table')
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
        <h3 class="text-xl font-bold text-center mb-2">Konfirmasi Penghapusan</h3>
        <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus karyawan ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-center gap-3">
            <button id="cancelDelete" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                Batal
            </button>
            <form id="deleteForm" method="POST" action="" class="flex-1">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete-employee-id" name="id" value="">
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-3xl w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Edit Karyawan</h3>
            <button type="button" id="closeEditModal" class="text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form action="{{ route($routePrefix.'.employees.update', '') }}" id="editEmployeeForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id">
            <input type="hidden" id="edit_employee_id" name="employee_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="edit_name" 
                        name="name" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                </div>

                <div>
                    <label for="edit_department_id" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                    <select 
                        id="edit_department_id" 
                        name="department_id" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm"
                        required
                    >
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_position" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input 
                        type="text" 
                        id="edit_position" 
                        name="position" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan jabatan"
                        required
                    >
                </div>

                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input 
                        type="email" 
                        id="edit_email" 
                        name="email" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan alamat email"
                    >
                </div>

                <div>
                    <label for="edit_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input 
                        type="text" 
                        id="edit_phone" 
                        name="phone" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan nomor telepon"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <div class="flex gap-4 p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center">
                            <input type="radio" id="edit_male" name="gender" value="L" class="mr-2" required>
                            <label for="edit_male">Laki-laki</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="edit_female" name="gender" value="P" class="mr-2">
                            <label for="edit_female">Perempuan</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelEditEmployee" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg font-medium hover:bg-opacity-90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-3xl w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Tambah Karyawan</h3>
            <button type="button" id="closeAddModal" class="text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form action="{{ route($routePrefix.'.employees.store') }}" method="POST">
            @csrf
            <input type="hidden" id="employee_id" name="employee_id" value="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                    <select 
                        id="department_id" 
                        name="department_id" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm"
                        required
                    >
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input 
                        type="text" 
                        id="position" 
                        name="position" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan jabatan"
                        required
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan alamat email"
                    >
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input 
                        type="text" 
                        id="phone" 
                        name="phone" 
                        class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-primary focus:border-primary transition-all shadow-sm" 
                        placeholder="Masukkan nomor telepon"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <div class="flex gap-4 p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center">
                            <input type="radio" id="male" name="gender" value="L" class="mr-2" required>
                            <label for="male">Laki-laki</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="female" name="gender" value="P" class="mr-2">
                            <label for="female">Perempuan</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelAddEmployee" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg font-medium hover:bg-opacity-90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
                    Simpan
                </button>
            </div>
        </form>
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
                
                // Set action URL with employee ID - fixed URL construction
                document.getElementById('deleteForm').action = "{{ route($routePrefix.'.employees.delete', '') }}/" + employeeId;
            });
        });
    }
    
    // Function to initialize edit buttons
    function initializeEditButtons() {
        document.querySelectorAll('.edit-employee').forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const employeeIdValue = this.getAttribute('data-employee-id');
                const departmentId = this.getAttribute('data-department');
                const position = this.getAttribute('data-position');
                const gender = this.getAttribute('data-gender');
                const phone = this.getAttribute('data-phone');
                const email = this.getAttribute('data-email');
                
                // Set form values
                document.getElementById('edit_id').value = employeeId;
                document.getElementById('edit_employee_id').value = employeeIdValue || '';
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_department_id').value = departmentId;
                document.getElementById('edit_position').value = position || '';
                document.getElementById('edit_phone').value = phone || '';
                document.getElementById('edit_email').value = email || '';
                
                // Set gender radio button
                if (gender === 'L') {
                    document.getElementById('edit_male').checked = true;
                } else if (gender === 'P') {
                    document.getElementById('edit_female').checked = true;
                }
                
                // Set the form action URL using route
                const formAction = document.getElementById('editEmployeeForm').getAttribute('action');
                document.getElementById('editEmployeeForm').action = formAction.replace('', '/' + employeeId);
                
                // Show modal
                document.getElementById('editEmployeeModal').classList.remove('hidden');
                document.getElementById('editEmployeeModal').classList.add('flex');
            });
        });
    }

    // Function to show loading indicator
    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('hidden');
        document.getElementById('employeesTableContainer').classList.add('opacity-50');
    }
    
    // Function to hide loading indicator
    function hideLoading() {
        document.getElementById('loadingIndicator').classList.add('hidden');
        document.getElementById('employeesTableContainer').classList.remove('opacity-50');
    }

    // Initialize when the page first loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeDeleteButtons();
        initializeEditButtons();
        
        // Add Employee Modal
        const addEmployeeModal = document.getElementById('addEmployeeModal');
        const btnAddEmployee = document.getElementById('btnAddEmployee');
        const cancelAddEmployee = document.getElementById('cancelAddEmployee');
        const closeAddModal = document.getElementById('closeAddModal');
        
        if (btnAddEmployee) {
            btnAddEmployee.addEventListener('click', function() {
                addEmployeeModal.classList.remove('hidden');
                addEmployeeModal.classList.add('flex');
            });
        }
        
        if (cancelAddEmployee) {
            cancelAddEmployee.addEventListener('click', function() {
                addEmployeeModal.classList.add('hidden');
                addEmployeeModal.classList.remove('flex');
            });
        }
        
        if (closeAddModal) {
            closeAddModal.addEventListener('click', function() {
                addEmployeeModal.classList.add('hidden');
                addEmployeeModal.classList.remove('flex');
            });
        }
        
        // Edit Employee Modal
        const editEmployeeModal = document.getElementById('editEmployeeModal');
        const cancelEditEmployee = document.getElementById('cancelEditEmployee');
        const closeEditModal = document.getElementById('closeEditModal');
        
        if (cancelEditEmployee) {
            cancelEditEmployee.addEventListener('click', function() {
                editEmployeeModal.classList.add('hidden');
                editEmployeeModal.classList.remove('flex');
            });
        }
        
        if (closeEditModal) {
            closeEditModal.addEventListener('click', function() {
                editEmployeeModal.classList.add('hidden');
                editEmployeeModal.classList.remove('flex');
            });
        }
        
        // Close modal button
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmDeleteModal').classList.add('hidden');
            document.getElementById('confirmDeleteModal').classList.remove('flex');
        });

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

        // Search input event
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                // For better UX, we'll filter when typing stops
                if (searchInput.value.length === 0 || searchInput.value.length >= 2) {
                    updateEmployeesList();
                }
            }, 500));
            
            // Prevent form submission on Enter to use our AJAX handler instead
            searchInput.closest('form').addEventListener('submit', function(e) {
                e.preventDefault();
                updateEmployeesList();
            });
        }

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
        showLoading();
        
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
            document.getElementById('employeesTableContainer').innerHTML = html;
            hideLoading();
            // Reinitialize buttons after content update
            initializeDeleteButtons();
            initializeEditButtons();
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('employeesTableContainer').innerHTML = `<div class="text-center py-8 text-red-600">Error loading data: ${error.message}</div>`;
            hideLoading();
        });
    }
</script>
@endpush