@extends('layouts.app')

@section('title', 'Tambah Kegiatan')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg mx-auto bg-white/20 backdrop-blur-lg shadow-2xl rounded-lg p-8">
        <h2 class="text-3xl font-semibold text-center text-gray-200 mb-6">
            Tambah Kegiatan
        </h2>

        <!-- Error Validation -->
        @if($errors->any())
        <div class="bg-red-500/20 text-red-400 p-3 rounded-md mb-4">
            <ul>
                @foreach($errors->all() as $error)
                <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-500/20 text-green-400 p-3 rounded-md mb-4">
            ✅ {{ session('success') }}
        </div>
        @endif

        <!-- Form -->
        <form id="activityForm" action="{{ route('activity.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Nama</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-user text-gray-400 mr-2"></i>
                    <select name="nama" id="employee_select" class="w-full bg-transparent border-none outline-none text-gray-900" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->name }}" data-department="{{ $employee->department->name }}">
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Departemen -->
            <div>
                <label for="department" class="block text-sm font-medium text-gray-300 mb-1">Departemen</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-building text-gray-400 mr-2"></i>
                    <select id="department" name="department" class="w-full bg-transparent border-none outline-none text-gray-900" required readonly>
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->name }}" {{ old('department') == $department->name ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal Jam Mulai -->
            <div>
                <label for="start_datetime" class="block text-sm font-medium text-gray-300 mb-1">Tanggal Jam Mulai</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                    <input type="text" id="start_datetime" name="start_datetime" placeholder="Pilih tanggal dan waktu mulai" class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400" required>
                </div>
            </div>

            <!-- Tanggal Jam Selesai -->
            <div>
                <label for="end_datetime" class="block text-sm font-medium text-gray-300 mb-1">Tanggal Jam Selesai</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                    <input type="text" id="end_datetime" name="end_datetime" placeholder="Pilih tanggal dan waktu selesai" class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400" required>
                </div>
            </div>

            <!-- Tipe Kegiatan -->
            <div>
                <label for="activity_type" class="block text-sm font-medium text-gray-300 mb-1">Tipe Kegiatan</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-list text-gray-400 mr-2"></i>
                    <select id="activity_type" name="activity_type" class="w-full bg-transparent border-none outline-none text-gray-900" required>
                        <option value="">Pilih Tipe Kegiatan</option>
                        <option value="Meeting" {{ old('activity_type') == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="Invititation" {{ old('activity_type') == 'Invititation' ? 'selected' : '' }}>Invititation</option>
                        <option value="Survey" {{ old('activity_type') == 'Survey' ? 'selected' : '' }}>Survey</option>
                    </select>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Deskripsi</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-comment-dots text-gray-400 mr-2"></i>
                    <textarea id="description" name="description" rows="3" placeholder="Masukkan deskripsi kegiatan" class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400" required>{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white p-3 rounded-md shadow-lg transform hover:scale-105 transition duration-300">
                <i class="fas fa-check-circle mr-2"></i> Submit Kegiatan
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Flatpickr Date Time Picker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<!-- Flatpickr Date Time Picker JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Flatpickr untuk input tanggal dan waktu
    flatpickr("#start_datetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
    flatpickr("#end_datetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });

    // Auto-populasi Departemen berdasarkan pilihan Nama
    const employeeSelect = document.getElementById('employee_select');
    const departmentSelect = document.getElementById('department');

    employeeSelect.addEventListener('change', function() {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        const department = selectedOption.getAttribute('data-department');
        if (department) {
            departmentSelect.value = department;
            departmentSelect.setAttribute('readonly', 'readonly');
        } else {
            departmentSelect.value = '';
            departmentSelect.removeAttribute('readonly');
        }
    });
});
</script>
@endpush
