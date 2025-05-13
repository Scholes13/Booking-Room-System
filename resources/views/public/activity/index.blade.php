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
                        name="name" 
                        id="employee_select" 
                        class="appearance-none w-full bg-gray-800 text-white border-none outline-none placeholder-gray-400" 
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
                </div>
            </div>

            <!-- DEPARTEMEN (readonly <select>) -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-200 mb-1">Departemen</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-building text-white mr-2"></i>
                    <select 
                        id="department_id" 
                        name="department_id"
                        class="appearance-none w-full bg-gray-800 text-white border-none outline-none placeholder-gray-400" 
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
                        class="w-full bg-transparent border-none outline-none text-white placeholder-gray-400" 
                        required
                        value="{{ old('end_datetime') }}"
                    >
                </div>
            </div>

            <!-- TIPE KEGIATAN (Custom Dropdown Alpine) -->
            <div x-data="activityTypeDropdown()" class="relative">
                <label class="block text-sm font-medium text-gray-200 mb-1">Tipe Kegiatan <span class="text-red-500">*</span></label>
                <button type="button"
                        @click="open = !open"
                        class="w-full bg-gray-800 text-white p-3 rounded-md shadow-md flex justify-between items-center focus:outline-none"
                        :class="{'ring-2 ring-red-500': validationError}">
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
                     class="absolute z-20 mt-2 w-full bg-gray-800 rounded-md shadow-lg max-h-60 overflow-y-auto">
                    <template x-for="opt in options" :key="opt">
                        <div @click="selectOption(opt)"
                             class="cursor-pointer px-4 py-2 text-white hover:bg-gray-700"
                             x-text="opt">
                        </div>
                    </template>
                </div>
                <!-- Hidden Input -->
                <input type="hidden" name="activity_type" x-ref="typeInput" :value="selected" required>
                
                <!-- Error Message -->
                <div x-show="validationError" class="text-red-400 text-sm mt-1">
                    Silahkan pilih tipe kegiatan
                </div>
                
                <!-- "Lainnya" Field - hanya muncul ketika "Lainnya" dipilih -->
                <div x-show="showOtherField" class="mt-2">
                    <label for="activity_type_other" class="block text-sm font-medium text-gray-200 mb-1">Spesifikasi Tipe Kegiatan</label>
                    <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                        <i class="fas fa-info-circle text-white mr-2"></i>
                        <input 
                            type="text" 
                            id="activity_type_other" 
                            name="activity_type_other" 
                            class="w-full bg-transparent border-none outline-none text-white placeholder-gray-400" 
                            placeholder="Masukkan tipe kegiatan lainnya"
                            value="{{ old('activity_type_other') }}"
                            :required="showOtherField"
                        >
                    </div>
                </div>
            </div>

            <!-- PROVINSI -->
            <div>
                <label for="province" class="block text-sm font-medium text-gray-200 mb-1">Provinsi</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-map-marker-alt text-white mr-2"></i>
                    <select 
                        id="province" 
                        name="province"
                        class="appearance-none w-full bg-gray-800 text-white border-none outline-none placeholder-gray-400" 
                        required
                    >
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ old('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- KOTA -->
            <div>
                <label for="city" class="block text-sm font-medium text-gray-200 mb-1">Kota/Kabupaten</label>
                <div class="bg-gray-800 text-white p-3 rounded-md shadow-md flex items-center">
                    <i class="fas fa-city text-white mr-2"></i>
                    <select 
                        id="city" 
                        name="city"
                        class="appearance-none w-full bg-gray-800 text-white border-none outline-none placeholder-gray-400" 
                        required
                    >
                        <option value="">Pilih Kota/Kabupaten</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>
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
function activityTypeDropdown() {
    return {
        open: false,
        selected: '{{ old('activity_type') ?: 'Pilih Tipe Kegiatan' }}',
        options: @json($activityTypes),
        showOtherField: {{ old('activity_type') === 'Lainnya' ? 'true' : 'false' }},
        validationError: false,
        init() {
            this.showOtherField = this.selected === 'Lainnya';
            
            // Tambahkan handler untuk validasi form
            const form = document.getElementById('activityForm');
            form.addEventListener('submit', (e) => {
                if (this.selected === 'Pilih Tipe Kegiatan') {
                    e.preventDefault();
                    this.validationError = true;
                    this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
                this.validationError = false;
                return true;
            });
        },
        selectOption(opt) {
            this.selected = opt;
            this.open = false;
            this.$refs.typeInput.value = opt;
            this.showOtherField = (opt === 'Lainnya');
            this.validationError = false;
        }
    }
}

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
</script>
@endpush
