@php
    $layout = isset($isSuperAdmin) && $isSuperAdmin ? 'superadmin.layout' : 'admin.layout';
@endphp

@extends($layout)

@section('title', 'Manage Bookings')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Manage Bookings</h1>
        <a href="{{ route('admin.bookings.export') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#24448c] text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#1c3670] transition-colors">
            Export CSV
        </a>
    </div>

    <div class="flex flex-col gap-6 bg-white rounded-lg p-6">
        <!-- Search Bar -->
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="search" id="searchBookings" class="block w-full p-3 ps-10 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary" placeholder="Search bookings">
        </div>

        <!-- Filter Tabs -->
        <div class="flex mb-4">
            <button type="button" id="btnAllBookings" class="filter-btn active px-4 py-2 rounded-full bg-[#24448c] text-white font-medium mr-2 hover:bg-[#1c3670] transition-colors">All</button>
            <button type="button" id="btnTodayBookings" class="filter-btn px-4 py-2 rounded-full bg-gray-100 text-dark font-medium mr-2 hover:bg-gray-200 transition-colors">Today</button>
            <button type="button" id="btnWeekBookings" class="filter-btn px-4 py-2 rounded-full bg-gray-100 text-dark font-medium mr-2 hover:bg-gray-200 transition-colors">This Week</button>
            <button type="button" id="btnMonthBookings" class="filter-btn px-4 py-2 rounded-full bg-gray-100 text-dark font-medium hover:bg-gray-200 transition-colors">This Month</button>
        </div>

        <!-- Bookings Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-sm text-dark">
                    <tr class="border-b border-border">
                        <th scope="col" class="px-4 py-3">Room</th>
                        <th scope="col" class="px-4 py-3">Time</th>
                        <th scope="col" class="px-4 py-3">Organizer</th>
                        <th scope="col" class="px-4 py-3">Description</th>
                        <th scope="col" class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    @foreach($bookings as $booking)
                    <tr class="border-b border-border booking-row" 
                        data-date="{{ $booking->date }}" 
                        data-room="{{ $booking->meetingRoom->name ?? '' }}" 
                        data-organizer="{{ $booking->nama }}">
                        <td class="px-4 py-4">{{ $booking->meetingRoom->name ?? 'N/A' }}</td>
                        <td class="px-4 py-4">
                            {{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }} <br>
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        </td>
                        <td class="px-4 py-4">{{ $booking->nama }}</td>
                        <td class="px-4 py-4">
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
                            
                            @if(count($attendees) > 0)
                                @foreach($attendees as $attendee)
                                    {{ $attendee }}<br>
                                @endforeach
                                @if(count($attendees) > 3)
                                    <span class="text-gray-500">+{{ count($attendees) - 3 }} more</span>
                                @endif
                            @else
                                {{ $booking->booking_type == 'internal' ? 'Internal Meeting' : 'External: ' . $booking->external_description }}
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="text-accent hover:underline mr-2">Edit</a>
                            <button type="button" class="delete-booking text-danger hover:underline" data-id="{{ $booking->id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium text-dark">{{ $bookings->firstItem() ?? 0 }}</span> to 
                <span class="font-medium text-dark">{{ $bookings->lastItem() ?? 0 }}</span> of 
                <span class="font-medium text-dark">{{ $bookings->total() ?? 0 }}</span> 
                <span class="text-gray-500">results</span>
            </div>
            
            <div class="flex items-center space-x-1">
                @if ($bookings->onFirstPage())
                    <span class="px-3 py-1 border border-gray-300 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $bookings->previousPageUrl() }}" class="px-3 py-1 border border-border rounded-md hover:bg-secondary text-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
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
                    <a href="{{ $bookings->url(1) }}" class="px-3 py-1 border border-border rounded-md hover:bg-secondary text-dark">1</a>
                    @if ($startPage > 2)
                        <span class="px-2 text-gray-500">...</span>
                    @endif
                @endif

                @for ($i = $startPage; $i <= $endPage; $i++)
                    @if ($i == $bookings->currentPage())
                        <span class="px-3 py-1 border border-[#24448c] bg-[#24448c] text-white rounded-md">{{ $i }}</span>
                    @else
                        <a href="{{ $bookings->url($i) }}" class="px-3 py-1 border border-border rounded-md hover:bg-gray-100 text-dark">{{ $i }}</a>
                    @endif
                @endfor

                @if ($endPage < $bookings->lastPage())
                    @if ($endPage < $bookings->lastPage() - 1)
                        <span class="px-2 text-gray-500">...</span>
                    @endif
                    <a href="{{ $bookings->url($bookings->lastPage()) }}" class="px-3 py-1 border border-border rounded-md hover:bg-secondary text-dark">{{ $bookings->lastPage() }}</a>
                @endif

                @if ($bookings->hasMorePages())
                    <a href="{{ $bookings->nextPageUrl() }}" class="px-3 py-1 border border-border rounded-md hover:bg-secondary text-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="px-3 py-1 border border-gray-300 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal (Hidden by default) -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this booking? This action cannot be undone.</p>
        <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Cancel</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-[#24448c] text-white rounded-lg hover:bg-[#1c3670] transition-colors">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter logic
        const filterButtons = document.querySelectorAll('.filter-btn');
        const bookingRows = document.querySelectorAll('.booking-row');
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Calculate date ranges
        const thisWeekStart = new Date(today);
        thisWeekStart.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
        
        const thisWeekEnd = new Date(thisWeekStart);
        thisWeekEnd.setDate(thisWeekStart.getDate() + 6); // End of week (Saturday)
        
        const thisMonthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        const thisMonthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        // Set active filter
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-[#24448c]', 'text-white');
                    btn.classList.add('bg-gray-100', 'text-dark');
                });
                
                // Add active class to clicked button
                this.classList.add('active', 'bg-[#24448c]', 'text-white');
                this.classList.remove('bg-gray-100', 'text-dark');
                
                // Filter bookings based on button id
                const filterId = this.id;
                
                bookingRows.forEach(row => {
                    const bookingDate = new Date(row.dataset.date);
                    bookingDate.setHours(0, 0, 0, 0);
                    
                    switch(filterId) {
                        case 'btnAllBookings':
                            row.classList.remove('hidden');
                            break;
                        case 'btnTodayBookings':
                            if (bookingDate.toDateString() === today.toDateString()) {
                                row.classList.remove('hidden');
                            } else {
                                row.classList.add('hidden');
                            }
                            break;
                        case 'btnWeekBookings':
                            if (bookingDate >= thisWeekStart && bookingDate <= thisWeekEnd) {
                                row.classList.remove('hidden');
                            } else {
                                row.classList.add('hidden');
                            }
                            break;
                        case 'btnMonthBookings':
                            if (bookingDate >= thisMonthStart && bookingDate <= thisMonthEnd) {
                                row.classList.remove('hidden');
                            } else {
                                row.classList.add('hidden');
                            }
                            break;
                    }
                });
            });
        });

        // Search functionality
        const searchInput = document.getElementById('searchBookings');
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            
            bookingRows.forEach(row => {
                const roomName = row.dataset.room.toLowerCase();
                const organizer = row.dataset.organizer.toLowerCase();
                const rowContent = row.textContent.toLowerCase();
                
                if (roomName.includes(searchText) || 
                    organizer.includes(searchText) || 
                    rowContent.includes(searchText)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });

        // Delete booking functionality
        const deleteButtons = document.querySelectorAll('.delete-booking');
        const deleteModal = document.getElementById('deleteModal');
        const cancelDelete = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const bookingId = this.dataset.id;
                deleteForm.action = `{{ route('admin.bookings.delete', '') }}/${bookingId}`;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });
        
        cancelDelete.addEventListener('click', function() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });
    });
</script>
@endpush 