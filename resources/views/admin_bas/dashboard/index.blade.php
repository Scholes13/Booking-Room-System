@extends('admin_bas.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }
    
    .card-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    }
    
    .card-gradient-2 {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .card-gradient-3 {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    
    .card-gradient-4 {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .hover-scale {
        transition: transform 0.2s ease;
    }
    
    .hover-scale:hover {
        transform: translateY(-2px);
    }
    
    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .pagination-link {
        transition: all 0.2s ease;
    }
    
    .pagination-link:hover {
        background-color: #e2e8f0;
    }
    
    .pagination-active {
        background-color: #2563eb;
        color: white;
        border-color: #2563eb;
    }
    
    .pagination-active:hover {
        background-color: #2563eb;
        color: white;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Success Alert -->
    @if (session('success'))
    <div id="successAlert" class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-sm flex items-center justify-between">
        <div class="flex items-center">
            <div class="flex-shrink-0 text-green-500">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
        <button onclick="dismissAlert('successAlert')" class="text-green-500 hover:text-green-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin BAS</h1>
            <p class="text-gray-500 mt-1">Ringkasan aktivitas dan statistik</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
            <a href="{{ route('bas.activities.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium text-sm transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Aktivitas
            </a>
            <a href="{{ route('bas.activities.calendar') }}" class="inline-flex items-center justify-center bg-white border border-gray-200 hover:bg-gray-50 text-blue-600 py-2 px-4 rounded-lg font-medium text-sm transition-colors">
                <i class="far fa-calendar-alt mr-2"></i>
                Lihat Kalender
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1: Total Aktivitas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover-scale">
            <div class="card-gradient p-5 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium opacity-90">Total Aktivitas</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $totalActivities ?? 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3 flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white">
                <a href="{{ route('bas.activities.index') }}" class="text-blue-600 text-sm font-medium hover:underline flex items-center">
                    Lihat semua
                    <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Card 2: Aktivitas Hari Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover-scale">
            <div class="card-gradient-2 p-5 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium opacity-90">Aktivitas Hari Ini</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $todayActivities }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3 flex items-center justify-center">
                        <i class="fas fa-calendar-day text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white">
                <a href="{{ route('bas.activities.index') }}?date={{ date('Y-m-d') }}" class="text-green-600 text-sm font-medium hover:underline flex items-center">
                    Lihat detail
                    <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Card 3: Aktivitas Minggu Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover-scale">
            <div class="card-gradient-3 p-5 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium opacity-90">Aktivitas Minggu Ini</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $weekActivities }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3 flex items-center justify-center">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white">
                <a href="{{ route('bas.activities.index') }}?week=current" class="text-purple-600 text-sm font-medium hover:underline flex items-center">
                    Lihat detail
                    <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Card 4: Aktivitas Bulan Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover-scale">
            <div class="card-gradient-4 p-5 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium opacity-90">Aktivitas Bulan Ini</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $monthActivities }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white">
                <a href="{{ route('bas.activities.index') }}?month={{ date('Y-m') }}" class="text-orange-600 text-sm font-medium hover:underline flex items-center">
                    Lihat detail
                    <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Upcoming Activities Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Aktivitas Mendatang</h2>
                <p class="text-sm text-gray-500 mt-1">Aktivitas yang akan datang dalam 7 hari ke depan</p>
            </div>
            <a href="{{ route('bas.activities.index') }}" class="text-blue-600 font-medium text-sm hover:underline mt-2 sm:mt-0 flex items-center">
                Lihat semua <i class="fas fa-external-link-alt ml-1 text-xs"></i>
            </a>
        </div>
        
        @if(count($upcomingActivities) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Aktivitas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($upcomingActivities as $activity)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->name }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->department->name ?? 'Tidak ada departemen' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->city }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->province }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}</div>
                            <div class="text-sm text-gray-500 text-center">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->activity_type ?: 'Umum' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 line-clamp-2">{{ $activity->description ?? 'Tidak ada deskripsi' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="status-badge 
                                @if($activity->status == 'scheduled') bg-blue-100 text-blue-800 
                                @elseif($activity->status == 'ongoing') bg-yellow-100 text-yellow-800 
                                @elseif($activity->status == 'completed') bg-green-100 text-green-800 
                                @else bg-gray-100 text-gray-800 @endif">
                                <i class="fas fa-{{ $activity->status == 'scheduled' ? 'clock' : ($activity->status == 'ongoing' ? 'circle-notch' : ($activity->status == 'completed' ? 'check-circle' : 'info-circle')) }} mr-1"></i>
                                {{ ucfirst($activity->status ?? 'scheduled') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @else
        <div class="p-6 text-center">
            <div class="mx-auto h-12 w-12 text-gray-400">
                <i class="fas fa-calendar-times fa-2x"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aktivitas mendatang</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada aktivitas yang dijadwalkan dalam 7 hari ke depan.</p>
            <div class="mt-6">
                <a href="{{ route('bas.activities.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Aktivitas
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Activities Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h2>
                <p class="text-sm text-gray-500 mt-1">Aktivitas yang baru saja ditambahkan atau diperbarui</p>
            </div>
            <a href="{{ route('bas.activities.index') }}" class="text-blue-600 font-medium text-sm hover:underline mt-2 sm:mt-0 flex items-center">
                Lihat semua <i class="fas fa-external-link-alt ml-1 text-xs"></i>
            </a>
        </div>
        
        @if(count($recentActivities) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Aktivitas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentActivities as $activity)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->name }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->department->name ?? 'Tidak ada departemen' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->city }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->province }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}</div>
                            <div class="text-sm text-gray-500 text-center">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->activity_type ?: 'Umum' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 line-clamp-2">{{ $activity->description ?? 'Tidak ada deskripsi' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('bas.activities.edit', $activity->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('bas.activities.destroy', $activity->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @else
        <div class="p-6 text-center">
            <div class="mx-auto h-12 w-12 text-gray-400">
                <i class="fas fa-clipboard-list fa-2x"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aktivitas terbaru</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada aktivitas yang tercatat baru-baru ini.</p>
            <div class="mt-6">
                <a href="{{ route('bas.activities.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Aktivitas
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dismiss alert function
    function dismissAlert(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Auto-hide alert after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 1s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 1000);
            }, 5000);
        });
    });
</script>
@endpush 