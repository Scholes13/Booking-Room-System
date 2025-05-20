@extends('superadmin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Super Admin Dashboard</h1>
    </div>

    <!-- System Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Admin Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-[#24448c] shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-[#24448c] bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-[#24448c]" viewBox="0 0 256 256">
                    <path d="M230.14,142.6l-20.9-6.9a16,16,0,0,0-19,8.7l-3.1,6.9a16,16,0,0,0,2.9,18.1,77,77,0,0,1-13.2,13.2,16,16,0,0,0-18.1-2.9l-6.9,3.1a16,16,0,0,0-8.7,19l6.9,20.9A16,16,0,0,0,163.2,232,16.4,16.4,0,0,0,168,232a76.5,76.5,0,0,0,64-64A16,16,0,0,0,230.14,142.6ZM168,216a15.6,15.6,0,0,0-2.2.2l-6.9-20.9,6.9-3.1a32,32,0,0,1,36.7,5.9A60.4,60.4,0,0,1,168,216ZM96,128a32,32,0,1,0-32-32A32,32,0,0,0,96,128Zm0-48a16,16,0,1,1-16,16A16,16,0,0,1,96,80Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Total Admin</h2>
                <p class="text-2xl font-bold text-dark">{{ \App\Models\User::whereIn('role', ['admin', 'admin_bas', 'sales_mission'])->count() }}</p>
            </div>
        </div>

        <!-- Ruangan Meeting Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-[#24448c] shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-[#24448c] bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-[#24448c]" viewBox="0 0 256 256">
                    <path d="M112,104a8,8,0,0,1-8,8H96v40a8,8,0,0,1-16,0V112H72a8,8,0,0,1,0-16h32A8,8,0,0,1,112,104ZM232,92.74V152a40,40,0,0,1-36.63,39.85,64,64,0,0,1-118.7.15H40a16,16,0,0,1-16-16V80A16,16,0,0,1,40,64H96.81a40,40,0,0,1,73.31-28.85A32,32,0,0,1,211.69,80h7.57A12.76,12.76,0,0,1,232,92.74Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Ruangan Meeting</h2>
                <p class="text-2xl font-bold text-dark">{{ \App\Models\MeetingRoom::count() }}</p>
            </div>
        </div>

        <!-- Total Booking Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-[#24448c] shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-[#24448c] bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-[#24448c]" viewBox="0 0 256 256">
                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Total Booking</h2>
                <p class="text-2xl font-bold text-dark">{{ \App\Models\Booking::count() }}</p>
            </div>
        </div>
        
        <!-- Total Activities Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-[#24448c] shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-[#24448c] bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-[#24448c]" viewBox="0 0 256 256">
                    <path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,56H82.17L64.9,73.27a8,8,0,0,0,11.3,11.3L112,49l35.8,35.56a8,8,0,0,0,11.3-11.3L142.5,56H216V88H40Zm176,144H40V104H216Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Total Aktivitas</h2>
                <p class="text-2xl font-bold text-dark">{{ \App\Models\Activity::count() }}</p>
            </div>
        </div>
    </div>

    <!-- Dashboard Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="flex border-b">
            <button class="dashboard-tab active p-4 font-medium border-b-2 border-[#24448c]" data-tab="system">System Overview</button>
            <button class="dashboard-tab p-4 font-medium border-b-2 border-transparent" data-tab="admin">Admin Dashboard</button>
            <button class="dashboard-tab p-4 font-medium border-b-2 border-transparent" data-tab="adminbas">Admin BAS Dashboard</button>
            <button class="dashboard-tab p-4 font-medium border-b-2 border-transparent" data-tab="salesmission">Sales Mission Dashboard</button>
        </div>

        <!-- System Overview Tab Content -->
        <div id="system-content" class="dashboard-content p-6">
            <h2 class="text-lg font-semibold mb-4">Quick Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('superadmin.users') }}" class="flex items-center p-4 bg-[#24448c]/10 rounded-lg hover:bg-[#24448c]/20 transition">
                    <div class="mr-4 text-[#24448c]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M230.14,142.6l-20.9-6.9a16,16,0,0,0-19,8.7l-3.1,6.9a16,16,0,0,0,2.9,18.1,77,77,0,0,1-13.2,13.2,16,16,0,0,0-18.1-2.9l-6.9,3.1a16,16,0,0,0-8.7,19l6.9,20.9A16,16,0,0,0,163.2,232,16.4,16.4,0,0,0,168,232a76.5,76.5,0,0,0,64-64A16,16,0,0,0,230.14,142.6Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium">Kelola User</h3>
                        <p class="text-sm text-gray-600">Tambah, edit, atau hapus user admin</p>
                    </div>
                </a>
                
                <a href="{{ route('superadmin.meeting_rooms') }}" class="flex items-center p-4 bg-[#24448c]/10 rounded-lg hover:bg-[#24448c]/20 transition">
                    <div class="mr-4 text-[#24448c]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M112,104a8,8,0,0,1-8,8H96v40a8,8,0,0,1-16,0V112H72a8,8,0,0,1,0-16h32A8,8,0,0,1,112,104ZM232,92.74V152a40,40,0,0,1-36.63,39.85,64,64,0,0,1-118.7.15H40a16,16,0,0,1-16-16V80A16,16,0,0,1,40,64H96.81a40,40,0,0,1,73.31-28.85A32,32,0,0,1,211.69,80h7.57A12.76,12.76,0,0,1,232,92.74Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium">Ruang Meeting</h3>
                        <p class="text-sm text-gray-600">Kelola ruang meeting</p>
                    </div>
                </a>
                
                <a href="{{ route('superadmin.activities.index') }}" class="flex items-center p-4 bg-[#24448c]/10 rounded-lg hover:bg-[#24448c]/20 transition">
                    <div class="mr-4 text-[#24448c]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,56H82.17L64.9,73.27a8,8,0,0,0,11.3,11.3L112,49l35.8,35.56a8,8,0,0,0,11.3-11.3L142.5,56H216V88H40Zm176,144H40V104H216Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium">Aktivitas</h3>
                        <p class="text-sm text-gray-600">Kelola data aktivitas</p>
                    </div>
                </a>
                
                <a href="{{ route('superadmin.reports') }}" class="flex items-center p-4 bg-amber-50 rounded-lg hover:bg-amber-100 transition">
                    <div class="mr-4 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M208,40H48A16,16,0,0,0,32,56v58.77c0,89.61,75.82,119.34,91,124.39a15.53,15.53,0,0,0,10,0c15.2-5.05,91-34.78,91-124.39V56A16,16,0,0,0,208,40Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium">Laporan</h3>
                        <p class="text-sm text-gray-600">Lihat berbagai laporan sistem</p>
                    </div>
                </a>
                
                <a href="{{ route('superadmin.logs.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <div class="mr-4 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm56-88a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h72A8,8,0,0,1,184,128Zm0-32a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h72A8,8,0,0,1,184,96Zm0,64a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h72A8,8,0,0,1,184,160Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium">Activity Logs</h3>
                        <p class="text-sm text-gray-600">Pantau aktivitas admin</p>
                    </div>
                </a>
                
                <a href="{{ route('superadmin.activities.sales_mission') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                    <div class="mr-4 text-orange-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M224,128a95.76,95.76,0,0,1-31.8,71.37A72,72,0,0,0,128,160a40,40,0,1,0-40-40,40,40,0,0,0,40,40,72,72,0,0,0-64.2,39.37A96,96,0,0,1,224,128Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium">Sales Mission</h3>
                        <p class="text-sm text-gray-600">Kelola aktivitas sales mission</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Admin Dashboard Tab Content -->
        <div id="admin-content" class="dashboard-content p-6 hidden">
            <h2 class="text-lg font-semibold mb-4">Admin Dashboard</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Today's Bookings Card -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Booking Hari Ini</h3>
                        <span class="text-xs bg-blue-100 text-blue-800 py-1 px-2 rounded-full">{{ now()->format('d M Y') }}</span>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Booking::whereDate('date', now())->count() }}</p>
                </div>
                
                <!-- Most Booked Room -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Ruangan Terpopuler</h3>
                    </div>
                    @php
                        $popularRoom = \App\Models\Booking::select('meeting_room_id', \DB::raw('count(*) as total'))
                            ->groupBy('meeting_room_id')
                            ->orderBy('total', 'desc')
                            ->first();
                            
                        $roomName = $popularRoom ? \App\Models\MeetingRoom::find($popularRoom->meeting_room_id)->name ?? 'Unknown' : 'N/A';
                        $bookingCount = $popularRoom ? $popularRoom->total : 0;
                    @endphp
                    <p class="text-lg font-bold">{{ $roomName }}</p>
                    <p class="text-sm text-gray-600">{{ $bookingCount }} bookings</p>
                </div>
                
                <!-- Total Bookings This Month -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Booking Bulan Ini</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Booking::whereYear('date', now()->year)->whereMonth('date', now()->month)->count() }}</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-medium text-gray-600 mb-3">Booking Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruangan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach(\App\Models\Booking::with('meetingRoom')->orderBy('created_at', 'desc')->limit(5)->get() as $booking)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $booking->name }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $booking->meetingRoom->name ?? 'Unknown' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Admin BAS Dashboard Tab Content -->
        <div id="adminbas-content" class="dashboard-content p-6 hidden">
            <h2 class="text-lg font-semibold mb-4">Admin BAS Dashboard</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Activities Card -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Total Aktivitas</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Activity::count() }}</p>
                </div>
                
                <!-- Today's Activities -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Aktivitas Hari Ini</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Activity::whereDate('start_datetime', now())->count() }}</p>
                </div>
                
                <!-- This Week Activities -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Aktivitas Minggu Ini</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Activity::whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</p>
                </div>
                
                <!-- This Month Activities -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Aktivitas Bulan Ini</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Activity::whereYear('start_datetime', now()->year)->whereMonth('start_datetime', now()->month)->count() }}</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-medium text-gray-600 mb-3">Aktivitas Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach(\App\Models\Activity::orderBy('created_at', 'desc')->limit(5)->get() as $activity)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $activity->name }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $activity->activity_type }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">
                                    @if($activity->start_datetime > now())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Upcoming</span>
                                    @elseif($activity->end_datetime < now())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Completed</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ongoing</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sales Mission Dashboard Tab Content -->
        <div id="salesmission-content" class="dashboard-content p-6 hidden">
            <h2 class="text-lg font-semibold mb-4">Sales Mission Dashboard</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Total Sales Missions -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Total Sales Mission</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Activity::where('activity_type', 'Sales Mission')->whereHas('salesMissionDetail')->count() }}</p>
                    <p class="text-xs text-gray-500">Total keseluruhan sales mission</p>
                </div>
                
                <!-- This Month Sales Missions -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Sales Mission Bulan Ini</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\Activity::where('activity_type', 'Sales Mission')->whereHas('salesMissionDetail')->whereYear('start_datetime', now()->year)->whereMonth('start_datetime', now()->month)->count() }}</p>
                    <p class="text-xs text-gray-500">Sales mission bulan {{ now()->format('F Y') }}</p>
                </div>
                
                <!-- Companies Visited -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600">Perusahaan Dikunjungi</h3>
                    </div>
                    <p class="text-2xl font-bold">{{ \App\Models\SalesMissionDetail::distinct('company_name')->count('company_name') }}</p>
                    <p class="text-xs text-gray-500">Jumlah perusahaan yang dikunjungi</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Top Provinces -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-medium text-gray-600 mb-3">Top Provinsi</h3>
                    @php
                        $topProvinces = \App\Models\Activity::where('activity_type', 'Sales Mission')
                            ->whereHas('salesMissionDetail')
                            ->select('province', \DB::raw('count(*) as count'))
                            ->groupBy('province')
                            ->orderBy('count', 'desc')
                            ->limit(5)
                            ->get();
                        
                        $maxCount = $topProvinces->max('count') ?: 1;
                    @endphp
                    
                    <div class="space-y-3">
                        @foreach($topProvinces as $province)
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>{{ $province->province }}</span>
                                <span>{{ $province->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ ($province->count / $maxCount) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Top Cities -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-medium text-gray-600 mb-3">Top Kota</h3>
                    @php
                        $topCities = \App\Models\Activity::where('activity_type', 'Sales Mission')
                            ->whereHas('salesMissionDetail')
                            ->select('city', \DB::raw('count(*) as count'))
                            ->groupBy('city')
                            ->orderBy('count', 'desc')
                            ->limit(5)
                            ->get();
                        
                        $maxCityCount = $topCities->max('count') ?: 1;
                    @endphp
                    
                    <div class="space-y-3">
                        @foreach($topCities as $city)
                        <div>
                            <div class="flex justify-between text-sm">
                                <span>{{ $city->city }}</span>
                                <span>{{ $city->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($city->count / $maxCityCount) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-medium text-gray-600 mb-3">Sales Mission Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php
                                $recentSalesMissions = \App\Models\Activity::where('activity_type', 'Sales Mission')
                                    ->whereHas('salesMissionDetail')
                                    ->with('salesMissionDetail', 'department')
                                    ->orderBy('start_datetime', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            
                            @forelse($recentSalesMissions as $mission)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $mission->salesMissionDetail->company_name }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $mission->salesMissionDetail->company_pic }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $mission->city }}, {{ $mission->province }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($mission->start_datetime)->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-sm text-center text-gray-500">Belum ada data sales mission</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching functionality
        const tabs = document.querySelectorAll('.dashboard-tab');
        const contents = document.querySelectorAll('.dashboard-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                // Remove active class from all tabs and add to current
                tabs.forEach(t => t.classList.remove('active', 'border-[#24448c]'));
                tab.classList.add('active', 'border-[#24448c]');
                
                // Hide all contents and show current
                contents.forEach(content => content.classList.add('hidden'));
                document.getElementById(`${tabId}-content`).classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection
