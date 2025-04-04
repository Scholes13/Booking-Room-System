@extends('superadmin.layout')

@section('title', 'Tambah Aktivitas')

@section('content')
<div class="py-5 px-4">
    <div class="flex justify-between items-center mb-5">
        <h1 class="text-dark tracking-light text-[32px] font-bold leading-tight">Tambah Aktivitas Baru</h1>
        <a href="{{ route('superadmin.activities.index') }}" class="flex min-w-[84px] max-w-[180px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-white border border-primary text-primary text-sm font-bold leading-normal tracking-[0.015em]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <span class="truncate">Kembali</span>
        </a>
    </div>

    <!-- Error Validation -->
    @if($errors->any())
    <div class="bg-red-500/20 text-red-600 p-3 rounded-md mb-4">
        <ul>
            @foreach($errors->all() as $error)
            <li>⚠️ {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-xl border border-border p-6 shadow-sm">
        <form id="activityForm" action="{{ route('superadmin.activities.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Nama</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-user text-gray-400 mr-2"></i>
                        <select name="name" 
                                id="employee_select"
                                class="w-full bg-transparent border-none outline-none text-dark"
                                required>
                            <option value="">Pilih Karyawan</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->name }}" 
                                        data-department="{{ $employee->department->id }}"
                                        {{ old('name') == $employee->name ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Departemen -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-dark mb-2">Departemen</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-building text-gray-400 mr-2"></i>
                        <select id="department_id" 
                                name="department_id" 
                                class="w-full bg-transparent border-none outline-none text-dark" 
                                required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Jenis Aktivitas -->
                <div>
                    <label for="activity_type" class="block text-sm font-medium text-dark mb-2">Jenis Aktivitas</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-tasks text-gray-400 mr-2"></i>
                        <select id="activity_type" 
                                name="activity_type" 
                                class="w-full bg-transparent border-none outline-none text-dark" 
                                required>
                            <option value="">Pilih Jenis Aktivitas</option>
                            @foreach($activityTypes as $type)
                            <option value="{{ $type }}" {{ old('activity_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Jenis Aktivitas (Lainnya) -->
                <div id="activity_type_other_container" class="hidden">
                    <label for="activity_type_other" class="block text-sm font-medium text-dark mb-2">Jenis Aktivitas Lainnya</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-edit text-gray-400 mr-2"></i>
                        <input type="text" 
                               id="activity_type_other" 
                               name="activity_type_other" 
                               value="{{ old('activity_type_other') }}" 
                               class="w-full bg-transparent border-none outline-none text-dark placeholder-gray-400" 
                               placeholder="Sebutkan jenis aktivitas lainnya...">
                    </div>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province" class="block text-sm font-medium text-dark mb-2">Provinsi</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-map text-gray-400 mr-2"></i>
                        <select id="province" 
                                name="province" 
                                class="w-full bg-transparent border-none outline-none text-dark" 
                                required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ old('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Kota -->
                <div>
                    <label for="city" class="block text-sm font-medium text-dark mb-2">Kota</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-city text-gray-400 mr-2"></i>
                        <select id="city" 
                                name="city" 
                                class="w-full bg-transparent border-none outline-none text-dark" 
                                required>
                            <option value="">Pilih Kota</option>
                            @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Waktu Mulai -->
                <div>
                    <label for="start_datetime" class="block text-sm font-medium text-dark mb-2">Waktu Mulai</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                        <input type="datetime-local" 
                                id="start_datetime" 
                                name="start_datetime" 
                                value="{{ old('start_datetime') }}" 
                                class="w-full bg-transparent border-none outline-none text-dark" 
                                required>
                    </div>
                </div>

                <!-- Waktu Selesai -->
                <div>
                    <label for="end_datetime" class="block text-sm font-medium text-dark mb-2">Waktu Selesai</label>
                    <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                        <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                        <input type="datetime-local" 
                                id="end_datetime" 
                                name="end_datetime" 
                                value="{{ old('end_datetime') }}" 
                                class="w-full bg-transparent border-none outline-none text-dark" 
                                required>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-dark mb-2">Deskripsi</label>
                <div class="flex items-center bg-white rounded-md p-2 shadow-sm border border-border">
                    <i class="fas fa-comment-dots text-gray-400 mr-2 self-start mt-1"></i>
                    <textarea id="description" 
                            name="description" 
                            class="w-full bg-transparent border-none outline-none text-dark placeholder-gray-400" 
                            rows="4" 
                            placeholder="Berikan deskripsi kegiatan..."
                            required>{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="flex min-w-[120px] cursor-pointer items-center justify-center rounded-full h-10 px-6 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill department when employee is selected
        const employeeSelect = document.getElementById('employee_select');
        const departmentSelect = document.getElementById('department_id');
        
        employeeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const departmentId = selectedOption.getAttribute('data-department');
                if (departmentId) {
                    departmentSelect.value = departmentId;
                }
            }
        });
        
        // Handle "Lainnya" activity type
        const activityTypeSelect = document.getElementById('activity_type');
        const activityTypeOtherContainer = document.getElementById('activity_type_other_container');
        
        activityTypeSelect.addEventListener('change', function() {
            if (this.value === 'Lainnya') {
                activityTypeOtherContainer.classList.remove('hidden');
                document.getElementById('activity_type_other').setAttribute('required', 'required');
            } else {
                activityTypeOtherContainer.classList.add('hidden');
                document.getElementById('activity_type_other').removeAttribute('required');
            }
        });
        
        // Check initial value
        if (activityTypeSelect.value === 'Lainnya') {
            activityTypeOtherContainer.classList.remove('hidden');
            document.getElementById('activity_type_other').setAttribute('required', 'required');
        }
        
        // Form validation
        document.getElementById('activityForm').addEventListener('submit', function(e) {
            const startDateTime = new Date(document.getElementById('start_datetime').value);
            const endDateTime = new Date(document.getElementById('end_datetime').value);
            
            if (endDateTime <= startDateTime) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'Waktu selesai harus setelah waktu mulai',
                    icon: 'error',
                    confirmButtonColor: '#22428e'
                });
            }
        });
    });
</script>
@endpush 