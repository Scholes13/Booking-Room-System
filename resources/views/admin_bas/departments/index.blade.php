@extends('admin_bas.layout')

@section('title', 'Departemen')

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
        <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Departemen</h1>
        <button id="btnTambahDepartemen" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-bas text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
            </svg>
            Tambah Departemen
        </button>
    </div>

    <div class="flex gap-6">
        <!-- Total Departemen Card -->
        <div class="bg-white rounded-lg p-6 flex items-center gap-4 min-w-64 border-l-4 border-bas shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-bas bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-bas" viewBox="0 0 256 256">
                    <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm12-88a12,12,0,1,1-12-12A12,12,0,0,1,140,128Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,184,128Zm-88,0a12,12,0,1,1-12-12A12,12,0,0,1,96,128Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Total Departemen</h2>
                <p class="text-2xl font-bold text-dark">{{ $departments->count() }}</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-6 bg-white rounded-lg p-6">
        <!-- Departments Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-sm text-dark">
                    <tr class="border-b border-border">
                        <th scope="col" class="px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3">Nama Departemen</th>
                        <th scope="col" class="px-4 py-3">Jumlah Karyawan</th>
                        <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="departmentsTableBody">
                    @forelse($departments as $index => $department)
                    <tr class="border-b border-border department-row">
                        <td class="px-4 py-4">{{ $index + 1 }}</td>
                        <td class="px-4 py-4 font-medium">{{ $department->name }}</td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 bg-gray-100 text-xs rounded-full">{{ $department->employees_count }}</span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex flex-col items-end space-y-1">
                                <a href="{{ route('bas.departments.edit', $department->id) }}" class="text-bas font-medium text-sm hover:underline">Edit</a>
                                <button type="button" class="delete-department text-danger font-medium text-sm hover:underline" data-id="{{ $department->id }}">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="border-b border-border">
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            Belum ada departemen. Silahkan tambahkan departemen baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div id="addDepartmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Tambah Departemen</h3>
        
        <form action="{{ route('bas.departments.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-dark mb-1">Nama Departemen</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="w-full p-3 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-bas focus:border-bas" 
                    placeholder="Masukkan nama departemen"
                    required
                >
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="cancelAddDepartment" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-bas text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Konfirmasi Penghapusan</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus departemen ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Batal</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-danger text-white rounded-lg">Hapus</button>
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
            
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                
                departmentRows.forEach(row => {
                    const departmentName = row.children[1].textContent.toLowerCase();
                    
                    if (departmentName.includes(searchText)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
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
                deleteForm.action = `{{ route('bas.departments.delete', '') }}/${departmentId}`;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });
        
        cancelDelete.addEventListener('click', function() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });
    });
</script>
@endpush 