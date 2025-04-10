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
                <button id="btnToday" class="filter-btn active px-3 py-1 text-sm rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
                    Hari Ini
                </button>
                <button id="btnWeek" class="filter-btn px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200 transition">
                    Minggu Ini
                </button>
                <button id="btnMonth" class="filter-btn px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200 transition">
                    Bulan Ini
                </button>
            </div>
            
            <!-- Date Picker -->
            <div class="date-picker-container flex-1 min-w-[200px]">
                <input type="text" id="datePicker" placeholder="Pilih tanggal" class="w-full pl-3 pr-8 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-calendar-alt"></i>
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
                <span id="bookingTrend" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-arrow-up trend-up mr-1"></i> 10%
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
                <span id="usageTrend" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <i class="fas fa-arrow-down trend-down mr-1"></i> 5%
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
        <a href="{{ route('admin.bookings.export') }}" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
            <span class="truncate">Export to Excel</span>
        </a>
        <button id="btnReset" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-secondary text-dark text-sm font-bold leading-normal tracking-[0.015em]">
            <span class="truncate">Reset</span>
        </button>
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
    
    .filter-btn.active {
        background-color: #3B82F6;
        color: white;
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
<!-- Load utilities first (DashboardUtils, dsb.) -->
<script src="{{ asset('js/dashboard/utils.js') }}"></script>
<script src="{{ asset('js/dashboard/constants.js') }}"></script>
<script src="{{ asset('js/dashboard/stats.js') }}"></script>
<script src="{{ asset('js/dashboard/filters.js') }}"></script>
<!-- Main.js yang menangani handleDelete -->
<script src="{{ asset('js/dashboard/main.js') }}"></script>
<!-- Date Picker -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date picker
        const datePicker = flatpickr("#datePicker", {
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    // Remove active class from period filters
                    document.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                        btn.classList.add('bg-gray-100', 'text-gray-800');
                    });
                    
                    // Update UI
                    document.getElementById('currentFilterDisplay').textContent = `Showing: Bookings on ${dateStr}`;
                    filterBookings('custom', dateStr);
                }
            }
        });
        
        // Filter buttons event listeners
        document.getElementById('btnToday').addEventListener('click', function() {
            updateActiveFilter(this, 'today');
            filterBookings('today');
        });
        
        document.getElementById('btnWeek').addEventListener('click', function() {
            updateActiveFilter(this, 'week');
            filterBookings('week');
        });
        
        document.getElementById('btnMonth').addEventListener('click', function() {
            updateActiveFilter(this, 'month');
            filterBookings('month');
        });
        
        // Reset button
        document.getElementById('btnReset').addEventListener('click', function() {
            // Reset to today's view
            updateActiveFilter(document.getElementById('btnToday'), 'today');
            filterBookings('today');
            datePicker.clear();
        });
        
        // Add interactive effects for cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            // Add click effect
            card.addEventListener('mousedown', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            card.addEventListener('mouseup', function() {
                this.style.transform = '';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
        
        function updateActiveFilter(button, filterType) {
            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                btn.classList.add('bg-gray-100', 'text-gray-800');
            });
            
            button.classList.add('active', 'bg-blue-100', 'text-blue-800');
            button.classList.remove('bg-gray-100', 'text-gray-800');
            
            // Clear date picker
            datePicker.clear();
            
            // Update filter display
            const displayText = {
                'today': "Today's bookings",
                'week': "This week's bookings",
                'month': "This month's bookings"
            };
            document.getElementById('currentFilterDisplay').textContent = `Showing: ${displayText[filterType]}`;
        }
        
        // Function to fetch bookings from server
        async function fetchBookings(filterType, customDate = null) {
            try {
                // Show loading overlay
                document.getElementById('loadingOverlay').classList.remove('hidden');
                
                // Prepare URL with query parameters
                let url = '/admin/dashboard/bookings';
                const params = new URLSearchParams();
                
                if (filterType === 'today') {
                    params.append('filter', 'today');
                } else if (filterType === 'week') {
                    params.append('filter', 'week');
                } else if (filterType === 'month') {
                    params.append('filter', 'month');
                } else if (filterType === 'custom' && customDate) {
                    params.append('filter', 'custom');
                    params.append('date', customDate);
                }
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                
                // Fetch data from server
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                
                // Update table with fetched data
                updateBookingTableWithData(data.bookings);
                
                // Update stats with fetched data
                updateStatsWithData(data.stats);
                
                // Remove loading state from all cards
                document.querySelectorAll('.card').forEach(card => {
                    card.classList.remove('is-loading');
                });
                
                return data;
            } catch (error) {
                console.error('Error fetching bookings:', error);
                // Show error message
                document.getElementById('bookingTableBody').innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-red-500">
                            <div class="flex flex-col items-center py-8">
                                <i class="fas fa-exclamation-circle text-red-400 text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Error loading data</p>
                                <p class="text-sm text-gray-500">Please try again later</p>
                            </div>
                        </td>
                    </tr>
                `;
            } finally {
                // Hide loading overlay
                document.getElementById('loadingOverlay').classList.add('hidden');
            }
        }
        
        // Function to update booking table with fetched data
        function updateBookingTableWithData(bookings) {
            const tbody = document.getElementById('bookingTableBody');
            tbody.innerHTML = '';
            
            if (!bookings || bookings.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        <div class="flex flex-col items-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Tidak ada data booking</p>
                            <p class="text-sm text-gray-500">Silakan tambahkan booking baru</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
                
                // Update showing text
                document.getElementById('currentFilterDisplay').textContent = `Showing: Today's bookings`;
                document.getElementById('showingCount').textContent = '0';
                document.getElementById('totalCount').textContent = '0';
                document.getElementById('totalBookingsCount').textContent = '0';
                return;
            }
            
            const now = new Date();
            
            // Remove duplicate bookings
            const uniqueBookingIds = new Set();
            const uniqueBookings = [];
            
            bookings.forEach(booking => {
                if (!uniqueBookingIds.has(booking.id)) {
                    uniqueBookingIds.add(booking.id);
                    uniqueBookings.push(booking);
                }
            });
            
            // Create and append table rows
            uniqueBookings.forEach(booking => {
                // Determine booking status
                let status, statusClass;
                
                // Parse booking time
                const [startHour, startMinute] = booking.start_time.split(':').map(Number);
                const [endHour, endMinute] = booking.end_time.split(':').map(Number);
                
                // Create Date objects for comparison
                const bookingDate = new Date(booking.date);
                const startDateTime = new Date(bookingDate);
                startDateTime.setHours(startHour, startMinute);
                const endDateTime = new Date(bookingDate);
                endDateTime.setHours(endHour, endMinute);
                
                if (now >= startDateTime && now <= endDateTime) {
                    status = "Ongoing";
                    statusClass = "bg-red-100 text-red-800";
                } else if (now < startDateTime) {
                    status = "Scheduled";
                    statusClass = "bg-purple-100 text-purple-800";
                } else {
                    status = "Completed";
                    statusClass = "bg-green-100 text-green-800";
                }
                
                // Create row HTML
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 booking-row';
                tr.setAttribute('data-id', booking.id);
                tr.setAttribute('data-endtime', `${booking.date} ${booking.end_time}`);
                
                // Get room name
                const roomName = booking.meeting_room ? booking.meeting_room.name : 
                               (booking.meetingRoom ? booking.meetingRoom.name : 'N/A');
                
                // Set row HTML content
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${roomName}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${booking.department || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${booking.nama || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 booking-date">${booking.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="booking-time">${booking.start_time}</span> - <span class="booking-endtime">${booking.end_time}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex gap-2">
                            <a href="/admin/bookings/${booking.id}/edit" class="text-primary hover:text-primary/80">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="text-danger hover:text-danger/80 delete-booking" data-id="${booking.id}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </td>
                `;
                
                // Add row to table
                tbody.appendChild(tr);
            });
            
            // Update counter display
            document.getElementById('showingCount').textContent = uniqueBookings.length > 0 ? 1 : 0;
            document.getElementById('totalCount').textContent = uniqueBookings.length;
            document.getElementById('totalBookingsCount').textContent = uniqueBookings.length;
            
            // Update pagination buttons
            document.getElementById('prevPage').disabled = true;
            document.getElementById('nextPage').disabled = uniqueBookings.length <= 10;
            
            // Initialize delete buttons after table is updated
            initializeDeleteButtons();
        }
        
        // Function to initialize delete buttons
        function initializeDeleteButtons() {
            const deleteButtons = document.querySelectorAll('.delete-booking');
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            
            deleteButtons.forEach(button => {
                // Remove existing event listeners to prevent duplicates
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                // Add new event listener
                newButton.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-id');
                    deleteForm.action = `{{ route('admin.bookings.delete', '') }}/${bookingId}`;
                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');
                });
            });
        }
        
        // Function to update stats with fetched data
        function updateStatsWithData(stats) {
            if (!stats) return;
            
            // Update total bookings
            if (stats.total_bookings !== undefined) {
                document.getElementById('totalBookings').textContent = stats.total_bookings;
            }
            
            // Update booking comparison
            if (stats.booking_comparison) {
                // Get the comparison data
                const comparisonData = stats.booking_comparison;
                const percentageChange = comparisonData.percentage_change;
                const isIncrease = comparisonData.is_increase;
                const comparisonText = comparisonData.comparison_text;
                
                // Update the comparison text below the total bookings
                let comparisonMessage = '';
                if (percentageChange === 0) {
                    comparisonMessage = `Tidak ada perubahan ${comparisonText}`;
                } else if (isIncrease) {
                    comparisonMessage = `${percentageChange}% lebih banyak ${comparisonText}`;
                } else {
                    comparisonMessage = `${Math.abs(percentageChange)}% lebih sedikit ${comparisonText}`;
                }
                document.getElementById('bookingComparison').textContent = comparisonMessage;
                
                // Update trend indicator
                const bookingTrend = document.getElementById('bookingTrend');
                
                if (percentageChange === 0) {
                    // No change
                    bookingTrend.innerHTML = `<i class="fas fa-equals trend-neutral mr-1"></i> No Change`;
                    bookingTrend.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                    bookingTrend.classList.add('bg-gray-100', 'text-gray-800');
                } else if (isIncrease) {
                    // Increase
                    bookingTrend.innerHTML = `<i class="fas fa-arrow-up trend-up mr-1"></i> ${percentageChange}%`;
                    bookingTrend.classList.remove('bg-red-100', 'text-red-800', 'bg-gray-100', 'text-gray-800');
                    bookingTrend.classList.add('bg-green-100', 'text-green-800');
                } else {
                    // Decrease
                    bookingTrend.innerHTML = `<i class="fas fa-arrow-down trend-down mr-1"></i> ${Math.abs(percentageChange)}%`;
                    bookingTrend.classList.remove('bg-green-100', 'text-green-800', 'bg-gray-100', 'text-gray-800');
                    bookingTrend.classList.add('bg-red-100', 'text-red-800');
                }
            }
            
            // Update room usage
            if (stats.room_usage !== undefined) {
                document.getElementById('roomUsage').textContent = `${stats.room_usage}%`;
                document.getElementById('usageBar').style.width = `${stats.room_usage}%`;
                
                // Update usage info
                if (stats.room_usage > 80) {
                    document.getElementById('usageInfo').textContent = 'High usage';
                    document.getElementById('usageTrend').innerHTML = `<i class="fas fa-arrow-up trend-up mr-1"></i> ${stats.room_usage - 80}%`;
                    document.getElementById('usageTrend').classList.remove('bg-red-100', 'text-red-800', 'bg-gray-100', 'text-gray-800');
                    document.getElementById('usageTrend').classList.add('bg-green-100', 'text-green-800');
                } else {
                    document.getElementById('usageInfo').textContent = 'Normal usage';
                    document.getElementById('usageTrend').innerHTML = `<i class="fas fa-equals trend-neutral mr-1"></i> No Change`;
                    document.getElementById('usageTrend').classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                    document.getElementById('usageTrend').classList.add('bg-gray-100', 'text-gray-800');
                }
            }
            
            // Update most used room
            if (stats.most_used_room) {
                document.getElementById('mostUsedRoom').textContent = stats.most_used_room.name || 'No data';
                document.getElementById('roomUsageHours').textContent = `${stats.most_used_room.bookings_count || 0} bookings`;
            }
            
            // Update top departments
            if (stats.top_departments && stats.top_departments.length > 0) {
                const topDeptsContainer = document.getElementById('topDepartments');
                topDeptsContainer.innerHTML = '';
                
                const colors = ['blue', 'green', 'purple'];
                stats.top_departments.forEach((dept, index) => {
                    const color = colors[index % colors.length];
                    topDeptsContainer.innerHTML += `
                        <span class="px-2 py-1 bg-${color}-100 text-${color}-800 text-xs font-medium rounded">${dept.name}</span>
                    `;
                });
            }
        }
        
        // Function to show loading state for all cards
        function showLoadingState() {
            // Add loading class to all stat cards
            document.querySelectorAll('.card').forEach(card => {
                card.classList.add('is-loading');
            });
            
            // Reset values to loading placeholders
            document.getElementById('totalBookings').innerHTML = '<div class="loading-placeholder"></div>';
            document.getElementById('bookingComparison').innerHTML = '<div class="loading-placeholder"></div>';
            document.getElementById('roomUsage').innerHTML = '<div class="loading-placeholder"></div>';
            document.getElementById('usageBar').style.width = '0%'; // Reset usage bar width
            document.getElementById('usageInfo').innerHTML = '<div class="loading-placeholder"></div>';
            document.getElementById('mostUsedRoom').innerHTML = '<div class="loading-placeholder"></div>';
            document.getElementById('roomUsageHours').innerHTML = '<div class="loading-placeholder"></div>';
            document.getElementById('topDepartments').innerHTML = '<div class="loading-placeholder"></div>';
            
            // Reset trend indicators
            document.getElementById('bookingTrend').innerHTML = '';
            document.getElementById('usageTrend').innerHTML = '';
        }
        
        // Function to filter bookings
        function filterBookings(filterType, customDate = null) {
            // Show loading state first
            showLoadingState();
            
            // Fetch bookings from server
            fetchBookings(filterType, customDate);
        }
        
        // Force Hari Ini to be active when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Reset all filter buttons first
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                btn.classList.add('bg-gray-100', 'text-gray-800');
            });
            
            // Force Hari Ini button to be active
            const btnToday = document.getElementById('btnToday');
            btnToday.classList.add('active', 'bg-blue-100', 'text-blue-800');
            btnToday.classList.remove('bg-gray-100', 'text-gray-800');
            
            // Load today's bookings
            filterBookings('today');
        });
        
        // Also initialize on page load (in case DOMContentLoaded already fired)
        updateActiveFilter(document.getElementById('btnToday'), 'today');
        filterBookings('today');
        
        if (window.Dashboard) {
            window.Dashboard.initialize();
        } else {
            console.error('Dashboard tidak terinisialisasi dengan benar');
        }
        
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
                <button type="submit" class="px-4 py-2 bg-danger text-white rounded-lg">Delete</button>
            </form>
        </div>
    </div>
</div>
