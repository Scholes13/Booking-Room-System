@extends('admin_bas.layout')

@section('title', 'Kalender Aktivitas')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<style>
    .fc-event {
        cursor: pointer;
    }
    .fc-event-title {
        font-weight: 500;
    }
    .fc-daygrid-event-dot {
        display: none;
    }
    /* Theme colors for event status */
    .event-scheduled {
        background-color: #93c5fd;
        border-color: #3b82f6;
    }
    .event-ongoing {
        background-color: #86efac;
        border-color: #22c55e;
    }
    .event-completed {
        background-color: #d1d5db;
        border-color: #6b7280;
    }
    .event-cancelled {
        background-color: #fca5a5;
        border-color: #ef4444;
    }
    /* Hide time part in all-day events */
    .fc-daygrid-event .fc-event-time {
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
        <!-- Legend -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex items-center">
                <span class="inline-block w-3 h-3 rounded-full mr-2 bg-blue-300 border border-blue-500"></span>
                <span class="text-sm">Scheduled</span>
            </div>
            <div class="flex items-center">
                <span class="inline-block w-3 h-3 rounded-full mr-2 bg-green-300 border border-green-500"></span>
                <span class="text-sm">Ongoing</span>
            </div>
            <div class="flex items-center">
                <span class="inline-block w-3 h-3 rounded-full mr-2 bg-gray-300 border border-gray-500"></span>
                <span class="text-sm">Completed</span>
            </div>
            <div class="flex items-center">
                <span class="inline-block w-3 h-3 rounded-full mr-2 bg-red-300 border border-red-500"></span>
                <span class="text-sm">Cancelled</span>
            </div>
        </div>
        
        <!-- Calendar -->
        <div id="calendar"></div>
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
        // Fetch activities
        fetch('{{ route("bas.activities.json") }}')
            .then(response => response.json())
            .then(data => {
                initializeCalendar(data);
            })
            .catch(error => console.error('Error loading activities:', error));
            
        function initializeCalendar(events) {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events.map(event => ({
                    id: event.id,
                    title: event.name,
                    start: event.date + 'T' + event.start_time,
                    end: event.date + 'T' + event.end_time,
                    className: 'event-' + event.status,
                    extendedProps: {
                        room: event.room,
                        status: event.status,
                        description: event.description,
                        organizer: event.organizer
                    }
                })),
                eventClick: function(info) {
                    showModal(info.event);
                }
            });
            calendar.render();
        }
        
        window.showModal = function(event) {
            document.getElementById('modal-title').textContent = event.title;
            document.getElementById('modal-room').textContent = event.extendedProps.room;
            
            // Format date and time
            const startDate = new Date(event.start);
            const endDate = new Date(event.end);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit' };
            
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
                    statusText = 'Scheduled';
                    statusClass = 'bg-blue-100 text-blue-800';
                    break;
                case 'ongoing':
                    statusText = 'Ongoing';
                    statusClass = 'bg-green-100 text-green-800';
                    break;
                case 'completed':
                    statusText = 'Completed';
                    statusClass = 'bg-gray-100 text-gray-800';
                    break;
                case 'cancelled':
                    statusText = 'Cancelled';
                    statusClass = 'bg-red-100 text-red-800';
                    break;
            }
            
            statusEl.innerHTML = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>`;
            
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