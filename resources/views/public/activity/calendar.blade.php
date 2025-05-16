@extends('layouts.app')

@section('title', 'Activity Calendar')

@section('content')
<div class="content container mx-auto mt-8" id="mainContent">
    <div class="container mx-auto py-6 px-4 md:px-12">
        <!-- Top Controls -->
        <div class="flex flex-col md:flex-row mb-6 gap-4">
            <!-- Calendar Controls -->
            <div class="flex-1 bg-white/10 backdrop-blur-lg p-4 rounded-xl shadow-lg flex flex-col md:flex-row gap-4">
                <div id="calendar-view-controls" class="flex items-center space-x-2 flex-wrap">
                    <div class="bg-gray-900/80 rounded-lg px-2 py-1">
                        <button id="view-month" class="view-btn active px-3 py-1 rounded text-sm font-medium">Month</button>
                        <button id="view-week" class="view-btn px-3 py-1 rounded text-sm font-medium">Week</button>
                        <button id="view-day" class="view-btn px-3 py-1 rounded text-sm font-medium">Day</button>
                    </div>
                    <button id="today-btn" class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1 rounded text-sm font-medium transition-colors">Today</button>
                    <div class="flex items-center space-x-2">
                        <button id="prev-btn" class="bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded text-sm"><i class="fas fa-chevron-left"></i></button>
                        <span id="current-range" class="text-sm font-medium text-white"></span>
                        <button id="next-btn" class="bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded text-sm"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white/10 backdrop-blur-lg p-4 rounded-xl shadow-lg mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Filters -->
                <div class="flex-1 flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label for="departmentFilter" class="block text-xs text-gray-400 mb-1">Department</label>
                        <select id="departmentFilter" class="bg-gray-700 text-white rounded-lg px-4 py-2 w-full">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
                    </div>
                    <div class="flex-1">
                        <label for="activityTypeFilter" class="block text-xs text-gray-400 mb-1">Activity Type</label>
                        <select id="activityTypeFilter" class="bg-gray-700 text-white rounded-lg px-4 py-2 w-full">
                <option value="">All Activity Types</option>
                @foreach($activityTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
                    </div>
        </div>
        
                <!-- Search -->
                <div class="flex-1">
                    <label for="searchInput" class="block text-xs text-gray-400 mb-1">Search by Name</label>
                    <div class="relative">
                        <input type="text" id="searchInput" class="bg-gray-700 text-white rounded-lg pl-10 pr-4 py-2 w-full" placeholder="Search activities...">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar Container -->
        <div id="calendar-container" class="max-w-screen-xl mx-auto bg-white/10 backdrop-blur-lg rounded-xl shadow-xl overflow-hidden">
            <div id="calendar" class="min-w-[600px]"></div>
        </div>


    </div>

    <!-- Modal Detail Event -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white/10 backdrop-blur-lg p-6 rounded-lg shadow-xl max-w-md w-full mx-4 border border-white/20">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white" id="modalTitle"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div id="modalTime" class="text-white font-medium"></div>
                <div class="text-gray-300">
                    <p class="font-semibold">Department:</p>
                    <p id="modalDepartment" class="ml-2"></p>
                </div>
                <div class="text-gray-300">
                    <p class="font-semibold">Activity Type:</p>
                    <p id="modalActivityType" class="ml-2"></p>
                </div>
                <div class="text-gray-300">
                    <p class="font-semibold">Location:</p>
                    <p id="modalLocation" class="ml-2"></p>
                </div>
                <div class="text-gray-300">
                    <p class="font-semibold">Description:</p>
                    <p id="modalDescription" class="ml-2"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal "More Activities" -->
    <div id="moreModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-2xl max-w-md w-full mx-4 border border-white/20">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white flex items-center" id="moreModalTitle">
                    <span class="bg-blue-500 w-8 h-8 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-day text-white"></i>
                    </span>
                    <span>Activities</span>
                </h3>
                <button onclick="closeMoreModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="moreEventsList" class="text-gray-200 space-y-2 max-h-[60vh] overflow-y-auto pr-2">
                <!-- Events will be dynamically added here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<!-- Add Inter font for a more aesthetic look -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --fc-border-color: rgba(255, 255, 255, 0.1);
        --fc-event-border-color: transparent;
        --fc-now-indicator-color: #ffcc00;
        --fc-today-bg-color: rgba(255, 255, 255, 0.1);
        --fc-highlight-color: rgba(255, 255, 255, 0.05);
        --primary-color: #ffcc00;
        --event-bg-color: rgba(18, 18, 18, 0.8);
        --hover-color: rgba(255, 204, 0, 0.95);
        --font-family: 'Inter', sans-serif;
    }

    /* Background */
    body {
        background: url('https://booking.maharajapratama.com/images/bg.png') no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Overlay */
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1;
    }
        display: block;
    }

    /* Konten */
    .content {
        position: relative;
        z-index: 2;
        transition: opacity 0.3s ease-in-out;
    }

    /* View buttons */
    .view-btn {
        color: #999;
        transition: all 0.2s;
    }
    
    .view-btn.active {
        background-color: var(--primary-color);
        color: #000;
    }
    
    

    /* Responsiveness */
    @media (max-width: 768px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }

    /* Filter elements */
    select, input {
        background-color: rgba(30, 41, 59, 0.8);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        color: #fff;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        font-size: 0.875rem;
    }

        select:focus, input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.2);
    }

    /* Calendar Container */
    #calendar-container {
        background: rgba(15, 23, 42, 0.7) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 0.75rem !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        padding: 1rem !important;
        overflow: hidden !important;
    }

    

    @media (max-width: 640px) {
        #calendar-container {
            padding: 0.5rem !important;
            border-radius: 0.75rem !important;
        }
    }



    /* ============== FULLCALENDAR STYLING ============== */
    .fc {
        --fc-border-color: var(--fc-border-color);
        --fc-page-bg-color: transparent;
        --fc-neutral-bg-color: rgba(15, 23, 42, 0.7);
        --fc-list-event-hover-bg-color: rgba(255, 204, 0, 0.1);
        --fc-today-bg-color: var(--fc-today-bg-color);
        font-family: 'Inter', sans-serif;
        max-width: 100%;
        background: transparent !important;
    }

    

    /* Header Toolbar */
    .fc-header-toolbar {
        display: none !important; /* We're using our custom controls */
}

