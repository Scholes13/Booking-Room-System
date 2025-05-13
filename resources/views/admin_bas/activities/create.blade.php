@extends('admin_bas.layout')

@section('title', 'Tambah Aktivitas')

@section('content')
<div class="flex flex-col h-full">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Tambah Aktivitas</h1>
        <a href="{{ route('bas.activities.index') }}" class="inline-flex items-center justify-center text-bas bg-secondary hover:bg-opacity-90 py-2 px-4 rounded-md font-semibold text-sm">
            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Error Validation -->
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-3">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <form action="{{ route('bas.activities.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NAMA -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                    <select name="name" id="employee_select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach(App\Models\Employee::orderBy('name')->get() as $employee)
                            <option value="{{ $employee->name }}" data-department-id="{{ $employee->department_id }}" {{ old('name') == $employee->name ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- DEPARTEMEN - Will be auto-selected based on employee -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Departemen <span class="text-red-500">*</span></label>
                    <select name="department_id" id="department_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('department_id') border-red-500 @enderror" required>
                        <option value="">Pilih Departemen</option>
                        @foreach(App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- WAKTU MULAI -->
                <div>
                    <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('start_datetime') border-red-500 @enderror" value="{{ old('start_datetime') }}" required>
                    @error('start_datetime')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- WAKTU SELESAI -->
                <div>
                    <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('end_datetime') border-red-500 @enderror" value="{{ old('end_datetime') }}" required>
                    @error('end_datetime')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- PROVINSI -->
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                    <select name="province" id="province" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('province') border-red-500 @enderror" required>
                        <option value="">Pilih Provinsi</option>
                    </select>
                    @error('province')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- KOTA -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota <span class="text-red-500">*</span></label>
                    <select name="city" id="city" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('city') border-red-500 @enderror" required>
                        <option value="">Pilih Kota</option>
                    </select>
                    @error('city')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- TIPE KEGIATAN -->
                <div>
                    <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas <span class="text-red-500">*</span></label>
                    <select name="activity_type" id="activity_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('activity_type') border-red-500 @enderror" required>
                        <option value="">Pilih Jenis Aktivitas</option>
                        <option value="Meeting" {{ old('activity_type') == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="Sales Call" {{ old('activity_type') == 'Sales Call' ? 'selected' : '' }}>Sales Call</option>
                        <option value="Internal Activities" {{ old('activity_type') == 'Internal Activities' ? 'selected' : '' }}>Internal Activities</option>
                        <option value="Pilih Tipe Kegiatan" {{ old('activity_type') == 'Pilih Tipe Kegiatan' ? 'selected' : '' }}>Pilih Tipe Kegiatan</option>
                        <option value="Lainnya" {{ old('activity_type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('activity_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- SPESIFIKASI TIPE KEGIATAN LAINNYA -->
                <div id="other_activity_type_container" style="display: none;">
                    <label for="activity_type_other" class="block text-sm font-medium text-gray-700 mb-1">Spesifikasi Tipe Kegiatan</label>
                    <input type="text" name="activity_type_other" id="activity_type_other" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('activity_type_other') }}" placeholder="Masukkan tipe kegiatan lainnya">
                </div>
            </div>
            
            <!-- DESKRIPSI -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- SUBMIT BUTTON -->
            <div class="flex justify-end">
                <button type="submit" class="bg-bas hover:bg-opacity-90 text-white py-2 px-6 rounded-md font-semibold transition-all hover:shadow-md">
                    <i class="fas fa-check-circle mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.getElementById('employee_select');
        const departmentSelect = document.getElementById('department_id');
        const activityTypeSelect = document.getElementById('activity_type');
        const otherActivityTypeContainer = document.getElementById('other_activity_type_container');
        const otherActivityTypeInput = document.getElementById('activity_type_other');
        
        // Link employee selection to department
        if (employeeSelect) {
            employeeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const departmentId = selectedOption.getAttribute('data-department-id');
                
                if (departmentId) {
                    departmentSelect.value = departmentId;
                }
            });
            
            // Initialize on page load if there's a selected employee
            if (employeeSelect.selectedIndex > 0) {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                const departmentId = selectedOption.getAttribute('data-department-id');
                
                if (departmentId) {
                    departmentSelect.value = departmentId;
                }
            }
        }
        
        // Handle activity type "Lainnya" option
        if (activityTypeSelect) {
            function toggleOtherActivityTypeField() {
                if (activityTypeSelect.value === 'Lainnya') {
                    otherActivityTypeContainer.style.display = 'block';
                    otherActivityTypeInput.setAttribute('required', 'required');
                } else {
                    otherActivityTypeContainer.style.display = 'none';
                    otherActivityTypeInput.removeAttribute('required');
                }
            }
            
            // Initialize display status
            toggleOtherActivityTypeField();
            
            // Handle change events
            activityTypeSelect.addEventListener('change', toggleOtherActivityTypeField);
        }
        
        // Province and city management
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        
        // Data untuk provinsi dan kota di Indonesia
        const indonesiaData = {
            "Aceh": ["Banda Aceh", "Langsa", "Lhokseumawe", "Sabang", "Subulussalam"],
            "Sumatera Utara": ["Binjai", "Gunungsitoli", "Medan", "Padang Sidempuan", "Pematangsiantar", "Sibolga", "Tanjungbalai", "Tebing Tinggi"],
            "Sumatera Barat": ["Bukittinggi", "Padang", "Padang Panjang", "Pariaman", "Payakumbuh", "Sawahlunto", "Solok"],
            "Riau": ["Dumai", "Pekanbaru"],
            "Jambi": ["Jambi", "Sungai Penuh"],
            "Sumatera Selatan": ["Lubuk Linggau", "Pagar Alam", "Palembang", "Prabumulih"],
            "Bengkulu": ["Bengkulu"],
            "Lampung": ["Bandar Lampung", "Metro"],
            "Kepulauan Bangka Belitung": ["Pangkal Pinang"],
            "Kepulauan Riau": ["Batam", "Tanjung Pinang"],
            "DKI Jakarta": ["Jakarta Barat", "Jakarta Pusat", "Jakarta Selatan", "Jakarta Timur", "Jakarta Utara"],
            "Jawa Barat": ["Bandung", "Bekasi", "Bogor", "Cimahi", "Cirebon", "Depok", "Sukabumi", "Tasikmalaya"],
            "Jawa Tengah": ["Magelang", "Pekalongan", "Salatiga", "Semarang", "Surakarta", "Tegal"],
            "DI Yogyakarta": ["Bantul", "Gunungkidul", "Kulon Progo", "Sleman", "Yogyakarta"],
            "Jawa Timur": ["Batu", "Blitar", "Kediri", "Madiun", "Malang", "Mojokerto", "Pasuruan", "Probolinggo", "Surabaya"],
            "Banten": ["Cilegon", "Serang", "Tangerang", "Tangerang Selatan"],
            "Bali": ["Denpasar"],
            "Nusa Tenggara Barat": ["Bima", "Mataram"],
            "Nusa Tenggara Timur": ["Kupang"],
            "Kalimantan Barat": ["Pontianak", "Singkawang"],
            "Kalimantan Tengah": ["Palangka Raya"],
            "Kalimantan Selatan": ["Banjarbaru", "Banjarmasin"],
            "Kalimantan Timur": ["Balikpapan", "Bontang", "Samarinda"],
            "Kalimantan Utara": ["Tarakan"],
            "Sulawesi Utara": ["Bitung", "Kotamobagu", "Manado", "Tomohon"],
            "Sulawesi Tengah": ["Palu"],
            "Sulawesi Selatan": ["Makassar", "Palopo", "Parepare"],
            "Sulawesi Tenggara": ["Baubau", "Kendari"],
            "Gorontalo": ["Gorontalo"],
            "Sulawesi Barat": ["Mamuju"],
            "Maluku": ["Ambon", "Tual"],
            "Maluku Utara": ["Ternate", "Tidore Kepulauan"],
            "Papua": ["Jayapura"],
            "Papua Barat": ["Sorong"]
        };
        
        // Populate province dropdown
        Object.keys(indonesiaData).forEach(province => {
            const option = document.createElement('option');
            option.value = province;
            option.textContent = province;
            provinceSelect.appendChild(option);
        });
        
        // Set default province to DI Yogyakarta
        const oldProvince = "{{ old('province', 'DI Yogyakarta') }}";
        if (oldProvince) {
            provinceSelect.value = oldProvince;
            populateCities(oldProvince);
        }
        
        // When province changes, update city dropdown
        provinceSelect.addEventListener('change', function() {
            const selectedProvince = this.value;
            populateCities(selectedProvince);
        });
        
        // Function to populate cities based on selected province
        function populateCities(province) {
            // Clear current options
            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
            
            if (!province) {
                return;
            }
            
            // Add city options
            const cities = indonesiaData[province] || [];
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
            
            // Set Sleman as default if province is DI Yogyakarta
            if (province === 'DI Yogyakarta') {
                const oldCity = "{{ old('city', 'Sleman') }}";
                if (cities.includes(oldCity)) {
                    citySelect.value = oldCity;
                }
            } else {
                // If there's an old city value for this province, select it
                const oldCity = "{{ old('city') }}";
                if (oldCity && cities.includes(oldCity)) {
                    citySelect.value = oldCity;
                }
            }
        }
        
        // Initialize cities dropdown with default or old values
        populateCities(provinceSelect.value);
    });
</script>
@endpush 