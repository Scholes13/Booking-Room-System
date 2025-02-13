@extends('layouts.app')

@section('title', 'Calendar View')

@section('content')
<div class="container mx-auto py-6 px-4 md:px-12">
    <!-- Filter Departemen -->
    <div class="mb-6">
        <select id="departmentFilter" class="bg-gray-700 text-white rounded-lg px-4 py-2 w-full md:w-64">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}">{{ $dept }}</option>
            @endforeach
        </select>
    </div>
    
    <!-- Calendar Container -->
    <div class="max-w-screen-xl mx-auto p-4 bg-white/10 backdrop-blur-lg rounded-xl shadow-xl" id="calendar-container">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal untuk detail event -->
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
@endsection

@push('scripts')
<!-- FullCalendar CSS & JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<style>
/* Pastikan kalender menggunakan lebar penuh secara default */
#calendar {
    width: 100%;
    margin: 0 auto;
}

/* ===================== RESPONSIVE STYLING ===================== */
@media (max-width: 768px) {
    /* Toolbar di mobile: ditumpuk ke bawah */
    .fc-toolbar {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    /* Perkecil font-size judul agar tidak memakan ruang berlebihan */
    .fc-toolbar-title {
        font-size: 1rem; 
    }

    /* Izinkan scroll horizontal hanya di tampilan month view */
    .fc-dayGridMonth-view {
        min-width: 500px;  /* Agar kolom tanggal tidak terlalu sempit */
    }

    /* Tambahkan scroll horizontal agar grid tidak terpotong */
    #calendar {
        overflow-x: auto;
    }

    /* Jarak ke bawah dan penataan sel di mobile */
    #calendar-container {
        margin-bottom: 1.5rem; /* Jarak ke bawah */
    }
    .fc-daygrid-day-frame {
        min-height: 90px; /* Tinggi minimum sel di month view */
        padding: 0.5rem;  /* Spasi di dalam sel */

        /* === Penambahan snippet untuk menempatkan "+ more" di bawah === */
        display: flex;
        flex-direction: column; 
        justify-content: flex-start;
        height: 100%;
        position: relative;
    }
    .fc-daygrid-day-top {
        margin-bottom: auto; /* Dorong event & more link ke bawah */
    }
    .fc-daygrid-day-bottom {
        margin-top: auto;
        margin-bottom: 4px; /* Sedikit jarak dari dasar sel */
    }
}

@media (min-width: 1024px) {
    /* Contoh: beri margin antar event di week view agar lebih rapi */
    .fc-timeGridWeek-view .fc-event {
        margin-bottom: 8px;
    }
}

/* ===================== STYLE EVENT ===================== */
.fc-daygrid-event, .fc-timeGridWeek-view .fc-event, .fc-timeGridDay-view .fc-event {
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
}

.fc-event-content {
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 6px;
}

/* Jam */
.fc-event-time {
    font-size: 0.95em;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.95);
    letter-spacing: 0.2px;
}

/* Room Name - Warna unik untuk setiap ruang */
.fc-event-room {
    font-size: 0.9em;
    font-weight: 600;
    white-space: normal;
    transition: all 0.3s ease-in-out;
    letter-spacing: 0.2px;
    margin: 2px 0;
}

/* Deskripsi */
.fc-event-description {
    font-size: 0.85em;
    color: rgba(255, 255, 255, 0.9);
    white-space: normal;
    overflow-wrap: break-word;
}

/* Hover effect dengan animasi smooth */
.fc-daygrid-event:hover, 
.fc-timeGridWeek-view .fc-event:hover, 
.fc-timeGridDay-view .fc-event:hover {
    background: rgba(255, 204, 0, 0.95) !important;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3) !important;
}

/* Efek Hover - Room Name dan text lain berubah warna */
.fc-daygrid-event:hover .fc-event-time,
.fc-timeGridWeek-view .fc-event:hover .fc-event-time,
.fc-timeGridDay-view .fc-event:hover .fc-event-time,
.fc-daygrid-event:hover .fc-event-room,
.fc-timeGridWeek-view .fc-event:hover .fc-event-room,
.fc-timeGridDay-view .fc-event:hover .fc-event-room,
.fc-daygrid-event:hover .fc-event-description,
.fc-timeGridWeek-view .fc-event:hover .fc-event-description,
.fc-timeGridDay-view .fc-event:hover .fc-event-description {
    color: rgba(0, 0, 0, 0.9) !important;
}

