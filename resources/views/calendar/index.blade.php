@extends('layouts.app')

@section('title', 'Calendar View - Hover Kuning, Ruangan Hitam (Termasuk di More Modal)')

@section('content')
<div class="container mx-auto py-6 px-4 md:px-12">
    <!-- Filter Departemen dan Ruangan -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <select id="departmentFilter" class="bg-gray-700 text-white rounded-lg px-4 py-2 w-full md:w-64">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}">{{ $dept }}</option>
            @endforeach
        </select>

        <select id="roomFilter" class="bg-gray-700 text-white rounded-lg px-4 py-2 w-full md:w-64">
            <option value="">All Rooms</option>
            @foreach($rooms as $room)
                <option value="{{ $room }}">{{ $room }}</option>
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
                <p class="font-semibold">Time:</p>
                <p id="modalTime" class="ml-2"></p>
            </div>
            <div class="text-gray-300">
                <p class="font-semibold">Room:</p>
                <p id="modalRoom" class="ml-2"></p>
            </div>
            <div class="text-gray-300">
                <p class="font-semibold">Description:</p>
                <p id="modalDescription" class="ml-2"></p>
            </div>
            <div class="text-gray-300">
                <p class="font-semibold">Created By:</p>
                <p id="modalCreatedBy" class="ml-2"></p>
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
@endsection

@push('scripts')
<!-- FullCalendar CSS & JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<style>
/* ============== MONTH VIEW (dayGrid) ============== */
.fc-daygrid-event {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    padding: 8px 10px !important;
    background: rgba(18, 18, 18, 0.9) !important; 
    border-radius: 8px !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-y: auto !important;
    min-width: 100px !important;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(5px) !important;
    color: #fff !important;
}

.fc-daygrid-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3) !important;
}

.fc-daygrid-event:hover .fc-event-room {
    color: #000 !important;
}

/* "More" link bawaan Month View */
.fc-dayGridMonth-view .fc-daygrid-more-link {
    font-size: 12px !important;
    color: white !important;
    background-color: rgba(30, 30, 30, 0.85) !important;
    padding: 5px 8px !important;
    border-radius: 6px !important;
    font-weight: bold !important;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease-in-out;
}

.fc-dayGridMonth-view .fc-daygrid-more-link:hover {
    background-color: rgba(255, 204, 0, 0.9) !important;
    color: black !important;
    text-decoration: none;
    transform: scale(1.05);
}

/* Popover Month View */
.fc-popover {
    background: rgba(20, 20, 20, 0.95) !important;
    color: white !important;
    border-radius: 8px !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.6) !important;
}

.fc-popover-body {
    background: rgba(20, 20, 20, 0.9) !important;
    color: white !important;
}

.fc-popover-header {
    background: rgba(40, 40, 40, 0.9) !important;
    color: white !important;
    font-size: 14px !important;
    font-weight: bold !important;
}

/* Hover di popover => kuning, teks hitam */
.fc-popover .fc-daygrid-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 12px rgba(0,0,0,0.3) !important;
}

.fc-popover .fc-daygrid-event:hover .fc-event-room {
    color: #000 !important;
}

/* ============== WEEK/DAY VIEW (timeGrid) ============== */
.fc-timeGridWeek-view .fc-event,
.fc-timeGridDay-view .fc-event {
    background: rgba(18, 18, 18, 0.9) !important;
    color: #fff !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(5px) !important;
    transition: all 0.3s ease-in-out;
    min-width: 70px !important;
    margin: 0 4px !important;
    padding: 6px 8px !important;
    font-size: 0.8rem !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
    overflow: hidden !important;
}

/* Style khusus untuk event "+more" di week/day view */
.fc-timeGridWeek-view .more-event,
.fc-timeGridDay-view .more-event {
    background: rgba(75, 85, 99, 0.9) !important;
    color: #fff !important;
    font-weight: bold !important;
    text-align: center !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    z-index: 5 !important;
}

.fc-timeGridWeek-view .more-event:hover,
.fc-timeGridDay-view .more-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
    transform: translateY(-2px) scale(1.02);
}

