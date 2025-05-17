<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Werkudara OpsCenter')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
        rel="stylesheet"
        as="style"
        onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
    />

    <!-- Toastify JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- Stack Styles: agar CSS dari child view (misal Flatpickr) termuat -->
    @stack('styles')

    <style>
        :root {
            --primary-color: #26458e;
        }
        
        /* Background */
        body {
            font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;
            background: url('{{ asset('images/bg.png') }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: white;
        }

        /* Overlay */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.15);
            z-index: 1;
        }

        /* Konten */
        .content {
            position: relative;
            z-index: 2;
            transition: opacity 0.3s ease-in-out;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            z-index: 50;
            background: transparent;
            position: relative;
        }

        /* Struktur Navbar */
        .navbar-container {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        /* Logo (Tengah) */
        .navbar-center img {
            height: 40px;
            width: auto;
        }

        /* Navbar Right (Desktop) */
        .navbar-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .navbar-right a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            padding: 6px 12px;
            border-radius: 4px;
            position: relative;
        }

        .navbar-right a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-1px);
        }
        
        .navbar-right a.active {
            background-color: white;
            color: var(--primary-color);
            font-weight: 500;
        }

        /* Dropdown Styling */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        
        .dropdown-button:after {
            content: '▼';
            font-size: 10px;
            margin-left: 5px;
            opacity: 0.8;
        }
        
        .dropdown-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            color: #333;
            min-width: 150px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease-in-out;
            z-index: 100;
            margin-top: 4px;
        }
        
        .dropdown-content a {
            display: block;
            padding: 8px 12px;
            font-size: 14px;
            text-decoration: none;
            color: #333;
            transition: all 0.15s;
        }
        
        .dropdown-content a:hover {
            background: rgba(38, 69, 142, 0.1);
            color: var(--primary-color);
            padding-left: 16px;
        }
        
        .dropdown:hover .dropdown-content {
            opacity: 1;
            visibility: visible;
        }

        /* Mode Desktop */
        @media (min-width: 769px) {
            /* Tampilkan Judul */
            .navbar-left h1 {
                display: block;
            }

            /* Logo tetap di tengah */
            .navbar-center {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }

            /* Navbar Right tetap terlihat */
            .navbar-right {
                display: flex !important;
            }

            /* Sembunyikan Hamburger Menu */
            .menu-toggle {
                display: none;
            }
        }

        /* Mode Mobile */
        @media (max-width: 768px) {
            /* Sembunyikan Judul */
            .navbar-left h1 {
                display: none;
            }

            /* Logo berpindah ke kiri */
            .navbar-center {
                position: static;
                transform: none;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                flex-grow: 1;
            }

            /* Navbar Right disembunyikan */
            .navbar-right {
                display: none;
            }

            /* Tampilkan Hamburger Menu */
            .menu-toggle {
                display: block;
                cursor: pointer;
            }

            .menu-toggle div {
                background-color: white;
                height: 3px;
                width: 25px;
                margin: 5px 0;
            }
        }

        /* Fullscreen Menu (Mobile) */
        .fullscreen-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f5f8ff;
            background-image: radial-gradient(circle at 80% 20%, rgba(38, 69, 142, 0.08), transparent 40%), 
                            radial-gradient(circle at 20% 80%, rgba(38, 69, 142, 0.05), transparent 40%);
            background-attachment: fixed;
            z-index: 100;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 25px;
            transition: opacity 0.3s ease-in-out;
        }

        .fullscreen-menu.active {
            display: flex;
        }

        .fullscreen-menu a {
            font-size: 18px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 6px;
            transition: all 0.2s;
            min-width: 200px;
        }

        .fullscreen-menu a:hover {
            background: rgba(38, 69, 142, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Tombol Close Menu */
        .close-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 32px;
            cursor: pointer;
            color: var(--primary-color);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.2s;
        }
        
        .close-menu:hover {
            background: rgba(255, 255, 255, 0.5);
            transform: rotate(90deg);
        }
        
        /* Form elements styling */
        .form-input, .form-select {
            background-color: rgba(245, 247, 250, 0.9);
            border: 1px solid rgba(38, 69, 142, 0.2);
            color: #333;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            width: 100%;
            transition: all 0.2s;
        }
        
        .form-input::placeholder {
            color: rgba(100, 116, 139, 0.6);
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: rgba(38, 69, 142, 0.5);
            box-shadow: 0 0 0 2px rgba(38, 69, 142, 0.2);
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: #26458e;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1c3a7a;
        }

        /* Pastikan Flatpickr muncul di atas elemen lainnya */
        .flatpickr-calendar {
            z-index: 1000 !important;
        }

        /* Custom Toastify Styling */
        .toastify {
            font-family: var(--font-family);
            padding: 14px 20px;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            opacity: 0.98;
            backdrop-filter: blur(8px);
            transform: translateY(0);
            transition: transform 0.3s ease;
            max-width: 380px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: none;
        }
        
        .toastify.success-toast {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.98), rgba(5, 150, 105, 0.98));
            color: white;
        }
        
        .toastify.error-toast {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.98), rgba(220, 38, 38, 0.98));
            color: white;
        }
        
        .toastify .toast-close {
            opacity: 0.7;
            padding: 0 8px;
            font-size: 20px;
            color: white;
            transition: all 0.2s;
        }
        
        .toastify .toast-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }
        
        .toastify:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Animation for toast appearance */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 0.98;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 0.98;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .toastify.toastify-right.toastify-top {
            animation: slideInRight 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }

        .toastify.toastify-right.toastify-top.hideMe {
            animation: slideOutRight 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }
    </style>
