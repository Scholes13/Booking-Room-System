@php
    $user = Auth::user();
    $isSuperAdmin = $user && $user->role === 'superadmin';
    $isAdminBAS = $user && $user->role === 'admin_bas';
    
    if ($isSuperAdmin) {
        $prefix = 'superadmin.';
    } elseif ($isAdminBAS) {
        $prefix = 'bas.';
    } else {
        $prefix = 'admin.';
    }
@endphp

<div class="bg-white h-screen w-64 fixed left-0 top-0 shadow-lg">
    <div class="p-4">
        <h1 class="text-xl font-bold mb-8">Meeting Room Booking</h1>
        
        <nav class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route($prefix . 'dashboard') }}" 
               class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs($prefix . 'dashboard') ? 'bg-gray-100' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            @if($isSuperAdmin)
            <!-- Manajemen User (hanya untuk superadmin) -->
            <a href="{{ route('superadmin.users') }}" 
               class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs('superadmin.users*') ? 'bg-gray-100' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Manajemen User</span>
            </a>
            @endif

            <!-- Bookings -->
            <a href="{{ route($prefix . 'bookings.index') }}" 
               class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs($prefix . 'bookings*') ? 'bg-gray-100' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Bookings</span>
            </a>

            <!-- Ruang Meeting -->
            <a href="{{ route($prefix . 'meeting_rooms') }}" 
               class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs($prefix . 'meeting_rooms*') ? 'bg-gray-100' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Ruang Meeting</span>
            </a>

            <!-- Departemen -->
            <a href="{{ route($prefix . 'departments') }}" 
               class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs($prefix . 'departments*') ? 'bg-gray-100' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Departemen</span>
            </a>

            <!-- Karyawan -->
            <a href="{{ route($prefix . 'employees') }}" 
               class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs($prefix . 'employees*') ? 'bg-gray-100' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Karyawan</span>
            </a>

            <!-- Activity Reports -->
            <a href="{{ route($prefix . 'activity.index') }}" 
               class="flex items-center p-2 text-gray-700 rounded hover:bg-gray-100 {{ request()->routeIs($prefix . 'activity.*') ? 'bg-gray-100' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Activity Reports</span>
            </a>
        </nav>
    </div>
</div> 