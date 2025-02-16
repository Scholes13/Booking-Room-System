@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div id="dashboard" class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Booking Hari Ini Card -->
        <div class="bg-blue-50 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 id="bookingTitle" class="text-xl font-semibold text-gray-800">Booking Hari Ini</h3>
                    <p id="todayBookings" class="text-3xl font-bold mt-1">0</p>
                </div>
            </div>
            <p id="nextBookingInfo" class="text-gray-600">Tidak ada booking berikutnya</p>
        </div>

        <!-- Penggunaan Ruangan Card -->
        <div class="bg-green-50 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-door-open text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Penggunaan Ruangan</h3>
                    <p id="roomUsage" class="text-3xl font-bold mt-1">0%</p>
                </div>
            </div>
            <p id="mostUsedRoom" class="text-gray-600">Ruangan terbanyak: -</p>
        </div>
    </div>
    
    <!-- Filter and Export Buttons Container -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <!-- Filter Buttons -->
        <div id="filterButtons" class="flex flex-wrap gap-3">
            <button id="btnToday" class="filter-btn flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-calendar-day"></i> Hari Ini
            </button>
            <button id="btnWeek" class="filter-btn flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                <i class="fas fa-calendar-week"></i> Minggu Ini
            </button>
            <button id="btnHour" class="filter-btn flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-clock"></i> Jam Ini
            </button>
            <button id="btnMonth" class="filter-btn flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-calendar-alt"></i> Bulan Ini
            </button>
            <button id="btnReset" class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>
        
        <!-- Export Button -->
        <a href="{{ route('admin.bookings.export') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-file-export"></i> Export to Excel
        </a>
    </div>
    
    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm relative max-w-full overflow-x-auto">
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="hidden absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-t-2 border-b-2 border-blue-500 rounded-full animate-spin"></div>
                <p class="text-blue-500 font-medium">Memuat data...</p>
            </div>
        </div>

        <!-- Tabel Booking -->
        <table id="bookingTable" 
               class="min-w-full table-fixed divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <!-- Tentukan lebar kolom, misalnya w-48 untuk Nama -->
                    <th class="px-6 py-3 w-48 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-6 py-3 w-32 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departemen
                    </th>
                    <th class="px-6 py-3 w-32 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-6 py-3 w-32 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jam Mulai
                    </th>
                    <th class="px-6 py-3 w-32 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jam Selesai
                    </th>
                    <!-- Ruang Meeting juga bisa panjang -->
                    <th class="px-6 py-3 w-48 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ruang Meeting
                    </th>
                    <!-- Deskripsi sering panjang, beri lebar lebih besar -->
                    <th class="px-6 py-3 w-64 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Deskripsi
                    </th>
                    <th class="px-6 py-3 w-32 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 booking-row" data-endtime="{{ $booking->date }} {{ $booking->end_time }}">
                    <!-- Kolom "Nama" -->
                    <td class="px-6 py-4 text-sm text-gray-900 
                               whitespace-normal break-words">
                        {{ $booking->nama }}
                    </td>
                    <!-- Departemen -->
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $booking->department }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 booking-date">
                        {{ $booking->date }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 booking-time">
                        {{ $booking->start_time }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 booking-endtime">
                        {{ $booking->end_time }}
                    </td>
                    <!-- Ruang Meeting -->
                    <td class="px-6 py-4 text-sm text-gray-900 
                               whitespace-normal break-words">
                        {{ $booking->meetingRoom->name }}
                    </td>
                    <!-- Deskripsi -->
                    <td class="px-6 py-4 text-sm text-gray-900 
                               whitespace-normal break-words">
                        {{ $booking->description }}
                    </td>
                    <!-- Aksi -->
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.bookings.delete', $booking->id) }}" 
                                  method="POST" 
                                  class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Tidak ada data booking</p>
                            <p class="text-sm text-gray-400">Silakan tambahkan booking baru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load utilities first (DashboardUtils, dsb.) -->
<script src="{{ asset('js/dashboard/utils.js') }}"></script>
<script src="{{ asset('js/dashboard/constants.js') }}"></script>
<script src="{{ asset('js/dashboard/stats.js') }}"></script>
<script src="{{ asset('js/dashboard/filters.js') }}"></script>
<!-- Main.js yang menangani handleDelete -->
<script src="{{ asset('js/dashboard/main.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Dashboard) {
            window.Dashboard.initialize();
        } else {
            console.error('Dashboard tidak terinisialisasi dengan benar');
        }
    });
</script>
@endpush
