<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin Panel - @yield('title')</title>
    
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
                        primary: '#24448c',
                        secondary: '#F4EFE6',
                        dark: '#1C160C',
                        accent: '#24448c',
                        danger: '#24448c',
                        border: '#E9DFCE',
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
        }
    </style>
</head>
<body class="bg-gray-50 text-dark">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-white shadow-md">
        <div class="flex items-center justify-center p-4 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <div class="text-primary">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                        <path d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" fill="currentColor"></path>
                    </svg>
                </div>
                <h2 class="text-dark text-lg font-bold leading-tight tracking-[-0.015em]">Bookings Room</h2>
            </div>
        </div>
        
        <div id="sidebar-content" class="py-4">
            <nav class="px-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('superadmin.dashboard') ? 'active' : '' }}">
                            <div class="text-inherit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M240,160v24a16,16,0,0,1-16,16H115.93a4,4,0,0,1-3.24-6.35L174.27,109a8.21,8.21,0,0,0-1.37-11.3,8,8,0,0,0-11.37,1.61l-72,99.06A4,4,0,0,1,86.25,200H32a16,16,0,0,1-16-16V161.13c0-1.79,0-3.57.13-5.33a4,4,0,0,1,4-3.8H48a8,8,0,0,0,8-8.53A8.17,8.17,0,0,0,47.73,136H23.92a4,4,0,0,1-3.87-5c12-43.84,49.66-77.13,95.52-82.28a4,4,0,0,1,4.43,4V80a8,8,0,0,0,8.53,8A8.17,8.17,0,0,0,136,79.73V52.67a4,4,0,0,1,4.43-4A112.18,112.18,0,0,1,236.23,131a4,4,0,0,1-3.88,5H208.27a8.17,8.17,0,0,0-8.25,7.47,8,8,0,0,0,8,8.53h27.92a4,4,0,0,1,4,3.86C240,157.23,240,158.61,240,160Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.users') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('superadmin.users') || Request::routeIs('superadmin.users.*') ? 'active' : '' }}">
                            <div class="text-inherit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M230.14,142.6l-20.9-6.9a16,16,0,0,0-19,8.7l-3.1,6.9a16,16,0,0,0,2.9,18.1,77,77,0,0,1-13.2,13.2,16,16,0,0,0-18.1-2.9l-6.9,3.1a16,16,0,0,0-8.7,19l6.9,20.9A16,16,0,0,0,163.2,232,16.4,16.4,0,0,0,168,232a76.5,76.5,0,0,0,64-64A16,16,0,0,0,230.14,142.6ZM168,216a15.6,15.6,0,0,0-2.2.2l-6.9-20.9,6.9-3.1a32,32,0,0,1,36.7,5.9A60.4,60.4,0,0,1,168,216ZM96,128a32,32,0,1,0-32-32A32,32,0,0,0,96,128Zm0-48a16,16,0,1,1-16,16A16,16,0,0,1,96,80Zm88-32a24,24,0,1,0-24-24A24,24,0,0,0,184,48Zm0-32a8,8,0,1,1-8,8A8,8,0,0,1,184,16ZM61.3,184.2a71.5,71.5,0,0,1,18.1-5.9,102.6,102.6,0,0,1,33.2,0A71.5,71.5,0,0,1,130.7,184,8,8,0,0,0,136,200a8.3,8.3,0,0,0,2-.3,87.9,87.9,0,0,0-22.3-7.5,118.5,118.5,0,0,0-15.6-1.8,127.5,127.5,0,0,0-38.2,0A87.9,87.9,0,0,0,40,198a8,8,0,1,0,5.3,15.1,71.5,71.5,0,0,1,18.1-5.9A116.1,116.1,0,0,1,96,204a8,8,0,0,0,0,16,117.3,117.3,0,0,0-35.4-3.8A87.9,87.9,0,0,0,40,223.8a8,8,0,1,0,5.3,15.2,71.5,71.5,0,0,1,18.1-5.9,98.6,98.6,0,0,1,65.2,0,71.5,71.5,0,0,1,7.9,3.1,8,8,0,0,0,6.7-.2A7.2,7.2,0,0,0,147,232a7.9,7.9,0,0,0-2.2-8,82.5,82.5,0,0,0-10.9-4.6,69.1,69.1,0,0,0-8-2.2,8,8,0,0,0-1.7-.2A8.1,8.1,0,0,0,120,218a8.5,8.5,0,0,0-.7-3.1,85.9,85.9,0,0,0-8.2-15.4,8,8,0,0,0-9-3.1,72.1,72.1,0,0,0-8,2.2,87.9,87.9,0,0,0-22.3,7.5,7.6,7.6,0,0,0-4.3,4.3,7.5,7.5,0,0,0,0,6.1,8,8,0,0,0,10.4,4.3A57.3,57.3,0,0,1,96,196.1a63.3,63.3,0,0,1,26.2,5.6l-2.3,1.3a83.8,83.8,0,0,0-8,5.4,8,8,0,0,0-2.8,9.4c.4.8.7,1.7,1,2.6A111.6,111.6,0,0,0,96,220a113.5,113.5,0,0,0-12.9.8q-1.2-2.4-2.7-4.8a8,8,0,0,0-10.2-3.1,71.5,71.5,0,0,0-8,2.2,86.5,86.5,0,0,0-5.5,2.1,8,8,0,0,0-4.3,4.4,7.5,7.5,0,0,0,0,6.1A8,8,0,0,0,56,232a7.6,7.6,0,0,0,3.4-.8,70.6,70.6,0,0,1,3.7-1.4c.3.5.6,1,1,1.5a8,8,0,0,0,13.1-9.1c-.2-.3-.3-.5-.5-.8l1-.3a8,8,0,0,0,5.7-9.7,81.1,81.1,0,0,0-2.8-8.7l2-.7a71.5,71.5,0,0,1,18.1-2.9A101.2,101.2,0,0,1,104,199a8.9,8.9,0,0,0,1.7.2,8,8,0,0,0,1.7-15.8,117.5,117.5,0,0,0-19.2.6c1-2.3,2.1-4.6,3.3-6.7a8,8,0,0,0-13.8-8,106.4,106.4,0,0,0-8,14.4,8,8,0,0,0-8.4.5Zm64.6,33.4-1.7.6c-.5-.5-1.1-1-1.6-1.4a88.3,88.3,0,0,1,4.7-3.1Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Manajemen User</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.bookings.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('superadmin.bookings*') ? 'active' : '' }}">
                            <div class="text-inherit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Bookings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.activities.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('superadmin.activities*') ? 'active' : '' }}">
                            <div class="text-inherit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,56H82.17L64.9,73.27a8,8,0,0,0,11.3,11.3L112,49l35.8,35.56a8,8,0,0,0,11.3-11.3L142.5,56H216V88H40Zm176,144H40V104H216Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Aktivitas</span>
                        </a>
                    </li>
                    <li>
                        <div class="group">
                            <button class="sidebar-item flex items-center justify-between w-full px-3 py-2.5 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="text-inherit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,56H216V88H40ZM40,200V104H216v96Z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium">Management</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200 group-hover:rotate-180"></i>
                            </button>
                            <ul class="pl-8 mt-1 space-y-1 hidden group-hover:block animate-fade-in">
                                <li>
                                    <a href="{{ route('superadmin.meeting_rooms') }}" class="sidebar-item flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ Request::routeIs('superadmin.meeting_rooms*') ? 'active' : '' }}">
                                        <i class="fas fa-door-open text-xs"></i>
                                        <span>Ruang Meeting</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('superadmin.departments') }}" class="sidebar-item flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ Request::routeIs('superadmin.departments*') ? 'active' : '' }}">
                                        <i class="fas fa-sitemap text-xs"></i>
                                        <span>Departemen</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('superadmin.employees') }}" class="sidebar-item flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ Request::routeIs('superadmin.employees*') ? 'active' : '' }}">
                                        <i class="fas fa-users text-xs"></i>
                                        <span>Karyawan</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('superadmin.activity-types.index') }}" class="sidebar-item flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ Request::routeIs('superadmin.activity-types*') ? 'active' : '' }}">
                                        <i class="fas fa-list-alt text-xs"></i>
                                        <span>Jenis Aktivitas</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.logs.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('superadmin.logs*') ? 'active' : '' }}">
                            <div class="text-inherit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm56-88a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h72A8,8,0,0,1,184,128Zm0-32a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h72A8,8,0,0,1,184,96Zm0,64a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h72A8,8,0,0,1,184,160Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Activity Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.reports') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ Request::routeIs('superadmin.reports*') ? 'active' : '' }}">
                            <div class="text-inherit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Zm-32-80a8,8,0,0,1-8,8H96a8,8,0,0,1,0-16h64A8,8,0,0,1,168,136Zm0,32a8,8,0,0,1-8,8H96a8,8,0,0,1,0-16h64A8,8,0,0,1,168,168Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <div class="mt-auto p-4 border-t border-gray-200">
            <a href="{{ route('admin.logout') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg">
                <div class="text-inherit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M112,216a8,8,0,0,1-8,8H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h56a8,8,0,0,1,0,16H48V208h56A8,8,0,0,1,112,216Zm109.66-93.66-40-40a8,8,0,0,0-11.32,11.32L196.69,120H104a8,8,0,0,0,0,16h92.69l-26.35,26.34a8,8,0,0,0,11.32,11.32l40-40A8,8,0,0,0,221.66,122.34Z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium">Logout</span>
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
    @stack('scripts')
</body>
</html>
