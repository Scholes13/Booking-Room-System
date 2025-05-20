@extends('sales_mission.layout')

@section('title', 'Activity Logs')
@section('header', 'Activity Logs')
@section('description', 'View all system activity logs for the Sales Mission module')

@push('styles')
<style>
    .filter-section {
        transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out, margin 0.3s ease-in-out;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        margin: 0;
    }

    .filter-section.expanded {
        max-height: 500px;
        opacity: 1;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    /* Hide the submit button as we'll auto-submit */
    .filter-submit-btn {
        display: none;
    }
    
    /* Tooltip styling */
    .tooltip {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    
    .tooltip:hover::after {
        content: attr(title);
        position: absolute;
        left: 0;
        top: 100%;
        z-index: 1;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        width: 200px;
        white-space: normal;
        word-wrap: break-word;
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900">Activity Logs</h1>
    <div class="flex space-x-2">
        <a href="{{ route('sales_mission.logs.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Export
        </a>
        <button id="toggleFilters" class="bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filter
        </button>
    </div>
</div>

<!-- Filter Form -->
<div id="filterSection" class="filter-section bg-white rounded-lg shadow p-6 mb-6">
    <form id="logsFilterForm" action="{{ route('sales_mission.logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Admin</label>
            <select name="user_id" id="user_id" class="auto-submit-filter w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Semua Admin</option>
                @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ request('user_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
            <select name="action" id="action" class="auto-submit-filter w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Semua Aktivitas</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                class="auto-submit-filter w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50"
                onchange="this.form.submit()">
        </div>

        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                class="auto-submit-filter w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50"
                onchange="this.form.submit()">
        </div>

        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
            <div class="relative">
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari deskripsi aktivitas..."
                    class="auto-submit-filter w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50"
                    onkeyup="handleSearchInput(this)" autocomplete="off">
            </div>
        </div>

        <div class="col-span-full md:col-span-2 lg:col-span-3 flex items-center justify-end space-x-2 mt-4">
            <button type="button" id="resetFilter" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">Reset</button>
            <button type="submit" class="filter-submit-btn bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
    <!-- Responsive Table Container -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Admin
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Waktu
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Aktivitas
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                            Deskripsi
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Aksi
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $log->user->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ $log->ip_address }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-medium rounded-full 
                            @if($log->action == 'create') bg-green-100 text-green-800
                            @elseif($log->action == 'update') bg-amber-100 text-amber-800
                            @elseif($log->action == 'delete') bg-red-100 text-red-800
                            @elseif($log->action == 'export') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            <span class="flex items-center">
                                @if($log->action == 'create')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                @elseif($log->action == 'update')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                @elseif($log->action == 'delete')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                @elseif($log->action == 'export')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                @endif
                                {{ ucfirst($log->action) }}
                            </span>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $log->description }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('sales_mission.logs.show', $log->id) }}" class="text-amber-600 hover:text-amber-900 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-1">Detail</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p>No activity logs found</p>
                        <p class="mt-1 text-sm">Try changing your search criteria</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($logs->hasPages())
    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle filters section
        const filterSection = document.getElementById('filterSection');
        const toggleFilters = document.getElementById('toggleFilters');
        
        // Check if filters are applied and auto-expand
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.toString()) {
            filterSection.classList.add('expanded');
        }
        
        toggleFilters.addEventListener('click', function() {
            filterSection.classList.toggle('expanded');
        });
        
        // Reset filters
        const resetFilter = document.getElementById('resetFilter');
        resetFilter.addEventListener('click', function() {
            window.location.href = "{{ route('sales_mission.logs.index') }}";
        });
        
        // Debounce function for search input
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }
        
        // Handle search input with debounce
        window.handleSearchInput = debounce(function(input) {
            if (input.value.length >= 3 || input.value.length === 0) {
                document.getElementById('logsFilterForm').submit();
            }
        }, 500);
    });
</script>
@endpush 