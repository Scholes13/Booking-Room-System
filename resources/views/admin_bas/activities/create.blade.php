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
        <!-- AI Parser Section -->
        <div class="mb-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 class="text-lg font-semibold text-blue-800">AI Parser - Otomatis Isi Form</h3>
            </div>
            <p class="text-sm text-blue-600 mb-4">
                Masukkan teks seperti: "Meeting Team HR - Ruang Antaboga - PIC : Bu Dita - 09.00 - 15.00 WIB"
            </p>
            
            <div class="space-y-3">
                <div>
                    <label for="ai_input" class="block text-sm font-medium text-blue-700 mb-1">Input Teks Natural</label>
                    <textarea
                        id="ai_input"
                        rows="3"
                        class="w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Contoh: Meeting Team HR - Ruang Antaboga - PIC : Bu Dita - 09.00 - 15.00 WIB"
                    ></textarea>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button
                        type="button"
                        id="parse_btn"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Parse & Isi Form
                    </button>
                    
                    <button
                        type="button"
                        id="clear_ai_btn"
                        class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Clear
                    </button>
                </div>
                
                <!-- Parsing Results -->
                <div id="parse_results" class="hidden">
                    <div class="bg-white rounded-md border border-green-200 p-3">
                        <h4 class="text-sm font-medium text-green-800 mb-2">Hasil Parsing:</h4>
                        <div id="parse_matches" class="text-sm text-green-700"></div>
                    </div>
                </div>
                
                <!-- Parsing Errors -->
                <div id="parse_errors" class="hidden">
                    <div class="bg-red-50 rounded-md border border-red-200 p-3">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Error:</h4>
                        <div id="parse_error_text" class="text-sm text-red-700"></div>
                    </div>
                </div>
                
                <!-- Loading State -->
                <div id="parse_loading" class="hidden">
                    <div class="flex items-center text-blue-600">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Memproses teks...</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('bas.activities.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Hidden status field with default value -->
            <input type="hidden" name="status" value="scheduled">
            
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
                        @foreach($activityTypes as $activityType)
                            <option value="{{ $activityType->name }}" {{ old('activity_type') == $activityType->name ? 'selected' : '' }}>
                                {{ $activityType->name }}
                            </option>
                        @endforeach
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
        
        // AI Parser functionality
        const aiInput = document.getElementById('ai_input');
        const parseBtn = document.getElementById('parse_btn');
        const clearAiBtn = document.getElementById('clear_ai_btn');
        const parseResults = document.getElementById('parse_results');
        const parseErrors = document.getElementById('parse_errors');
        const parseLoading = document.getElementById('parse_loading');
        const parseMatches = document.getElementById('parse_matches');
        const parseErrorText = document.getElementById('parse_error_text');
        
        // Parse button click handler
        parseBtn.addEventListener('click', function() {
            const inputText = aiInput.value.trim();
            
            if (!inputText) {
                showError('Silakan masukkan teks untuk diparse');
                return;
            }
            
            // Show loading state
            showLoading(true);
            hideResults();
            
            // Make AJAX request to parse endpoint
            fetch('{{ route("bas.parse-activity") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    input: inputText
                })
            })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                
                if (data.success) {
                    populateForm(data.data);
                    showMatches(data.matches);
                } else {
                    showError(data.error || 'Parsing gagal');
                }
            })
            .catch(error => {
                showLoading(false);
                showError('Error: ' + error.message);
                console.error('Parse error:', error);
            });
        });
        
        // Clear button click handler
        clearAiBtn.addEventListener('click', function() {
            aiInput.value = '';
            hideResults();
            clearForm();
        });
        
        // Function to populate form with parsed data
        function populateForm(data) {
            // Employee name
            if (data.employee_name) {
                const employeeSelect = document.getElementById('employee_select');
                for (let option of employeeSelect.options) {
                    if (option.text.includes(data.employee_name) || option.value === data.employee_name) {
                        employeeSelect.value = option.value;
                        // Trigger change event to populate department
                        employeeSelect.dispatchEvent(new Event('change'));
                        break;
                    }
                }
            }
            
            // Department (will be auto-populated by employee change event)
            if (data.department_id && !data.employee_name) {
                document.getElementById('department_id').value = data.department_id;
            }
            
            // Activity type
            if (data.activity_type) {
                const activityTypeSelect = document.getElementById('activity_type');
                for (let option of activityTypeSelect.options) {
                    if (option.value === data.activity_type || option.text.toLowerCase().includes(data.activity_type.toLowerCase())) {
                        activityTypeSelect.value = option.value;
                        activityTypeSelect.dispatchEvent(new Event('change'));
                        break;
                    }
                }
            }
            
            // Start datetime
            if (data.start_datetime) {
                document.getElementById('start_datetime').value = data.start_datetime;
            }
            
            // End datetime
            if (data.end_datetime) {
                document.getElementById('end_datetime').value = data.end_datetime;
            }
            
            // Description
            if (data.description) {
                document.getElementById('description').value = data.description;
            }
        }
        
        // Function to clear form fields
        function clearForm() {
            document.getElementById('employee_select').selectedIndex = 0;
            document.getElementById('department_id').selectedIndex = 0;
            document.getElementById('activity_type').selectedIndex = 0;
            document.getElementById('start_datetime').value = '';
            document.getElementById('end_datetime').value = '';
            document.getElementById('description').value = '';
        }
        
        // Function to show loading state
        function showLoading(show) {
            if (show) {
                parseLoading.classList.remove('hidden');
                parseBtn.disabled = true;
                parseBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Parsing...';
            } else {
                parseLoading.classList.add('hidden');
                parseBtn.disabled = false;
                parseBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>Parse & Isi Form';
            }
        }
        
        // Function to show parsing matches
        function showMatches(matches) {
            if (matches && matches.length > 0) {
                parseMatches.innerHTML = matches.map(match => `<div class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>${match}</div>`).join('');
                parseResults.classList.remove('hidden');
            }
        }
        
        // Function to show error
        function showError(error) {
            parseErrorText.textContent = error;
            parseErrors.classList.remove('hidden');
        }
        
        // Function to hide results
        function hideResults() {
            parseResults.classList.add('hidden');
            parseErrors.classList.add('hidden');
        }
        
        // Add CSRF token to page head if not already present
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const csrfMeta = document.createElement('meta');
            csrfMeta.name = 'csrf-token';
            csrfMeta.content = '{{ csrf_token() }}';
            document.head.appendChild(csrfMeta);
        }
    });
</script>
@endpush