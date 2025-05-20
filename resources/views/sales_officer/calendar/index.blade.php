@extends('sales_officer.layout')

@section('title', 'Activity Calendar')
@section('header', 'Activity Calendar')
@section('description', 'View and manage your scheduled activities')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
@endpush

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div id="calendar"></div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: "{{ route('sales_officer.calendar.events') }}",
            eventClassNames: ['sales-officer-event'],
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            eventClick: function(info) {
                // Show event details (can be expanded with a modal/popup)
                alert('Activity: ' + info.event.title);
            }
        });
        calendar.render();
    });
</script>
@endpush
@endsection 