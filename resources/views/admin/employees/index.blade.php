@extends('admin.layout')

@section('title', 'Kelola Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Kelola Karyawan</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.employees.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus"></i>
                <span>Tambah Karyawan</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Karyawan Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Karyawan</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalEmployees }}</h3>
                </div>
            </div>
        </div>

        <!-- Karyawan Laki-laki Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-male text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Karyawan Laki-laki</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $maleEmployees }}</h3>
                </div>
            </div>
        </div>

        <!-- Karyawan Perempuan Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-female text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Karyawan Perempuan</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $femaleEmployees }}</h3>
                </div>
            </div>
        </div>

        <!-- Karyawan Aktif Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Karyawan Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalEmployees }}</h3>
                </div>
            </div>
        </div>
    </div>

     <!-- Search and Export Section -->
     <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search and Filter -->
            <div class="flex flex-col md:flex-row gap-4 flex-grow">
                <div class="flex-grow">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan</label>
                    <div class="relative">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Cari nama atau jabatan..."
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Departemen</label>
                    <select id="departmentFilter" 
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Gender</label>
                    <select id="genderFilter" 
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Gender</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Export Button -->
            <div class="flex items-end">
                <button type="button" 
                        onclick="exportData()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-export"></i>
                    <span>Export Data</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Daftar Karyawan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->gender_label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->department->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->position ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" 
                                   class="inline-flex items-center gap-2 px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('admin.employees.delete', $employee->id) }}" 
                                      method="POST" 
                                      class="inline-block delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada data karyawan</p>
                                <p class="text-sm text-gray-400">Silakan tambahkan karyawan baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const genderFilter = document.getElementById('genderFilter');
    let timeoutId;

    // Function untuk search dan filter
    function filterData() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            const searchValue = searchInput.value;
            const departmentValue = departmentFilter.value;
            const genderValue = genderFilter.value;

            window.location.href = `{{ route('admin.employees') }}?search=${searchValue}&department=${departmentValue}&gender=${genderValue}`;
        }, 500);
    }

    // Event listeners untuk filter
    searchInput.addEventListener('input', filterData);
    departmentFilter.addEventListener('change', filterData);
    genderFilter.addEventListener('change', filterData);

    // Set nilai filter dari URL jika ada
    const urlParams = new URLSearchParams(window.location.search);
    searchInput.value = urlParams.get('search') || '';
    departmentFilter.value = urlParams.get('department') || '';
    genderFilter.value = urlParams.get('gender') || '';

    // Sweet Alert untuk konfirmasi delete
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data karyawan akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

// Function untuk export
function exportData() {
    const urlParams = new URLSearchParams(window.location.search);
    window.location.href = `{{ route('admin.employees.export') }}?${urlParams.toString()}`;
}
</script>
@endpush