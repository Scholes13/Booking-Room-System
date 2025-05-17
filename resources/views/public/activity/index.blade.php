@extends('layouts.app')

@section('title', 'Tambah Kegiatan')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <!-- Container utama dengan efek glass -->
    <div class="w-full max-w-lg mx-auto bg-white/15 backdrop-blur-lg shadow-2xl rounded-lg p-8 border border-white/20">
        <h2 class="text-3xl font-semibold text-center text-white mb-6">
            Tambah Kegiatan
        </h2>

        <!-- Error Validation and Success Messages removed -->

        <!-- Form -->
        <form id="activityForm" action="{{ route('activity.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- NAMA (native <select>) -->
            <div>
                <label class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-user mr-2 opacity-80"></i>
                    Nama
                </label>
                <div class="relative">
                    <select 
                        name="name" 
                        id="employee_select" 
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm"
                        required
                    >
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees as $employee)
                            <option 
                                value="{{ $employee->name }}"
                                data-department-id="{{ $employee->department_id }}"
                                {{ old('name') == $employee->name ? 'selected' : '' }}
                            >
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- DEPARTEMEN (readonly <select>) -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-building mr-2 opacity-80"></i>
                    Departemen
                </label>
                <div class="relative">
                    <select 
                        id="department_id" 
                        name="department_id"
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm"
                        required 
                        readonly
                    >
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                            <option 
                                value="{{ $dept->id }}"
                                {{ old('department_id') == $dept->id ? 'selected' : '' }}
                            >
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- TANGGAL JAM MULAI -->
            <div>
                <label for="start_datetime" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 opacity-80"></i>
                    Tanggal Jam Mulai
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="start_datetime" 
                        name="start_datetime" 
                        class="form-input rounded-md pl-4 pr-10 py-2.5 w-full text-sm" 
                        required
                        value="{{ old('start_datetime') }}"
                    >
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white opacity-80">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- TANGGAL JAM SELESAI -->
            <div>
                <label for="end_datetime" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 opacity-80"></i>
                    Tanggal Jam Selesai
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="end_datetime" 
                        name="end_datetime" 
                        class="form-input rounded-md pl-4 pr-10 py-2.5 w-full text-sm" 
                        required
                        value="{{ old('end_datetime') }}"
                    >
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white opacity-80">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- TIPE KEGIATAN (menggunakan <select> standar) -->
            <div>
                <label for="activity_type" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-tasks mr-2 opacity-80"></i>
                    Tipe Kegiatan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select 
                        id="activity_type" 
                        name="activity_type"
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm"
                        required
                        onchange="checkOtherActivity()"
                    >
                        <option value="">Pilih Tipe Kegiatan</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type }}" {{ old('activity_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
                
                <!-- "Lainnya" Field - hanya muncul ketika "Lainnya" dipilih -->
                <div id="other_activity_container" class="mt-2" style="display: {{ old('activity_type') === 'Lainnya' ? 'block' : 'none' }}">
                    <label for="activity_type_other" class="block text-sm font-medium text-white mb-2 flex items-center">
                        <i class="fas fa-info-circle mr-2 opacity-80"></i>
                        Spesifikasi Tipe Kegiatan
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="activity_type_other" 
                            name="activity_type_other" 
                            class="form-input rounded-md pl-4 pr-4 py-2.5 w-full text-sm" 
                            placeholder="Masukkan tipe kegiatan lainnya"
                            value="{{ old('activity_type_other') }}"
                        >
                    </div>
                </div>
            </div>

            <!-- PROVINSI -->
            <div>
                <label for="province" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 opacity-80"></i>
                    Provinsi
                </label>
                <div class="relative">
                    <select 
                        id="province" 
                        name="province"
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm"
                        required
                    >
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ old('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- KOTA -->
            <div>
                <label for="city" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-city mr-2 opacity-80"></i>
                    Kota/Kabupaten
                </label>
                <div class="relative">
                    <select 
                        id="city" 
                        name="city"
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm"
                        required
                    >
                        <option value="">Pilih Kota/Kabupaten</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- DESKRIPSI -->
            <div>
                <label for="description" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-comment-dots mr-2 opacity-80"></i>
                    Deskripsi
                </label>
                <div class="relative">
                    <textarea 
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Masukkan deskripsi kegiatan"
                        class="form-input rounded-md pl-4 pr-4 py-2.5 w-full text-sm"
                        required
                    >{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <button
                type="submit"
                class="w-full bg-white text-primary p-3 rounded-md shadow-md 
                       transform hover:scale-105 transition duration-300 hover:bg-white/90 font-medium mt-4">
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

<!-- Add the same fonts as login page -->
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
<link
    rel="stylesheet"
    as="style"
    onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
/>

<!-- Flatpickr (Tema Material Blue) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
  :root {
    --primary-color: #26458e;
    --hover-color: #ffffff;
    --font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif;
  }

  body {
    font-family: var(--font-family);
  }

  /* Filter elements */
  .form-select, .form-input {
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 6px;
    width: 100%;
    transition: all 0.2s;
    backdrop-filter: blur(4px);
  }
  
  .form-input {
    padding: 0.625rem 0.75rem;
  }

  .form-select {
    appearance: none;
    background-image: none;
  }

  .form-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
  }

  .form-select:focus, .form-input:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
  }
  
  /* Add elegant hover effect */
  .form-select:hover, .form-input:hover {
    border-color: rgba(255, 255, 255, 0.5);
    background-color: rgba(255, 255, 255, 0.2);
  }

  /* Submit button */
  .bg-white {
    background-color: #ffffff;
  }

  .text-primary {
    color: var(--primary-color);
  }

  /* Flatpickr theming */
  .flatpickr-calendar {
    background: #ffffff;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-radius: 8px !important;
    border: none !important;
  }

  .flatpickr-day.selected, 
  .flatpickr-day.startRange, 
  .flatpickr-day.endRange, 
  .flatpickr-day.selected.inRange, 
  .flatpickr-day.startRange.inRange, 
  .flatpickr-day.endRange.inRange, 
  .flatpickr-day.selected:focus, 
  .flatpickr-day.startRange:focus, 
  .flatpickr-day.endRange:focus, 
  .flatpickr-day.selected:hover, 
  .flatpickr-day.startRange:hover, 
  .flatpickr-day.endRange:hover, 
  .flatpickr-day.selected.prevMonthDay, 
  .flatpickr-day.startRange.prevMonthDay, 
  .flatpickr-day.endRange.prevMonthDay, 
  .flatpickr-day.selected.nextMonthDay, 
  .flatpickr-day.startRange.nextMonthDay, 
  .flatpickr-day.endRange.nextMonthDay {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
  }

  .flatpickr-time input:hover, 
  .flatpickr-time .flatpickr-am-pm:hover, 
  .flatpickr-time input:focus, 
  .flatpickr-time .flatpickr-am-pm:focus {
    background: #f3f4f6;
  }