</head>
<body class="relative">

    <!-- Overlay -->
    <div class="overlay"></div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container mx-auto navbar-container">
            <!-- Kiri: Judul -->
            <div class="navbar-left">
                <h1 class="text-xl font-bold text-white">Werkudara OpsCenter</h1>
            </div>

            <!-- Tengah: Logo -->
            <div class="navbar-center">
                <img src="{{ asset('images/kuda.png') }}" alt="Logo Kuda">
            </div>

            <!-- Kanan: Menu Navigasi (Desktop) -->
            <div class="navbar-right">
                <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>

                <!-- Dropdown untuk Form -->
                <div class="dropdown">
                    <button class="dropdown-button">
                        Form
                    </button>
                    <div class="dropdown-content">
                        <a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'active' : '' }}">Booking</a>
                        <a href="{{ route('activity.create') }}" class="{{ request()->routeIs('activity.create') ? 'active' : '' }}">Activity</a>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="dropdown-button">
                        Calendar
                    </button>
                    <div class="dropdown-content">
                        <a href="{{ route('calendar.index') }}" class="{{ request()->routeIs('calendar.index') ? 'active' : '' }}">Room Booking</a>
                        <a href="{{ route('activity.calendar') }}" class="{{ request()->routeIs('activity.calendar') ? 'active' : '' }}">Activity</a>
                    </div>
                </div>
                <a href="/admin/login" class="{{ request()->is('admin/login') ? 'active' : '' }}">Login</a>
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="menu-toggle" id="mobile-menu">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </nav>

    <!-- Fullscreen Menu (Mobile) -->
    <div class="fullscreen-menu" id="fullscreenMenu">
        <span class="close-menu" id="closeMenu">&times;</span>
        <a href="/">Home</a>
        <!-- Menu Form dengan submenu -->
        <div class="text-2xl">
            <span>Form</span>
            <div class="mt-2">
                <a href="{{ route('booking.create') }}" class="block py-1">Form Booking</a>
                <a href="{{ route('activity.create') }}" class="block py-1">Activity</a>
            </div>
        </div>
        <!-- Calendar Menu with submenu -->
        <div class="text-2xl">
            <span>Calendar</span>
            <div class="mt-2">
                <a href="{{ route('calendar.index') }}" class="block py-1">Room Booking</a>
                <a href="{{ route('activity.calendar') }}" class="block py-1">Activity</a>
            </div>
        </div>
        <a href="/admin/login">Login</a>
    </div>

    <div class="content container mx-auto mt-8" id="mainContent">
        @yield('content')
    </div>

    <script>
        // Toggle Mobile Fullscreen Menu
        const mobileMenu = document.getElementById("mobile-menu");
        const fullscreenMenu = document.getElementById("fullscreenMenu");
        const closeMenu = document.getElementById("closeMenu");
        const mainContent = document.getElementById("mainContent");

        mobileMenu.addEventListener("click", () => {
            fullscreenMenu.classList.add("active");
            mainContent.style.opacity = "0";
        });

        closeMenu.addEventListener("click", () => {
            fullscreenMenu.classList.remove("active");
            mainContent.style.opacity = "1";
        });
    </script>

    <!-- Toast Notification System -->
    <script>
        // Toast notification helper functions
        window.showSuccessToast = function(message) {
            // Extract emoji if present at start of message
            let cleanMessage = message;
            let iconHTML = '<i class="fas fa-check-circle mr-2 text-xl"></i>';
            
            // Remove emoji if present (like '✅')
            if (message.startsWith('✅')) {
                cleanMessage = message.substring(2).trim();
            }
            
            Toastify({
                text: iconHTML + cleanMessage,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "",
                className: "success-toast",
                stopOnFocus: true,
                close: true,
                escapeMarkup: false,
                onClick: function(){}, // Callback after click
                onClose: function() {
                    // Add class to animate out
                    const toasts = document.querySelectorAll('.toastify');
                    toasts.forEach(toast => {
                        toast.classList.add('hideMe');
                    });
                }
            }).showToast();
        };

        window.showErrorToast = function(message) {
            // Extract emoji if present at start of message
            let cleanMessage = message;
            let iconHTML = '<i class="fas fa-exclamation-circle mr-2 text-xl"></i>';
            
            // Remove emoji if present (like '⚠️')
            if (message.startsWith('⚠️')) {
                cleanMessage = message.substring(2).trim();
            }
            
            Toastify({
                text: iconHTML + cleanMessage,
                duration: 4000,
                gravity: "top",
                position: "right",
                backgroundColor: "",
                className: "error-toast",
                stopOnFocus: true,
                close: true,
                escapeMarkup: false,
                onClick: function(){}, // Callback after click
                onClose: function() {
                    // Add class to animate out
                    const toasts = document.querySelectorAll('.toastify');
                    toasts.forEach(toast => {
                        toast.classList.add('hideMe');
                    });
                }
            }).showToast();
        };

        // Display any Laravel flash messages as toast notifications
        document.addEventListener("DOMContentLoaded", function() {
            // Check for success message in session
            const successMessage = "{{ session('success') }}";
            if (successMessage && successMessage !== '') {
                showSuccessToast('✅ ' + successMessage);
            }
            
            // Check for errors
            @if($errors->any())
                @foreach($errors->all() as $error)
                    showErrorToast('⚠️ {{ $error }}');
                @endforeach
            @endif
        });
    </script>

    @stack('scripts')

</body>
</html>
