<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Booking Ruangan')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Stack Styles: agar CSS dari child view (misal Flatpickr) termuat -->
    @stack('styles')

    <style>
        /* Background */
        body {
            background: url('{{ asset('images/bg.png') }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        /* Overlay */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
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
            padding: 10px 20px;
            z-index: 50;
            background: rgba(0, 0, 0, 0.8);
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
        }

        .navbar-right a:hover {
            color: #00c3ff;
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
        }

        /* 
           Ubah background dropdown menjadi gelap agar tidak terlalu kontras.
           Menggunakan rgba(0, 0, 0, 0.8) agar sedikit transparan. 
        */
        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(0, 0, 0, 0.85);
            color: #fff;
            min-width: 150px;
            border-radius: 0.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease-in-out;
            z-index: 100;
        }

        .dropdown-content a {
            display: block;
            padding: 8px 12px;
            font-size: 14px;
            text-decoration: none;
            color: #fff; /* Teks putih agar terbaca di latar gelap */
        }

        /* Sedikit efek hover lebih terang */
        .dropdown-content a:hover {
            background: rgba(255, 255, 255, 0.15);
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
            background: url('{{ asset('images/bg.png') }}') no-repeat center center fixed;
            background-size: cover;
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
            font-size: 24px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
        }

        .fullscreen-menu a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Tombol Close Menu */
        .close-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 35px;
            cursor: pointer;
            color: white;
        }

        /* Pastikan Flatpickr muncul di atas elemen lainnya */
        .flatpickr-calendar {
            z-index: 9999 !important;
        }
    </style>
</head>
<body class="relative text-white">

    <!-- Overlay -->
    <div class="overlay"></div>

    <!-- Navbar -->
    <nav class="content p-4">
        <div class="container mx-auto navbar-container">
            <!-- Kiri: Judul -->
            <div class="navbar-left">
                <h1 class="text-xl font-bold">Werkudara Meeting Room System</h1>
            </div>

            <!-- Tengah: Logo -->
            <div class="navbar-center">
                <img src="{{ asset('images/kuda.png') }}" alt="Logo Kuda">
            </div>

            <!-- Kanan: Menu Navigasi (Desktop) -->
            <div class="navbar-right">
                <a href="/">Home</a>

                <!-- Dropdown untuk Form -->
                <div class="dropdown">
                    <button class="dropdown-button">
                        Form
                    </button>
                    <div class="dropdown-content">
                        <a href="{{ route('booking.create') }}">Form Booking</a>
                        <a href="{{ route('activity.create') }}">Activity</a>
                    </div>
                </div>

                <a href="/calendar">Calendar Room</a>
                <a href="/admin/login">Login</a>
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
        <a href="/calendar">Calendar Room</a>
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

    @stack('scripts')

</body>
</html>
