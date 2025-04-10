@extends('admin_bas.layout')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col h-full">
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Dashboard Admin BAS</h1>
        <div class="flex gap-2">
            <a href="{{ route('bas.activities.create') }}" class="inline-flex items-center justify-center text-white bg-bas hover:bg-opacity-90 py-2 px-4 rounded-md font-semibold text-sm">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Aktivitas
            </a>
            <a href="{{ route('bas.activities.calendar') }}" class="inline-flex items-center justify-center text-bas bg-secondary hover:bg-opacity-90 py-2 px-4 rounded-md font-semibold text-sm">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Lihat Kalender
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Aktivitas -->
        <div class="bg-white p-5 rounded-lg shadow-sm border border-border">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Aktivitas</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $totalActivities ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 rounded-full p-3 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('bas.activities.index') }}" class="text-bas text-sm font-medium hover:underline">Lihat semua</a>
        </div>

        <!-- Card 2: Aktivitas Hari Ini -->
        <div class="bg-white p-5 rounded-lg shadow-sm border border-border">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Aktivitas Hari Ini</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $todayActivities }}</h3>
                </div>
                <div class="bg-green-100 rounded-full p-3 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('bas.activities.index') }}?date={{ date('Y-m-d') }}" class="text-bas text-sm font-medium hover:underline">Lihat detail</a>
        </div>

        <!-- Card 3: Aktivitas Minggu Ini -->
        <div class="bg-white p-5 rounded-lg shadow-sm border border-border">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Aktivitas Minggu Ini</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $weekActivities }}</h3>
                </div>
                <div class="bg-purple-100 rounded-full p-3 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('bas.activities.index') }}?week=current" class="text-bas text-sm font-medium hover:underline">Lihat detail</a>
        </div>

        <!-- Card 4: Aktivitas Bulan Ini -->
        <div class="bg-white p-5 rounded-lg shadow-sm border border-border">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Aktivitas Bulan Ini</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $monthActivities }}</h3>
                </div>
                <div class="bg-orange-100 rounded-full p-3 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('bas.activities.index') }}?month={{ date('Y-m') }}" class="text-bas text-sm font-medium hover:underline">Lihat detail</a>
        </div>
    </div>

    <!-- Upcoming Activities Section -->
    <div class="bg-white rounded-lg shadow-sm border border-border p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Aktivitas Mendatang</h2>
            <a href="{{ route('bas.activities.index') }}" class="text-bas font-medium text-sm">Lihat semua</a>
        </div>
        
        @if(count($upcomingActivities) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembuat</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($upcomingActivities as $activity)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $activity->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->city }}, {{ $activity->province }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($activity->start_datetime)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($activity->end_datetime)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($activity->status == 'scheduled') bg-blue-100 text-blue-800 
                                @elseif($activity->status == 'ongoing') bg-red-100 text-red-800 
                                @elseif($activity->status == 'completed') bg-green-100 text-green-800 
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($activity->status ?? 'scheduled') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('bas.activities.edit', $activity->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('bas.activities.destroy', $activity->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="bg-gray-50 p-4 text-center rounded">
            <p class="text-gray-500">Tidak ada aktivitas mendatang dalam 7 hari ke depan.</p>
        </div>
        @endif
    </div>

    <!-- Recent Activities Section -->
    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Aktivitas Terbaru</h2>
            <a href="{{ route('bas.activities.index') }}" class="text-bas font-medium text-sm">Lihat semua</a>
        </div>
        
        @if(count($recentActivities) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembuat</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentActivities as $activity)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $activity->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->city }}, {{ $activity->province }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($activity->start_datetime)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($activity->end_datetime)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($activity->status == 'scheduled') bg-blue-100 text-blue-800 
                                @elseif($activity->status == 'ongoing') bg-red-100 text-red-800 
                                @elseif($activity->status == 'completed') bg-green-100 text-green-800 
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($activity->status ?? 'scheduled') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('bas.activities.edit', $activity->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('bas.activities.destroy', $activity->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="bg-gray-50 p-4 text-center rounded">
            <p class="text-gray-500">Belum ada aktivitas terbaru.</p>
        </div>
        @endif
    </div>
</div>
@endsection 