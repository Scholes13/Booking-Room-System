@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-dark tracking-light text-[32px] font-bold leading-tight px-4 text-left pb-3 pt-6">Dashboard Overview</h1>

<!-- Stats Cards -->
<div class="flex flex-wrap gap-4 p-4">
    <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 border border-border">
        <p id="bookingLabel" class="text-dark text-sm font-medium leading-snug">Today's Bookings</p>
        <p id="todayBookings" class="text-dark tracking-light text-3xl font-bold leading-tight">2</p>
        <p class="text-primary text-xs font-medium leading-snug">+10%</p>
    </div>
    <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 border border-border">
        <p class="text-dark text-sm font-medium leading-snug">Usage Rate</p>
        <p id="roomUsage" class="text-dark tracking-light text-3xl font-bold leading-tight">107%</p>
        <p class="text-danger text-xs font-medium leading-snug">-5%</p>
    </div>
    <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 border border-border">
        <p class="text-dark text-sm font-medium leading-snug">Most Used Room</p>
        <p id="mostUsedRoom" class="text-dark tracking-light text-2xl font-bold leading-tight">ACS Room (32.0 jam)</p>
        <p class="text-accent text-xs font-medium leading-snug">No Change</p>
    </div>
    <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 border border-border">
        <p class="text-dark text-sm font-medium leading-snug">Top Departments</p>
        <p id="topDepartments" class="text-dark tracking-light text-2xl font-bold leading-tight">HR, GA, IT</p>
        <p class="text-accent text-xs font-medium leading-snug">No Change</p>
    </div>
</div>

<!-- Filter Tabs -->
<div class="pb-3">
    <div class="flex border-b border-border px-4 justify-between">
        <a id="btnToday" href="#" class="filter-btn flex flex-col items-center justify-center border-b-[3px] border-b-primary text-dark gap-1 pb-[7px] pt-2.5 flex-1">
            <div class="text-dark" data-icon="CalendarBlank" data-size="24px" data-weight="fill">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32Zm0,48H48V48H72v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24Z"></path>
                </svg>
            </div>
            <p class="text-dark text-sm font-bold leading-normal tracking-[0.015em]">Hari Ini</p>
        </a>
        <a id="btnWeek" href="#" class="filter-btn flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-accent gap-1 pb-[7px] pt-2.5 flex-1">
            <div class="text-accent" data-icon="CalendarCheck" data-size="24px" data-weight="regular">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-38.34-85.66a8,8,0,0,1,0,11.32l-48,48a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L116,164.69l42.34-42.35A8,8,0,0,1,169.66,122.34Z"></path>
                </svg>
            </div>
            <p class="text-accent text-sm font-bold leading-normal tracking-[0.015em]">Minggu Ini</p>
        </a>
        <a id="btnHour" href="#" class="filter-btn flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-accent gap-1 pb-[7px] pt-2.5 flex-1">
            <div class="text-accent" data-icon="Hourglass" data-size="24px" data-weight="regular">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                    <path d="M200,75.64V40a16,16,0,0,0-16-16H72A16,16,0,0,0,56,40V76a16.07,16.07,0,0,0,6.4,12.8L114.67,128,62.4,167.2A16.07,16.07,0,0,0,56,180v36a16,16,0,0,0,16,16H184a16,16,0,0,0,16-16V180.36a16.09,16.09,0,0,0-6.35-12.77L141.27,128l52.38-39.6A16.05,16.05,0,0,0,200,75.64ZM184,216H72V180l56-42,56,42.35Zm0-140.36L128,118,72,76V40H184Z"></path>
                </svg>
            </div>
            <p class="text-accent text-sm font-bold leading-normal tracking-[0.015em]">Jam Ini</p>
        </a>
        <a id="btnMonth" href="#" class="filter-btn flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-accent gap-1 pb-[7px] pt-2.5 flex-1">
            <div class="text-accent" data-icon="CalendarCheck" data-size="24px" data-weight="regular">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-38.34-85.66a8,8,0,0,1,0,11.32l-48,48a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L116,164.69l42.34-42.35A8,8,0,0,1,169.66,122.34Z"></path>
                </svg>
            </div>
            <p class="text-accent text-sm font-bold leading-normal tracking-[0.015em]">Bulan Ini</p>
        </a>
    </div>
</div>

