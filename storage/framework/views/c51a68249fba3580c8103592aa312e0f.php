<?php
    use Carbon\Carbon;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Sales Officer - <?php echo $__env->yieldContent('title'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('images/logo.png')); ?>" type="image/png">
    
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
                        primary: '#10b981', // Green color for Sales Officer
                        secondary: '#F4EFE6',
                        dark: '#1C160C',
                        accent: '#A18249',
                        danger: '#ef4444',
                        border: '#E9DFCE',
                        sales: '#10b981',
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    
    <!-- Compact Admin Styling -->
    <link rel="stylesheet" href="<?php echo e(asset('css/compact-admin.css')); ?>">

    <!-- ApexCharts (for charts) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <?php echo $__env->yieldPushContent('styles'); ?>
    
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
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
        
        /* Sales officer event styling */
        .sales-officer-event {
            background-color: #10b981 !important; 
            border-left: 4px solid #059669 !important;
            color: #064e3b !important;
        }
    
        .sales-officer-event .fc-event-title {
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
                <h2 class="text-dark text-lg font-bold leading-tight tracking-[-0.015em] logo-text">WG Sales Officer</h2>
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
                        <a href="<?php echo e(route('sales_officer.dashboard')); ?>" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo e(Request::routeIs('sales_officer.dashboard') ? 'active' : ''); ?>">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M240,160v24a16,16,0,0,1-16,16H115.93a4,4,0,0,1-3.24-6.35L174.27,109a8.21,8.21,0,0,0-1.37-11.3,8,8,0,0,0-11.37,1.61l-72,99.06A4,4,0,0,1,86.25,200H32a16,16,0,0,1-16-16V161.13c0-1.79,0-3.57.13-5.33a4,4,0,0,1,4-3.8H48a8,8,0,0,0,8-8.53A8.17,8.17,0,0,0,47.73,136H23.92a4,4,0,0,1-3.87-5c12-43.84,49.66-77.13,95.52-82.28a4,4,0,0,1,4.43,4V80a8,8,0,0,0,8.53,8A8.17,8.17,0,0,0,136,79.73V52.67a4,4,0,0,1,4.43-4A112.18,112.18,0,0,1,236.23,131a4,4,0,0,1-3.88,5H208.27a8.17,8.17,0,0,0-8.25,7.47,8,8,0,0,0,8,8.53h27.92a4,4,0,0,1,4,3.86C240,157.23,240,158.61,240,160Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(route('sales_officer.activities.index')); ?>" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo e(Request::routeIs('sales_officer.activities.*') ? 'active' : ''); ?>">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-96-88v64a8,8,0,0,1-16,0V132.94l-4.42,2.22a8,8,0,0,1-7.16-14.32l16-8A8,8,0,0,1,112,120Zm59.16,30.45L152,176h16a8,8,0,0,1,0,16H136a8,8,0,0,1-6.4-12.8l28.78-38.37A8,8,0,1,0,145.07,132a8,8,0,1,1-13.85-8A24,24,0,0,1,176,136,23.76,23.76,0,0,1,171.16,150.45Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Activities</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(route('sales_officer.contacts.index')); ?>" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo e(Request::routeIs('sales_officer.contacts.*') ? 'active' : ''); ?>">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M224,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H224a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Zm0,144H32V64H224V192ZM80,104a24,24,0,1,1-24,24A24,24,0,0,1,80,104Zm86.4,48H152a8,8,0,0,1,0-16h14.4a24,24,0,0,0,0-48H152a8,8,0,0,1,0-16h14.4a40,40,0,0,1,0,80Zm-16-32a8,8,0,0,1-8,8H120a8,8,0,0,1,0-16h22.4A8,8,0,0,1,150.4,120Zm-70.4,40h0a40,40,0,0,1-40-40H32v8a16,16,0,0,0,16,16H88A16,16,0,0,0,104,128V120H96A40,40,0,0,1,80,160Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Contacts</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(route('sales_officer.calendar')); ?>" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo e(Request::routeIs('sales_officer.calendar') ? 'active' : ''); ?>">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm64-88a8,8,0,0,1-8,8H128a8,8,0,0,1-8-8V72a8,8,0,0,1,16,0v48h48A8,8,0,0,1,192,128Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Activity Calendar</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(route('sales_officer.reports.index')); ?>" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo e(Request::routeIs('sales_officer.reports.*') ? 'active' : ''); ?>">
                            <div class="text-inherit sidebar-icon-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM152,41.38,188.69,78.06H152ZM200,216H56V40h80V88a8,8,0,0,0,8,8h56V216Zm-42.34-77.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.31L96,177.38l50.34-50.35a8,8,0,0,1,11.32,11.31Z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-text">Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Quick Stats Section -->
            <div class="quick-stats-section mt-6 px-4">
                <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Quick Stats</h5>
                
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-600">Month Activities</p>
                            <span class="text-xs text-primary font-semibold"><?php echo e(now()->format('M Y')); ?></span>
                        </div>
                        <div class="mt-1">
                            <p class="text-lg font-bold">65</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->            
            <div class="px-4 mt-6">
                <a href="<?php echo e(route('admin.logout')); ?>" class="logout-btn flex items-center gap-3 px-3 py-2.5 rounded-lg w-full">
                    <div class="text-inherit sidebar-icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M112,216a8,8,0,0,1-8,8H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h56a8,8,0,0,1,0,16H48V208h56A8,8,0,0,1,112,216Zm109.66-93.66-40-40a8,8,0,0,0-11.32,11.32L196.69,120H104a8,8,0,0,0,0,16h92.69l-26.35,26.34a8,8,0,0,0,11.32,11.32l40-40A8,8,0,0,0,221.66,122.34Z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium sidebar-text">Logout</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <main id="main-content">
        <!-- Top Navigation Bar -->
        <div class="bg-white shadow-sm mb-6">
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-800"><?php echo $__env->yieldContent('header', 'Dashboard'); ?></h1>
                    <p class="text-sm text-gray-600"><?php echo $__env->yieldContent('description', 'Overview and statistics'); ?></p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-semibold"><?php echo e(Auth::user()->name); ?></p>
                        <p class="text-xs text-gray-500">Sales Officer</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Flash Messages -->
        <?php if(session('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 mx-6 rounded animate-fade-in" role="alert">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Main Content Container -->
        <div class="px-6 pb-6">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if(sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    // Rotate the toggle button icon
                    this.classList.toggle('rotate-180');
                });
            }
            
            // Initialize flatpickr on any date inputs
            flatpickr(".datepicker", {
                locale: "id",
                dateFormat: "Y-m-d",
                allowInput: true
            });
            
            // Initialize flatpickr on any datetime inputs
            flatpickr(".datetimepicker", {
                locale: "id",
                dateFormat: "Y-m-d H:i",
                enableTime: true,
                time_24hr: true,
                allowInput: true
            });
            
            // Display SweetAlert2 notifications for flash messages
            <?php if(session('success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "<?php echo e(session('success')); ?>",
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "<?php echo e(session('error')); ?>",
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            <?php endif; ?>
        });
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html> <?php /**PATH F:\Project\Booking-Room-System\resources\views/sales_officer/layout.blade.php ENDPATH**/ ?>