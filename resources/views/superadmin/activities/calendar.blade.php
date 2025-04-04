@extends('superadmin.layout')

@section('title', 'Kalender Aktivitas')

@section('content')
<div class="py-5 px-4">
    <div class="flex justify-between items-center mb-5">
        <h1 class="text-dark tracking-light text-[32px] font-bold leading-tight">Kalender Aktivitas</h1>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.activities.index') }}" class="flex min-w-[84px] max-w-[180px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-white border border-primary text-primary text-sm font-bold leading-normal tracking-[0.015em]">
                <i class="fas fa-list mr-2"></i>
                <span class="truncate">Daftar Aktivitas</span>
            </a>
            <a href="{{ route('superadmin.activities.create') }}" class="flex min-w-[84px] max-w-[180px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
                <i class="fas fa-plus mr-2"></i>
                <span class="truncate">Tambah Aktivitas</span>
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-border p-6 shadow-sm">
        <!-- Filters -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="w-full md:w-auto">
                <label for="department_filter" class="block text-sm font-medium text-dark mb-2">Filter Departemen</label>
                <select id="department_filter" class="w-full md:w-60 p-2 border border-border rounded-md bg-white text-dark outline-none">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-auto">
                <label for="activity_type_filter" class="block text-sm font-medium text-dark mb-2">Filter Jenis Aktivitas</label>
                <select id="activity_type_filter" class="w-full md:w-60 p-2 border border-border rounded-md bg-white text-dark outline-none">
                    <option value="">Semua Jenis Aktivitas</option>
                    @foreach($activityTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-auto mt-auto">
                <button id="reset_filters" class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-secondary text-dark text-sm font-bold leading-normal tracking-[0.015em]">
                    <i class="fas fa-redo mr-2"></i> Reset Filter
                </button>
            </div>
        </div>
        
        <!-- Calendar -->
        <div id="activity_calendar" class="mt-4"></div>
        
        <!-- Event Details Modal -->
        <div id="eventModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-dark" id="modal-title"></h3>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 text-primary">
                                            <i class="fas fa-user-tie w-5 h-5"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-dark" id="modal-department"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 text-primary">
                                            <i class="fas fa-tasks w-5 h-5"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-dark" id="modal-activity-type"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 text-primary">
                                            <i class="fas fa-map-marker-alt w-5 h-5"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-dark" id="modal-location"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 text-primary">
                                            <i class="fas fa-calendar w-5 h-5"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-dark" id="modal-dates"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 text-primary">
                                            <i class="fas fa-info-circle w-5 h-5"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-dark" id="modal-description"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <a id="edit-event-btn" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <button type="button" id="close-modal-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
<style>
    .fc-event {
        cursor: pointer;
    }
    .fc .fc-toolbar-title {
        font-size: 1.25rem;
    }
    .fc .fc-button {
        background-color: #22428e;
        border-color: #22428e;
    }
    .fc .fc-button:hover {
        background-color: #1b3672;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #1b3672;
    }
    .fc-event.internal {
        background-color: #22428e;
        border-color: #1b3672;
    }
    .fc-event.external {
        background-color: #A18249;
        border-color: #8a6e3e;
    }
    .fc-event.training {
        background-color: #38a169;
        border-color: #2f855a;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/locales-all.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('activity_calendar');
        const departmentFilter = document.getElementById('department_filter');
        const activityTypeFilter = document.getElementById('activity_type_filter');
        const resetFiltersBtn = document.getElementById('reset_filters');
        const eventModal = document.getElementById('eventModal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const editEventBtn = document.getElementById('edit-event-btn');
        
        let currentEventId = null;
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'id',
            themeSystem: 'standard',
            height: 'auto',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
                hour12: false
            },
            events: function(info, successCallback, failureCallback) {
                fetch('{{ route("superadmin.activities.calendar.events") }}?' + new URLSearchParams({
                    start: info.startStr,
                    end: info.endStr,
                    department_id: departmentFilter.value,
                    activity_type: activityTypeFilter.value
                }))
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
            },
            eventClick: function(info) {
                currentEventId = info.event.id;
                
                // Set modal content
                document.getElementById('modal-title').textContent = info.event.title;
                document.getElementById('modal-department').textContent = 'Departemen: ' + info.event.extendedProps.department;
                document.getElementById('modal-activity-type').textContent = 'Jenis Aktivitas: ' + info.event.extendedProps.activity_type;
                document.getElementById('modal-location').textContent = 'Lokasi: ' + info.event.extendedProps.location;
                
                // Format dates
                const startDate = new Date(info.event.start);
                const endDate = new Date(info.event.end || info.event.start);
                
                const formatOptions = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit' 
                };
                
                const dateText = `${startDate.toLocaleDateString('id-ID', formatOptions)} - ${endDate.toLocaleDateString('id-ID', formatOptions)}`;
                document.getElementById('modal-dates').textContent = 'Waktu: ' + dateText;
                
                document.getElementById('modal-description').textContent = 'Deskripsi: ' + info.event.extendedProps.description;
                
                // Set edit button link
                document.getElementById('edit-event-btn').href = '{{ route("superadmin.activities.edit", "") }}/' + info.event.id;
                
                // Show modal
                eventModal.classList.remove('hidden');
            }
        });
        
        calendar.render();
        
        // Apply filters when changed
        departmentFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
        
        activityTypeFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
        
        // Reset filters
        resetFiltersBtn.addEventListener('click', function() {
            departmentFilter.value = '';
            activityTypeFilter.value = '';
            calendar.refetchEvents();
        });
        
        // Close modal
        closeModalBtn.addEventListener('click', function() {
            eventModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        eventModal.addEventListener('click', function(e) {
            if (e.target === eventModal) {
                eventModal.classList.add('hidden');
            }
        });
    });
</script>
@endpush 