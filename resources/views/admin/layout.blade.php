<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - @yield('title')</title>
    
    <!-- Tailwind CSS dan Konfigurasi -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E3A8A',
                        secondary: '#F59E0B',
                        sidebar: '#111827',
                        hover: '#1E40AF',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800 font-poppins overflow-x-hidden">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="sidebar w-64 bg-sidebar text-white fixed h-full z-50">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
            </div>
            
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg {{ Request::routeIs('admin.dashboard') ? 'bg-primary/20 text-primary' : 'hover:bg-white/10' }}">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.meeting_rooms') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg {{ Request::routeIs('admin.meeting_rooms') ? 'bg-primary/20 text-primary' : 'hover:bg-white/10' }}">
                    <i class="fas fa-door-open"></i>
                    Ruang Meeting
                </a>

                <a href="{{ route('admin.departments') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg {{ Request::routeIs('admin.departments') ? 'bg-primary/20 text-primary' : 'hover:bg-white/10' }}">
                    <i class="fas fa-building"></i>
                    Departemen
                </a>

                <a href="{{ route('admin.employees') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg {{ Request::routeIs('admin.employees') ? 'bg-primary/20 text-primary' : 'hover:bg-white/10' }}">
                    <i class="fas fa-users"></i>
                    Karyawan
                </a>

                <a href="{{ route('admin.reports') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg {{ Request::routeIs('admin.reports*') ? 'bg-primary/20 text-primary' : 'hover:bg-white/10' }}">
                    <i class="fas fa-file-alt"></i>
                    Reports
                </a>
            </nav>

            <div class="absolute bottom-0 w-full p-4">
                <a href="{{ route('admin.logout') }}" 
                   class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-lg">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content flex-1 ml-64 p-8 relative z-10 overflow-x-auto">
          @yield('content')
      </main>
      
    </div>

    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token(), 'baseUrl' => url('/'), 'currentRoute' => Route::currentRouteName()]) !!};
    </script>
    @stack('scripts')
</body>
</html>
