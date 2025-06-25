@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - @yield('title')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
    />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#26458e',
                        secondary: '#F4EFE6',
                        dark: '#1C160C',
                        accent: '#26458e',
                        danger: '#26458e',
                        border: '#E9DFCE',
                        admin: '#26458e',
                        success: '#26458e',
                        warning: '#26458e',
                        info: '#26458e',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-20px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Flatpickr (Date Picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- Compact Admin Styling -->
    <link rel="stylesheet" href="{{ asset('css/compact-admin.css') }}">

    @stack('styles')
    
    <style>
        body {
            font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;
            @apply bg-gray-50;
            overflow-x: hidden;
        }
        
        /* Sidebar styles */
        #sidebar {
            height: 100vh;
            overflow-y: visible;
            overflow-x: hidden;
            position: fixed;
            top: 0;
            left: 0;
            width: 16rem; /* w-64 = 16rem */
            z-index: 50;
            display: flex;
            flex-direction: column;
        }

        #sidebar-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Main content styles */
        main {
            min-height: 100vh;
            margin-left: 16rem; /* w-64 = 16rem */
            padding: 1.5rem;
        }
        
        .sidebar-item {
            @apply transition-all duration-300 ease-in-out;
        }
        
        .sidebar-item:hover {
            @apply bg-primary/10 text-primary;
            transform: translateX(4px);
        }
        
        .sidebar-item.active {
            @apply bg-primary/20 text-primary border-l-4 border-primary;
            position: relative;
            animation: activeMenuPulse 2s infinite;
        }
        
        @keyframes activeMenuPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(38, 69, 142, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(38, 69, 142, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(38, 69, 142, 0);
            }
        }
        
        /* Logout button special styling */
        .logout-btn {
            @apply transition-all duration-300 ease-in-out bg-red-600 text-white;
        }
        
        .logout-btn:hover {
            @apply bg-red-700 !important;
            transform: translateX(4px);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            @apply bg-gray-100;
        }
        
        ::-webkit-scrollbar-thumb {
            @apply bg-primary/20 rounded-full;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            @apply bg-primary/30;
        }
    </style>
</head>
<body class="bg-gray-50 text-dark">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-white shadow-md">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 sidebar-logo">
            <div class="flex items-center gap-2">
                <div class="text-primary">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                        <path d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" fill="currentColor"></path>
                    </svg>
                </div>
                <h2 class="text-dark text-lg font-bold leading-tight tracking-[-0.015em] logo-text">WG OpsCenter</h2>
            </div>
            
            <!-- Toggle Button -->
            <div class="sidebar-toggle-btn-container">
                <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div id="sidebar-content" class="py-4">
            <nav class="px-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M240,160v24a16,16,0,0,1-16,16H115.93a4,4,0,0,1-3.24-6.35L174.27,109a8.21,8.21,0,0,0-1.37-11.3,8,8,0,0,0-11.37,1.61l-72,99.06A4,4,0,0,1,86.25,200H32a16,16,0,0,1-16-16V161.13c0-1.79,0-3.57,.13-5.33a4,4,0,0,1,4-3.8H48a8,8,0,0,0,8-8.53A8.17,8.17,0,0,0,47.73,136H23.92a4,4,0,0,1-3.87-5c12-43.84,49.66-77.13,95.52-82.28a4,4,0,0,1,4.43,4V80a8,8,0,0,0,8.53,8A8.17,8.17,0,0,0,136,79.73V52.67a4,4,0,0,1,4.43-4A112.18,112.18,0,0,1,236.23,131a4,4,0,0,1-3.88,5H208.27a8.17,8.17,0,0,0-8.25,7.47,8,8,0,0,0,8,8.53h27.92a4,4,0,0,1,4,3.86C240,157.23,240,158.61,240,160Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.bookings.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('admin.bookings*') ? 'active' : '' }}">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Bookings</span>
                        </a>
                    </li>
                    <li>
                        <div class="group">
                            <button class="sidebar-item flex items-center justify-between w-full px-3 py-2.5 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="text-inherit sidebar-icon-container">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,56H216V88H40ZM40,200V104H216v96Z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium sidebar-text">Management</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200 group-hover:rotate-180 sidebar-dropdown-icon"></i>
                            </button>
                            <ul class="pl-8 mt-1 space-y-1 hidden group-hover:block animate-fade-in">
                                <li>
                                    <a href="{{ route('admin.meeting_rooms') }}" class="sidebar-item flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ Request::routeIs('admin.meeting_rooms*') ? 'active' : '' }}">
                                        <i class="fas fa-door-open text-xs"></i>
                                        <span>Ruang Meeting</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.departments') }}" class="sidebar-item flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ Request::routeIs('admin.departments*') ? 'active' : '' }}">
                                        <i class="fas fa-sitemap text-xs"></i>
                                        <span>Departemen</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.employees') }}" class="sidebar-item flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ Request::routeIs('admin.employees*') ? 'active' : '' }}">
                                        <i class="fas fa-users text-xs"></i>
                                        <span>Karyawan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('admin.reports') || Request::routeIs('admin.activity.*') ? 'active' : '' }}">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Zm-32-80a8,8,0,0,1-8,8H96a8,8,0,0,1,0-16h64A8,8,0,0,1,168,136Zm0,32a8,8,0,0,1-8,8H96a8,8,0,0,1,0-16h64A8,8,0,0,1,168,168Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Quick Stats -->
            <div class="px-4 mt-6 quick-stats-section">
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Quick Stats</h4>
                <div class="p-3 rounded-lg bg-gray-50 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Today's Bookings</span>
                        <span class="font-medium text-primary">{{ \App\Models\Booking::whereDate('date', \Carbon\Carbon::today())->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Active Rooms</span>
                        <span class="font-medium text-primary">{{ \App\Models\MeetingRoom::count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-auto p-4 border-t border-gray-200">
            <a href="{{ route('admin.logout') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-all duration-300 ease-in-out">
                <div class="sidebar-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M112,216a8,8,0,0,1-8,8H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h56a8,8,0,0,1,0,16H48V208h56A8,8,0,0,1,112,216Zm109.66-93.66-40-40a8,8,0,0,0-11.32,11.32L196.69,120H104a8,8,0,0,0,0,16h92.69l-26.35,26.34a8,8,0,0,0,11.32,11.32l40-40A8,8,0,0,0,221.66,122.34Z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium sidebar-text">Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(), 
            'baseUrl' => url('/'), 
            'currentRoute' => Route::currentRouteName()
        ]) !!};
    </script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('main');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            // Check localStorage for saved preference
            const sidebarCollapsed = localStorage.getItem('admin_sidebar_collapsed') === 'true';
            
            // Apply initial state
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                toggleBtn.classList.add('collapsed');
            }
            
            // Toggle sidebar on button click
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                toggleBtn.classList.toggle('collapsed');
                
                // Save preference to localStorage
                localStorage.setItem('admin_sidebar_collapsed', sidebar.classList.contains('collapsed'));
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
