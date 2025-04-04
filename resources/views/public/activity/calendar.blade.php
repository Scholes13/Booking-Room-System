@extends('layouts.app')

@section('title', 'Calendar View - Hover Kuning, Ruangan Hitam (Termasuk di More Modal)')

@section('content')
<div class="content container mx-auto mt-8" id="mainContent">
    <div class="container mx-auto py-6 px-4 md:px-12">
        <!-- Filter Departemen dan Jenis Aktivitas -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <select id="departmentFilter" class="bg-gray-700 text-white rounded-lg px-4 py-2 w-full md:w-64">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            <select id="activityTypeFilter" class="bg-gray-700 text-white rounded-lg px-4 py-2 w-full md:w-64">
                <option value="">All Activity Types</option>
                @foreach($activityTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Calendar Container dengan scroll horizontal untuk mobile -->
        <div id="calendar-container" class="max-w-screen-xl mx-auto p-4 bg-white/10 backdrop-blur-lg rounded-xl shadow-xl overflow-x-auto">
            <!-- Berikan minimum width pada kalender agar dapat di-scroll jika layar kecil -->
            <div id="calendar" class="min-w-[600px]"></div>
        </div>
    </div>

    <!-- Modal Detail Event -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white" id="modalTitle"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
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

    <!-- Modal "More" (Week/Day) -->
    <div id="moreModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white">More Events</h3>
                <button onclick="closeMoreModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="moreEventsList" class="text-gray-200 space-y-2"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

    /* Konten */
    .content {
        position: relative;
        z-index: 2;
        transition: opacity 0.3s ease-in-out;
    }

    /* Responsiveness */
    @media (max-width: 768px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }

    /* Card */
    .card {
        backdrop-filter: blur(10px);
        background-color: rgba(15, 23, 42, 0.7);
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
        transition: all 0.2s ease-in-out;
    }

    /* Filter elements */
    select {
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

    select:focus {
        outline: none;
        border-color: rgba(255, 204, 0, 0.5);
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
</style>
@endpush

@push('scripts')
<!-- FullCalendar CSS & JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<!-- Inter Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ============== GLOBAL CALENDAR STYLING ============== */
.fc {
    --fc-border-color: rgba(255, 255, 255, 0.1);
    --fc-page-bg-color: transparent;
    --fc-neutral-bg-color: rgba(15, 23, 42, 0.7);
    --fc-list-event-hover-bg-color: rgba(255, 204, 0, 0.1);
    --fc-today-bg-color: rgba(30, 41, 59, 0.7);
    font-family: 'Inter', sans-serif;
    max-width: 100%;
    background: transparent !important;
}

.fc .fc-toolbar {
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem !important;
}

.fc .fc-toolbar-title {
    font-size: 1.5rem !important;
    font-weight: 600;
    color: white;
}

@media (max-width: 640px) {
    .fc .fc-toolbar-title {
        font-size: 1.2rem !important;
        width: 100%;
        text-align: center;
    }
}

/* Button styling */
.fc .fc-button {
    background-color: rgba(30, 41, 59, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: white !important;
    font-weight: 500 !important;
    padding: 0.4rem 0.8rem !important;
    border-radius: 0.375rem !important;
    font-size: 0.875rem !important;
    transition: all 0.2s !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
}

.fc .fc-button:hover {
    background-color: rgba(45, 55, 72, 0.9) !important;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1) !important;
}

.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background-color: rgba(255, 204, 0, 0.9) !important;
    border-color: rgba(255, 204, 0, 0.5) !important;
    color: #000 !important;
}

/* Table headers */
.fc th {
    padding: 0.75rem 0 !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
    letter-spacing: 0.05em !important;
    color: rgba(255, 255, 255, 0.9) !important;
    background-color: rgba(30, 41, 59, 0.8) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

/* Day cells */
.fc-day {
    transition: background-color 0.2s;
    border-color: rgba(255, 255, 255, 0.1) !important;
    background-color: rgba(15, 23, 42, 0.7) !important;
}

.fc-day:hover {
    background-color: rgba(30, 41, 59, 0.9) !important;
}

.fc-day-today {
    background-color: rgba(30, 41, 59, 0.7) !important;
}

/* Weekend cell color */
.fc-day-sat, .fc-day-sun {
    background-color: rgba(15, 23, 42, 0.9) !important;
}

/* Day cell with no date in current month */
.fc .fc-daygrid-day.fc-day-other {
    background-color: rgba(10, 15, 25, 0.5) !important;
    opacity: 0.7;
}

/* Time slots */
.fc .fc-timegrid-slot {
    height: 2.5rem !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
    background-color: rgba(15, 23, 42, 0.7) !important;
}

.fc-timegrid-slot-lane {
    background-color: rgba(15, 23, 42, 0.7) !important;
}

.fc-timegrid-col.fc-day-today {
    background-color: rgba(30, 41, 59, 0.7) !important;
}

/* Now indicator */
.fc-now-indicator-line {
    border-color: rgba(255, 204, 0, 0.8) !important;
    border-width: 2px !important;
}

.fc-now-indicator-arrow {
    border-color: rgba(255, 204, 0, 0.8) !important;
}

/* ============== MONTH VIEW (dayGrid) ============== */
.fc-daygrid-event {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    padding: 0.25rem 0.5rem !important;
    background: rgba(18, 18, 18, 0.95) !important; 
    border-radius: 0.375rem !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-y: auto !important;
    min-width: 100px !important;
    transition: all 0.15s ease-in-out;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(5px) !important;
    color: #fff !important;
    margin: 0 0 1px 0 !important;
    min-height: 24px !important;
}

.fc-daygrid-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15) !important;
}

.fc-daygrid-event:hover .fc-event-activity-type {
    color: #000 !important;
}

/* "More" link styling */
.fc-dayGridMonth-view .fc-daygrid-more-link {
    font-size: 0.7rem !important;
    color: white !important;
    background-color: rgba(30, 30, 30, 0.9) !important;
    padding: 0.15rem 0.5rem !important;
    border-radius: 1rem !important;
    font-weight: 500 !important;
    margin-top: 0.125rem !important;
    display: inline-block !important;
}

.fc-dayGridMonth-view .fc-daygrid-more-link:hover {
    background-color: rgba(255, 204, 0, 0.9) !important;
    color: black !important;
    text-decoration: none;
}

/* Popover styling */
.fc-popover {
    background: rgba(15, 23, 42, 0.95) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 0.75rem !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
    overflow: hidden !important;
}

.fc-popover-body {
    padding: 0.5rem !important;
    max-height: 300px !important;
    overflow-y: auto !important;
}

.fc-popover-header {
    background: rgba(30, 41, 59, 0.9) !important;
    padding: 0.5rem 0.75rem !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.fc-popover .fc-daygrid-event {
    margin: 0.25rem 0 !important;
}

/* ============== WEEK/DAY VIEW (timeGrid) ============== */
.fc-timeGridWeek-view .fc-event,
.fc-timeGridDay-view .fc-event {
    background: rgba(18, 18, 18, 0.95) !important;
    color: #fff !important;
    border-radius: 0.25rem !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(5px) !important;
    transition: all 0.15s ease-in-out;
    font-size: 0.65rem !important;
    overflow: hidden !important;
    margin: 0 1px !important;
    padding: 0.125rem 0.375rem !important;
}

/* Style for "+more" events */
.fc-timeGridWeek-view .more-event,
.fc-timeGridDay-view .more-event {
    background: rgba(55, 65, 81, 0.9) !important;
    color: #fff !important;
    font-weight: 600 !important;
    text-align: center !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 0.7rem !important;
    border-radius: 0.25rem !important;
}

.fc-timeGridWeek-view .more-event:hover,
.fc-timeGridDay-view .more-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
}

.fc-timeGridWeek-view .fc-event:hover,
.fc-timeGridDay-view .fc-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15) !important;
}

