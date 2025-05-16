@extends('admin_bas.layout')

@section('title', 'Kalender Aktivitas')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    /* Calendar container styles */
    .fc {
        font-family: 'Poppins', sans-serif;
    }
    
    .fc-view {
        overflow: visible;
    }

    .fc-daygrid-day-frame {
        min-height: 110px;
    }
    
    /* Day number styles */
    .fc-daygrid-day-number {
        font-size: 0.9rem;
        font-weight: 500;
        padding: 6px 8px;
        color: #4a5568;
    }

    /* Column header styles */
    .fc-col-header-cell {
        padding: 8px 0;
        background-color: #f1f5f9;
        font-weight: 600;
    }
    
    .fc-col-header-cell-cushion {
        color: #334155;
    }

    /* Main event styling */
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        margin-bottom: 3px;
        border-left-width: 4px !important;
        padding: 3px 6px !important;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    /* Event time display */
    .fc-event-time {
        font-weight: 600;
        font-size: 0.85rem;
        margin-right: 3px;
        color: #1e293b;
    }

    /* Event title display */
    .fc-event-title {
        font-weight: 500;
        font-size: 0.85rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #1e293b;
    }

    .fc-daygrid-event-dot {
        display: none;
    }

    /* Event status color schemes with better contrast */
    .event-scheduled {
        background-color: #dbeafe !important;
        border-color: #3b82f6 !important;
        color: #1e40af !important;
    }
    
    .event-scheduled .fc-event-time,
    .event-scheduled .fc-event-title {
        color: #1e40af !important;
    }

    .event-ongoing {
        background-color: #dcfce7 !important;
        border-color: #22c55e !important;
        color: #166534 !important;
    }
    
    .event-ongoing .fc-event-time,
    .event-ongoing .fc-event-title {
        color: #166534 !important;
    }

    .event-completed {
        background-color: #f1f5f9 !important;
        border-color: #64748b !important;
        color: #334155 !important;
    }
    
    .event-completed .fc-event-time,
    .event-completed .fc-event-title {
        color: #334155 !important;
    }

    .event-cancelled {
        background-color: #fee2e2 !important;
        border-color: #ef4444 !important;
        color: #b91c1c !important;
    }
    
    .event-cancelled .fc-event-time,
    .event-cancelled .fc-event-title {
        color: #b91c1c !important;
    }

    /* Multi-day event styling */
    .fc-event.fc-event-start.fc-event-end {
        border-radius: 4px;
    }

    .fc-event.fc-event-start:not(.fc-event-end) {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        margin-right: 0;
    }

    .fc-event.fc-event-end:not(.fc-event-start) {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        margin-left: 0;
    }

    .fc-event:not(.fc-event-start):not(.fc-event-end) {
        border-radius: 0;
        margin-left: 0;
        margin-right: 0;
        border-left: 0 !important;
        border-right: 0 !important;
    }

    /* Today highlight */
    .fc-day-today {
        background-color: rgba(250, 240, 137, 0.1) !important;
    }
    
    /* "More" button styling */
    .fc-daygrid-more-link {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4b5563;
        background: #e5e7eb;
        border-radius: 4px;
        padding: 2px 8px;
        margin-top: 2px;
        display: inline-block;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    /* Hover effect */
    .fc-daygrid-more-link:hover {
        background: #d1d5db;
        color: #1f2937;
    }

    /* "More events" popover styling */
    .fc-popover {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .fc-popover-header {
        background-color: #f8fafc;
        padding: 8px 10px;
        font-weight: 600;
        color: #334155;
        border-bottom: 1px solid #e2e8f0;
    }

    .fc-popover-body {
        padding: 8px;
        max-height: 300px;
        overflow-y: auto;
    }
    
    /* Legend styling improvement */
    .status-legend {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-right: 8px;
    }
    
    .status-scheduled {
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }
    
    .status-ongoing {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    
    .status-completed {
        background-color: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
    
    .status-cancelled {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    
    /* Fix empty event boxes */
    .fc-daygrid-event-harness:empty {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col h-full">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Kalender Aktivitas</h1>
        <div class="flex gap-2">
            <a href="{{ route('bas.activities.create') }}" class="inline-flex items-center justify-center text-white bg-bas hover:bg-opacity-90 py-2 px-4 rounded-md font-semibold text-sm">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Aktivitas
            </a>
            <a href="{{ route('bas.activities.index') }}" class="inline-flex items-center justify-center text-bas bg-secondary hover:bg-opacity-90 py-2 px-4 rounded-md font-semibold text-sm">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Lihat Daftar
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <!-- Improved Legend -->
        <div class="flex flex-wrap gap-3 mb-6">
            <span class="status-legend status-scheduled">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                Dijadwalkan
            </span>
            <span class="status-legend status-ongoing">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                Sedang Berlangsung
            </span>
            <span class="status-legend status-completed">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                Selesai
            </span>
            <span class="status-legend status-cancelled">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                Dibatalkan
            </span>
        </div>
        
        <!-- Calendar -->
        <div id="calendar" class="min-h-[600px]"></div>
    </div>
    
    <!-- Activity Detail Modal -->
    <div id="activityModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title"></h3>
                            <div class="mt-4 space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Ruangan</span>
                                    <span class="font-medium" id="modal-room"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tanggal & Waktu</span>
                                    <span class="font-medium" id="modal-date"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <span class="font-medium" id="modal-status"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Penyelenggara</span>
                                    <span class="font-medium" id="modal-organizer"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Deskripsi</span>
                                    <p class="text-sm" id="modal-description"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a id="modal-edit-link" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-bas text-base font-medium text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bas-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Edit
                    </a>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Calendar initialized, fetching data...');
        
        // Fetch activities
        fetch('{{ route("bas.activities.json") }}')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Fetched activities:', data);
                if (data.length === 0) {
                    console.warn('No activities found in the response');
                }
                initializeCalendar(data);
            })
            .catch(error => {
                console.error('Error loading activities:', error);
                document.getElementById('calendar').innerHTML = 
                    '<div class="p-4 text-center text-red-600">Error loading activities. Please check console for details.</div>';
            });
            
        function initializeCalendar(events) {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events.map(event => {
                    // Make sure we have proper status values
                    const validStatuses = ['scheduled', 'ongoing', 'completed', 'cancelled'];
                    // Use status from extendedProps if available, otherwise from main object
                    const statusValue = event.extendedProps?.status || event.status || 'scheduled';
                    const status = validStatuses.includes(statusValue) ? statusValue : 'scheduled';
                    
                    console.log('Mapping event:', event.title, 'Status:', status);
                    
                    return {
                        id: event.id,
                        title: event.title,
                        start: event.start, // ISO format
                        end: event.end,     // ISO format
                        className: 'event-' + status,
                        url: '{{ route('bas.activities.edit', '') }}/' + event.id,
                        extendedProps: {
                            room: event.extendedProps.room,
                            status: status,
                            description: event.extendedProps.description,
                            organizer: event.extendedProps.organizer,
                            department: event.extendedProps.department
                        }
                    };
                }),
                eventDidMount: function(info) {
                    // Clean up empty event containers
                    if (!info.el.innerText.trim()) {
                        info.el.parentNode.style.display = 'none';
                    }
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault(); // Don't navigate to URL
                    showModal(info.event);
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false
                },
                // Better event rendering
                eventDisplay: 'block',
                dayMaxEvents: 2, // Limit number of events per day for better readability
                moreLinkText: '+{count} lagi',
                moreLinkClick: 'popover',
                locale: 'id',
                firstDay: 1, // Start week on Monday
                fixedWeekCount: false, // Only show the actual weeks in a month
                showNonCurrentDates: false, // Hide days from other months
                buttonText: {
                    today: 'Hari ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    day: 'Hari'
                }
            });
            calendar.render();
        }
        
        window.showModal = function(event) {
            document.getElementById('modal-title').textContent = event.title;
            document.getElementById('modal-room').textContent = event.extendedProps.room || 'Tidak ada ruangan';
            
            // Format date and time
            const startDate = new Date(event.start);
            const endDate = new Date(event.end || event.start); // Handle case where end might be null
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: false };
            
            document.getElementById('modal-date').textContent = 
                startDate.toLocaleDateString('id-ID', options) + ', ' +
                startDate.toLocaleTimeString('id-ID', timeOptions) + ' - ' +
                endDate.toLocaleTimeString('id-ID', timeOptions);
                
            // Set status with colored badge
            const statusEl = document.getElementById('modal-status');
            let statusText = 'Unknown';
            let statusClass = '';
            
            switch(event.extendedProps.status) {
                case 'scheduled':
                    statusText = 'Dijadwalkan';
                    statusClass = 'bg-blue-100 text-blue-800 border border-blue-200';
                    break;
                case 'ongoing':
                    statusText = 'Sedang Berlangsung';
                    statusClass = 'bg-green-100 text-green-800 border border-green-200';
                    break;
                case 'completed':
                    statusText = 'Selesai';
                    statusClass = 'bg-gray-100 text-gray-800 border border-gray-200';
                    break;
                case 'cancelled':
                    statusText = 'Dibatalkan';
                    statusClass = 'bg-red-100 text-red-800 border border-red-200';
                    break;
            }
            
            statusEl.innerHTML = `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>`;
            
            document.getElementById('modal-organizer').textContent = event.extendedProps.organizer || '-';
            document.getElementById('modal-description').textContent = event.extendedProps.description || '-';
            
            // Set edit link
            document.getElementById('modal-edit-link').href = "{{ route('bas.activities.edit', '') }}/" + event.id;
            
            // Show modal
            document.getElementById('activityModal').classList.remove('hidden');
        }
        
        window.closeModal = function() {
            document.getElementById('activityModal').classList.add('hidden');
        }
    });
</script>
@endpush 