<!-- Booking Table -->
<div class="px-4 py-3 @container">
    <div id="loadingOverlay" class="hidden absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-t-2 border-b-2 border-primary rounded-full animate-spin"></div>
            <p class="text-primary font-medium">Memuat data...</p>
        </div>
    </div>
    
    <div class="flex overflow-hidden rounded-xl border border-border bg-white">
        <table id="bookingTable" class="flex-1">
            <thead>
                <tr class="bg-white">
                    <th class="table-column-120 px-4 py-3 text-left text-dark w-[400px] text-sm font-medium leading-normal">Nama</th>
                    <th class="table-column-240 px-4 py-3 text-left text-dark w-[400px] text-sm font-medium leading-normal">Departemen</th>
                    <th class="table-column-360 px-4 py-3 text-left text-dark w-[400px] text-sm font-medium leading-normal">Tanggal</th>
                    <th class="table-column-480 px-4 py-3 text-left text-dark w-[400px] text-sm font-medium leading-normal">Jam Mulai</th>
                    <th class="table-column-600 px-4 py-3 text-left text-dark w-[400px] text-sm font-medium leading-normal">Jam Selesai</th>
                    <th class="table-column-720 px-4 py-3 text-left text-dark w-[400px] text-sm font-medium leading-normal">Ruangan Meeting</th>
                    <th class="table-column-840 px-4 py-3 text-left text-dark w-[400px] text-sm font-medium leading-normal">Deskripsi</th>
                    <th class="table-column-960 px-4 py-3 text-left text-dark w-60 text-sm font-medium leading-normal">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr class="booking-row border-t border-t-border" data-endtime="{{ $booking->date }} {{ $booking->end_time }}">
                    <td class="table-column-120 h-[72px] px-4 py-2 w-[400px] text-dark text-sm font-normal leading-normal">{{ $booking->nama }}</td>
                    <td class="table-column-240 h-[72px] px-4 py-2 w-[400px] text-dark text-sm font-normal leading-normal">{{ $booking->department }}</td>
                    <td class="table-column-360 h-[72px] px-4 py-2 w-[400px] text-dark text-sm font-normal leading-normal booking-date">{{ $booking->date }}</td>
                    <td class="table-column-480 h-[72px] px-4 py-2 w-[400px] text-dark text-sm font-normal leading-normal booking-time">{{ $booking->start_time }}</td>
                    <td class="table-column-600 h-[72px] px-4 py-2 w-[400px] text-dark text-sm font-normal leading-normal booking-endtime">{{ $booking->end_time }}</td>
                    <td class="table-column-720 h-[72px] px-4 py-2 w-[400px] text-dark text-sm font-normal leading-normal">{{ $booking->meetingRoom->name }}</td>
                    <td class="table-column-840 h-[72px] px-4 py-2 w-[400px] text-accent text-sm font-normal leading-normal">{{ $booking->description }}</td>
                    <td class="table-column-960 h-[72px] px-4 py-2 w-60 text-accent text-sm font-normal leading-normal">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="text-primary hover:text-primary/80">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.bookings.delete', $booking->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger hover:text-danger/80">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-accent">
                        <div class="flex flex-col items-center py-8">
                            <i class="fas fa-inbox text-accent text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Tidak ada data booking</p>
                            <p class="text-sm text-accent">Silakan tambahkan booking baru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <style>
        @container(max-width:120px){.table-column-120{display: none;}}
        @container(max-width:240px){.table-column-240{display: none;}}
        @container(max-width:360px){.table-column-360{display: none;}}
        @container(max-width:480px){.table-column-480{display: none;}}
        @container(max-width:600px){.table-column-600{display: none;}}
        @container(max-width:720px){.table-column-720{display: none;}}
        @container(max-width:840px){.table-column-840{display: none;}}
        @container(max-width:960px){.table-column-960{display: none;}}
    </style>
    
    <!-- Button Controls -->
    <div class="flex justify-stretch">
        <div class="flex flex-1 gap-3 flex-wrap px-4 py-3 justify-end">
            <a href="{{ route('admin.bookings.export') }}" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Export to Excel</span>
            </a>
            <button id="btnReset" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-secondary text-dark text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Reset</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load utilities first (DashboardUtils, dsb.) -->
<script src="{{ asset('js/dashboard/utils.js') }}"></script>
<script src="{{ asset('js/dashboard/constants.js') }}"></script>
<script src="{{ asset('js/dashboard/stats.js') }}"></script>
<script src="{{ asset('js/dashboard/filters.js') }}"></script>
<!-- Main.js yang menangani handleDelete -->
<script src="{{ asset('js/dashboard/main.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Change active filter tab
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Remove active class from all buttons
                filterBtns.forEach(b => {
                    b.classList.remove('border-b-primary', 'text-dark');
                    b.classList.add('border-b-transparent', 'text-accent');
                    b.querySelector('div').classList.remove('text-dark');
                    b.querySelector('div').classList.add('text-accent');
                    b.querySelector('p').classList.remove('text-dark');
                    b.querySelector('p').classList.add('text-accent');
                });
                
                // Add active class to clicked button
                this.classList.remove('border-b-transparent', 'text-accent');
                this.classList.add('border-b-primary', 'text-dark');
                this.querySelector('div').classList.remove('text-accent');
                this.querySelector('div').classList.add('text-dark');
                this.querySelector('p').classList.remove('text-accent');
                this.querySelector('p').classList.add('text-dark');
                
                // Execute original filter functionality
                if (window.Filters) {
                    const filterId = this.id;
                    if (filterId === 'btnToday') window.Filters.filterToday();
                    else if (filterId === 'btnWeek') window.Filters.filterThisWeek();
                    else if (filterId === 'btnHour') window.Filters.filterHour();
                    else if (filterId === 'btnMonth') window.Filters.filterThisMonth();
                }
            });
        });
        
        if (window.Dashboard) {
            window.Dashboard.initialize();
        } else {
            console.error('Dashboard tidak terinisialisasi dengan benar');
        }
    });
</script>
@endpush
