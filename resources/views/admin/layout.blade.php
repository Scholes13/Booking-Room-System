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
                        primary: '#22428e',
                        secondary: '#F4EFE6',
                        dark: '#1C160C',
                        accent: '#A18249',
                        danger: '#C12929',
                        border: '#E9DFCE',
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
        }
    </style>
</head>
<body class="bg-white text-dark">
    <div class="relative flex size-full min-h-screen flex-col overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <!-- Header -->
            <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-border px-10 py-3">
                <div class="flex items-center gap-4 text-dark">
                    <div class="size-4">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" fill="currentColor"></path>
                        </svg>
                    </div>
                    <h2 class="text-dark text-lg font-bold leading-tight tracking-[-0.015em]">Meeting Room Booking</h2>
                </div>
                <div class="flex flex-1 justify-end gap-8">
                    <div class="flex items-center gap-9">
                        <a class="text-dark text-sm font-medium leading-normal {{ Request::routeIs('admin.dashboard') ? 'font-bold' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        <a class="text-dark text-sm font-medium leading-normal {{ Request::routeIs('admin.meeting_rooms') ? 'font-bold' : '' }}" href="{{ route('admin.meeting_rooms') }}">Ruang Meeting</a>
                        <a class="text-dark text-sm font-medium leading-normal {{ Request::routeIs('admin.departments') ? 'font-bold' : '' }}" href="{{ route('admin.departments') }}">Departemen</a>
                        <a class="text-dark text-sm font-medium leading-normal {{ Request::routeIs('admin.employees') ? 'font-bold' : '' }}" href="{{ route('admin.employees') }}">Karyawan</a>
                        <a class="text-dark text-sm font-medium leading-normal {{ Request::routeIs('admin.reports') || Request::routeIs('admin.activity.*') ? 'font-bold' : '' }}" href="{{ route('admin.reports') }}">Reports</a>
                    </div>
                    <a href="{{ route('admin.logout') }}" class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 bg-secondary text-dark gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5">
                        <div class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                <path d="M112,216a8,8,0,0,1-8,8H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h56a8,8,0,0,1,0,16H48V208h56A8,8,0,0,1,112,216Zm109.66-93.66-40-40a8,8,0,0,0-11.32,11.32L196.69,120H104a8,8,0,0,0,0,16h92.69l-26.35,26.34a8,8,0,0,0,11.32,11.32l40-40A8,8,0,0,0,221.66,122.34Z"></path>
                            </svg>
                        </div>
                    </a>
                </div>
            </header>
            
            <!-- Main Content -->
            <div class="gap-1 px-6 flex flex-1 justify-center py-5">
                <div class="layout-content-container flex flex-col w-80">
                    <div class="flex h-full min-h-[700px] flex-col justify-between bg-white p-4">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-full {{ Request::routeIs('admin.dashboard') ? 'bg-secondary' : '' }}">
                                    <div class="text-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M240,160v24a16,16,0,0,1-16,16H115.93a4,4,0,0,1-3.24-6.35L174.27,109a8.21,8.21,0,0,0-1.37-11.3,8,8,0,0,0-11.37,1.61l-72,99.06A4,4,0,0,1,86.25,200H32a16,16,0,0,1-16-16V161.13c0-1.79,0-3.57.13-5.33a4,4,0,0,1,4-3.8H48a8,8,0,0,0,8-8.53A8.17,8.17,0,0,0,47.73,136H23.92a4,4,0,0,1-3.87-5c12-43.84,49.66-77.13,95.52-82.28a4,4,0,0,1,4.43,4V80a8,8,0,0,0,8.53,8A8.17,8.17,0,0,0,136,79.73V52.67a4,4,0,0,1,4.43-4A112.18,112.18,0,0,1,236.23,131a4,4,0,0,1-3.88,5H208.27a8.17,8.17,0,0,0-8.25,7.47,8,8,0,0,0,8,8.53h27.92a4,4,0,0,1,4,3.86C240,157.23,240,158.61,240,160Z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-dark text-sm font-medium leading-normal">Dashboard</p>
                                </a>
                                <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full {{ Request::routeIs('admin.bookings.index') || Request::routeIs('admin.bookings.edit') ? 'bg-secondary' : '' }}">
                                    <div class="text-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-dark text-sm font-medium leading-normal">Bookings</p>
                                </a>
                                <a href="{{ route('admin.meeting_rooms') }}" class="flex items-center gap-3 px-3 py-2 rounded-full {{ Request::routeIs('admin.meeting_rooms') ? 'bg-secondary' : '' }}">
                                    <div class="text-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M112,104a8,8,0,0,1-8,8H96v40a8,8,0,0,1-16,0V112H72a8,8,0,0,1,0-16h32A8,8,0,0,1,112,104ZM232,92.74V152a40,40,0,0,1-36.63,39.85,64,64,0,0,1-118.7.15H40a16,16,0,0,1-16-16V80A16,16,0,0,1,40,64H96.81a40,40,0,0,1,73.31-28.85A32,32,0,0,1,211.69,80h7.57A12.76,12.76,0,0,1,232,92.74ZM112,56a23.82,23.82,0,0,0,1.38,8H136a16,16,0,0,1,15.07,10.68A24,24,0,1,0,112,56Zm24,120h0V80H40v96h96Zm48-80H152v80a16,16,0,0,1-16,16H94.44A48,48,0,0,0,184,168Zm16-32a16,16,0,0,0-24.4-13.6A39.89,39.89,0,0,1,168,80h16A16,16,0,0,0,200,64Zm16,32H200v72a62.76,62.76,0,0,1-.36,6.75A24,24,0,0,0,216,152Z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-dark text-sm font-medium leading-normal">Ruang Meeting</p>
                                </a>
                                <a href="{{ route('admin.departments') }}" class="flex items-center gap-3 px-3 py-2 rounded-full {{ Request::routeIs('admin.departments') ? 'bg-secondary' : '' }}">
                                    <div class="text-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm12-88a12,12,0,1,1-12-12A12,12,0,0,1,140,128Zm44,0a12,12,0,1,1-12-12A12,12,0,0,1,184,128Zm-88,0a12,12,0,1,1-12-12A12,12,0,0,1,96,128Z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-dark text-sm font-medium leading-normal">Departemen</p>
                                </a>
                                <a href="{{ route('admin.employees') }}" class="flex items-center gap-3 px-3 py-2 rounded-full {{ Request::routeIs('admin.employees') ? 'bg-secondary' : '' }}">
                                    <div class="text-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24ZM74.08,197.5a64,64,0,0,1,107.84,0,87.83,87.83,0,0,1-107.84,0ZM96,120a32,32,0,1,1,32,32A32,32,0,0,1,96,120Zm97.76,66.41a79.66,79.66,0,0,0-36.06-28.75,48,48,0,1,0-59.4,0,79.66,79.66,0,0,0-36.06,28.75,88,88,0,1,1,131.52,0Z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-dark text-sm font-medium leading-normal">Karyawan</p>
                                </a>
                                <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-full {{ Request::routeIs('admin.reports') || Request::routeIs('admin.activity.*') ? 'bg-secondary' : '' }}">
                                    <div class="text-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M208,40H48A16,16,0,0,0,32,56v58.77c0,89.61,75.82,119.34,91,124.39a15.53,15.53,0,0,0,10,0c15.2-5.05,91-34.78,91-124.39V56A16,16,0,0,0,208,40ZM128,224c-9.26-3.08-43.29-16.32-63.87-49.5L128,129.76l63.87,44.71C171.31,207.61,137.34,220.85,128,224Zm80-109.18c0,17.64-3.36,32.63-8.72,45.34l-66.69-46.68a8,8,0,0,0-9.18,0L56.72,160.13C51.36,147.42,48,132.43,48,114.79V56l160,0Z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-dark text-sm font-medium leading-normal">Reports</p>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('admin.logout') }}" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]">
                            <span class="truncate">Logout</span>
                        </a>
                    </div>
                </div>
                <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

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
