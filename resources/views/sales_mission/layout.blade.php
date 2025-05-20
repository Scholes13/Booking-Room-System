@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sales Mission - @yield('title')</title>
    
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
                        primary: '#f59e0b', // Amber color for Sales Mission
                        secondary: '#F4EFE6',
                        dark: '#1C160C',
                        accent: '#A18249',
                        danger: '#f59e0b',
                        border: '#E9DFCE',
                        sales: '#f59e0b',
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_amber.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    
    <!-- Compact Admin Styling -->
    <link rel="stylesheet" href="{{ asset('css/compact-admin.css') }}">

    <!-- ApexCharts (for charts) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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
            transition: width 0.3s ease-in-out;
        }
        
        /* When sidebar is minimized */
        #sidebar.collapsed {
            width: 4rem !important;
        }
        
        #sidebar.collapsed .sidebar-icon-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        
        /* Fix icon alignment in minimized mode */
        #sidebar.collapsed .sidebar-item {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            justify-content: center;
        }
        
        #sidebar.collapsed .logo-text,
        #sidebar.collapsed .sidebar-text,
        #sidebar.collapsed .quick-stats-section,
        #sidebar.collapsed .sidebar-dropdown-icon {
            display: none;
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
            transition: margin-left 0.3s ease-in-out;
        }
        
        /* Expanded main content when sidebar is collapsed */
        main.expanded {
            margin-left: 4rem !important;
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
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }
        
        /* Sales mission event styling */
        .sales-mission-event {
            background-color: #f59e0b !important; 
            border-left: 4px solid #d97706 !important;
            color: #7f1d1d !important;
        }
    
        .sales-mission-event .fc-event-title {
            font-weight: 600 !important;
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
                <h2 class="text-dark text-lg font-bold leading-tight tracking-[-0.015em] logo-text">WG Sales Mission</h2>
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
                        <a href="{{ route('sales_mission.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('sales_mission.dashboard') ? 'active' : '' }}">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M240,160v24a16,16,0,0,1-16,16H115.93a4,4,0,0,1-3.24-6.35L174.27,109a8.21,8.21,0,0,0-1.37-11.3,8,8,0,0,0-11.37,1.61l-72,99.06A4,4,0,0,1,86.25,200H32a16,16,0,0,1-16-16V161.13c0-1.79,0-3.57.13-5.33a4,4,0,0,1,4-3.8H48a8,8,0,0,0,8-8.53A8.17,8.17,0,0,0,47.73,136H23.92a4,4,0,0,1-3.87-5c12-43.84,49.66-77.13,95.52-82.28a4,4,0,0,1,4.43,4V80a8,8,0,0,0,8.53,8A8.17,8.17,0,0,0,136,79.73V52.67a4,4,0,0,1,4.43-4A112.18,112.18,0,0,1,236.23,131a4,4,0,0,1-3.88,5H208.27a8.17,8.17,0,0,0-8.25,7.47,8,8,0,0,0,8,8.53h27.92a4,4,0,0,1,4,3.86C240,157.23,240,158.61,240,160Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales_mission.activities.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('sales_mission.activities.index') || Request::routeIs('sales_mission.activities.edit') ? 'active' : '' }}">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,56H82.17L64.9,73.27a8,8,0,0,0,11.3,11.3L112,49l35.8,35.56a8,8,0,0,0,11.3-11.3L142.5,56H216V88H40Zm176,144H40V104H216Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Sales Mission List</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales_mission.reports') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('sales_mission.reports') ? 'active' : '' }}">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Zm-32-80a8,8,0,0,1-8,8H96a8,8,0,0,1,0-16h64A8,8,0,0,1,168,136Zm0,32a8,8,0,0,1-8,8H96a8,8,0,0,1,0-16h64A8,8,0,0,1,168,168Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Reports</span>
                        </a>
                    </li>
                                        <!-- Activity Logs removed - only available to superadmin -->
                </ul>
            </nav>
            
        </div>
        
        <div class="mt-auto p-4 border-t border-gray-200">
            <a href="{{ route('admin.logout') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-all duration-300 ease-in-out">
                <div class="sidebar-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M112,216a8,8,0,0,1-8,8H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h56a8,8,0,0,1,0,16H48V208h56A8,8,0,0,1,112,216Zm109.66-93.66-40-40a8,8,0,0,0-11.32,11.32L196.69,120H104a8,8,0,0,0,0,16h92.69l-26.35,26.34a8,8,0,0,0,11.32,11.32l40-40A8,8,0,0,0,221.66,122.34Z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium sidebar-text">Sign Out</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Page Title -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">@yield('header')</h1>
                    <p class="text-sm text-gray-600 mt-1">@yield('description')</p>
                </div>
                @yield('actions')
            </div>
        </div>
        
        <!-- Flash Messages -->
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow animate-fade-in">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow animate-fade-in">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm">
                            <p class="font-medium">Terjadi beberapa kesalahan:</p>
                            <ul class="mt-1 list-disc list-inside text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Main Content -->
        @yield('content')
    </main>
    
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('main');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            // Check localStorage for saved preference
            const sidebarCollapsed = localStorage.getItem('sales_mission_sidebar_collapsed') === 'true';
            
            // Apply initial state
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                toggleBtn.classList.add('rotate-180');
            }
            
            // Toggle sidebar on button click
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                toggleBtn.classList.toggle('rotate-180');
                
                // Save preference to localStorage
                const isMinimized = sidebar.classList.contains('collapsed');
                localStorage.setItem('sales_mission_sidebar_collapsed', isMinimized);
            });
            
            // SweetAlert notifications for flash messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            @endif
            
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            @endif
            
            // Initialize SweetAlert2 for delete confirmations
            document.querySelectorAll('.delete-confirm').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#d1d5db',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
            
            // Initialize Flatpickr for date inputs
            document.querySelectorAll('.flatpickr-date').forEach(input => {
                flatpickr(input, {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    allowInput: true
                });
            });
            
            // Initialize Flatpickr for datetime inputs
            document.querySelectorAll('.flatpickr-datetime').forEach(input => {
                flatpickr(input, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    locale: "id",
                    allowInput: true
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html> 