/* Event content styling */
.fc-event-content {
    display: flex !important;
    flex-direction: column !important;
    gap: 0px !important;
    width: 100% !important;
    padding: 0 !important;
}

.fc-event-time, .fc-event-activity-type {
    font-size: 0.65rem !important;
    line-height: 1.2 !important;
    opacity: 0.9;
    font-weight: 500 !important;
}

.fc-event-activity-type {
    font-weight: 600 !important;
}

/* Multi-day event styling */
.continuous-event {
    background: linear-gradient(45deg, 
        rgba(55, 65, 81, 0.95) 25%, 
        rgba(255, 204, 0, 0.15) 25%, 
        rgba(255, 204, 0, 0.15) 50%, 
        rgba(55, 65, 81, 0.95) 50%, 
        rgba(55, 65, 81, 0.95) 75%, 
        rgba(255, 204, 0, 0.15) 75%, 
        rgba(255, 204, 0, 0.15)
    ) !important;
    background-size: 12px 12px !important;
    animation: move 1.5s linear infinite !important;
    border-left: 2px solid #ffcc00 !important;
    border-right: 2px solid #ffcc00 !important;
    z-index: 1 !important;
}

.fc-dayGridMonth-view .continuous-event {
    min-height: 24px !important;
}

.fc-timeGridWeek-view .continuous-event,
.fc-timeGridDay-view .continuous-event {
    left: 0 !important;
    right: 0 !important;
    margin: 0 2px !important;
}