/* --- Perbaikan Background "+ more" --- */
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

/* --- Perbaikan Popover --- */
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var eventModal = document.getElementById('eventModal');

    // Warna berbeda untuk setiap nama ruangan
    function getLightColorForRoom(roomName) {
        let colors = {
            "Meeting Room Besar": "#FF6B6B",
            "Dorme": "#4ECDC4",
            "Meeting Room Kecil": "#45B7D1",
            "Command Center": "#96C93D",
            "Ruang Rapat Direksi": "#A06CD5",
            "default": "#FF9F43"
        };
        return colors[roomName] || colors["default"];
    }

    // Format date + time (24 jam) untuk tampilan di modal
    function formatDateTime(date, time) {
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        };
        return new Date(date + 'T' + time).toLocaleString('id-ID', options);
    }

    // Tampilkan detail event di modal
    function showEventModal(info) {
        const event = info.event;
        document.getElementById('modalTitle').textContent = event.title;
        
        // Format start & end dengan 24 jam
        const startArr = event.startStr.split('T');
        const endArr = event.endStr ? event.endStr.split('T') : null;
        const startTime = startArr ? formatDateTime(startArr[0], startArr[1]) : '';
        const endTime = endArr ? formatDateTime(endArr[0], endArr[1]) : '';

        document.getElementById('modalTime').textContent = `${startTime}${endTime ? ' - ' + endTime : ''}`;
        document.getElementById('modalRoom').textContent = event.extendedProps.room_name || 'Meeting Room';
        document.getElementById('modalDescription').textContent = event.extendedProps.description || 'No description available';
        document.getElementById('modalCreatedBy').textContent = event.extendedProps.created_by || 'Belum ada nama';

        eventModal.classList.remove('hidden');
        eventModal.classList.add('flex');
    }

    // Tutup modal
    window.closeModal = function() {
        eventModal.classList.add('hidden');
        eventModal.classList.remove('flex');
    }

    // Tutup modal saat klik di luar area modal
    eventModal.addEventListener('click', function(e) {
        if (e.target === eventModal) {
            closeModal();
        }
    });

    // Inisialisasi FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        // Menggunakan locale 'id' agar axis & date format menyesuaikan
        locale: 'id',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        // Menampilkan rentang waktu 00:00 - 24:00 di week/day view
        slotMinTime: '00:00:00',
        slotMaxTime: '24:00:00',
        // Label jam di axis juga 24 jam
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        views: {
            dayGridMonth: {
                dayMaxEvents: 2,
                dayMaxEventRows: true,
                moreLinkClick: 'popover',
            }
        },
        // Ambil data event dari route
        events: function(info, successCallback, failureCallback) {
            fetch("{{ route('calendar.events') }}")
                .then(response => response.json())
                .then(events => {
                    const selectedDept = document.getElementById('departmentFilter').value;
                    const filteredEvents = selectedDept 
                        ? events.filter(event => event.extendedProps.department === selectedDept)
                        : events;
                    successCallback(filteredEvents);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        // Saat event di-klik, tampilkan modal
        eventClick: showEventModal,
        // Custom render event (untuk menampilkan jam 24 jam di label event)
        eventContent: function(arg) {
            let startTime = arg.event.start 
                ? arg.event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) 
                : '';
            let endTime = arg.event.end 
                ? arg.event.end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) 
                : '';

            let roomName = arg.event.extendedProps.room_name || 'Meeting Room';
            let description = arg.event.extendedProps.description || '';
            let roomColor = getLightColorForRoom(roomName);

            let innerHtml = `
                <div class="fc-event-content">
                    <div class="fc-event-time font-semibold">${startTime}${endTime ? ' - ' + endTime : ''}</div>
                    <div class="fc-event-room" style="color: ${roomColor};">${roomName}</div>
                    <div class="fc-event-description text-white text-sm">${description}</div>
                </div>
            `;
            return { html: innerHtml };
        }
    });

    // Filter department
    document.getElementById('departmentFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // Render kalender
    calendar.render();
});
</script>
@endpush
