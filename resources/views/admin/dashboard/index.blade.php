@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 pt-6">
    <h1 class="text-3xl font-bold text-gray-800">Booking Dashboard</h1>
    
    <!-- Filter Section -->
    <div class="w-full md:w-auto bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Time Period Filters -->
            <div class="flex items-center space-x-1">
                <button id="filter-today" data-filter="today" class="filter-btn px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    Hari Ini
                </button>
                <button id="filter-week" data-filter="week" class="filter-btn px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    Minggu Ini
                </button>
                <button id="filter-month" data-filter="month" class="filter-btn px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    Bulan Ini
                </button>
            </div>
            
            <!-- Date Picker -->
            <div id="filter-custom" data-filter="custom" class="date-picker-container filter-btn flex-1 min-w-[200px] relative">
                <input type="text" id="date-picker" placeholder="Pilih tanggal" class="w-full pl-3 pr-8 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-calendar-alt absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 px-4">
    <!-- Card 1: Total Bookings -->
    <div class="card highlight-card bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:glow">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-50 p-3 rounded-lg">
                <i class="fas fa-calendar-check text-blue-500 text-xl"></i>
            </div>
            <div class="text-right">
                <span id="bookingTrend" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    <i class="fas fa-equals trend-icon mr-1"></i>
                    <span class="trend-text">Loading...</span>
                </span>
            </div>
        </div>
        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Bookings</h3>
        <p id="totalBookings" class="text-3xl font-bold text-gray-800 mb-2">0</p>
        <div class="flex items-center text-xs text-gray-500">
            <i class="fas fa-info-circle mr-1 text-blue-400"></i>
            <span id="bookingComparison">0 bookings today</span>
        </div>
    </div>
    
    <!-- Card 2: Usage Rate -->
    <div class="card bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-purple-50 p-3 rounded-lg">
                <i class="fas fa-chart-line text-purple-500 text-xl"></i>
            </div>
            <div class="text-right">
                <span id="usageTrend" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    <i class="fas fa-equals trend-icon mr-1"></i>
                    <span class="trend-text">Loading...</span>
                </span>
            </div>
        </div>
        <h3 class="text-sm font-medium text-gray-500 mb-1">Usage Rate</h3>
        <div class="flex items-end">
            <p id="roomUsage" class="text-3xl font-bold text-gray-800 mb-2 mr-2">0%</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                <div id="usageBar" class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" style="width: 0%"></div>
            </div>
        </div>
        <div class="flex items-center text-xs text-gray-500">
            <i class="fas fa-info-circle mr-1 text-purple-400"></i>
            <span id="usageInfo">Loading usage data...</span>
        </div>
    </div>
    
    <!-- Card 3: Most Used Room -->
    <div class="card bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-amber-50 p-3 rounded-lg">
                <i class="fas fa-door-open text-amber-500 text-xl"></i>
            </div>
            <div class="text-right">
                <span id="roomTrend" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    <i class="fas fa-equals trend-neutral mr-1"></i> No Change
                </span>
            </div>
        </div>
        <h3 class="text-sm font-medium text-gray-500 mb-1">Most Used Room</h3>
        <p id="mostUsedRoom" class="text-2xl font-bold text-gray-800 mb-2">Loading...</p>
        <div class="flex items-center justify-between">
            <div class="flex items-center text-xs text-gray-500">
                <i class="fas fa-clock mr-1 text-amber-400"></i>
                <span id="roomUsageHours">0 hours</span>
            </div>
            <div class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded">
                Popular
            </div>
        </div>
    </div>
    
    <!-- Card 4: Top Departments -->
    <div class="card bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-emerald-50 p-3 rounded-lg">
                <i class="fas fa-building text-emerald-500 text-xl"></i>
            </div>
            <div class="text-right">
                <span id="deptTrend" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    <i class="fas fa-equals trend-neutral mr-1"></i> No Change
                </span>
            </div>
        </div>
        <h3 class="text-sm font-medium text-gray-500 mb-1">Top Departments</h3>
        <div id="topDepartments" class="flex flex-wrap gap-2 mb-3">
            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">Loading...</span>
        </div>
        <div class="flex items-center text-xs text-gray-500">
            <i class="fas fa-users mr-1 text-emerald-400"></i>
            <span id="deptInfo">Most active this month</span>
        </div>
    </div>
</div>

<!-- Display Validation Errors -->
@if($errors->any())
    <div class="px-4 mb-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <strong class="font-bold">Oops! Something went wrong.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- Export Button -->
<div class="flex justify-end px-4 mb-4">
    <a id="export-link" href="{{ route('admin.bookings.export') }}" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
        <span class="truncate">Export to Excel</span>
    </a>