.continuous-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
    animation: none !important;
}

@keyframes move {
    0% { background-position: 0 0; }
    100% { background-position: 12px 12px; }
}

/* Mobile/Small Screen Optimizations */
@media (max-width: 640px) {
    .fc .fc-toolbar {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .fc .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        margin-bottom: 0.5rem;
        gap: 0.25rem;
    }
    
    .fc .fc-button {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
    }
    
    .fc th {
        font-size: 0.7rem !important;
        padding: 0.5rem 0 !important;
    }
    
    .fc .fc-daygrid-day-number {
        font-size: 0.75rem;
        padding: 0.15rem 0.3rem;
    }
    
    .fc-daygrid-event {
        min-height: 20px !important;
        padding: 0.1rem 0.3rem !important;
    }
    
    .fc-event-time, .fc-event-activity-type {
        font-size: 0.6rem !important;
    }
    
    .fc-day-today .fc-daygrid-day-number {
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.75rem;
    }
}

/* Modal styling */
#eventModal, #moreModal {
    backdrop-filter: blur(8px);
}

.modal-content {
    background: rgba(15, 23, 42, 0.95);
    border-radius: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    max-width: 100%;
    width: 28rem;
    transform: translateY(0);
    transition: all 0.3s;
}

/* Additional utility classes */
.hidden { display: none; }
.flex { display: flex; }
.cursor-pointer { cursor: pointer; }

/* Day number */
.fc .fc-daygrid-day-number {
    padding: 0.25rem 0.5rem;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.85rem;
    font-weight: 500;
}

.fc-day-today .fc-daygrid-day-number {
    background-color: rgba(255, 204, 0, 0.9);
    color: #000;
    border-radius: 50%;
    width: 1.75rem;
    height: 1.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 0.25rem;
}

.fc .fc-timegrid-slot-label {
    font-size: 0.75rem !important;
    color: rgba(255, 255, 255, 0.7) !important;
}

