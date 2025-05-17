@extends('admin_bas.layout')

@section('title', 'Activities')

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

    /* Status badges */
    .badge {
        @apply inline-flex text-xs font-semibold px-2.5 py-0.5 rounded-full;
    }
    
    .badge-blue {
        @apply bg-blue-100 text-blue-800;
    }
    
    .badge-green {
        @apply bg-green-100 text-green-800;
    }
    
    .badge-indigo {
        @apply bg-indigo-100 text-indigo-800;
    }
    
    .badge-red {
        @apply bg-red-100 text-red-800;
    }
    
    .badge-gray {
        @apply bg-gray-100 text-gray-800;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Activities</h1>
            <p class="text-gray-500 mt-1">View and manage all activities</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('bas.activities.create') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-[#24448c] text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#1c3670] transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Activity
            </a>
            <a href="{{ route('bas.activities.calendar') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-gray-100 text-gray-700 gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-gray-200 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                View Calendar
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="flex flex-col gap-6 bg-white rounded-lg p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-700">Filter Activities</h2>
        <form action="{{ route('bas.activities.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" name="search" id="search" value="{{ request()->search }}" class="block w-full p-3 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" placeholder="Search activities...">
                </div>
            </div>
            
            <div>
                <select name="status" id="status" class="block w-full p-3 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request()->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ request()->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request()->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request()->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input type="date" name="start_date" id="start_date" value="{{ request()->start_date }}" class="block w-full p-3 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" placeholder="Start date">
                </div>
            </div>
            
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input type="date" name="end_date" id="end_date" value="{{ request()->end_date }}" class="block w-full p-3 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" placeholder="End date">
                </div>
            </div>
            
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-4">
                <button type="submit" class="px-5 py-2.5 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#24448c] focus:ring-opacity-50 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('bas.activities.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-opacity-50 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Activities Table -->
    <div class="flex flex-col gap-6 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-700">Activity List</h2>
            <div class="text-sm text-gray-500">Total: {{ $activities->total() }} activities</div>
        </div>
        
        <div class="overflow-x-auto rounded-lg scrollbar-thin scrollbar-thumb-gray-300">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Name</th>
                        <th scope="col" class="px-5 py-3.5">Location</th>
                        <th scope="col" class="px-5 py-3.5">Date</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5">Activity Type</th>
                        <th scope="col" class="px-5 py-3.5">Description</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $activity->name }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->department->name ?? '' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $activity->city }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->province }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-900">{{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') : 'N/A' }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('H:i') : '' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $statusClass = 'badge-gray';
                                if($activity->status === 'scheduled') $statusClass = 'badge-blue';
                                elseif($activity->status === 'ongoing') $statusClass = 'badge-green';
                                elseif($activity->status === 'completed') $statusClass = 'badge-indigo';
                                elseif($activity->status === 'cancelled') $statusClass = 'badge-red';
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($activity->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-900">{{ $activity->activity_type ?: 'General' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-500 line-clamp-2">{{ $activity->description ?? 'No description available' }}</div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('bas.activities.edit', $activity->id) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('bas.activities.destroy', $activity->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" onclick="return confirm('Are you sure you want to delete this activity?')">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                                <p class="text-gray-500 text-sm">Try changing your search criteria or adding a new activity</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center px-6 py-4 gap-4">
            <div class="text-sm text-gray-600 flex items-center bg-gray-50 px-4 py-2 rounded-lg">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                </svg>
                Showing <span class="font-medium text-dark px-1">{{ $activities->firstItem() ?? 0 }}</span> to 
                <span class="font-medium text-dark px-1">{{ $activities->lastItem() ?? 0 }}</span> of 
                <span class="font-medium text-dark px-1">{{ $activities->total() ?? 0 }}</span> 
                entries
            </div>
            
            <div class="flex items-center gap-1">
                @if ($activities->onFirstPage())
                    <span class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $activities->appends(request()->except('page'))->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                @php
                    $startPage = max($activities->currentPage() - 2, 1);
                    $endPage = min($startPage + 4, $activities->lastPage());
                    
                    if ($endPage - $startPage < 4 && $startPage > 1) {
                        $startPage = max($endPage - 4, 1);
                    }
                @endphp
                
                @if ($startPage > 1)
                    <a href="{{ $activities->appends(request()->except('page'))->url(1) }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">1</a>
                    @if ($startPage > 2)
                        <span class="text-gray-500 mx-1">...</span>
                    @endif
                @endif

                @for ($i = $startPage; $i <= $endPage; $i++)
                    @if ($i == $activities->currentPage())
                        <span class="w-9 h-9 flex items-center justify-center border border-[#24448c] bg-[#24448c] text-white rounded-lg">{{ $i }}</span>
                    @else
                        <a href="{{ $activities->appends(request()->except('page'))->url($i) }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">{{ $i }}</a>
                    @endif
                @endfor

                @if ($endPage < $activities->lastPage())
                    @if ($endPage < $activities->lastPage() - 1)
                        <span class="text-gray-500 mx-1">...</span>
                    @endif
                    <a href="{{ $activities->appends(request()->except('page'))->url($activities->lastPage()) }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">{{ $activities->lastPage() }}</a>
                @endif

                @if ($activities->hasMorePages())
                    <a href="{{ $activities->appends(request()->except('page'))->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
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