/* Table headers */
.fc th {
    padding: 0.75rem 0 !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
    letter-spacing: 0.05em !important;
    color: rgba(255, 255, 255, 0.9) !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-color: var(--fc-border-color) !important;
    }

    

/* Day cells */
.fc-day {
    transition: background-color 0.2s;
        border-color: var(--fc-border-color) !important;
        background-color: rgba(255, 255, 255, 0.08) !important;
        min-height: 110px !important; /* Adjusted for two-line events */
    }

    .fc-daygrid-day-bottom {
        padding-top: 2px !important;
        padding-bottom: 2px !important;
        text-align: center !important;
    }

    

.fc-day:hover {
        background-color: rgba(255, 255, 255, 0.12) !important;
    }

    

.fc-day-today {
        background-color: rgba(255, 255, 255, 0.15) !important;
}

/* Weekend cell color - now same as regular days */
.fc-day-sat, .fc-day-sun {
        background-color: rgba(255, 255, 255, 0.08) !important;
    }

    

/* Day cell with no date in current month - now same as regular days */
.fc .fc-daygrid-day.fc-day-other {
        background-color: rgba(255, 255, 255, 0.08) !important;
        opacity: 1;
    }

    

/* Time slots */
.fc .fc-timegrid-slot {
    height: 2.5rem !important;
        border-color: var(--fc-border-color) !important;
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    

.fc-timegrid-slot-lane {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    

.fc-timegrid-col.fc-day-today {
        background-color: var(--fc-today-bg-color) !important;
}

/* Now indicator */
.fc-now-indicator-line {
        border-color: var(--fc-now-indicator-color) !important;
    border-width: 2px !important;
}

.fc-now-indicator-arrow {
        border-color: var(--fc-now-indicator-color) !important;
}

    /* ============== EVENT STYLING ============== */
    /* All-day events and multi-day events - Google Calendar style */
    .fc-daygrid-block-event {
    display: flex !important;
    flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
    padding: 0.25rem 0.5rem !important;
        margin: 4px 0 !important;
        border-radius: 4px !important;
        border-left: 3px solid !important;
        background-color: rgba(18, 18, 18, 0.8) !important;
    backdrop-filter: blur(5px) !important;
        color: white !important;
        transition: all 0.15s ease-in-out;
        overflow: hidden !important;
        cursor: pointer !important;
        position: relative !important;
        min-height: 36px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    }

    /* Specific style for multi-day events */
    .fc-daygrid-block-event.fc-event-start {
        margin-left: 2px !important;
        border-top-left-radius: 4px !important;
        border-bottom-left-radius: 4px !important;
    }

    .fc-daygrid-block-event.fc-event-end {
        margin-right: 2px !important;
        border-top-right-radius: 4px !important;
        border-bottom-right-radius: 4px !important;
    }

    /* Fix for multi-day events to ensure they span across days */
    .fc-dayGridMonth-view .fc-event {
        margin-right: 0 !important;
        margin-left: 0 !important;
        margin-top: 4px !important; 
        margin-bottom: 4px !important;
        border-radius: 0 !important;
    }

    .fc-dayGridMonth-view .fc-event-start {
        margin-left: 2px !important;
        border-top-left-radius: 4px !important;
        border-bottom-left-radius: 4px !important;
    }

    .fc-dayGridMonth-view .fc-event-end {
        margin-right: 2px !important;
        border-top-right-radius: 4px !important;
        border-bottom-right-radius: 4px !important;
    }

    

    .fc-daygrid-block-event:hover {
        background-color: var(--hover-color) !important;
        color: #000 !important;
        filter: brightness(1.1);
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3) !important;
        z-index: 5 !important;
    }

    /* Day-view events */
    .fc-daygrid-dot-event {
        margin: 4px 2px !important;
        padding: 3px 6px !important;
        border-radius: 4px !important;
        background-color: rgba(18, 18, 18, 0.8) !important;
    backdrop-filter: blur(5px) !important;
        color: white !important;
    transition: all 0.15s ease-in-out;
        max-width: 100% !important;
    display: flex !important;
        flex-direction: column !important;
    align-items: center !important;
        gap: 0px !important;
        position: relative !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    }

    

    .fc-daygrid-dot-event:hover {
        background-color: var(--hover-color) !important;
    color: #000 !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3) !important;
        z-index: 5 !important;
    }

    /* Time-view events */
    .fc-timegrid-event {
        padding: 4px 6px !important;
        border-radius: 4px !important;
        background-color: rgba(18, 18, 18, 0.8) !important;
        backdrop-filter: blur(5px) !important;
        color: white !important;
        transition: all 0.15s ease-in-out;
        overflow: hidden !important;
        cursor: pointer !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        margin: 0 2px !important;
    }

    .fc-timegrid-event:hover {
        background-color: var(--hover-color) !important;
    color: #000 !important;
    transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3) !important;
        z-index: 5 !important;
}

/* Event content styling */
    .event-content {
    width: 100% !important;
        max-width: 100% !important;
}

    .fc-event-title {
    font-weight: 500 !important;
        font-size: 0.75rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
        width: 100% !important;
        line-height: 1.2 !important;
    }

    .fc-event-time {
        font-size: 0.75rem !important;
        opacity: 0.9 !important;
    }

    /* Make event cells more compact in month view */
    .fc-daygrid-event-harness {
        margin-bottom: 1px !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .fc-day {
            min-height: 90px !important;
        }
        
        .fc-daygrid-day-events {
            margin-bottom: 0 !important;
        }
        
        .event-date {
            font-size: 0.58rem !important;
        }
        
        .fc-event-title {
            font-size: 0.62rem !important;
        }
        
        .event-content i {
            font-size: 1.4rem !important;
            opacity: 0.15 !important;
        }
        
        .fc-daygrid-more-link {
            font-size: 0.65rem !important;
            padding: 2px 8px !important;
        }
        
        #calendar-view-controls {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
        }
    }

    /* Icon indicators for event types */
    .event-icon {
        margin-right: 6px;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    /* Multi-day event special styling */
    .multi-day-event {
        background-image: linear-gradient(45deg, 
            rgba(36, 68, 140, 0.9) 25%, 
            rgba(42, 78, 160, 0.9) 25%, 
            rgba(42, 78, 160, 0.9) 50%, 
            rgba(36, 68, 140, 0.9) 50%, 
            rgba(36, 68, 140, 0.9) 75%, 
            rgba(42, 78, 160, 0.9) 75%, 
            rgba(42, 78, 160, 0.9)) !important;
        background-size: 10px 10px !important;
        font-weight: bold !important;
        z-index: 1 !important; /* Ensure multi-day events are on top */
        margin: 6px 0 !important;
    }
    
    /* Ensure multi-day event is displayed for the entire range */
    .fc-day-grid-event.multi-day-event {
        margin-right: 0 !important;
        margin-left: 0 !important;
        margin-top: 6px !important;
        margin-bottom: 6px !important;
        right: 0 !important;
    }

    /* "More" link styling */
    .fc-daygrid-more-link {
        color: white !important;
        background: rgba(36, 68, 140, 0.7) !important; /* Changed to dark blue */
        padding: 3px 10px !important;
        border-radius: 20px !important; /* Fully rounded edges */
        font-size: 0.7rem !important;
        font-weight: 500 !important;
        margin-top: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease-in-out;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        width: auto !important;
        max-width: calc(100% - 10px) !important;
        margin-left: 5px !important;
        margin-right: 5px !important;
    }

    

    .fc-daygrid-more-link:hover {
        background-color: rgba(36, 68, 140, 0.9) !important;
        color: white !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15) !important;
    }

    
    
    /* Add a plus icon before the text */
    .fc-daygrid-more-link::before {
        content: '';
        font-size: 0.7rem;
        margin-right: 0px;
        opacity: 0.9;
    }

    /* Mobile optimizations */
    @media (max-width: 640px) {
        .fc th {
            font-size: 0.7rem !important;
            padding: 0.5rem 0 !important;
        }
        
        .fc-daygrid-day-number {
            font-size: 0.8rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        
        .fc-event-title {
            font-size: 0.7rem !important;
        }
    }

    /* Helper classes */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom scrollbar for the modal */
    #moreEventsList::-webkit-scrollbar {
        width: 6px;
    }
    
    #moreEventsList::-webkit-scrollbar-track {
        background: rgba(55, 65, 81, 0.1);
        border-radius: 10px;
    }
    
    #moreEventsList::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.5);
        border-radius: 10px;
    }
    
    #moreEventsList::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.7);
    }
    
    /* For Firefox */
    #moreEventsList {
        scrollbar-width: thin;
        scrollbar-color: rgba(59, 130, 246, 0.5) rgba(55, 65, 81, 0.1);
}
</style>
@endpush

