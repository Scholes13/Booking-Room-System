@extends('sales_mission.layout')

@section('title', 'Preview Sales Mission Agenda')
@section('header', 'Preview Agenda')
@section('description', 'Review the agenda before export')

@section('content')
<div class="space-y-6">
    <!-- Actions -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('sales_mission.reports.agenda') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sales">
            <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
        <form action="{{ route('sales_mission.reports.agenda.export') }}" method="POST" class="inline-block">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export to Excel
            </button>
        </form>
    </div>

    <!-- Agenda Preview -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header Information -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">AGENDA DINAS SALES MISSION</h2>
                    <p class="text-md text-gray-700">Employee Information</p>
                </div>
                <div class="text-right text-sm">
                    <p class="text-gray-600">Date of Create</p>
                    <p class="font-medium">{{ \Carbon\Carbon::now()->format('l, j F Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-x-6 gap-y-4 text-sm mb-4">
                <div>
                    <p class="text-gray-600">Name</p>
                    <p class="font-medium">{{ $team->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Department</p>
                    <p class="font-medium">{{ $team->department ?? 'PT Werkudara Nirwana Sakti' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Month</p>
                    <p class="font-medium">{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('F Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Group</p>
                    <p class="font-medium">
                        @if($team->members && $team->members->isNotEmpty())
                            {{ $team->members->pluck('name')->implode(', ') }}
                        @else
                            No members assigned
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Purpose</p>
                    <p class="font-medium">Sales Call Yogyakarta</p> {{-- Consider making this dynamic --}}
                </div>
                <div>
                    <p class="text-gray-600">Periode</p>
                    <p class="font-medium">{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('j') : '' }} - {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('j F Y') : 'N/A' }}</p>
                </div>
            </div>
            <p class="text-xs italic text-gray-600">* Agenda disesuaikan dengan kebutuhan acara</p>
        </div>

        <!-- Agenda Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Instansi / Corporate</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Bisnis</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat Instansi</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Telpon</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @php 
                        $currentDay = 0; 
                        $previousDate = null;
                        $activityNumberThisDay = 1;
                    @endphp
                    @if($groupedActivities instanceof \Illuminate\Support\Collection && $groupedActivities->isNotEmpty())
                        @foreach($groupedActivities as $date => $activities)
                            @if($date !== $previousDate)
                                @php 
                                    $currentDay++; 
                                    $activityNumberThisDay = 1; // Reset nomor untuk hari baru
                                    $previousDate = $date;
                                @endphp
                            @endif
                            @if(is_iterable($activities))
                                @foreach($activities as $activity)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $loop->first ? $currentDay : '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $loop->first ? \Carbon\Carbon::parse($date)->format('d-M-y') : '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $loop->first ? \Carbon\Carbon::parse($date)->format('l') : '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($activity->start_datetime ?? now())->format('H:i') }}</td>
                                        <td class="px-2 py-3 whitespace-nowrap">{{ $activityNumberThisDay++ }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div>{{ optional($activity->salesMissionDetail)->company_name ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->blitz_company_name : '-') }}</div>
                                            @if(isset($activity->is_blitz) && $activity->is_blitz)
                                                <div class="text-xs text-purple-600 italic">Blitz</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if(isset($activity->is_blitz) && $activity->is_blitz)
                                                {{-- Kosong untuk Blitz --}}
                                            @else
                                                {{ optional($activity->salesMissionDetail)->business_line ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-normal max-w-xs">{{ optional($activity->salesMissionDetail)->company_address ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->department : '-') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ optional($activity->salesMissionDetail)->company_pic ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->contact_name : '-') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ optional($activity->salesMissionDetail)->company_position ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->contact_job_title : '-') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ optional($activity->salesMissionDetail)->company_contact ?? (isset($activity->is_blitz) ? optional($activity->originalBlitzSurvey)->contact_mobile : '-') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No activities found for the selected period</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="p-6 mt-6 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p>Kensrie Diah Ayuningtyas</p> {{-- Placeholder --}}
                    <p class="text-xs text-gray-600">Field Coordinator</p> {{-- Placeholder --}}
                </div>
                <div class="text-right">
                    <p>Nofri Eka Sanjaya</p> {{-- Placeholder --}}
                    <p class="text-xs text-gray-600">Captain Sales Mission</p> {{-- Placeholder --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 