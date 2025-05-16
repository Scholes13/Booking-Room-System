@extends('admin_bas.layout')

@section('title', 'Aktivitas')

@push('styles')
<style>
    /* Scrollbar styling */
    .scrollbar-thin {
        scrollbar-width: thin;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
    
    /* Table improvements */
    @media (max-width: 768px) {
        .responsive-table {
            display: block;
            width: 100%;
        }
        
        .responsive-table thead {
            display: none;
        }
        
        .responsive-table tbody {
            display: block;
            width: 100%;
        }
        
        .responsive-table tr {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        
        .responsive-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .responsive-table td:last-child {
            border-bottom: none;
        }
        
        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            text-align: left;
            color: #4b5563;
            padding-right: 0.5rem;
        }
        
        .responsive-table td.action-cell {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
        
        .responsive-table td.action-cell::before {
            content: none;
        }
    }
    
    /* Card hover effect */
    .hover-card-effect {
        transition: all 0.2s ease-in-out;
    }
    
    .hover-card-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Custom pagination styling to match the image */
    .custom-pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .pagination-info {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .custom-pagination {
        display: flex;
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    
    .custom-pagination li {
        margin: 0 2px;
    }
    
    .custom-pagination li a {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        text-decoration: none;
    }
    
    .custom-pagination li.active a {
        background-color: #1e3a8a;
        color: white;
        border-color: #1e3a8a;
    }
    
    .custom-pagination li a:hover:not(.active) {
        background-color: #f9fafb;
    }
    
    /* Hide default Laravel pagination */
    .hidden-pagination {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col h-full">
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm" role="alert">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold">Aktivitas</h1>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('bas.activities.create') }}" class="inline-flex items-center justify-center text-white bg-bas hover:bg-opacity-90 py-2 px-4 rounded-lg font-semibold text-sm transition duration-200 ease-in-out shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Aktivitas
            </a>
            <a href="{{ route('bas.activities.calendar') }}" class="inline-flex items-center justify-center text-bas bg-secondary hover:bg-opacity-90 py-2 px-4 rounded-lg font-semibold text-sm transition duration-200 ease-in-out shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Lihat Kalender
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-5 rounded-lg shadow-sm border border-border mb-6 hover-card-effect">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Filter Aktivitas</h2>
        <form action="{{ route('bas.activities.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <div class="relative">
                    <input type="text" name="search" id="search" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pl-10" placeholder="Nama aktivitas" value="{{ request()->search }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Semua Status</option>
                    <option value="scheduled" {{ request()->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ request()->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request()->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request()->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <div class="relative">
                    <input type="date" name="start_date" id="start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pl-10" value="{{ request()->start_date }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <div class="relative">
                    <input type="date" name="end_date" id="end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pl-10" value="{{ request()->end_date }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="flex items-end gap-2 lg:col-span-4">
                <button type="submit" class="bg-bas hover:bg-opacity-90 text-white py-2 px-4 rounded-lg shadow-sm hover:shadow transition duration-200 ease-in-out flex-grow">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </span>
                </button>
                <a href="{{ route('bas.activities.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg shadow-sm hover:shadow transition duration-200 ease-in-out flex-grow">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </span>
                </a>
            </div>
        </form>
    </div>

    <!-- Activities Table -->
    <div class="bg-white rounded-lg shadow-sm border border-border overflow-hidden hover-card-effect">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h2 class="text-lg font-semibold text-gray-700">Daftar Aktivitas</h2>
            <div class="text-sm text-gray-500">Total: {{ $activities->total() }} aktivitas</div>
        </div>
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Aktivitas</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->name }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->department->name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 min-w-[180px]">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->city }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->province }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') : 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('H:i') : '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = 'gray';
                                if($activity->status === 'scheduled') $statusColor = 'blue';
                                elseif($activity->status === 'ongoing') $statusColor = 'green';
                                elseif($activity->status === 'completed') $statusColor = 'indigo';
                                elseif($activity->status === 'cancelled') $statusColor = 'red';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                {{ ucfirst($activity->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $activity->activity_type ?: 'Umum' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 line-clamp-2">{{ $activity->description ?? 'Tidak ada deskripsi' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3">
                                <a href="{{ route('bas.activities.edit', $activity->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('bas.activities.destroy', $activity->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?')">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-lg">Tidak ada aktivitas yang ditemukan</p>
                                <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau tambahkan aktivitas baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="custom-pagination-container">
            <div class="pagination-info">
                Showing {{ $activities->firstItem() ?? 0 }} to {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} results
            </div>
            
            <div class="hidden-pagination">
                {{ $activities->links() }}
            </div>
            
            <ul class="custom-pagination">
                <li>
                    <a href="{{ $activities->previousPageUrl() }}" {{ $activities->currentPage() == 1 ? 'tabindex="-1"' : '' }} style="{{ $activities->currentPage() == 1 ? 'color: #d1d5db; cursor: not-allowed;' : '' }}">
                        &lt;
                    </a>
                </li>
                
                @php
                    $startPage = max($activities->currentPage() - 2, 1);
                    $endPage = min($startPage + 4, $activities->lastPage());
                    
                    if ($endPage - $startPage < 4) {
                        $startPage = max($endPage - 4, 1);
                    }
                @endphp
                
                @for ($i = $startPage; $i <= $endPage; $i++)
                    <li class="{{ $activities->currentPage() == $i ? 'active' : '' }}">
                        <a href="{{ $activities->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
                
                <li>
                    <a href="{{ $activities->nextPageUrl() }}" {{ $activities->currentPage() == $activities->lastPage() ? 'tabindex="-1"' : '' }} style="{{ $activities->currentPage() == $activities->lastPage() ? 'color: #d1d5db; cursor: not-allowed;' : '' }}">
                        &gt;
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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