</div>

<!-- Booking Table -->
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6 mx-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Booking Details</h2>
        <div class="flex items-center space-x-2">
            <span id="currentFilterDisplay" class="text-sm text-gray-600">Showing: Today's bookings</span>
        </div>
    </div>
    
    <div id="loadingOverlay" class="hidden absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-t-2 border-b-2 border-primary rounded-full animate-spin"></div>
            <p class="text-primary font-medium">Memuat data...</p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked By</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="bookingTableBody" class="bg-white divide-y divide-gray-200">
                <!-- Table body will be populated by AJAX -->
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-500">
            Showing <span id="showingCount">0</span> to <span id="totalCount">0</span> of <span id="totalBookingsCount">0</span> bookings
        </div>
        <div class="flex space-x-2">
            <button id="prevPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50" disabled>Previous</button>
            <button id="nextPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50" disabled>Next</button>
        </div>
    </div>
</div>

<!-- Button Controls -->
<div class="flex justify-stretch px-4 mb-6">
    <div class="flex flex-1 gap-3 flex-wrap justify-end">
        
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 0.8; }
        100% { opacity: 0.6; }
    }
    
    .card {
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .card.is-loading {
        pointer-events: none;
    }
    
    .loading-placeholder {
        height: 1.2em;
        width: 80%;
        background-color: #e2e8f0;
        border-radius: 0.25rem;
        display: inline-block;
        animation: pulse 1.5s infinite ease-in-out;
    }
    
    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.3s; }
    .card:nth-child(4) { animation-delay: 0.4s; }
    
    .trend-up {
        color: #10B981;
    }
    
    .trend-down {
        color: #EF4444;
    }
    
    .trend-neutral {
        color: #6B7280;
    }
    
    .glow {
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
    }
    
    .highlight-card {
        position: relative;
        overflow: hidden;
    }
    
    .highlight-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3B82F6, #8B5CF6);
    }
    
    .date-picker-container {
        position: relative;
    }
    
    .date-picker-container i {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6B7280;
    }
</style>
@endsection

@push('scripts')
<!-- Include our new dashboard booking JavaScript -->
@vite('resources/js/dashboard-booking.js')
@endpush

@push('modals')
<!-- Edit Booking Modal -->
<div id="editBookingModal" class="modal-container fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Edit Booking</h3>
            <button class="modal-close text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
        </div>
        <form id="editBookingForm" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nama_select" class="block text-sm font-medium text-gray-700">Booked By</label>
                    <select id="edit_nama_select" name="nama" class="mt-1 block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" required>
                        <option value="">Select an employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->name }}" data-department="{{ $employee->department->name ?? '' }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_department_select" class="block text-sm font-medium text-gray-700">Department</label>
                    <select id="edit_department_select" name="department" class="mt-1 block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" required>
                         <option value="">Select a department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->name }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label for="edit_meeting_room_id" class="block text-sm font-medium text-gray-700">Meeting Room</label>
                    <select id="edit_meeting_room_id" name="meeting_room_id" class="mt-1 block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" required>
                        <option value="">Select a room</option>
                        @foreach($allMeetingRooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" id="edit_date" name="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="edit_start_time" class="block text-xs text-gray-500 mb-1">Start Time</label>
                            <input type="time" id="edit_start_time" name="start_time" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                        </div>
                        <div>
                            <label for="edit_end_time" class="block text-xs text-gray-500 mb-1">End Time</label>
                            <input type="time" id="edit_end_time" name="end_time" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                        </div>
                    </div>
                </div>
                 <div class="col-span-2">
                    <label for="edit_booking_type" class="block text-sm font-medium text-gray-700">Booking Type</label>
                    <select id="edit_booking_type" name="booking_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="internal">Internal</option>
                        <option value="external">External</option>
                    </select>
                </div>
                <div id="edit_external_description_container" class="col-span-2 hidden">
                    <label for="edit_external_description" class="block text-sm font-medium text-gray-700">External Details</label>
                    <textarea id="edit_external_description" name="external_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div class="col-span-2">
                    <label for="edit_description" class="block text-sm font-medium text-gray-700">Description / Agenda</label>
                    <textarea id="edit_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" class="modal-close bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:opacity-90">Update Booking</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteBookingModal" class="modal-container fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full m-4">
        <div class="flex justify-between items-center pb-3">
             <h3 class="text-lg font-bold">Confirm Deletion</h3>
             <button class="modal-close text-2xl leading-none">&times;</button>
        </div>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this booking? This action cannot be undone.</p>
        <div class="flex justify-end gap-3">
            <button type="button" class="modal-close px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Cancel</button>
            <form id="deleteBookingForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endpush