</style>
@endpush

@push('scripts')
<!-- Flatpickr & Locale Indonesia -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add form submit event listener
    const activityForm = document.getElementById('activityForm');
    if (activityForm) {
        activityForm.addEventListener('submit', function(event) {
            // Don't show if validation fails
            if (!activityForm.checkValidity()) return;
            
            // Show loading toast when form is submitted
            Toastify({
                text: "Submitting activity...",
                duration: 0, // Won't disappear until page refreshes
                gravity: "top",
                position: "right",
                backgroundColor: "",
                className: "success-toast",
                stopOnFocus: true
            }).showToast();
        });
    }

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
    const departmentSelect = document.getElementById('department_id');

    employeeSelect.addEventListener('change', function() {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        const departmentId = selectedOption.getAttribute('data-department-id');
        if (departmentId) {
            departmentSelect.value = departmentId;
            departmentSelect.setAttribute('readonly', 'readonly');
        } else {
            departmentSelect.value = '';
            departmentSelect.removeAttribute('readonly');
        }
    });

    // Memastikan form validation berjalan
    const form = document.getElementById('activityForm');
    form.addEventListener('submit', function(e) {
        const activityType = document.getElementById('activity_type');
        if (!activityType.value) {
            e.preventDefault();
            activityType.classList.add('ring-2', 'ring-red-500');
            return false;
        }
        return true;
    });

    // Data provinsi dan kota di Indonesia
    const indonesiaData = {
        "Aceh": ["Aceh Barat", "Aceh Barat Daya", "Aceh Besar", "Aceh Jaya", "Aceh Selatan", "Aceh Singkil", "Aceh Tamiang", "Aceh Tengah", "Aceh Tenggara", "Aceh Timur", "Aceh Utara", "Banda Aceh", "Bener Meriah", "Bireuen", "Gayo Lues", "Langsa", "Lhokseumawe", "Nagan Raya", "Pidie", "Pidie Jaya", "Sabang", "Simeulue", "Subulussalam"],
        
        "Sumatera Utara": ["Asahan", "Batu Bara", "Binjai", "Dairi", "Deli Serdang", "Gunungsitoli", "Humbang Hasundutan", "Karo", "Labuhanbatu", "Labuhanbatu Selatan", "Labuhanbatu Utara", "Langkat", "Mandailing Natal", "Medan", "Nias", "Nias Barat", "Nias Selatan", "Nias Utara", "Padang Lawas", "Padang Lawas Utara", "Padang Sidempuan", "Pakpak Bharat", "Pematangsiantar", "Samosir", "Serdang Bedagai", "Sibolga", "Simalungun", "Tanjungbalai", "Tapanuli Selatan", "Tapanuli Tengah", "Tapanuli Utara", "Tebing Tinggi", "Toba Samosir"],
        
        "Sumatera Barat": ["Agam", "Bukittinggi", "Dharmasraya", "Kepulauan Mentawai", "Lima Puluh Kota", "Padang", "Padang Panjang", "Padang Pariaman", "Pariaman", "Pasaman", "Pasaman Barat", "Payakumbuh", "Pesisir Selatan", "Sawah Lunto", "Sijunjung", "Solok", "Solok Selatan", "Tanah Datar"],
        
        "Riau": ["Bengkalis", "Dumai", "Indragiri Hilir", "Indragiri Hulu", "Kampar", "Kepulauan Meranti", "Kuantan Singingi", "Pekanbaru", "Pelalawan", "Rokan Hilir", "Rokan Hulu", "Siak"],
        
        "Kepulauan Riau": ["Batam", "Bintan", "Karimun", "Kepulauan Anambas", "Lingga", "Natuna", "Tanjung Pinang"],
        
        "Jambi": ["Batanghari", "Bungo", "Jambi", "Kerinci", "Merangin", "Muaro Jambi", "Sarolangun", "Sungai Penuh", "Tanjung Jabung Barat", "Tanjung Jabung Timur", "Tebo"],
        
        "Sumatera Selatan": ["Banyuasin", "Empat Lawang", "Lahat", "Lubuk Linggau", "Muara Enim", "Musi Banyuasin", "Musi Rawas", "Musi Rawas Utara", "Ogan Ilir", "Ogan Komering Ilir", "Ogan Komering Ulu", "Ogan Komering Ulu Selatan", "Ogan Komering Ulu Timur", "Pagar Alam", "Palembang", "Penukal Abab Lematang Ilir", "Prabumulih"],
        
        "Bangka Belitung": ["Bangka", "Bangka Barat", "Bangka Selatan", "Bangka Tengah", "Belitung", "Belitung Timur", "Pangkal Pinang"],
        
        "Bengkulu": ["Bengkulu", "Bengkulu Selatan", "Bengkulu Tengah", "Bengkulu Utara", "Kaur", "Kepahiang", "Lebong", "Mukomuko", "Rejang Lebong", "Seluma"],
        
        "Lampung": ["Bandar Lampung", "Lampung Barat", "Lampung Selatan", "Lampung Tengah", "Lampung Timur", "Lampung Utara", "Mesuji", "Metro", "Pesawaran", "Pesisir Barat", "Pringsewu", "Tanggamus", "Tulang Bawang", "Tulang Bawang Barat", "Way Kanan"],
        
        "DKI Jakarta": ["Jakarta Barat", "Jakarta Pusat", "Jakarta Selatan", "Jakarta Timur", "Jakarta Utara", "Kepulauan Seribu"],
        
        "Jawa Barat": ["Bandung", "Bandung Barat", "Banjar", "Bekasi", "Bogor", "Ciamis", "Cianjur", "Cimahi", "Cirebon", "Depok", "Garut", "Indramayu", "Karawang", "Kuningan", "Majalengka", "Pangandaran", "Purwakarta", "Subang", "Sukabumi", "Sumedang", "Tasikmalaya"],
        
        "Banten": ["Cilegon", "Lebak", "Pandeglang", "Serang", "Tangerang", "Tangerang Selatan"],
        
        "Jawa Tengah": ["Banjarnegara", "Banyumas", "Batang", "Blora", "Boyolali", "Brebes", "Cilacap", "Demak", "Grobogan", "Jepara", "Karanganyar", "Kebumen", "Kendal", "Klaten", "Kudus", "Magelang", "Pati", "Pekalongan", "Pemalang", "Purbalingga", "Purworejo", "Rembang", "Salatiga", "Semarang", "Sragen", "Sukoharjo", "Surakarta", "Tegal", "Temanggung", "Wonogiri", "Wonosobo"],
        
        "DI Yogyakarta": ["Bantul", "Gunungkidul", "Kulon Progo", "Sleman", "Yogyakarta"],
        
        "Jawa Timur": ["Bangkalan", "Banyuwangi", "Batu", "Blitar", "Bojonegoro", "Bondowoso", "Gresik", "Jember", "Jombang", "Kediri", "Lamongan", "Lumajang", "Madiun", "Magetan", "Malang", "Mojokerto", "Nganjuk", "Ngawi", "Pacitan", "Pamekasan", "Pasuruan", "Ponorogo", "Probolinggo", "Sampang", "Sidoarjo", "Situbondo", "Sumenep", "Surabaya", "Trenggalek", "Tuban", "Tulungagung"],
        
        "Bali": ["Badung", "Bangli", "Buleleng", "Denpasar", "Gianyar", "Jembrana", "Karangasem", "Klungkung", "Tabanan"],
        
        "Nusa Tenggara Barat": ["Bima", "Dompu", "Lombok Barat", "Lombok Tengah", "Lombok Timur", "Lombok Utara", "Mataram", "Sumbawa", "Sumbawa Barat"],
        
        "Nusa Tenggara Timur": ["Alor", "Belu", "Ende", "Flores Timur", "Kupang", "Lembata", "Malaka", "Manggarai", "Manggarai Barat", "Manggarai Timur", "Nagekeo", "Ngada", "Rote Ndao", "Sabu Raijua", "Sikka", "Sumba Barat", "Sumba Barat Daya", "Sumba Tengah", "Sumba Timur", "Timor Tengah Selatan", "Timor Tengah Utara"],
        
        "Kalimantan Barat": ["Bengkayang", "Kapuas Hulu", "Kayong Utara", "Ketapang", "Kubu Raya", "Landak", "Melawi", "Mempawah", "Pontianak", "Sambas", "Sanggau", "Sekadau", "Singkawang", "Sintang"],
        
        "Kalimantan Tengah": ["Barito Selatan", "Barito Timur", "Barito Utara", "Gunung Mas", "Kapuas", "Katingan", "Kotawaringin Barat", "Kotawaringin Timur", "Lamandau", "Murung Raya", "Palangka Raya", "Pulang Pisau", "Seruyan", "Sukamara"],
        
        "Kalimantan Selatan": ["Balangan", "Banjar", "Banjarbaru", "Banjarmasin", "Barito Kuala", "Hulu Sungai Selatan", "Hulu Sungai Tengah", "Hulu Sungai Utara", "Kotabaru", "Tabalong", "Tanah Bumbu", "Tanah Laut", "Tapin"],
        
        "Kalimantan Timur": ["Balikpapan", "Berau", "Bontang", "Kutai Barat", "Kutai Kartanegara", "Kutai Timur", "Mahakam Ulu", "Paser", "Penajam Paser Utara", "Samarinda"],
        
        "Kalimantan Utara": ["Bulungan", "Malinau", "Nunukan", "Tana Tidung", "Tarakan"],
        
        "Sulawesi Utara": ["Bolaang Mongondow", "Bolaang Mongondow Selatan", "Bolaang Mongondow Timur", "Bolaang Mongondow Utara", "Bitung", "Kepulauan Sangihe", "Kepulauan Siau Tagulandang Biaro", "Kepulauan Talaud", "Kotamobagu", "Manado", "Minahasa", "Minahasa Selatan", "Minahasa Tenggara", "Minahasa Utara", "Tomohon"],
        
        "Gorontalo": ["Boalemo", "Bone Bolango", "Gorontalo", "Gorontalo Utara", "Pohuwato"],
        
        "Sulawesi Tengah": ["Banggai", "Banggai Kepulauan", "Banggai Laut", "Buol", "Donggala", "Morowali", "Morowali Utara", "Palu", "Parigi Moutong", "Poso", "Sigi", "Tojo Una-Una", "Tolitoli"],
        
        "Sulawesi Barat": ["Majene", "Mamasa", "Mamuju", "Mamuju Tengah", "Pasangkayu", "Polewali Mandar"],
        
        "Sulawesi Selatan": ["Bantaeng", "Barru", "Bone", "Bulukumba", "Enrekang", "Gowa", "Jeneponto", "Kepulauan Selayar", "Luwu", "Luwu Timur", "Luwu Utara", "Makassar", "Maros", "Palopo", "Pangkajene dan Kepulauan", "Parepare", "Pinrang", "Sidenreng Rappang", "Sinjai", "Soppeng", "Takalar", "Tana Toraja", "Toraja Utara", "Wajo"],
        
        "Sulawesi Tenggara": ["Bombana", "Buton", "Buton Selatan", "Buton Tengah", "Buton Utara", "Kendari", "Kolaka", "Kolaka Timur", "Kolaka Utara", "Konawe", "Konawe Kepulauan", "Konawe Selatan", "Konawe Utara", "Muna", "Muna Barat", "Wakatobi"],
        
        "Maluku": ["Ambon", "Buru", "Buru Selatan", "Kepulauan Aru", "Maluku Barat Daya", "Maluku Tengah", "Maluku Tenggara", "Maluku Tenggara Barat", "Seram Bagian Barat", "Seram Bagian Timur", "Tual"],
        
        "Maluku Utara": ["Halmahera Barat", "Halmahera Tengah", "Halmahera Timur", "Halmahera Selatan", "Halmahera Utara", "Kepulauan Sula", "Pulau Morotai", "Pulau Taliabu", "Ternate", "Tidore Kepulauan"],
        
        "Papua": ["Asmat", "Biak Numfor", "Boven Digoel", "Deiyai", "Dogiyai", "Intan Jaya", "Jayapura", "Jayawijaya", "Keerom", "Kepulauan Yapen", "Lanny Jaya", "Mamberamo Raya", "Mamberamo Tengah", "Mappi", "Merauke", "Mimika", "Nabire", "Nduga", "Paniai", "Pegunungan Bintang", "Puncak", "Puncak Jaya", "Sarmi", "Supiori", "Tolikara", "Waropen", "Yahukimo", "Yalimo"],
        
        "Papua Barat": ["Fakfak", "Kaimana", "Manokwari", "Manokwari Selatan", "Maybrat", "Pegunungan Arfak", "Raja Ampat", "Sorong", "Sorong Selatan", "Tambrauw", "Teluk Bintuni", "Teluk Wondama"],
        
        "Papua Selatan": ["Boven Digoel", "Mappi", "Merauke", "Asmat"],
        
        "Papua Tengah": ["Deiyai", "Dogiyai", "Intan Jaya", "Mimika", "Nabire", "Paniai", "Puncak", "Puncak Jaya"],
        
        "Papua Pegunungan": ["Jayawijaya", "Lanny Jaya", "Mamberamo Tengah", "Nduga", "Pegunungan Bintang", "Tolikara", "Yahukimo", "Yalimo"]
    };

    // Populate provinsi dropdown
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');

    // Populate provinsi dropdown with options
    Object.keys(indonesiaData).forEach(province => {
        const option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        provinceSelect.appendChild(option);
    });

    // Set value from old input if exists, otherwise set defaults to DI Yogyakarta and Sleman
    const oldProvince = "{{ old('province') }}";
    const oldCity = "{{ old('city') }}";
    
    if (oldProvince) {
        provinceSelect.value = oldProvince;
        populateCities(oldProvince);
        
        if (oldCity) {
            citySelect.value = oldCity;
        }
    } else {
        // Set default to DI Yogyakarta
        provinceSelect.value = "DI Yogyakarta";
        populateCities("DI Yogyakarta");
        
        // Set default city to Sleman
        setTimeout(() => {
            citySelect.value = "Sleman";
        }, 100);
    }

    // When province changes, update city dropdown
    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        populateCities(selectedProvince);
    });

    // Function to populate cities based on selected province
    function populateCities(province) {
        // Clear current options
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        
        if (!province) {
            citySelect.disabled = true;
            return;
        }
        
        // Enable city dropdown and add options
        citySelect.disabled = false;
        
        const cities = indonesiaData[province] || [];
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }
});

// Fungsi untuk menampilkan/menyembunyikan field "Lainnya"
function checkOtherActivity() {
    const activityType = document.getElementById('activity_type');
    const otherContainer = document.getElementById('other_activity_container');
    const otherInput = document.getElementById('activity_type_other');
    
    if (activityType.value === 'Lainnya') {
        otherContainer.style.display = 'block';
        otherInput.required = true;
    } else {
        otherContainer.style.display = 'none';
        otherInput.required = false;
    }
}

// Jalankan sekali saat halaman dimuat
document.addEventListener('DOMContentLoaded', checkOtherActivity);
</script>
@endpush