/* Filter layout */
.flex.flex-col.md\:flex-row.gap-4.mb-6 {
    display: flex;
    align-items: center;
    background: rgba(15, 23, 42, 0.7);
    padding: 1rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Event alignment fix */
.fc-event-content {
    display: flex !important;
    flex-direction: column !important;
    gap: 0px !important;
    width: 100% !important;
    padding: 0 !important;
}

/* Today column highlight */
.fc .fc-day-today {
    border: 1px solid rgba(255, 204, 0, 0.3) !important;
}

/* Fixed spacing between events */
.fc-daygrid-event-harness {
    margin: 1px 0 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper Functions
    function getLightColorForActivityType(activityType) {
        let colors = {
            "Meeting": "#EF5350",
            "Invitation": "#66BB6A",
            "Survey": "#42A5F5",
            "default": "#FFEE58"
        };
        return colors[activityType] || colors["default"];
    }

    function formatTimeRange(start, end) {
        const startDate = new Date(start);
        const endDate = end ? new Date(end) : null;
        const now = new Date();
        
        const startStr = startDate.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
        
        if (!endDate) return startStr;
        
        const endStr = endDate.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });

        // Hitung waktu tersisa
        const timeUntilStart = startDate - now;
        const timeUntilEnd = endDate - now;
        
        let timeIndicator = '';
        
        if (timeUntilStart > 0) {
            // Acara belum dimulai
            const hoursUntilStart = Math.floor(timeUntilStart / (1000 * 60 * 60));
            const minutesUntilStart = Math.floor((timeUntilStart % (1000 * 60 * 60)) / (1000 * 60));
            
            if (hoursUntilStart > 0) {
                timeIndicator = `(Mulai dalam ${hoursUntilStart} jam ${minutesUntilStart} menit)`;
            } else {
                timeIndicator = `(Mulai dalam ${minutesUntilStart} menit)`;
            }
        } else if (timeUntilEnd > 0) {
            // Acara sedang berlangsung
            const hoursUntilEnd = Math.floor(timeUntilEnd / (1000 * 60 * 60));
            const minutesUntilEnd = Math.floor((timeUntilEnd % (1000 * 60 * 60)) / (1000 * 60));
            
            if (hoursUntilEnd > 0) {
                timeIndicator = `(Berakhir dalam ${hoursUntilEnd} jam ${minutesUntilEnd} menit)`;
            } else {
                timeIndicator = `(Berakhir dalam ${minutesUntilEnd} menit)`;
            }
        } else {
            // Acara sudah selesai
            timeIndicator = '(Selesai)';
        }
        
        return `${startStr} - ${endStr} ${timeIndicator}`;
    }

    // Transform events untuk Week/Day View
    function transformEventsForTimeGrid(events) {
        const limit = 1;
        const grouped = {};
        const processedEvents = new Set();
        const finalEvents = [];

        // Pertama, identifikasi dan proses event yang berganti hari
        events.forEach(evt => {
            const startDate = new Date(evt.start);
            const endDate = new Date(evt.end);
            
            // Check if event spans multiple days
            const startDay = startDate.toISOString().split('T')[0];
            const endDay = endDate.toISOString().split('T')[0];
            
            if (startDay !== endDay) {
                // Event berganti hari
                finalEvents.push({
                    ...evt,
                    title: `${evt.title} (Multi-day)`,
                    classNames: ['continuous-event'],
                    display: 'block',
                    overlap: false,
                    backgroundColor: 'rgba(75, 85, 99, 0.9)',
                    borderColor: '#ffcc00',
                    textColor: '#ffffff',
                    extendedProps: {
                        ...evt.extendedProps,
                        isMultiDay: true
                    }
                });
                processedEvents.add(evt.id);
            }
        });

        // Kemudian proses event regular (dalam hari yang sama)
        events.forEach(evt => {
            if (processedEvents.has(evt.id)) return; // Skip event yang sudah diproses

            const startDate = new Date(evt.start);
            const endDate = new Date(evt.end);
            const startDay = startDate.toISOString().split('T')[0];
            const endDay = endDate.toISOString().split('T')[0];

            if (startDay === endDay) {
                // Generate key untuk setiap 30 menit interval
                const timeKey = `${startDate.toISOString().split('T')[0]}_${startDate.getHours()}_${Math.floor(startDate.getMinutes() / 30) * 30}`;
                
                if (!grouped[timeKey]) {
                    grouped[timeKey] = [];
                }
                grouped[timeKey].push(evt);
            }
        });

        // Proses event regular yang overlap
        Object.entries(grouped).forEach(([timeKey, eventsInSlot]) => {
            if (eventsInSlot.length <= limit) {
                eventsInSlot.forEach(evt => {
                    if (!processedEvents.has(evt.id)) {
                        finalEvents.push(evt);
                        processedEvents.add(evt.id);
                    }
                });
            } else {
                // Tampilkan event pertama
                const firstEvent = eventsInSlot[0];
                if (!processedEvents.has(firstEvent.id)) {
                    finalEvents.push(firstEvent);
                    processedEvents.add(firstEvent.id);
                }

                // Tambahkan +more untuk event lainnya
                const hiddenEvents = eventsInSlot.slice(1).filter(evt => !processedEvents.has(evt.id));
                if (hiddenEvents.length > 0) {
                    finalEvents.push({
                        id: `more-${timeKey}`,
                        title: `+${hiddenEvents.length} more`,
                        start: firstEvent.start,
                        end: firstEvent.end,
                        classNames: ['more-event'],
                        extendedProps: {
                            isMore: true,
                            hiddenEvents: hiddenEvents.map(evt => ({
                                id: evt.id,
                                title: evt.title,
                                time: formatTimeRange(evt.start, evt.end),
                                department: evt.extendedProps?.department,
                                activity_type: evt.extendedProps?.activity_type,
                                location: evt.extendedProps?.location,
                                description: evt.extendedProps?.description
                            }))
                        }
                    });
                    hiddenEvents.forEach(evt => processedEvents.add(evt.id));
                }
            }
        });

        return finalEvents;
    }

    // Modal Handlers
    const eventModal = document.getElementById('eventModal');
    const moreModal = document.getElementById('moreModal');
    const moreEventsList = document.getElementById('moreEventsList');

    function showEventModal(evt) {
        document.getElementById('modalTitle').textContent = evt.title || 'No Title';
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

    function showMoreModal(hiddenEvents) {
        moreEventsList.innerHTML = '';

        hiddenEvents.forEach(ev => {
            let card = document.createElement('div');
            card.classList.add('p-3', 'rounded', 'shadow-md', 'cursor-pointer');
            card.style.background = 'rgba(18,18,18,0.9)';
            card.style.border = '1px solid rgba(255,255,255,0.1)';
            card.style.backdropFilter = 'blur(5px)';
            card.style.color = '#fff';
            card.style.transition = 'all 0.3s ease-in-out';
            card.style.marginBottom = '8px';

            // Hover effects
            card.addEventListener('mouseenter', () => {
                card.style.background = 'rgba(255,204,0,0.95)';
                card.style.color = '#000';
                let activityTypeEl = card.querySelector('.activity-type');
                if (activityTypeEl) {
                    activityTypeEl.style.color = '#000';
                }
            });

            card.addEventListener('mouseleave', () => {
                card.style.background = 'rgba(18,18,18,0.9)';
                card.style.color = '#fff';
                let activityTypeEl = card.querySelector('.activity-type');
                if (activityTypeEl) {
                    let originalColor = getLightColorForActivityType(ev.activity_type || '');
                    activityTypeEl.style.color = originalColor;
                }
            });

            let activityColor = getLightColorForActivityType(ev.activity_type || '');
            card.innerHTML = `
                <div class="font-bold text-sm">${ev.title} (${ev.time})</div>
                <div class="text-xs activity-type" style="color: ${activityColor};">
                    Activity: ${ev.activity_type || 'Unknown'}
                </div>
                <div class="text-xs">Dept: ${ev.department || ''}</div>
                <div class="text-xs">Location: ${ev.location || 'No location'}</div>
                <div class="text-xs text-gray-300">${ev.description || ''}</div>
            `;

            card.addEventListener('click', () => {
                closeMoreModal();
                showEventModal(ev);
            });

            moreEventsList.appendChild(card);
        });

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
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '00:00:00',
        slotMaxTime: '24:00:00',
        slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        eventOverlap: false,
        nowIndicator: true,
        displayEventEnd: true,
        eventDisplay: 'block',
        height: 'auto',

        views: {
            dayGridMonth: {
                dayMaxEvents: false, // Tampilkan semua event
                moreLinkClick: 'popover'
            },
            timeGridWeek: {
                dayMaxEvents: false,
                nowIndicator: true
            },
            timeGridDay: {
                dayMaxEvents: false,
                nowIndicator: true
            }
        },

        events: function(info, successCallback, failureCallback) {
            let url = new URL("{{ route('activity.calendar.events') }}");
            url.searchParams.append('start', info.startStr);
            url.searchParams.append('end', info.endStr);

            const selectedDept = document.getElementById('departmentFilter').value;
            const selectedActivityType = document.getElementById('activityTypeFilter').value;
            
            if (selectedDept) url.searchParams.append('department_id', selectedDept);
            if (selectedActivityType) url.searchParams.append('activity_type', selectedActivityType);

            fetch(url)
                .then(response => response.json())
                .then(rawEvents => {
                    // Transform events untuk calendar
                    const transformedEvents = rawEvents.map(event => {
                        const startDate = new Date(event.start);
                        const endDate = new Date(event.end);
                        const startDay = startDate.toISOString().split('T')[0];
                        const endDay = endDate.toISOString().split('T')[0];
                        const isMultiDay = startDay !== endDay;

                        return {
                            id: event.id,
                            title: event.title,
                            start: event.start,
                            end: event.end,
                            allDay: event.allDay || false,
                            display: isMultiDay ? 'block' : 'auto',
                            classNames: isMultiDay ? ['continuous-event'] : [],
                            backgroundColor: isMultiDay ? 'rgba(75, 85, 99, 0.9)' : getLightColorForActivityType(event.extendedProps.activity_type),
                            borderColor: isMultiDay ? '#ffcc00' : 'transparent',
                            textColor: '#FFFFFF',
                            extendedProps: {
                                ...event.extendedProps,
                                isMultiDay: isMultiDay
                            }
                        };
                    });

                    successCallback(transformedEvents);
                })
                .catch(err => {
                    console.error('Error fetching events:', err);
                    failureCallback(err);
                });
        },

        eventDidMount: function(info) {
            if (info.event.extendedProps.isMultiDay) {
                // Pastikan event multi-hari ditampilkan dengan benar
                info.el.style.zIndex = '1';
                
                if (calendar.view.type.includes('timeGrid')) {
                    info.el.style.left = '0';
                    info.el.style.right = '0';
                    info.el.style.margin = '0 4px';
                }
            }
        },

        // Tambahkan eventClick handler
        eventClick: function(info) {
            let props = info.event.extendedProps;
            if (props.isMore) {
                let hidden = props.hiddenEvents || [];
                showMoreModal(hidden);
            } else {
                let st = info.event.start;
                let ed = info.event.end;
                let startStr = st ? st.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                let endStr = ed ? ed.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                
                // Format tanggal untuk event multi-hari
                let timeStr;
                if (props.isMultiDay) {
                    timeStr = `${st.toLocaleDateString('id-ID')} ${startStr} - ${ed.toLocaleDateString('id-ID')} ${endStr}`;
                } else {
                    timeStr = endStr ? `${startStr} - ${endStr}` : startStr;
                }

                showEventModal({
                    title: info.event.title,
                    time: timeStr,
                    department: props.department,
                    activity_type: props.activity_type,
                    location: props.location,
                    description: props.description
                });
            }
        },

        // Tambahkan eventContent handler
        eventContent: function(arg) {
            if (arg.event.extendedProps.isMore) {
                return {
                    html: `<div class="flex items-center justify-center w-full h-full font-bold">
                            ${arg.event.title}
                          </div>`
                };
            }

            let startTime = arg.event.start 
                ? arg.event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false })
                : '';
            let endTime = arg.event.end 
                ? arg.event.end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false })
                : '';
            let activityType = arg.event.extendedProps.activity_type || 'Unknown';
            let creator = arg.event.title || 'Unknown';

            // Format tanggal untuk event multi-hari
            let dateStr = '';
            if (arg.event.extendedProps.isMultiDay) {
                const startDate = arg.event.start;
                const endDate = arg.event.end;
                dateStr = `${startDate.toLocaleDateString('id-ID')} ${startTime} - ${endDate.toLocaleDateString('id-ID')} ${endTime}`;
            } else {
                dateStr = endTime ? `${startTime} - ${endTime}` : startTime;
            }

            // Popover styling
            if (arg.view.type.includes('popover')) {
                return {
                    html: `
                        <div class="fc-event-content p-2">
                            <div class="text-xs text-white text-opacity-90">${dateStr}</div>
                            <div class="text-xs font-semibold">${activityType} - ${creator}</div>
                        </div>
                    `
                };
            }
            
            return {
                html: `
                    <div class="fc-event-content">
                        <div class="text-xs text-white text-opacity-90">${dateStr}</div>
                        <div class="text-xs font-semibold">${activityType} - ${creator}</div>
                    </div>
                `
            };
        }
    });

    // Filter event handlers
    document.getElementById('departmentFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    document.getElementById('activityTypeFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // Render calendar
    calendar.render();

    // Handle window resize to adjust the view based on screen size
    window.addEventListener('resize', function() {
        const width = window.innerWidth;
        if (width < 768) {
            if (calendar.view.type === 'timeGridWeek') {
                calendar.changeView('timeGridDay');
            }
        }
    });
    
    // Add smooth animation to modal
    window.showEventModal = function(evt) {
        document.getElementById('modalTitle').textContent = evt.title || 'No Title';
        document.getElementById('modalDepartment').textContent = evt.department || '';
        document.getElementById('modalActivityType').textContent = evt.activity_type || '';
        document.getElementById('modalLocation').textContent = evt.location || '';
        document.getElementById('modalDescription').textContent = evt.description || '';

        eventModal.classList.remove('hidden');
        eventModal.classList.add('flex');
        
        // Add animation
        setTimeout(() => {
            const modalContent = eventModal.querySelector('.bg-gray-800');
            if (modalContent) {
                modalContent.classList.add('transform', 'scale-100', 'opacity-100');
                modalContent.classList.remove('transform', 'scale-95', 'opacity-0');
            }
        }, 10);
    }

    window.closeModal = function() {
        const modalContent = eventModal.querySelector('.bg-gray-800');
        if (modalContent) {
            modalContent.classList.add('transform', 'scale-95', 'opacity-0');
            modalContent.classList.remove('transform', 'scale-100', 'opacity-100');
        }
        
        setTimeout(() => {
            eventModal.classList.add('hidden');
            eventModal.classList.remove('flex');
        }, 200);
    }
});
</script>
@endpush
