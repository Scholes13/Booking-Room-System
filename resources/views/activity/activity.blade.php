@extends('layouts.app')

@section('title', 'Tambah Kegiatan')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <!-- Container utama dengan efek glass -->
    <div class="w-full max-w-lg mx-auto bg-white/10 backdrop-blur-lg shadow-2xl rounded-lg p-8">
        <h2 class="text-3xl font-semibold text-center text-white mb-6">
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

            <!-- NAMA (native <select>) -->
            <div>
                <label class="block text-sm font-medium text-gray-200 mb-1">Nama</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-user text-white mr-2"></i>
                    <select 
                        name="nama" 
                        id="employee_select" 
                        class="appearance-none w-full bg-gray-800 text-white border-none outline-none placeholder-gray-400" 
                        required
                    >
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees as $employee)
                            <option 
                                value="{{ $employee->name }}"
                                data-department="{{ $employee->department->name }}"
                                {{ old('nama') == $employee->name ? 'selected' : '' }}
                            >
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- DEPARTEMEN (readonly <select>) -->
            <div>
                <label for="department" class="block text-sm font-medium text-gray-200 mb-1">Departemen</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-building text-white mr-2"></i>
                    <select 
                        id="department" 
                        name="department"
                        class="appearance-none w-full bg-gray-800 text-white border-none outline-none placeholder-gray-400" 
                        required 
                        readonly
                    >
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                            <option 
                                value="{{ $dept->name }}"
                                {{ old('department') == $dept->name ? 'selected' : '' }}
                            >
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- TANGGAL JAM MULAI -->
            <div>
                <label for="start_datetime" class="block text-sm font-medium text-gray-200 mb-1">Tanggal Jam Mulai</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-calendar-alt text-white mr-2"></i>
                    <input 
                        type="text" 
                        id="start_datetime" 
                        name="start_datetime" 
                        placeholder="Pilih tanggal dan waktu mulai" 
                        class="w-full bg-transparent border-none outline-none text-white placeholder-gray-400" 
                        required
                        value="{{ old('start_datetime') }}"
                    >
                </div>
            </div>

            <!-- TANGGAL JAM SELESAI -->
            <div>
                <label for="end_datetime" class="block text-sm font-medium text-gray-200 mb-1">Tanggal Jam Selesai</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-calendar-alt text-white mr-2"></i>
                    <input 
                        type="text" 
                        id="end_datetime" 
                        name="end_datetime" 
                        placeholder="Pilih tanggal dan waktu selesai" 
                        class="w-full bg-transparent border-none outline-none text-white placeholder-gray-400" 
                        required
                        value="{{ old('end_datetime') }}"
                    >
                </div>
            </div>

            <!-- TIPE KEGIATAN (Custom Dropdown Alpine) -->
            <div x-data="{
                    open: false,
                    selected: '{{ old('activity_type') ?: 'Pilih Tipe Kegiatan' }}',
                    options: ['Meeting','Invititation','Survey'],
                    selectOption(opt) {
                        this.selected = opt;
                        this.open = false;
                        $refs.typeInput.value = opt;
                    }
                }" class="relative">
                <label class="block text-sm font-medium text-gray-200 mb-1">Tipe Kegiatan</label>
                <button type="button"
                        @click="open = !open"
                        class="w-full bg-gray-800 text-white p-3 rounded-md shadow-md flex justify-between items-center focus:outline-none">
                    <span x-text="selected"></span>
                    <svg class="w-5 h-5 transform transition-transform duration-200"
                         :class="{'rotate-180': open}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2" 
                              d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <!-- Menu Dropdown -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="absolute z-20 mt-2 w-full bg-gray-800 rounded-md shadow-lg">
                    <template x-for="opt in options" :key="opt">
                        <div @click="selectOption(opt)"
                             class="cursor-pointer px-4 py-2 text-white hover:bg-gray-700"
                             x-text="opt">
                        </div>
                    </template>
                </div>
                <!-- Hidden Input -->
                <input type="hidden" name="activity_type" x-ref="typeInput" :value="selected">
            </div>

            <!-- DESKRIPSI -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-200 mb-1">Deskripsi</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-comment-dots text-white mr-2"></i>
                    <textarea 
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Masukkan deskripsi kegiatan"
                        class="w-full bg-transparent border-none outline-none text-white placeholder-gray-400"
                        required
                    >{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <button
            type="submit"
            class="w-full bg-blue-600 text-white p-3 rounded-md shadow-md 
                   transform hover:scale-105 transition duration-300 hover:bg-blue-700">
            <i class="fas fa-check-circle mr-2"></i> Submit Kegiatan
          </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Gunakan Font Poppins -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<!-- Flatpickr (Tema Material Blue) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
  body {
    font-family: 'Poppins', sans-serif;
  }

  /* Paksa background & teks untuk dropdown <select> */
  select, select option {
    background-color: #1f2937 !important; /* bg-gray-800 */
    color: #fff !important;
  }
</style>
@endpush

@push('scripts')
<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Flatpickr & Locale Indonesia -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Flatpickr (Tanggal & Waktu Mulai)
    flatpickr("#start_datetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        minuteIncrement: 1,
        locale: "id"
    });

    // Inisialisasi Flatpickr (Tanggal & Waktu Selesai)
    flatpickr("#end_datetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        minuteIncrement: 1,
        locale: "id"
    });

    // Auto-populasi Departemen berdasarkan Nama Karyawan
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