.fc-timeGridWeek-view .fc-event:hover,
.fc-timeGridDay-view .fc-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    color: #000 !important;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3) !important;
    cursor: pointer;
}

.fc-timeGridWeek-view .fc-event:hover .fc-event-room,
.fc-timeGridDay-view .fc-event:hover .fc-event-room {
    color: #000 !important;
}

/* Konten event: jam, room, dsb. */
.fc-event-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.fc-event-time {
    font-size: 0.85em;
    font-weight: 600;
}

.fc-event-room {
    font-size: 0.85em;
    font-weight: 600;
    margin: 2px 0;
}

.fc-event-description {
    font-size: 0.75em;
    white-space: normal;
    overflow-wrap: break-word;
}

/* Utility */
.hidden { display: none; }
.flex { display: flex; }
.cursor-pointer { cursor: pointer; }

/* Highlight hari ini di tampilan month/week/day */
.fc-day-today {
    background-color: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
}

/* (Opsional) Menonjolkan nomor tanggal pada tampilan month view */
.fc-day-today .fc-daygrid-day-number {
    background-color: #ffcc00;
    color: #000;
    border-radius: 50%;
    padding: 2px 6px;
    font-weight: bold;
}

/* (Opsional) Mengatur garis indikator waktu saat ini (week/day view) */
.fc-now-indicator-line {
    border-top: 2px solid #ffcc00 !important;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper Functions
    function getLightColorForRoom(roomName) {
        let colors = {
            "Meeting Room Besar": "#EF5350",
            "Dorme": "#66BB6A",
            "Meeting Room Kecil": "#42A5F5",
            "Command Center": "#FFA726",
            "Ruang Rapat Direksi": "#AB47BC",
            "ACS Room": "#26C6DA",
            "default": "#FFEE58"
        };
        return colors[roomName] || colors["default"];
    }

    function formatTimeRange(start, end) {
        const startDate = new Date(start);
        const endDate = end ? new Date(end) : null;
        
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
        
        return `${startStr} - ${endStr}`;
    }

    // Transform events untuk Week/Day View
    function transformEventsForTimeGrid(events) {
        const limit = 1;
        const grouped = {};
        
        // Kelompokkan events berdasarkan slot waktu
        events.forEach(evt => {
            const startDate = new Date(evt.start);
            const endDate = new Date(evt.end);
            
            // Generate key untuk setiap 30 menit interval dalam rentang waktu event
            let currentTime = new Date(startDate);
            while (currentTime < endDate) {
                const timeKey = `${currentTime.toISOString().split('T')[0]}_${currentTime.getHours()}_${Math.floor(currentTime.getMinutes() / 30) * 30}`;
                
                if (!grouped[timeKey]) {
                    grouped[timeKey] = [];
                }
                grouped[timeKey].push({
                    ...evt,
                    originalStart: evt.start,
                    originalEnd: evt.end,
                    // Set start dan end untuk slot 30 menit
                    start: currentTime.toISOString(),
                    end: new Date(currentTime.getTime() + 30 * 60000).toISOString()
                });
                
                currentTime = new Date(currentTime.getTime() + 30 * 60000);
            }
        });
        
        const finalEvents = [];
        const processedEvents = new Set(); // Untuk tracking event yang sudah diproses
        
        Object.entries(grouped).forEach(([timeKey, eventsInSlot]) => {
            if (eventsInSlot.length <= limit) {
                // Untuk event yang belum diproses, tambahkan dengan waktu aslinya
                eventsInSlot.forEach(evt => {
                    if (!processedEvents.has(evt.id)) {
                        finalEvents.push({
                            ...evt,
                            start: evt.originalStart,
                            end: evt.originalEnd
                        });
                        processedEvents.add(evt.id);
                    }
                });
            } else {
                // Ambil event pertama jika belum diproses
                const firstEvent = eventsInSlot[0];
                if (!processedEvents.has(firstEvent.id)) {
                    finalEvents.push({
                        ...firstEvent,
                        start: firstEvent.originalStart,
                        end: firstEvent.originalEnd
                    });
                    processedEvents.add(firstEvent.id);
                }
                
                // Tambahkan +more untuk slot ini
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
                                time: formatTimeRange(evt.originalStart, evt.originalEnd),
                                room_name: evt.extendedProps?.room_name,
                                description: evt.extendedProps?.description,
                                created_by: evt.extendedProps?.created_by
                            }))
                        }
                    });
                    
                    // Tandai semua hidden events sebagai sudah diproses
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
        document.getElementById('modalTime').textContent = evt.time || '';
        document.getElementById('modalRoom').textContent = evt.room_name || 'Meeting Room';
        document.getElementById('modalDescription').textContent = evt.description || '';
        document.getElementById('modalCreatedBy').textContent = evt.created_by || '';

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
                let roomNameEl = card.querySelector('.room-name');
                if (roomNameEl) {
                    roomNameEl.style.color = '#000';
                }
            });

            card.addEventListener('mouseleave', () => {
                card.style.background = 'rgba(18,18,18,0.9)';
                card.style.color = '#fff';
                let roomNameEl = card.querySelector('.room-name');
                if (roomNameEl) {
                    let originalColor = getLightColorForRoom(ev.room_name || '');
                    roomNameEl.style.color = originalColor;
                }
            });

            let roomColor = getLightColorForRoom(ev.room_name || '');
            card.innerHTML = `
                <div class="font-bold text-sm">${ev.title} (${ev.time})</div>
                <div class="text-xs room-name" style="color: ${roomColor};">
                    Room: ${ev.room_name || 'Meeting Room'}
                </div>
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
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '00:00:00',
        slotMaxTime: '24:00:00',
        slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        eventOverlap: false,
        nowIndicator: true, // Menampilkan garis penanda waktu berjalan

        views: {
            dayGridMonth: {
                dayMaxEvents: 2,
                dayMaxEventRows: true,
                moreLinkClick: 'popover'
            }
        },

        events: function(info, successCallback, failureCallback) {
            let url = new URL("{{ route('calendar.events') }}");
            url.searchParams.append('start', info.startStr);
            url.searchParams.append('end', info.endStr);

            const selectedDept = document.getElementById('departmentFilter').value;
            const selectedRoom = document.getElementById('roomFilter').value;
            if (selectedDept) url.searchParams.append('department', selectedDept);
            if (selectedRoom) url.searchParams.append('room', selectedRoom);

            fetch(url)
                .then(response => response.json())
                .then(rawEvents => {
                    let isTimeGrid = (calendar.view.type === 'timeGridWeek' || calendar.view.type === 'timeGridDay');
                    if (isTimeGrid) {
                        let transformed = transformEventsForTimeGrid(rawEvents);
                        successCallback(transformed);
                    } else {
                        successCallback(rawEvents);
                    }
                })
                .catch(err => {
                    console.error('Error fetching events:', err);
                    failureCallback(err);
                });
        },

        viewDidMount: function() {
            calendar.refetchEvents();
        },

        datesSet: function() {
            calendar.refetchEvents();
        },

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
                let timeStr = endStr ? `${startStr} - ${endStr}` : startStr;

                showEventModal({
                    title: info.event.title,
                    time: timeStr,
                    room_name: props.room_name,
                    description: props.description,
                    created_by: props.created_by
                });
            }
        },

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
            let roomName = arg.event.extendedProps.room_name || 'Meeting Room';
            let description = arg.event.extendedProps.description || '';
            let roomColor = getLightColorForRoom(roomName);

            let timeHtml = endTime ? `${startTime} - ${endTime}` : startTime;
            return {
                html: `
                    <div class="fc-event-content">
                        <div class="fc-event-time">${timeHtml}</div>
                        <div class="fc-event-room" style="color: ${roomColor};">${roomName}</div>
                        <div class="fc-event-description">${description}</div>
                    </div>
                `
            };
        }
    });

    // Filter event handlers
    document.getElementById('departmentFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    document.getElementById('roomFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // Render calendar
    calendar.render();
});
</script>
@endpush
