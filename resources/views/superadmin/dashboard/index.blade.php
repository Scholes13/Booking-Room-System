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

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total User Card -->
        <div class="bg-white rounded-lg p-4 flex items-center gap-4 border-l-4 border-[#24448c] shadow-sm">
            <div class="flex justify-center items-center rounded-full bg-[#24448c] bg-opacity-10 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" class="text-[#24448c]" viewBox="0 0 256 256">
                    <path d="M230.14,142.6l-20.9-6.9a16,16,0,0,0-19,8.7l-3.1,6.9a16,16,0,0,0,2.9,18.1,77,77,0,0,1-13.2,13.2,16,16,0,0,0-18.1-2.9l-6.9,3.1a16,16,0,0,0-8.7,19l6.9,20.9A16,16,0,0,0,163.2,232,16.4,16.4,0,0,0,168,232a76.5,76.5,0,0,0,64-64A16,16,0,0,0,230.14,142.6ZM168,216a15.6,15.6,0,0,0-2.2.2l-6.9-20.9,6.9-3.1a32,32,0,0,1,36.7,5.9A60.4,60.4,0,0,1,168,216ZM96,128a32,32,0,1,0-32-32A32,32,0,0,0,96,128Zm0-48a16,16,0,1,1-16,16A16,16,0,0,1,96,80Z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-500 font-medium">Total Admin</h2>
                <p class="text-2xl font-bold text-dark">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
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

    <!-- Quick Links -->
    <div class="bg-white rounded-lg p-6 shadow-sm">
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
        </div>
    </div>
</div>
@endsection
