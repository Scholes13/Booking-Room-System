@extends('layouts.app')

@section('title', 'Activity Calendar')

@section('content')
<div class="content container mx-auto mt-8" id="mainContent">
    <div class="container mx-auto py-6 px-4 md:px-12">
        <!-- Top Controls -->
        <div class="flex flex-col md:flex-row mb-6 gap-4">
            <!-- Calendar Controls -->
            <div class="flex-1 bg-white/15 backdrop-blur-lg p-4 rounded-lg shadow-lg flex flex-col md:flex-row gap-4 border border-white/20">
                <div id="calendar-view-controls" class="flex items-center space-x-2 flex-wrap">
                    <div class="bg-white/10 rounded-lg p-1 border border-white/20">
                        <button id="view-month" class="view-btn active px-4 py-1.5 rounded text-sm font-medium">Month</button>
                        <button id="view-week" class="view-btn px-4 py-1.5 rounded text-sm font-medium">Week</button>
                        <button id="view-day" class="view-btn px-4 py-1.5 rounded text-sm font-medium">Day</button>
                    </div>
                    <button id="today-btn" class="bg-white hover:bg-white/90 text-primary px-4 py-1.5 rounded text-sm font-medium transition-colors shadow-sm">Today</button>
                    <div class="flex items-center space-x-2">
                        <button id="prev-btn" class="bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded text-sm text-white shadow-sm"><i class="fas fa-chevron-left"></i></button>
                        <span id="current-range" class="text-sm font-medium text-white"></span>
                        <button id="next-btn" class="bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded text-sm text-white shadow-sm"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white/15 backdrop-blur-lg p-5 rounded-lg shadow-lg mb-6 border border-white/20">
            <div class="flex flex-col md:flex-row gap-5">
                <!-- Filters -->
                <div class="flex-1 flex flex-col md:flex-row gap-5">
                    <div class="flex-1">
                        <label for="departmentFilter" class="block text-sm text-white mb-2 font-medium flex items-center">
                            <i class="fas fa-building mr-2 opacity-80"></i>
                            Department
                        </label>
                        <div class="relative">
                            <select id="departmentFilter" class="form-select rounded-md pl-4 pr-10 py-2.5 w-full text-sm appearance-none">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                                <i class="fas fa-chevron-down text-xs opacity-80"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <label for="activityTypeFilter" class="block text-sm text-white mb-2 font-medium flex items-center">
                            <i class="fas fa-list-alt mr-2 opacity-80"></i>
                            Activity Type
                        </label>
                        <div class="relative">
                            <select id="activityTypeFilter" class="form-select rounded-md pl-4 pr-10 py-2.5 w-full text-sm appearance-none">
                                <option value="">All Activity Types</option>
                                @foreach($activityTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                                <i class="fas fa-chevron-down text-xs opacity-80"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search -->
                <div class="flex-1">
                    <label for="searchInput" class="block text-sm text-white mb-2 font-medium flex items-center">
                        <i class="fas fa-search mr-2 opacity-80"></i>
                        Search by Name
                    </label>
                    <div class="relative">
                        <input type="text" id="searchInput" class="form-input rounded-md pl-10 pr-4 py-2.5 w-full text-sm" placeholder="Search activities...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-white/50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar Container -->
        <div id="calendar-container" class="max-w-screen-xl mx-auto bg-white/15 backdrop-blur-lg rounded-lg shadow-xl border border-white/20 overflow-hidden">
            <div id="calendar" class="min-w-[600px]"></div>
        </div>
    </div>

    <!-- Modal Detail Event -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-6 rounded-lg shadow-2xl max-w-md w-full mx-4 border border-gray-200">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div id="modalTime" class="text-gray-700 font-medium flex items-center">
                    <i class="far fa-clock mr-2 text-primary"></i>
                    <span></span>
                </div>
                <div class="text-gray-600 flex">
                    <p class="font-semibold w-28">Department:</p>
                    <p id="modalDepartment" class="flex-1"></p>
                </div>
                <div class="text-gray-600 flex">
                    <p class="font-semibold w-28">Activity Type:</p>
                    <p id="modalActivityType" class="flex-1"></p>
                </div>
                <div class="text-gray-600 flex">
                    <p class="font-semibold w-28">Location:</p>
                    <p id="modalLocation" class="flex-1"></p>
                </div>
                
                <!-- Sales Mission specific fields - hidden by default -->
                <div id="salesMissionDetails" class="space-y-2 p-3 rounded-lg bg-amber-50 border border-amber-200 hidden">
                    <h4 class="font-bold text-amber-700 flex items-center">
                        <i class="fas fa-building mr-2"></i>Sales Mission Details
                    </h4>
                    <div class="text-gray-600 flex">
                        <p class="font-semibold w-28">Company:</p>
                        <p id="modalCompanyName" class="flex-1"></p>
                    </div>
                    <div class="text-gray-600 flex">
                        <p class="font-semibold w-28">PIC:</p>
                        <p id="modalCompanyPic" class="flex-1"></p>
                    </div>
                    <div class="text-gray-600 flex">
                        <p class="font-semibold w-28">Contact:</p>
                        <p id="modalCompanyContact" class="flex-1"></p>
                    </div>
                    <div class="text-gray-600">
                        <p class="font-semibold mb-1">Address:</p>
                        <p id="modalCompanyAddress" class="bg-white p-2 rounded-md text-sm"></p>
                    </div>
                </div>
                
                <div class="text-gray-600">
                    <p class="font-semibold mb-1">Description:</p>
                    <p id="modalDescription" class="bg-gray-50 p-3 rounded-md text-sm"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal "More Activities" -->
    <div id="moreModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-0 rounded-lg shadow-2xl max-w-md w-full mx-4 border border-gray-200 overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-primary p-4 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold flex items-center" id="moreModalTitle">
                    <span class="bg-white w-10 h-10 rounded-full flex items-center justify-center mr-3 shadow-sm text-primary">
                        <i class="fas fa-calendar-day"></i>
                    </span>
                    <span></span>
                </h3>
                <button onclick="closeMoreModal()" class="text-white hover:text-white/80 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="moreEventsList" class="p-2 text-gray-700 max-h-[70vh] overflow-y-auto">
                <!-- Events will be dynamically added here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<!-- Add the same fonts as login page -->
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
<link
    rel="stylesheet"
    as="style"
    onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
/>
<style>
    :root {
        --fc-border-color: rgba(255, 255, 255, 0.2);
        --fc-event-border-color: transparent;
        --fc-now-indicator-color: #ffffff;
        --fc-today-bg-color: rgba(255, 255, 255, 0.15);
        --fc-highlight-color: rgba(255, 255, 255, 0.15);
        --primary-color: #26458e;
        --event-bg-color: rgba(255, 255, 255, 0.95);
        --hover-color: #ffffff;
        --font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif;
    }

    /* Content */
    .content {
        position: relative;
        z-index: 2;
        transition: opacity 0.3s ease-in-out;
    }

    /* View buttons */
    .view-btn {
        color: rgba(255, 255, 255, 0.8);
        transition: all 0.2s;
    }
    
    .view-btn.active {
        background-color: white;
        color: var(--primary-color);
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    /* Custom styling for Sales Mission events */
    .sales-mission-event {
        background-color: #f59e0b !important; /* Amber color for WG sales mission */
        border-left: 4px solid #d97706 !important;
        color: #7f1d1d !important; /* Dark red text */
    }
    
    .sales-mission-event .fc-event-title {
        font-weight: 600 !important;
    }
    
    .sales-mission-event .fc-event-title:before {
        content: "📈 ";
    }
    
    .sales-mission-event .fc-event-title-container {
        background-color: rgba(255, 255, 255, 0.2);
        padding-top: 2px;
        padding-bottom: 2px;
    }
    
    /* Responsiveness */
    @media (max-width: 768px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }

    /* Filter elements */
    .form-select, .form-input {
        background-color: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 6px;
        width: 100%;
        transition: all 0.2s;
        backdrop-filter: blur(4px);
    }
    
    .form-input {
        padding: 0.625rem 0.75rem;
    }

    .form-select {
        appearance: none;
        background-image: none;
    }

    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-select:focus, .form-input:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
    }
    
    /* Add elegant hover effect */
    .form-select:hover, .form-input:hover {
        border-color: rgba(255, 255, 255, 0.5);
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Calendar Container */
    #calendar-container {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 10px !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        padding: 1.5rem !important;
        overflow: hidden !important;
    }

    @media (max-width: 640px) {
        #calendar-container {
            padding: 0.75rem !important;
            border-radius: 0.75rem !important;
        }
    }

    /* ============== FULLCALENDAR STYLING ============== */
    .fc {
        --fc-border-color: var(--fc-border-color);
        --fc-page-bg-color: transparent;
        --fc-neutral-bg-color: rgba(255, 255, 255, 0.1);
        --fc-list-event-hover-bg-color: rgba(255, 255, 255, 0.1);
        --fc-today-bg-color: var(--fc-today-bg-color);
        font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif;
        max-width: 100%;
        background: transparent !important;
        color: white;
    }
    
    /* Increase the day cell height to provide more space for events */
    .fc .fc-daygrid-day {
        min-height: 120px !important;
    }
    
    /* Ensure consistent spacing between events */
    .fc .fc-daygrid-event-harness {
        margin-bottom: 4px !important;
    }

    .fc .fc-toolbar-title {
        color: white;
        font-weight: 600;
    }

    .fc .fc-col-header-cell-cushion {
        color: white;
        font-weight: 600;
        padding: 10px 4px;
    }

    .fc .fc-daygrid-day-number {
        color: white;
        padding: 8px;
        font-weight: 500;
    }
    
    .fc .fc-daygrid-day.fc-day-today {
        background-color: rgba(255, 255, 255, 0.2) !important;
    }

    /* Event styling */
    .fc-event {
        background-color: var(--event-bg-color) !important;
        color: var(--primary-color) !important;
        border-radius: 4px !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        padding: 2px !important;
        margin-bottom: 4px !important;
        margin-top: 1px !important;
        font-size: 0.75rem !important;
        font-weight: normal !important;
        cursor: pointer !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }

    /* Multi-day event styling */
    .multi-day-event {
        background-color: var(--event-bg-color) !important;
        color: var(--primary-color) !important;
    }

    .fc-event:hover, .multi-day-event:hover {
        background-color: var(--hover-color) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    .fc-h-event .fc-event-main {
        color: var(--primary-color) !important;
    }
    
    /* Custom event classes */
    .event-time {
        font-size: 0.7rem;
        font-weight: normal;
        color: #444444;
        opacity: 0.95;
    }
    
    .event-department {
        font-size: 0.65rem;
        background-color: var(--primary-color);
        color: white;
        padding: 1px 4px;
        border-radius: 3px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .event-title {
        font-size: 0.75rem;
        font-weight: bold;
        color: #333333;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .event-activity-type {
        font-size: 0.65rem;
        font-weight: normal;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-top: 2px;
        line-height: 1.2;
    }
    
    .activity-type-badge {
        padding: 1px 4px;
        border-radius: 3px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.65rem;
    }

    /* Button styling */
    .fc .fc-button {
        background-color: rgba(255, 255, 255, 0.15) !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
        color: white !important;
        font-weight: 500 !important;
        padding: 0.4rem 0.8rem !important;
        border-radius: 6px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    }

    .fc .fc-button:hover {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: white !important;
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: white !important;
        border-color: white !important;
        color: var(--primary-color) !important;
        font-weight: 600 !important;
    }

    /* Today button */
    .fc .fc-button-primary.fc-today-button {
        background-color: white !important;
        border-color: white !important;
        color: var(--primary-color) !important;
        font-weight: 600 !important;
    }

    .fc .fc-button-primary.fc-today-button:hover {
        background-color: rgba(255, 255, 255, 0.9) !important;
    }

    /* More events link */
    .fc-daygrid-more-link {
        color: white !important;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 2px 8px;
        margin: 4px auto 0;
        width: max-content;
    }
    
    .fc-daygrid-more-link:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    /* Day styling */
    .fc-day-past {
        opacity: 0.85;
    }
    
    /* Custom scrollbar */
    #moreEventsList::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    #moreEventsList::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }
    
    #moreEventsList::-webkit-scrollbar-thumb {
        background: rgba(38, 69, 142, 0.5);
        border-radius: 10px;
    }
    
    #moreEventsList::-webkit-scrollbar-thumb:hover {
        background: rgba(38, 69, 142, 0.7);
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
        
        // Pre-defined aesthetically pleasing color pairs (background and text color)
        const colorPalette = [
            { bgColor: '#EBF4FF', textColor: '#1E40AF' }, // Light blue
            { bgColor: '#F0FDF4', textColor: '#166534' }, // Light green
            { bgColor: '#FEF3C7', textColor: '#92400E' }, // Light yellow
            { bgColor: '#FEE2E2', textColor: '#991B1B' }, // Light red
            { bgColor: '#F3E8FF', textColor: '#6B21A8' }, // Light purple
            { bgColor: '#E0F2FE', textColor: '#0369A1' }, // Light sky blue
            { bgColor: '#ECFDF5', textColor: '#065F46' }, // Light teal
            { bgColor: '#FEF2F2', textColor: '#B91C1C' }, // Light rose
            { bgColor: '#FDF2F8', textColor: '#9D174D' }, // Light pink
            { bgColor: '#F5F3FF', textColor: '#5B21B6' }, // Light violet
            { bgColor: '#FFFBEB', textColor: '#B45309' }, // Light amber
            { bgColor: '#F0FDFA', textColor: '#115E59' }, // Light cyan
            { bgColor: '#F8FAFC', textColor: '#334155' }, // Light slate
            { bgColor: '#F1F5F9', textColor: '#475569' }, // Light gray
            { bgColor: '#F9FAFB', textColor: '#374151' }  // Light cool gray
        ];
        
        // Cache for activity type colors
        const activityTypeColorCache = {};
        
        // Function to generate a consistent color for each activity type
        window.getActivityTypeColor = function(activityType) {
            // If we already calculated this color, return from cache
            if (activityTypeColorCache[activityType]) {
                return activityTypeColorCache[activityType];
            }
            
            // Generate a number from the activity type string
            let hash = 0;
            for (let i = 0; i < activityType.length; i++) {
                hash = ((hash << 5) - hash) + activityType.charCodeAt(i);
                hash = hash & hash; // Convert to 32bit integer
            }
            
            // Get a consistent color from the palette based on hash
            const colorIndex = Math.abs(hash) % colorPalette.length;
            const color = colorPalette[colorIndex];
            
            // Cache the color for this activity type
            activityTypeColorCache[activityType] = color;
            
            return color;
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
        // Update modal content with event data
        document.getElementById('modalTitle').innerText = evt.title;
        document.getElementById('modalTime').querySelector('span').innerText = evt.time;
        document.getElementById('modalDepartment').innerText = evt.department;
        document.getElementById('modalActivityType').innerText = evt.activity_type;
        document.getElementById('modalLocation').innerText = evt.location || 'Not specified';
        document.getElementById('modalDescription').innerText = evt.description || 'No description provided.';
        
        // Handle Sales Mission details
        const salesMissionDetails = document.getElementById('salesMissionDetails');
        if (evt.salesMissionDetails && evt.salesMissionDetails.isSalesMission) {
            // Populate Sales Mission fields
            document.getElementById('modalCompanyName').innerText = evt.salesMissionDetails.company_name || 'N/A';
            document.getElementById('modalCompanyPic').innerText = evt.salesMissionDetails.company_pic || 'N/A';
            document.getElementById('modalCompanyContact').innerText = evt.salesMissionDetails.company_contact || 'N/A';
            document.getElementById('modalCompanyAddress').innerText = evt.salesMissionDetails.company_address || 'N/A';
            
            // Show Sales Mission section
            salesMissionDetails.classList.remove('hidden');
        } else {
            // Hide Sales Mission section
            salesMissionDetails.classList.add('hidden');
        }
        
        // Show the modal
        document.getElementById('eventModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('eventModal').style.display = 'none';
    }

    // Make it globally accessible for onclick attribute
    window.closeModal = closeModal;

    eventModal.addEventListener('click', function(e) {
        if (e.target === eventModal) {
            closeModal();
        }
    });

        window.showMoreModal = function(hiddenEvents) {
            moreEventsList.innerHTML = '';

            // Get the date of the first event for the modal title
            if (hiddenEvents.length > 0) {
                const firstEvent = hiddenEvents[0];
                const eventDate = new Date(firstEvent.start);
                
                // Format date for display: "Hari, DD Bulan YYYY"
                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                const formattedDate = eventDate.toLocaleDateString('id-ID', options);
                
                // Capitalize first letter
                const capitalizedDate = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
                
                // Update modal title
                document.querySelector('#moreModalTitle span:last-child').textContent = capitalizedDate;
            }

            // Sort events by start time
            hiddenEvents.sort((a, b) => new Date(a.start) - new Date(b.start));
            
            // Add each event to the list
            hiddenEvents.forEach(ev => addEventCard(ev));
            
            function addEventCard(ev) {
                const card = document.createElement('div');
                card.classList.add('bg-white', 'rounded-lg', 'border', 'border-gray-100', 'shadow-sm', 'mb-3', 'overflow-hidden', 'transition-all', 'duration-200', 'cursor-pointer', 'hover:shadow-md', 'hover:border-gray-200');
                
                // Add hover style with JavaScript for more control
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-2px)';
                    card.style.backgroundColor = '#f9fafb';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                    card.style.backgroundColor = '#ffffff';
                });
                
                // Determine which avatar icon to use based on activity type or department
                let avatarIcon = 'fa-user';
                let avatarColor = '#f0f0f0';
                let avatarTextColor = '#777';
                
                if (ev.activity_type?.toLowerCase().includes('training') || 
                    ev.activity_type?.toLowerCase().includes('education') ||
                    ev.department?.toLowerCase().includes('hr')) {
                    avatarIcon = 'fa-graduation-cap';
                    avatarColor = '#e6efff';
                    avatarTextColor = '#1a56db';
                }
                
                // Format date for display
                const start = new Date(ev.start);
                const end = new Date(ev.end || ev.start);
                
                // Format: "DD Bulan, HH.MM - HH.MM"
                const date = start.getDate();
                const month = start.toLocaleString('id-ID', { month: 'long' });
                const startTime = start.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false});
                const endTime = end.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false});
                
                const dateTimeStr = `${date} ${month}, ${startTime} - ${endTime}`;
                
                // Get department abbreviation
                const deptName = ev.department || '';
                
                card.innerHTML = `
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mr-3" style="background-color: ${avatarColor}">
                                <i class="fas ${avatarIcon} text-xl" style="color: ${avatarTextColor}"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900">${ev.title}</h3>
                                <div class="text-sm text-gray-600 mt-1">${dateTimeStr}</div>
                                <div class="flex items-center mt-2">
                                    <span class="inline-flex items-center bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded">
                                        <i class="fas fa-file-alt mr-1 opacity-70"></i> ${deptName}
                                    </span>
                                </div>
                                ${ev.description ? `<div class="mt-3 text-sm text-gray-700">${ev.description}</div>` : ''}
                            </div>
                        </div>
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
                    <span class="bg-primary w-8 h-8 rounded-full flex items-center justify-center mr-3">
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
                                    backgroundColor: config.color, // Use same color scheme for all events
                                    borderColor: config.color,
                            textColor: '#FFFFFF',
                                    display: 'auto', // Use auto display for all events
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
                container.className = 'p-1 w-full';
                container.style.marginBottom = '2px';
                
                // Create top row with time and department
                const topRow = document.createElement('div');
                topRow.className = 'flex justify-between items-center w-full';
                
                // Time element
                const timeEl = document.createElement('div');
                timeEl.className = 'event-time';
                
                // Format time
                const start = new Date(event.start);
                const end = event.end ? new Date(event.end) : start;
                
                if (isMultiDay) {
                    // Format multi-day events with the date range
                    timeEl.innerText = `${start.getDate()}/${start.getMonth()+1}-${end.getDate()}/${end.getMonth()+1}`;
                } else {
                    timeEl.innerText = `${start.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false})}–${end.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', hour12: false})}`;
                }
                
                // Department badge
                const deptEl = document.createElement('div');
                deptEl.className = 'event-department';
                
                // Get department name and abbreviate if too long
                let deptName = props.department || '';
                if (deptName.length > 12) {
                    // Try to create abbreviation if possible
                    const words = deptName.split(' ');
                    if (words.length > 1) {
                        deptName = words.map(word => word.charAt(0)).join('');
                    } else {
                        deptName = deptName.substring(0, 10) + '...';
                    }
                }
                deptEl.innerText = deptName;
                
                topRow.appendChild(timeEl);
                topRow.appendChild(deptEl);
                container.appendChild(topRow);
                
                // Name/title row
                const nameEl = document.createElement('div');
                nameEl.className = 'event-title mt-1';
                nameEl.title = event.title; // Show full name on hover
                
                // Abbreviate long names if needed
                let displayName = event.title;
                if (displayName.length > 20) {
                    const nameParts = displayName.split(' ');
                    if (nameParts.length >= 2) {
                        // For names like "First Middle Last", show "First L."
                        displayName = nameParts[0] + ' ' + nameParts[nameParts.length - 1].charAt(0) + '.';
                    } else {
                        displayName = displayName.substring(0, 18) + '...';
                    }
                }
                nameEl.innerText = displayName;
                
                container.appendChild(nameEl);
                
                // Activity type row (if available)
                if (props.activity_type) {
                    const activityTypeEl = document.createElement('div');
                    activityTypeEl.className = 'event-activity-type';
                    
                    // Abbreviate activity type if needed
                    let activityType = props.activity_type;
                    if (activityType.length > 22) {
                        activityType = activityType.substring(0, 20) + '...';
                    }
                    
                    // Get color for this activity type
                    const activityColor = getActivityTypeColor(activityType);
                    
                    // Create a span with background color
                    const coloredSpan = document.createElement('span');
                    coloredSpan.className = 'activity-type-badge';
                    coloredSpan.innerText = activityType;
                    coloredSpan.style.backgroundColor = activityColor.bgColor;
                    coloredSpan.style.color = activityColor.textColor;
                    
                    activityTypeEl.appendChild(coloredSpan);
                    container.appendChild(activityTypeEl);
                }
                
                return { domNodes: [container] };
            },

            // Make sure all events display properly with consistent spacing
        eventDidMount: function(info) {
                // Apply styling to all events for consistent spacing
                if (info.view.type === 'dayGridMonth') {
                    // Consistent styling for all events
                    info.el.style.backgroundColor = 'var(--event-bg-color)';
                    info.el.style.color = 'var(--primary-color)';
                    info.el.style.fontWeight = 'normal';
                    // Add padding to ensure content displays properly
                    info.el.style.padding = '2px';
                    // Add margin for better spacing between events
                    info.el.style.marginBottom = '4px';
                    info.el.style.marginTop = '1px';
                    // Add border radius for consistency
                    info.el.style.borderRadius = '4px';
                    // Add shadow
                    info.el.style.boxShadow = '0 1px 2px rgba(0, 0, 0, 0.05)';
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

                // Prepare Sales Mission details if available
                const salesMissionDetails = {
                    isSalesMission: props.activity_type === 'Sales Mission',
                    company_name: props.company_name || '',
                    company_pic: props.company_pic || '',
                    company_contact: props.company_contact || '',
                    company_address: props.company_address || ''
                };

                showEventModal({
                    title: event.title,
                    time: `${startStr} - ${endStr}`,
                    department: props.department,
                    activity_type: props.activity_type,
                    location: props.location,
                    description: props.description,
                    salesMissionDetails: salesMissionDetails
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
