@extends('sales_mission.layout') {{-- Or your admin layout, e.g., @extends('layouts.admin') --}}

@section('title', 'Daily Team Schedule')
@section('header', 'Daily Team Schedule')

@push('styles')
<style>
    .schedule-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }
    
    .time-column {
        width: 80px;
    }
    
    .schedule-table th,
    .schedule-table td {
        border: 1px solid #ddd;
        padding: 0;
        text-align: center;
        font-size: 0.8rem;
        height: 40px; /* Fixed height for each cell */
        position: relative; /* Added for absolute positioning context */
    }
    
    .schedule-table th {
        background-color: #f8f9fa;
        padding: 8px;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .time-slot {
        padding: 8px;
        background-color: #f8f9fa;
        font-weight: 500;
        position: sticky;
        left: 0;
        z-index: 5;
    }
    
    .schedule-cell {
        position: relative;
        padding: 0;
        vertical-align: top;
    }
    
    .activity-container {
        position: absolute;
        left: 2px;
        right: 2px;
        top: 2px;
        bottom: 2px;
        display: flex;
        flex-direction: column;
        justify-content: center; /* Changed to center vertically */
        align-items: center; /* Added to center horizontally */
        background-color: #fff3cd;
        border-radius: 4px;
        padding: 4px;
        overflow: hidden;
        text-align: center; /* Added to center text */
    }
    
    .activity-container:hover {
        background-color: #ffe5b4;
    }
    
    .activity-title {
        font-size: 0.75rem;
        font-weight: 600;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%; /* Added to ensure ellipsis works properly */
    }
    
    .activity-time {
        font-size: 0.65rem;
        color: #666;
        margin: 2px 0 0 0;
    }

    .date-filter-form {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .date-filter-form label {
        font-weight: bold;
    }
    
    .date-filter-form input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    
    .date-filter-form button {
        padding: 8px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .date-filter-form button:hover {
        background-color: #0056b3;
    }

    /* Updated rowspan cell styles */
    .schedule-cell[rowspan] {
        vertical-align: top;
        height: auto !important; /* Override fixed height for rowspan cells */
    }
    
    .schedule-cell[rowspan] .activity-container {
        position: absolute;
        top: 2px;
        bottom: 2px;
        left: 2px;
        right: 2px;
        height: auto !important; /* Let height be determined by cell */
        display: flex;
        flex-direction: column;
        justify-content: center; /* Center content vertically */
        align-items: center; /* Center content horizontally */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Schedule for {{ $carbonSelectedDate->format('D, d M Y') }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales_mission.daily_schedule') }}" method="GET" class="date-filter-form">
                        <label for="date">Select Date:</label>
                        <input type="date" id="date" name="date" value="{{ $selectedDate }}" class="form-control form-control-sm" style="width: auto;">
                        <button type="submit" class="btn btn-sm btn-primary">View Schedule</button>
                    </form>

                    @if($teams->isEmpty())
                        <p>No teams available to display.</p>
                    @else
                        <div class="table-responsive">
                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th class="time-column">Time</th>
                                        @foreach ($teams as $team)
                                            <th>{{ $team->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timeSlots as $slot)
                                        <tr>
                                            <td class="time-slot">{{ $slot }}</td>
                                            @foreach ($teams as $columnIndex => $team)
                                                {{-- DEBUG: Info Tim untuk sel ini --}}
                                                <!-- 
                                                    Slot: {{ $slot }}, 
                                                    Tim di Kolom Index: {{ $columnIndex }}, 
                                                    Nama Tim: {{ $team->name }}, 
                                                    ID Tim yg dipakai: {{ $team->id }}
                                                -->
                                                @php
                                                    $activityData = $scheduleData[$slot][$team->id] ?? null;
                                                    $companyNameForDebug = is_array($activityData) ? $activityData['company_name'] : 'NULL';
                                                @endphp
                                                
                                                {{-- DEBUG: Data mentah yang diambil untuk slot dan tim ini --}}
                                                <!-- 
                                                    Activity Data Fetched for [{{ $slot }}][{{ $team->id }} - {{ $team->name }}]: 
                                                    {{ $companyNameForDebug }}
                                                    Raw: {{ print_r($activityData, true) }}
                                                -->

                                                {{-- DEBUG KHUSUS UNTUK HARBOUR ENERGY & TOYOTA SEKITAR JAM 11:00 --}}
                                                @if($slot == '11:00' || $slot == '11:30')
                                                    @if(is_array($activityData) && (strpos($activityData['company_name'], 'Harbour Energy') !== false || strpos($activityData['company_name'], 'Toyota') !== false))
                                                        <!-- 
                                                            Slot {{ $slot }} - Tim {{ $team->name }} (ID: {{ $team->id }}) - DITEMUKAN TARGET DEBUG: {{ $activityData['company_name'] }}
                                                            Rowspan akan: {{ $activityData['rowspan'] ?? 'N/A' }}
                                                        -->
                                                    @endif
                                                    @if($companyNameForDebug === 'Harbour Energy')
                                                    <!-- KHUSUS DEBUG HARBOUR: Muncul di kolom Tim {{ $team->name }} (ID: {{ $team->id }}) untuk slot {{ $slot }} -->
                                                    @endif
                                                @endif


                                                @if (is_array($activityData))
                                                    @php
                                                        // Calculate the actual height based on rowspan
                                                        $heightInPixels = 40 * $activityData['rowspan'];
                                                    @endphp
                                                    <td class="schedule-cell" rowspan="{{ $activityData['rowspan'] }}" style="height: {{ $heightInPixels }}px;">
                                                        <!-- DEBUG: Activity: {{ $activityData['company_name'] }}, Rowspan: {{ $activityData['rowspan'] }} -->
                                                        <div class="activity-container">
                                                            <p class="activity-title" title="{{ $activityData['company_name'] }}">
                                                                {{ Str::limit($activityData['company_name'], 25) }}
                                                            </p>
                                                            <p class="activity-time">
                                                                {{ $activityData['start_time'] }} - {{ $activityData['end_time'] }}
                                                            </p>
                                                        </div>
                                                    </td>
                                                @elseif ($activityData === null)
                                                    <td class="schedule-cell">
                                                        {{-- Sel Kosong --}}
                                                        <!-- Slot: {{ $slot }}, Tim: {{ $team->name }} (ID: {{ $team->id }}) - Sel Kosong -->
                                                    </td>
                                                @endif {{-- Akhir dari if (is_array($activityData)) --}}
                                            @endforeach {{-- Akhir loop $teams untuk sel data --}}
                                        </tr>
                                    @endforeach {{-- Akhir loop $timeSlots --}}
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Add any JavaScript specific to this page here
</script>
@endpush 