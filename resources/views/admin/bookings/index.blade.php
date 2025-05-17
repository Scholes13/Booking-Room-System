@php
    $layout = isset($isSuperAdmin) && $isSuperAdmin ? 'superadmin.layout' : 'admin.layout';
@endphp

@extends($layout)

@section('title', 'Manage Bookings')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Manage Bookings</h1>
            <p class="text-gray-500 mt-1">View and manage all room booking schedules</p>
        </div>
        <a href="{{ route('admin.bookings.export') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-[#24448c] text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#1c3670] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export CSV
        </a>
    </div>

    <div class="flex flex-col gap-6 bg-white rounded-lg p-6 shadow-sm">
        <!-- Search Bar -->
        <div class="relative w-full md:w-1/2">
            <form action="{{ route('admin.bookings.index') }}" method="GET">
                <!-- Preserve filter parameters -->
                @if (request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                @if (request('date'))
                <input type="hidden" name="date" value="{{ request('date') }}">
                @endif
                
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" id="searchBookings" name="search" value="{{ request('search') }}" class="block w-full p-3 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" placeholder="Search by room, organizer, or description...">
            </form>
        </div>

        <!-- Filter and Date picker Container -->
        <div class="flex flex-col lg:flex-row gap-4 items-center">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap mb-0 lg:mb-4 gap-2">
                <a href="{{ route('admin.bookings.index') }}" class="filter-btn {{ !request('filter') ? 'active bg-[#24448c] text-white' : 'bg-gray-100 text-dark' }} px-4 py-2 rounded-lg font-medium hover:bg-[#1c3670] transition-colors shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    All
                </a>
                <a href="{{ route('admin.bookings.index', ['filter' => 'today']) }}" class="filter-btn {{ request('filter') == 'today' ? 'active bg-[#24448c] text-white' : 'bg-gray-100 text-dark' }} px-4 py-2 rounded-lg font-medium hover:bg-[#1c3670] transition-colors shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    Today
                </a>
                <a href="{{ route('admin.bookings.index', ['filter' => 'week']) }}" class="filter-btn {{ request('filter') == 'week' ? 'active bg-[#24448c] text-white' : 'bg-gray-100 text-dark' }} px-4 py-2 rounded-lg font-medium hover:bg-[#1c3670] transition-colors shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    This Week
                </a>
                <a href="{{ route('admin.bookings.index', ['filter' => 'month']) }}" class="filter-btn {{ request('filter') == 'month' ? 'active bg-[#24448c] text-white' : 'bg-gray-100 text-dark' }} px-4 py-2 rounded-lg font-medium hover:bg-[#1c3670] transition-colors shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    This Month
                </a>
            </div>

            <!-- Date Picker -->
            <div class="relative ml-auto">
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="inline">
                    <!-- Preserve search parameter if exists -->
                    @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input type="text" id="dateFilter" name="date" value="{{ request('date') }}" class="block w-full md:w-52 p-2.5 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] shadow-sm" placeholder="Select date">
                    <input type="submit" class="hidden">
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Room</th>
                        <th scope="col" class="px-5 py-3.5">Time</th>
                        <th scope="col" class="px-5 py-3.5">Organizer</th>
                        <th scope="col" class="px-5 py-3.5">Description</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    @foreach($bookings as $booking)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 booking-row" 
                        data-date="{{ $booking->date }}" 
                        data-room="{{ $booking->meetingRoom->name ?? '' }}" 
                        data-organizer="{{ $booking->nama }}">
                        <td class="px-5 py-4 font-medium">
                            {{ $booking->meetingRoom->name ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</span>
                                <span class="text-gray-500 text-xs mt-1">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            {{ $booking->nama }}
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $description = $booking->description;
                                $attendees = [];
                                
                                if (!empty($description)) {
                                    // Ekstrak nama dari deskripsi (asumsi format tertentu)
                                    $matches = [];
                                    preg_match_all('/([A-Za-z\s]+)(?:,|$)/', $description, $matches);
                                    if (!empty($matches[1])) {
                                        $attendees = array_slice($matches[1], 0, 3); // Max 3 attendees
                                    }
                                }
                            @endphp
                            
                            <div class="max-w-xs">
                                @if(count($attendees) > 0)
                                    <div class="text-gray-500 text-xs">Attendees:</div>
                                    <div class="mt-1">
                                        @foreach($attendees as $attendee)
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-xs rounded-md mb-1 mr-1">{{ $attendee }}</span>
                                        @endforeach
                                        @if(count($attendees) > 3)
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-xs rounded-md">+{{ count($attendees) - 3 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    @if($booking->booking_type == 'internal')
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-md font-medium">Internal Meeting</span>
                                    @else
                                        <div>
                                            <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-md font-medium">External</span>
                                            <p class="text-gray-600 text-xs mt-1">{{ $booking->external_description }}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" class="edit-booking px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center" 
                                    data-id="{{ $booking->id }}"
                                    data-name="{{ $booking->nama }}"
                                    data-department="{{ $booking->department }}"
                                    data-room="{{ $booking->meeting_room_id }}"
                                    data-date="{{ $booking->date }}"
                                    data-start="{{ substr($booking->start_time, 0, 5) }}"
                                    data-end="{{ substr($booking->end_time, 0, 5) }}"
                                    data-description="{{ $booking->description }}"
                                    data-booking-type="{{ $booking->booking_type }}"
                                    data-external-description="{{ $booking->external_description }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" class="delete-booking px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" data-id="{{ $booking->id }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- No Data Found Message -->
                    <tr id="noDataRow" class="{{ count($bookings) > 0 ? 'hidden' : '' }}">
                        <td colspan="5" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                                <p class="text-gray-500 text-sm">Try changing your search criteria or filter settings</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center mt-6 gap-4">
            <div class="text-sm text-gray-600 flex items-center bg-gray-50 px-4 py-2 rounded-lg">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                </svg>
                Showing <span class="font-medium text-dark px-1">{{ $bookings->firstItem() ?? 0 }}</span> to 
                <span class="font-medium text-dark px-1">{{ $bookings->lastItem() ?? 0 }}</span> of 
                <span class="font-medium text-dark px-1">{{ $bookings->total() ?? 0 }}</span> 
                entries
            </div>
            
            <div class="flex items-center gap-1">
                @if ($bookings->onFirstPage())
                    <span class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $bookings->appends(request()->except('page'))->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                @php
                    $startPage = max($bookings->currentPage() - 2, 1);
                    $endPage = min($startPage + 4, $bookings->lastPage());
                    
                    if ($endPage - $startPage < 4 && $startPage > 1) {
                        $startPage = max($endPage - 4, 1);
                    }
                @endphp
                
                @if ($startPage > 1)
                    <a href="{{ $bookings->appends(request()->except('page'))->url(1) }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">1</a>
                    @if ($startPage > 2)
                        <span class="text-gray-500 mx-1">...</span>
                    @endif
                @endif

                @for ($i = $startPage; $i <= $endPage; $i++)
                    @if ($i == $bookings->currentPage())
                        <span class="w-9 h-9 flex items-center justify-center border border-[#24448c] bg-[#24448c] text-white rounded-lg">{{ $i }}</span>
                    @else
                        <a href="{{ $bookings->appends(request()->except('page'))->url($i) }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">{{ $i }}</a>
                    @endif
                @endfor

                @if ($endPage < $bookings->lastPage())
                    @if ($endPage < $bookings->lastPage() - 1)
                        <span class="text-gray-500 mx-1">...</span>
                    @endif
                    <a href="{{ $bookings->appends(request()->except('page'))->url($bookings->lastPage()) }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-800">{{ $bookings->lastPage() }}</a>
                @endif

                @if ($bookings->hasMorePages())
                    <a href="{{ $bookings->appends(request()->except('page'))->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600">
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

<!-- Edit Booking Modal -->
<div id="editBookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-4xl w-full mx-4 transform transition-transform duration-300 scale-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Edit Booking</h3>
            <button type="button" id="closeEditBookingModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="editBookingErrors" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <ul id="editBookingErrorsList" class="text-red-700"></ul>
            </div>
        </div>
        
        <form id="editBookingForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <select name="nama" 
                        id="edit_employee_select"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    <option value="">Pilih Karyawan</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->name }}" 
                                data-department="{{ $employee->department->name }}">
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Departemen -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                <select name="department" 
                        id="edit_department_select"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    @foreach($departments as $department)
                        <option value="{{ $department->name }}">
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ruang Meeting -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ruang Meeting</label>
                <select name="meeting_room_id" 
                        id="edit_meeting_room_id"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    @foreach($meetingRooms as $room)
                        <option value="{{ $room->id }}">
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" 
                       name="date" 
                       id="edit_date"
                       class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                       required>
            </div>

            <!-- Waktu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                    <input type="time" 
                           name="start_time" 
                           id="edit_start_time"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                    <input type="time" 
                           name="end_time" 
                           id="edit_end_time"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" 
                          id="edit_description"
                          rows="3"
                          class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"></textarea>
            </div>

            <!-- Booking Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="booking_type" 
                        id="edit_booking_type"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    <option value="internal">Internal</option>
                    <option value="external">Eksternal</option>
                </select>
            </div>

            <!-- External Description -->
            <div id="edit_external_description_container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Eksternal</label>
                <textarea name="external_description" 
                          id="edit_external_description"
                          rows="3"
                          class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                          placeholder="Silakan isi detail terkait booking eksternal..."></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3 pt-4">
                <button type="button"
                        id="cancelEditBooking"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2.5 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors focus:outline-none focus:ring-2 focus:ring-[#24448c]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal (Hidden by default) -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="flex justify-center mb-4 text-red-500">
            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-center mb-2">Confirm Deletion</h3>
        <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this booking? This action cannot be undone.</p>
        <div class="flex justify-center gap-3">
            <button id="cancelDelete" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                Cancel
            </button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded and parsed');
        
        // Initialize flatpickr
        try {
            // Check if flatpickr is available
            if (typeof flatpickr === 'undefined') {
                console.error('Flatpickr library is not loaded');
                return;
            }
            
            console.log('Initializing flatpickr...');
            
            // Get the dateFilter element
            const dateFilterElement = document.getElementById('dateFilter');
            if (!dateFilterElement) {
                console.error('Date filter element not found');
                return;
            }
            
            // Initialize flatpickr
            const datePicker = flatpickr("#dateFilter", {
                dateFormat: "Y-m-d",
                locale: 'id',
                altInput: true,
                altFormat: "d/m/Y",
                placeholder: "Select date",
                disableMobile: true,
                position: "below",
                onChange: function(selectedDates, dateStr, instance) {
                    // Get the form and submit it
                    const form = instance.input.closest('form');
                    if (form && dateStr) {
                        form.submit();
                    }
                }
            });
            
            // Check for existing date parameter
            const currentDate = "{{ request('date') }}";
            if (currentDate) {
                datePicker.setDate(currentDate);
            }
            
            console.log('Flatpickr initialized successfully');
            
            // Search functionality
            const searchInput = document.getElementById('searchBookings');
            if (searchInput) {
                console.log('Search input found');
                
                // Get the parent form
                const searchForm = searchInput.closest('form');
                
                let searchTimeout;
                searchInput.addEventListener('keyup', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        searchForm.submit();
                    }, 500); // 500ms delay before submitting
                });
            } else {
                console.error('Search input not found');
            }
            
            // Edit booking functionality
            const editButtons = document.querySelectorAll('.edit-booking');
            const editBookingModal = document.getElementById('editBookingModal');
            const closeEditBookingModal = document.getElementById('closeEditBookingModal');
            const cancelEditBooking = document.getElementById('cancelEditBooking');
            const editBookingForm = document.getElementById('editBookingForm');
            
            let currentBookingId = null;
            
            if (editButtons && editButtons.length > 0 && editBookingModal && closeEditBookingModal && cancelEditBooking && editBookingForm) {
                console.log('Edit functionality elements found');
                
                editButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault(); // Prevent any default action
                        console.log('Edit button clicked');
                        
                        // Get booking data from button attributes
                        currentBookingId = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');
                        const department = this.getAttribute('data-department');
                        const roomId = this.getAttribute('data-room');
                        const date = this.getAttribute('data-date');
                        const startTime = this.getAttribute('data-start');
                        const endTime = this.getAttribute('data-end');
                        const description = this.getAttribute('data-description');
                        const bookingType = this.getAttribute('data-booking-type');
                        const externalDescription = this.getAttribute('data-external-description');
                        
                        // Set form action
                        editBookingForm.action = "{{ route('admin.bookings.update', '') }}/" + currentBookingId;
                        
                        // Fill form fields
                        // Select employee in dropdown
                        const employeeSelect = document.getElementById('edit_employee_select');
                        for (let i = 0; i < employeeSelect.options.length; i++) {
                            if (employeeSelect.options[i].value === name) {
                                employeeSelect.selectedIndex = i;
                                break;
                            }
                        }
                        
                        // Select department in dropdown
                        const departmentSelect = document.getElementById('edit_department_select');
                        for (let i = 0; i < departmentSelect.options.length; i++) {
                            if (departmentSelect.options[i].value === department) {
                                departmentSelect.selectedIndex = i;
                                break;
                            }
                        }
                        
                        // Select meeting room in dropdown
                        const roomSelect = document.getElementById('edit_meeting_room_id');
                        for (let i = 0; i < roomSelect.options.length; i++) {
                            if (roomSelect.options[i].value == roomId) {
                                roomSelect.selectedIndex = i;
                                break;
                            }
                        }
                        
                        // Set other form values
                        document.getElementById('edit_date').value = date;
                        document.getElementById('edit_start_time').value = startTime;
                        document.getElementById('edit_end_time').value = endTime;
                        document.getElementById('edit_description').value = description || '';
                        
                        // Set booking type and handle external description visibility
                        const bookingTypeSelect = document.getElementById('edit_booking_type');
                        const externalDescContainer = document.getElementById('edit_external_description_container');
                        const externalDescTextarea = document.getElementById('edit_external_description');
                        
                        // Select booking type in dropdown
                        for (let i = 0; i < bookingTypeSelect.options.length; i++) {
                            if (bookingTypeSelect.options[i].value === bookingType) {
                                bookingTypeSelect.selectedIndex = i;
                                break;
                            }
                        }
                        
                        // Show/hide external description based on booking type
                        if (bookingType === 'external') {
                            externalDescContainer.classList.remove('hidden');
                            externalDescTextarea.setAttribute('required', true);
                            externalDescTextarea.value = externalDescription || '';
                        } else {
                            externalDescContainer.classList.add('hidden');
                            externalDescTextarea.removeAttribute('required');
                        }
                        
                        // Show modal
                        editBookingModal.classList.remove('hidden');
                        editBookingModal.classList.add('flex');
                    });
                });
                
                // Close modal events
                closeEditBookingModal.addEventListener('click', closeEditModal);
                cancelEditBooking.addEventListener('click', closeEditModal);
                
                function closeEditModal() {
                    editBookingModal.classList.add('hidden');
                    editBookingModal.classList.remove('flex');
                    
                    // Clear form errors
                    const errorsDiv = document.getElementById('editBookingErrors');
                    errorsDiv.classList.add('hidden');
                    document.getElementById('editBookingErrorsList').innerHTML = '';
                    
                    // Reset borders
                    document.getElementById('edit_start_time').style.borderColor = '';
                    document.getElementById('edit_end_time').style.borderColor = '';
                }
                
                // Handle booking type change to show/hide external description
                const bookingTypeSelect = document.getElementById('edit_booking_type');
                const externalDescContainer = document.getElementById('edit_external_description_container');
                const externalDescTextarea = document.getElementById('edit_external_description');
                
                bookingTypeSelect.addEventListener('change', function() {
                    if (this.value === 'external') {
                        externalDescContainer.classList.remove('hidden');
                        externalDescTextarea.setAttribute('required', true);
                    } else {
                        externalDescContainer.classList.add('hidden');
                        externalDescTextarea.removeAttribute('required');
                    }
                });
                
                // Auto-populate department when employee is selected
                const employeeSelect = document.getElementById('edit_employee_select');
                const departmentSelect = document.getElementById('edit_department_select');

                employeeSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.department) {
                        // Find and select the matching department option
                        for (let i = 0; i < departmentSelect.options.length; i++) {
                            if (departmentSelect.options[i].value === selectedOption.dataset.department) {
                                departmentSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                });
                
                // Availability checker for edit form
                const dateInput = document.getElementById('edit_date');
                const roomSelect = document.getElementById('edit_meeting_room_id');
                const startTimeInput = document.getElementById('edit_start_time');
                const endTimeInput = document.getElementById('edit_end_time');
                const submitButton = editBookingForm.querySelector('button[type="submit"]');
                let timeoutId;
                
                // Function to check availability
                async function checkAvailability() {
                    if (!dateInput.value || !roomSelect.value || !startTimeInput.value || !endTimeInput.value) return;

                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(async () => {
                        try {
                            const response = await fetch(`/admin/bookings/available-times?date=${dateInput.value}&meeting_room_id=${roomSelect.value}`);
                            if (!response.ok) throw new Error('Network response was not ok');
                            const bookings = await response.json();

                            // Reset style input waktu dan button
                            startTimeInput.style.borderColor = '';
                            endTimeInput.style.borderColor = '';
                            submitButton.disabled = false;
                            
                            // Cek setiap booking yang ada
                            const currentStart = startTimeInput.value;
                            const currentEnd = endTimeInput.value;
                            
                            let hasConflict = false;
                            
                            bookings.forEach(booking => {
                                // Skip checking for the booking being edited
                                if (booking.id && booking.id == currentBookingId) return;
                                
                                const bookingStart = booking.start;
                                const bookingEnd = booking.end;
                                
                                if (currentStart < bookingEnd && currentEnd > bookingStart) {
                                    hasConflict = true;
                                }
                            });
                            
                            if (hasConflict) {
                                startTimeInput.style.borderColor = '#EF4444'; // red-500
                                endTimeInput.style.borderColor = '#EF4444';
                                submitButton.disabled = true;
                                
                                // Show error message
                                showError('The selected time conflicts with an existing booking.');
                            } else {
                                hideError();
                            }
                        } catch (error) {
                            console.error('Error checking availability:', error);
                        }
                    }, 300);
                }

                // Function to show error message
                function showError(message) {
                    let errorDiv = document.getElementById('time-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.id = 'time-error';
                        errorDiv.className = 'mt-2 text-red-600 text-sm';
                        endTimeInput.parentNode.appendChild(errorDiv);
                    }
                    errorDiv.textContent = message;
                }

                // Function to hide error message
                function hideError() {
                    const errorDiv = document.getElementById('time-error');
                    if (errorDiv) errorDiv.remove();
                }
                
                // Add event listeners for availability checking
                dateInput.addEventListener('change', checkAvailability);
                roomSelect.addEventListener('change', checkAvailability);
                startTimeInput.addEventListener('change', checkAvailability);
                endTimeInput.addEventListener('change', checkAvailability);
                
                // Form submission handler with validation
                editBookingForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Check for booking type and external description
                    const bookingType = document.getElementById('edit_booking_type').value;
                    const externalDesc = document.getElementById('edit_external_description').value;
                    
                    if (bookingType === 'external' && !externalDesc) {
                        const errorsDiv = document.getElementById('editBookingErrors');
                        const errorsList = document.getElementById('editBookingErrorsList');
                        
                        errorsDiv.classList.remove('hidden');
                        errorsList.innerHTML = '<li>External description is required for external bookings.</li>';
                        return;
                    }
                    
                    // Submit the form
                    this.submit();
                });
            } else {
                console.error('Edit functionality elements not found properly');
            }
            
            // Delete booking functionality
            const deleteButtons = document.querySelectorAll('.delete-booking');
            const deleteModal = document.getElementById('deleteModal');
            const cancelDelete = document.getElementById('cancelDelete');
            const deleteForm = document.getElementById('deleteForm');
            
            if (deleteButtons && deleteButtons.length > 0 && deleteModal && cancelDelete && deleteForm) {
                console.log('Delete functionality elements found');
                
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault(); // Prevent any default action
                        console.log('Delete button clicked');
                        
                        const bookingId = this.getAttribute('data-id');
                        console.log('Delete clicked for booking ID:', bookingId);
                        
                        deleteForm.action = `{{ route('admin.bookings.delete', '') }}/${bookingId}`;
                        deleteModal.classList.remove('hidden');
                        deleteModal.classList.add('flex');
                    });
                });
                
                cancelDelete.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Delete cancelled');
                    deleteModal.classList.add('hidden');
                    deleteModal.classList.remove('flex');
                });
            } else {
                console.error('Delete functionality elements not found properly');
            }
            
        } catch (error) {
            console.error('Error in bookings page JavaScript:', error);
        }
    });
</script>
@endpush