@push('scripts')
<!-- FullCalendar CSS & JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<!-- Inter Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
        // Activity type icons and colors mapping
        const activityTypeConfig = {
            'Meeting': { icon: 'fa-handshake', color: '#4C1D95' },
            'Training': { icon: 'fa-graduation-cap', color: '#2563EB' },
            'Workshop': { icon: 'fa-users-gear', color: '#059669' },
            'Conference': { icon: 'fa-users', color: '#9333EA' },
            'Invitation': { icon: 'fa-envelope-open-text', color: '#F59E0B' },
            'Survey': { icon: 'fa-clipboard-list', color: '#10B981' },
            'Courtesy Visit': { icon: 'fa-handshake', color: '#8B5CF6' },
            'External Activities': { icon: 'fa-building-user', color: '#EC4899' },
            'Hosting': { icon: 'fa-user-tie', color: '#EF4444' },
            'Internal Activities': { icon: 'fa-people-group', color: '#3B82F6' },
            'Meeting External': { icon: 'fa-handshake', color: '#6D28D9' },
            'Business Trip': { icon: 'fa-plane', color: '#F97316' },
            'Onsite Work': { icon: 'fa-building', color: '#14B8A6' },
            'Remote Work': { icon: 'fa-laptop-house', color: '#A855F7' },
            'Leave': { icon: 'fa-umbrella-beach', color: '#EC4899' },
            'Sick Leave': { icon: 'fa-hospital', color: '#EF4444' },
            'Holiday': { icon: 'fa-calendar-day', color: '#3B82F6' },
            'Lainnya': { icon: 'fa-calendar-check', color: '#6B7280' },
            'default': { icon: 'fa-calendar', color: '#6B7280' }
        };

        // Helper functions
        function getActivityConfig(activityType) {
            // Extract base activity type if it's "Lainnya: Something"
            let baseType = activityType;
            if (activityType && activityType.startsWith('Lainnya:')) {
                baseType = 'Lainnya';
            }
            
            return activityTypeConfig[baseType] || activityTypeConfig.default;
        }

        function formatTimeRange(startDate, endDate) {
            if (!startDate || !endDate) return '';
            
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            
            let timeStr = '';
            if (diffDays > 1) {
                // Format for multi-day events
                const startFormatter = new Intl.DateTimeFormat('id-ID', { 
                    month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false 
                });
                const endFormatter = new Intl.DateTimeFormat('id-ID', { 
                    month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false 
                });
                timeStr = `${startFormatter.format(start)} - ${endFormatter.format(end)}`;
            } else {
                // Format for single day events
                const dateFormatter = new Intl.DateTimeFormat('id-ID', { month: 'short', day: 'numeric' });
                const timeFormatter = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
                
                timeStr = `${dateFormatter.format(start)}, ${timeFormatter.format(start)} - ${timeFormatter.format(end)}`;
            }
            
            return timeStr;
        }





        // Calendar view control
        const viewBtns = document.querySelectorAll('.view-btn');
        const todayBtn = document.getElementById('today-btn');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const currentRangeEl = document.getElementById('current-range');
        
        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Change calendar view based on button id
                const viewType = btn.id.replace('view-', '');
                if (viewType === 'month') {
                    calendar.changeView('dayGridMonth');
                } else if (viewType === 'week') {
                    calendar.changeView('timeGridWeek');
                } else if (viewType === 'day') {
                    calendar.changeView('timeGridDay');
                }
                
                updateCurrentRange();
            });
        });
        
        todayBtn.addEventListener('click', () => {
            calendar.today();
            updateCurrentRange();
        });
        
        prevBtn.addEventListener('click', () => {
            calendar.prev();
            updateCurrentRange();
        });
        
        nextBtn.addEventListener('click', () => {
            calendar.next();
            updateCurrentRange();
        });
        
        function updateCurrentRange() {
            const view = calendar.view;
            const start = view.currentStart;
            const end = view.currentEnd;
            
            // Format date range based on view type
            if (view.type === 'dayGridMonth') {
                currentRangeEl.textContent = new Intl.DateTimeFormat('id-ID', { 
                    month: 'long', year: 'numeric' 
                }).format(start);
            } else if (view.type === 'timeGridWeek') {
                const weekStart = new Intl.DateTimeFormat('id-ID', { 
                    month: 'short', day: 'numeric' 
                }).format(start);
                
                // End date is exclusive in FullCalendar, subtract 1 day
                const weekEndDate = new Date(end);
                weekEndDate.setDate(weekEndDate.getDate() - 1);
                
                const weekEnd = new Intl.DateTimeFormat('id-ID', { 
                    month: 'short', day: 'numeric', year: 'numeric' 
                }).format(weekEndDate);
                
                currentRangeEl.textContent = `${weekStart} - ${weekEnd}`;
            } else if (view.type === 'timeGridDay') {
                currentRangeEl.textContent = new Intl.DateTimeFormat('id-ID', { 
                    weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' 
                }).format(start);
            }
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            calendar.refetchEvents();
        });

        // Filter handlers
        const departmentFilter = document.getElementById('departmentFilter');
        const activityTypeFilter = document.getElementById('activityTypeFilter');
        
        departmentFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
        
        activityTypeFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });

        // Modal handlers
    const eventModal = document.getElementById('eventModal');
    const moreModal = document.getElementById('moreModal');
    const moreEventsList = document.getElementById('moreEventsList');

        window.showEventModal = function(evt) {
        document.getElementById('modalTitle').textContent = evt.title || 'No Title';
            document.getElementById('modalTime').textContent = evt.time || '';
        document.getElementById('modalDepartment').textContent = evt.department || '';
        document.getElementById('modalActivityType').textContent = evt.activity_type || '';
        document.getElementById('modalLocation').textContent = evt.location || '';
        document.getElementById('modalDescription').textContent = evt.description || '';

        eventModal.classList.remove('hidden');
        eventModal.classList.add('flex');
    }

    window.closeModal = function() {
        eventModal.classList.add('hidden');
        eventModal.classList.remove('flex');
    }

    eventModal.addEventListener('click', function(e) {
        if (e.target === eventModal) {
            closeModal();
        }
    });

        window.showMoreModal = function(hiddenEvents) {
        moreEventsList.innerHTML = '';

            // Sort events by start time
            hiddenEvents.sort((a, b) => {
                // First sort multi-day events to the top
                if (a.isMultiDay && !b.isMultiDay) return -1;
                if (!a.isMultiDay && b.isMultiDay) return 1;
                
                // Then sort by start time
                return new Date(a.start) - new Date(b.start);
            });
            
            // Group events by type (multi-day vs. regular)
            const multiDayEvents = hiddenEvents.filter(ev => ev.isMultiDay);
            const regularEvents = hiddenEvents.filter(ev => !ev.isMultiDay);
            
            // Add section for multi-day events if any
            if (multiDayEvents.length > 0) {
                const multiDayHeader = document.createElement('div');
                multiDayHeader.className = 'text-sm font-medium text-white/90 mb-2 mt-4 border-b border-white/20 pb-1';
                multiDayHeader.textContent = 'Multi-day Events';
                moreEventsList.appendChild(multiDayHeader);
                
                multiDayEvents.forEach(ev => addEventCard(ev));
            }
            
            // Add section for regular events
            if (regularEvents.length > 0) {
                const regularHeader = document.createElement('div');
                regularHeader.className = 'text-sm font-medium text-white/90 mb-2 mt-4 border-b border-white/20 pb-1';
                regularHeader.textContent = regularEvents.length > 0 && multiDayEvents.length > 0 ? 'Regular Events' : '';
                moreEventsList.appendChild(regularHeader);
                
                regularEvents.forEach(ev => addEventCard(ev));
            }

            function addEventCard(ev) {
                const config = getActivityConfig(ev.activity_type);
                const isMultiDay = ev.isMultiDay;
                
            let card = document.createElement('div');
                card.classList.add('p-3', 'rounded-lg', 'shadow-md', 'cursor-pointer', 'relative', 'transition-all', 'duration-200');
                
                // Different styling for multi-day events
                if (isMultiDay) {
                    card.style.background = 'rgba(36, 68, 140, 0.15)';
                    card.style.borderLeft = '3px solid #24448c';
                } else {
                    card.style.background = 'rgba(31, 41, 55, 0.5)';
                    card.style.borderLeft = `3px solid ${config.color}`;
                }
                
            card.style.marginBottom = '8px';

            // Hover effects
            card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-2px)';
                    card.style.boxShadow = '0 5px 10px rgba(0, 0, 0, 0.2)';
                    card.style.background = isMultiDay ? 'rgba(36, 68, 140, 0.25)' : 'rgba(31, 41, 55, 0.7)';
            });

            card.addEventListener('mouseleave', () => {
                    card.style.transform = 'none';
                    card.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
                    card.style.background = isMultiDay ? 'rgba(36, 68, 140, 0.15)' : 'rgba(31, 41, 55, 0.5)';
                });

                // Format dates for display
                let timeDisplay = '';
                if (ev.time) {
                    timeDisplay = ev.time;
                } else if (ev.start && ev.end) {
                    const start = new Date(ev.start);
                    const end = new Date(ev.end);
                    
                    if (isMultiDay) {
                        timeDisplay = `${start.getDate()}/${start.getMonth()+1} - ${end.getDate()}/${end.getMonth()+1}`;
                    } else {
                        timeDisplay = `${start.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false})} - ${end.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false})}`;
                    }
                }

            card.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0 rounded-full bg-opacity-20 p-2 mr-3" style="background-color: ${config.color}20">
                            <i class="fas ${isMultiDay ? 'fa-calendar-week' : config.icon}" style="color: ${config.color}"></i>
                </div>
                        <div class="flex-1 overflow-hidden">
                            <div class="font-semibold text-sm truncate" title="${ev.title}">${ev.title}</div>
                            <div class="text-xs text-gray-300 mt-1 flex items-center">
                                <i class="far fa-clock mr-1 opacity-75"></i> ${timeDisplay}
                            </div>
                            <div class="text-xs mt-2 truncate text-gray-400" title="${ev.department || ''}">
                                <i class="fas fa-building mr-1 opacity-75"></i> ${ev.department || 'No Department'}
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mt-2 line-clamp-2" title="${ev.description || ''}">
                        ${ev.description || 'No description available'}
                    </div>
            `;

            card.addEventListener('click', () => {
                closeMoreModal();
                showEventModal(ev);
            });

            moreEventsList.appendChild(card);
            }

        moreModal.classList.remove('hidden');
        moreModal.classList.add('flex');
    }

    window.closeMoreModal = function() {
        moreModal.classList.add('hidden');
        moreModal.classList.remove('flex');
    }

    moreModal.addEventListener('click', function(e) {
        if (e.target === moreModal) {
            closeMoreModal();
        }
    });

    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'id',
        initialView: window.innerWidth < 768 ? 'dayGridMonth' : 'dayGridMonth',
            headerToolbar: false, // We're using our own controls
            slotMinTime: '06:00:00',
            slotMaxTime: '21:00:00',
            slotDuration: '00:30:00',
        slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        nowIndicator: true,
            dayMaxEvents: 3, // Limit to 3 events per day
            allDayContent: 'All day',
        height: 'auto',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            // Configure the "more" link click behavior
            moreLinkClick: function(info) {
                // Get the date of the clicked "more" link
                const clickedDate = info.date;
                
                // Format the date for display in modal title
                const formattedDate = new Intl.DateTimeFormat('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }).format(clickedDate);
                
                // Create event objects for the modal
                const moreEventsForModal = info.allSegs.map(seg => {
                    const evt = seg.event;
                    const props = evt.extendedProps;
                    
                    return {
                        id: evt.id,
                        title: evt.title,
                        start: evt.start,
                        end: evt.end || evt.start,
                        time: formatTimeRange(evt.start, evt.end || evt.start),
                        department: props.department,
                        activity_type: props.activity_type,
                        description: props.description,
                        location: props.location,
                        isMultiDay: props.isMultiDay
                    };
                });
                
                // Show the "more" modal with the list of events
                const modalTitleEl = document.getElementById('moreModalTitle');
                // Update the icon and title
                modalTitleEl.innerHTML = `
                    <span class="bg-blue-500 w-8 h-8 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-day text-white"></i>
                    </span>
                    <span>${formattedDate}</span>
                `;
                
                showMoreModal(moreEventsForModal);
                
                // Return 'popover' to prevent default behavior
                return 'popover';
            },
            moreLinkText: function(n) {
                return `+${n} ${n === 1 ? 'other activity' : 'other activities'}`;
        },

        events: function(info, successCallback, failureCallback) {
            let url = new URL("{{ route('activity.calendar.events') }}");
            url.searchParams.append('start', info.startStr);
            url.searchParams.append('end', info.endStr);

                const selectedDept = departmentFilter.value;
                const selectedActivityType = activityTypeFilter.value;
                const searchTerm = searchInput.value.trim();
            
            if (selectedDept) url.searchParams.append('department_id', selectedDept);
            if (selectedActivityType) url.searchParams.append('activity_type', selectedActivityType);
                if (searchTerm) url.searchParams.append('search', searchTerm);

            fetch(url)
                .then(response => response.json())
                .then(rawEvents => {
                        // Transform events for calendar display
                        const transformedEvents = rawEvents
                            // Filter by search term if provided
                            .filter(event => {
                                if (!searchTerm) return true;
                                return event.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                       (event.extendedProps.description || '').toLowerCase().includes(searchTerm.toLowerCase());
                            })
                            .map(event => {
                        const startDate = new Date(event.start);
                        const endDate = new Date(event.end);
                        const startDay = startDate.toISOString().split('T')[0];
                        const endDay = endDate.toISOString().split('T')[0];
                        const isMultiDay = startDay !== endDay;
                                const isAllDay = startDate.getHours() === 0 && startDate.getMinutes() === 0 &&
                                                endDate.getHours() === 0 && endDate.getMinutes() === 0;

                                // Get activity type configuration
                                const config = getActivityConfig(event.extendedProps.activity_type);
                                
                                const baseEvent = {
                            id: event.id,
                            title: event.title,
                            start: event.start,
                            end: event.end,
                                    allDay: isAllDay || isMultiDay,
                                    backgroundColor: isMultiDay ? '#24448c' : config.color, // Different color for multi-day events
                                    borderColor: isMultiDay ? '#24448c' : config.color,
                            textColor: '#FFFFFF',
                                    display: isMultiDay ? 'block' : 'auto',
                                    classNames: isMultiDay ? ['multi-day-event'] : [],
                            extendedProps: {
                                ...event.extendedProps,
                                        isMultiDay: isMultiDay,
                                        iconClass: isMultiDay ? 'fa-calendar-week' : config.icon,
                                        originalColor: config.color
                            }
                        };
                                
                                // Return a single event - FullCalendar will handle multi-day display
                                return baseEvent;
                    });

                    successCallback(transformedEvents);
                })
                .catch(err => {
                    console.error('Error fetching events:', err);
                    failureCallback(err);
                });
        },

            eventContent: function(info) {
                const event = info.event;
                const props = event.extendedProps;
                const isMultiDay = props.isMultiDay;
                
                // Create event container
                const container = document.createElement('div');
                container.className = 'event-content flex flex-col items-center justify-center w-full gap-1 relative';
                
                // Create date line (first line)
                const dateLineContainer = document.createElement('div');
                dateLineContainer.className = 'w-full text-center';
                
                const dateLine = document.createElement('div');
                dateLine.className = 'event-date text-xs opacity-85 font-medium whitespace-nowrap overflow-hidden text-ellipsis';
                
                // Format date range
                const start = new Date(event.start);
                const end = event.end ? new Date(event.end) : start;
                
                // Different date format based on multi-day status
                if (isMultiDay) {
                    // For multi-day: "4/7 - 8/7" format
                    dateLine.innerText = `${start.getDate()}/${start.getMonth()+1} - ${end.getDate()}/${end.getMonth()+1}`;
                } else {
                    // For single-day: "14:00 - 16:00" format
                    dateLine.innerText = `${start.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false})} - ${end.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false})}`;
                }
                
                dateLineContainer.appendChild(dateLine);
                container.appendChild(dateLineContainer);
                
                // Create centered icon
                const iconContainer = document.createElement('div');
                iconContainer.className = 'absolute inset-0 flex items-center justify-center opacity-20';
                
                const config = getActivityConfig(props.activity_type);
                const icon = document.createElement('i');
                icon.className = `fas ${props.iconClass} text-xl`;
                iconContainer.appendChild(icon);
                container.appendChild(iconContainer);
                
                // Create name line (second line) - using event title as the creator name
                const nameLineContainer = document.createElement('div');
                nameLineContainer.className = 'w-full text-center';
                
                const nameLine = document.createElement('div');
                nameLine.className = 'event-title text-xs font-medium whitespace-nowrap overflow-hidden text-ellipsis';
                nameLine.title = event.title; // Show full name on hover
                nameLine.innerText = event.title;
                
                nameLineContainer.appendChild(nameLine);
                container.appendChild(nameLineContainer);
                
                return { domNodes: [container] };
            },

            // Make sure multi-day events display properly
        eventDidMount: function(info) {
                // Apply multi-day styling
            if (info.event.extendedProps.isMultiDay) {
                    // Add a distinctive pattern to multi-day events
                    if (info.view.type === 'dayGridMonth') {
                        info.el.style.backgroundImage = 'linear-gradient(45deg, rgba(36, 68, 140, 0.9) 25%, rgba(42, 78, 160, 0.9) 25%, rgba(42, 78, 160, 0.9) 50%, rgba(36, 68, 140, 0.9) 50%, rgba(36, 68, 140, 0.9) 75%, rgba(42, 78, 160, 0.9) 75%, rgba(42, 78, 160, 0.9))';
                        info.el.style.backgroundSize = '10px 10px';
                        info.el.style.fontWeight = 'bold';
                    }
                }
            },

        eventClick: function(info) {
                const event = info.event;
                const props = event.extendedProps;
                const startStr = event.start ? new Date(event.start).toLocaleString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }) : '';
                
                // Use original end date if available
                const endDate = props.original_end ? new Date(props.original_end) : event.end;
                
                const endStr = endDate ? new Date(endDate).toLocaleString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }) : '';

                showEventModal({
                    title: event.title,
                    time: `${startStr} - ${endStr}`,
                    department: props.department,
                    activity_type: props.activity_type,
                    location: props.location,
                    description: props.description
                });
            },
            
            // Make sure all-day and multi-day events appear at the top
            eventOrder: function(a, b) {
                // All-day events should appear at the top
                if (a.allDay && !b.allDay) return -1;
                if (!a.allDay && b.allDay) return 1;
                
                // If both are all-day, sort by start time
                return 0;
            }
        });

    calendar.render();

        // Update current range display on initial load
        updateCurrentRange();
        
        // Handle window resize
    window.addEventListener('resize', function() {
            calendar.updateSize();
        });
});
</script>
@endpush
