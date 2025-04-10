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
        }
        
        @media (min-width: 768px) {
            main {
                margin-left: 16rem; /* w-64 = 16rem */
            }
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
        }
        
        .sidebar-subitem {
            @apply transition-all duration-200 ease-in-out;
        }
        
        .sidebar-subitem:hover {
            @apply bg-primary/5 text-primary;
            transform: translateX(2px);
        }
        
        .sidebar-subitem.active {
            @apply bg-primary/10 text-primary;
        }
        
        .card-hover {
            @apply transition-all duration-300 ease-in-out;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
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
<body class="overflow-x-hidden">
    <div class="min-h-screen flex">
        <!-- Mobile Sidebar Toggle -->
        <div class="md:hidden fixed bottom-4 right-4 z-50">
            <button id="sidebarToggle" class="p-3 rounded-full bg-primary text-white shadow-lg hover:bg-primary/90 transition-all">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-white shadow-lg z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <div class="flex items-center space-x-2">
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-[#26458e] text-white">
                            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6">
                                <path d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-lg">Meeting Room</span>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-[#26458e] text-white">Admin</span>
                </div>
                
                <!-- Sidebar Content -->
                <div id="sidebar-content" class="flex-1 overflow-y-auto py-4 px-2">
                    <!-- User Profile -->
                    <div class="flex items-center space-x-3 p-3 mb-4 rounded-lg bg-gray-50 animate-fade-in">
                        <div class="h-10 w-10 rounded-full bg-[#26458e]/10 flex items-center justify-center text-[#26458e]">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="font-medium">Admin</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                    
                    <!-- Main Menu -->
                    <nav>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center p-3 rounded-lg {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-3 text-[#26458e]"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            
                            <li>
                                <a href="{{ route('admin.bookings.index') }}" class="sidebar-item flex items-center p-3 rounded-lg {{ Request::routeIs('admin.bookings*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-check mr-3 text-[#26458e]"></i>
                                    <span>Bookings</span>
                                </a>
                            </li>
                            
                            <li>
                                <div class="group">
                                    <button class="sidebar-item flex items-center justify-between w-full p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-building mr-3 text-[#26458e]"></i>
                                            <span>Management</span>
                                        </div>
                                        <i class="fas fa-chevron-down text-xs text-[#26458e] transition-transform duration-200 group-hover:rotate-180"></i>
                                    </button>
                                    <ul class="pl-8 mt-1 space-y-1 hidden group-hover:block animate-slide-in">
                                        <li>
                                            <a href="{{ route('admin.meeting_rooms') }}" class="sidebar-subitem flex items-center p-2 rounded-lg text-sm {{ Request::routeIs('admin.meeting_rooms') ? 'active' : '' }}">
                                                <i class="fas fa-door-open mr-2 text-xs"></i>
                                                <span>Ruang Meeting</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.departments') }}" class="sidebar-subitem flex items-center p-2 rounded-lg text-sm {{ Request::routeIs('admin.departments') ? 'active' : '' }}">
                                                <i class="fas fa-sitemap mr-2 text-xs"></i>
                                                <span>Departemen</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.employees') }}" class="sidebar-subitem flex items-center p-2 rounded-lg text-sm {{ Request::routeIs('admin.employees') ? 'active' : '' }}">
                                                <i class="fas fa-users mr-2 text-xs"></i>
                                                <span>Karyawan</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            
                            <li>
                                <a href="{{ route('admin.reports') }}" class="sidebar-item flex items-center p-3 rounded-lg {{ Request::routeIs('admin.reports') || Request::routeIs('admin.activity.*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-bar mr-3 text-[#26458e]"></i>
                                    <span>Reports</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-4"></div>
                    
                    <!-- Quick Stats -->
                    <div class="p-3 mb-4 rounded-lg bg-gray-50 animate-fade-in">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Quick Stats</h4>
                        <div class="space-y-2">
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
                
                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-gray-200">
                    <a href="{{ route('admin.logout') }}" class="flex items-center justify-center p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2 text-gray-600"></i>
                        <span class="font-medium">Logout</span>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 md:ml-64 w-full transition-all duration-300">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-900 animate-slide-in">
                        @yield('title', 'Dashboard')
                    </h1>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="p-2 rounded-full hover:bg-gray-100">
                                <i class="fas fa-bell text-gray-500"></i>
                                <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-primary animate-pulse-slow"></span>
                            </button>
                        </div>
                        <div class="hidden md:flex items-center space-x-2">
                            <div class="h-8 w-8 rounded-full bg-[#26458e]/10 flex items-center justify-center text-[#26458e]">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="text-sm font-medium">Admin</span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden animate-fade-in">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(), 
            'baseUrl' => url('/'), 
            'currentRoute' => Route::currentRouteName()
        ]) !!};
        
        // Save sidebar state to session storage
        function saveSidebarState(isOpen) {
            sessionStorage.setItem('sidebarOpen', isOpen ? 'true' : 'false');
        }
        
        // Load sidebar state from session storage
        function loadSidebarState() {
            const sidebar = document.getElementById('sidebar');
            const savedState = sessionStorage.getItem('sidebarOpen');
            
            // Default to open on desktop, closed on mobile
            if (savedState === 'true' || (savedState === null && window.innerWidth >= 768)) {
                sidebar.classList.remove('-translate-x-full');
                saveSidebarState(true);
            } else {
                sidebar.classList.add('-translate-x-full');
                saveSidebarState(false);
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSidebarState();
        });
        
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                saveSidebarState(false);
            } else {
                sidebar.classList.remove('-translate-x-full');
                saveSidebarState(true);
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth < 768 && 
                !sidebar.contains(event.target) && 
                event.target !== sidebarToggle && 
                !sidebarToggle.contains(event.target)) {
                sidebar.classList.add('-translate-x-full');
                saveSidebarState(false);
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.getElementById('sidebar').classList.remove('-translate-x-full');
                saveSidebarState(true);
            } else {
                document.getElementById('sidebar').classList.add('-translate-x-full');
                saveSidebarState(false);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
