{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<script>
    // CRITICAL: Set theme IMMEDIATELY before any rendering
    (function() {
        // Czytaj theme z Blade data (z bazy danych)
        const theme = '{{ Auth::check() ? (Auth::user()->preferences_with_defaults['theme'] ?? 'light') : 'light' }}';
        const html = document.documentElement;
        const isDark = theme === 'dark';

        // Set background on HTML element FIRST
        html.style.backgroundColor = isDark ? '#111827' : '#f9fafb';

        // Add dark class if needed
        if (isDark) {
            html.classList.add('dark');
        }

        // Set body background too (for when body loads)
        const style = document.createElement('style');
        style.innerHTML = `body { background-color: ${isDark ? '#111827' : '#f9fafb'} !important; }`;
        document.head.appendChild(style);
    })();
</script>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Rehamed') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @yield('styles')
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }

        /* General & Auth Page Styles */
        .hero-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .form-container { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1); }
        .form-input { background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px); }
        .form-input:focus { background: rgba(255, 255, 255, 0.25); border-color: #fbbf24; }
        .form-input::placeholder { color: rgba(255, 255, 255, 0.7); }
        .btn-primary { background: linear-gradient(135deg, #fbbf24, #f59e0b); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(251, 191, 36, 0.4); }
        .toggle-form { color: #fbbf24; font-weight: 600; }
        .checkbox-custom { appearance: none; width: 20px; height: 20px; border: 2px solid rgba(255, 255, 255, 0.5); border-radius: 4px; background: rgba(255, 255, 255, 0.1); cursor: pointer; }
        .checkbox-custom:checked { background: #fbbf24; border-color: #fbbf24; }
        .error-message { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .success-message { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #86efac; }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #1a202c;
            color: #e2e8f0;
        }

        /* Dark mode overrides - mniej agresywne, bez !important dla Tailwind */
        body.dark-mode .bg-white {
            background-color: #2d3748;
        }

        body.dark-mode .bg-gray-50 {
            background-color: #1a202c;
        }

        body.dark-mode .bg-gray-100 {
            background-color: #374151;
        }

        body.dark-mode .text-gray-900 {
            color: #e2e8f0;
        }

        body.dark-mode .text-gray-800 {
            color: #e2e8f0;
        }

        body.dark-mode .text-gray-700 {
            color: #cbd5e0;
        }

        body.dark-mode .text-gray-600 {
            color: #cbd5e0;
        }

        body.dark-mode .text-gray-500 {
            color: #a0aec0;
        }

        /* Klasy Tailwind dark: mają wyższy priorytet */
        .dark .dark\:bg-gray-800 {
            background-color: #1f2937 !important;
        }

        .dark .dark\:bg-gray-700 {
            background-color: #374151 !important;
        }

        .dark .dark\:bg-gray-900 {
            background-color: #111827 !important;
        }

        .dark .dark\:text-white {
            color: #ffffff !important;
        }

        .dark .dark\:text-gray-300 {
            color: #d1d5db !important;
        }

        .dark .dark\:text-gray-400 {
            color: #9ca3af !important;
        }

        .dark .dark\:hover\:bg-gray-700:hover {
            background-color: #374151 !important;
        }

        .dark .dark\:divide-gray-700 > * + * {
            border-color: #374151 !important;
        }

        body.dark-mode .border-gray-200,
        body.dark-mode .border-gray-300 {
            border-color: #4a5568 !important;
        }

        body.dark-mode input,
        body.dark-mode select,
        body.dark-mode textarea {
            background-color: #374151 !important;
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }

        body.dark-mode input:focus,
        body.dark-mode select:focus,
        body.dark-mode textarea:focus {
            background-color: #4a5568 !important;
            border-color: #667eea !important;
        }

        body.dark-mode input::placeholder,
        body.dark-mode select::placeholder,
        body.dark-mode textarea::placeholder {
            color: #718096 !important;
        }

        body.dark-mode .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3) !important;
        }

        body.dark-mode .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2) !important;
        }

        /* Navbar Dark Mode */
        body.dark-mode nav.navbar-glass {
            background-color: rgba(45, 55, 72, 0.95) !important;
            border-bottom-color: #4a5568 !important;
        }

        body.dark-mode .navbar-glass {
            backdrop-filter: blur(20px);
            background: rgba(45, 55, 72, 0.95) !important;
        }

        /* Sidebar Dark Mode */
        body.dark-mode .sidebar-glass {
            background-color: rgba(45, 55, 72, 0.95) !important;
            border-right-color: #4a5568 !important;
        }

        /* Cards Dark Mode */
        body.dark-mode .card,
        body.dark-mode .stat-card {
            background-color: #2d3748 !important;
        }

        /* Dropdowns Dark Mode */
        body.dark-mode .user-dropdown,
        body.dark-mode .reports-dropdown {
            background: linear-gradient(145deg, rgba(45, 55, 72, 0.95), rgba(45, 55, 72, 0.9)) !important;
            border-color: rgba(74, 85, 104, 0.5) !important;
        }

        body.dark-mode .user-dropdown-item,
        body.dark-mode .reports-dropdown-item {
            color: #cbd5e0 !important;
        }

        body.dark-mode .user-dropdown-item:hover,
        body.dark-mode .reports-dropdown-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2)) !important;
            color: #667eea !important;
        }

        /* Notification Dropdown Dark Mode */
        body.dark-mode #notification-dropdown {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        body.dark-mode #notification-dropdown h3 {
            color: #ffffff !important;
        }

        body.dark-mode #notification-dropdown .border-gray-200,
        body.dark-mode #notification-dropdown .border-b,
        body.dark-mode #notification-dropdown .border-t {
            border-color: #374151 !important;
        }

        body.dark-mode #notifications-list .divide-gray-100 > * {
            border-color: #374151 !important;
        }

        body.dark-mode #mark-all-read {
            color: #818cf8 !important;
        }

        body.dark-mode #mark-all-read:hover {
            color: #a5b4fc !important;
        }

        body.dark-mode #notification-dropdown a {
            color: #818cf8 !important;
        }

        body.dark-mode #notification-dropdown a:hover {
            color: #a5b4fc !important;
        }

        body.dark-mode #notification-dropdown .text-gray-500,
        body.dark-mode #notification-dropdown .text-gray-400 {
            color: #9ca3af !important;
        }

        /* Tables Dark Mode - tylko dla tabel BEZ klas dark: */
        body.dark-mode table:not([class*="dark:"]) {
            color: #e2e8f0 !important;
        }

        body.dark-mode table:not([class*="dark:"]) thead {
            background-color: #374151 !important;
        }

        body.dark-mode table:not([class*="dark:"]) tbody tr {
            border-color: #4a5568 !important;
        }

        body.dark-mode table:not([class*="dark:"]) tbody tr:hover {
            background-color: #374151 !important;
        }

        /* Buttons Dark Mode */
        body.dark-mode .btn-outline {
            border-color: #4a5568 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode .btn-outline:hover {
            background-color: #374151 !important;
        }

        /* Scrollbar Dark Mode */
        body.dark-mode ::-webkit-scrollbar-track {
            background: #2d3748;
        }

        body.dark-mode ::-webkit-scrollbar-thumb {
            background: #4a5568;
        }

        body.dark-mode ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }

        /* Dashboard Styles */
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .navbar-glass {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: 64px;
        }

        /* Mobile sticky navbar enhancement */
        @media (max-width: 767px) {
            .navbar-glass {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(25px);
                box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                position: fixed !important;
                top: 0 !important;
                z-index: 9999 !important;
            }

            body {
                padding-top: 0;
            }

            body.dark-mode .navbar-glass {
                background: rgba(45, 55, 72, 0.98) !important;
                border-bottom: 1px solid #4a5568 !important;
            }
        }

        .sidebar-glass {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-item { transition: all 0.3s ease; }
        .nav-item:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateX(8px);
        }
        .nav-item.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .logo-icon { animation: heartbeat 2s infinite; }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .notification-badge { animation: bounce 2s infinite; }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* PozostaÅ‚e style bez zmian... */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            transition: opacity 0.3s ease;
        }

        .sidebar-collapsed {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar-collapsed.open {
            transform: translateX(0);
            width: 100% !important;
            max-width: 100% !important;
        }

        .sidebar-toggle {
            transition: transform 0.3s ease;
        }

        .sidebar-toggle.rotated {
            transform: rotate(180deg);
        }

        /* Desktop Sidebar States */
        .sidebar-desktop {
            /* slightly narrower sidebar to give main content more room */
            width: 220px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 30;
            border-right: 1px solid rgba(229, 231, 235, 0.8);
        }

        .sidebar-desktop.collapsed {
            /* smaller collapsed width */
            width: 64px;
        }

        /* Collapsed sidebar styling */
        .sidebar-desktop.collapsed .sidebar-content {
            padding: 1rem 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar-desktop.collapsed .nav-item {
            width: 48px;
            height: 48px;
            padding: 0;
            margin: 0.25rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            position: relative;
            transition: all 0.2s ease;
            background: transparent;
        }

        .sidebar-desktop.collapsed .nav-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.15));
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.25);
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .sidebar-desktop.collapsed .nav-item.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        .sidebar-desktop.collapsed .nav-item i {
            margin: 0;
            font-size: 18px;
            color: inherit;
        }

        .sidebar-desktop.collapsed .nav-text {
            display: none;
        }

        /* Main content positioning */
        .main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.expanded {
            margin-left: 220px;
        }

        .main-content.collapsed {
            margin-left: 64px;
        }

        @media (max-width: 767px) {
            .main-content {
                margin-left: 0 !important;
            }
        }

        /* Enhanced Tooltip System */
        .sidebar-desktop.collapsed .nav-item::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 16px);
            top: 50%;
            transform: translateY(-50%) translateX(-8px);
            background: linear-gradient(135deg, #1f2937, #374151);
            color: white;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-desktop.collapsed .nav-item::before {
            content: '';
            position: absolute;
            left: calc(100% + 8px);
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1f2937;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1001;
        }

        .sidebar-desktop.collapsed .nav-item:hover::after,
        .sidebar-desktop.collapsed .nav-item:hover::before {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-desktop.collapsed .nav-item:hover::after {
            transform: translateY(-50%) translateX(0);
        }

        .sidebar-desktop.collapsed #reports-submenu {
            display: none !important;
        }

        .sidebar-desktop.collapsed #reports-chevron {
            display: none !important;
        }

        /* User dropdown styling */
        .user-dropdown {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(102, 126, 234, 0.15);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15), 0 8px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            min-width: 280px;
            animation: dropdownSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes dropdownSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: #374151;
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 0.125rem 0;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .user-dropdown-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            color: #667eea;
            transform: translateX(4px);
            border-color: rgba(102, 126, 234, 0.2);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        .user-dropdown-item i {
            width: 18px;
            margin-right: 0.75rem;
        }

        .user-dropdown-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(156, 163, 175, 0.3), transparent);
            margin: 0.5rem 1rem;
        }

        .user-avatar {
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .user-avatar:hover {
            border-color: rgba(102, 126, 234, 0.3);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .user-menu-button {
            padding: 0.5rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .user-menu-button:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-color: rgba(102, 126, 234, 0.2);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        /* Mobile Navigation Menu */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .mobile-menu.open {
            max-height: 400px;
        }

        /* Responsive optimizations */
        @media (max-width: 640px) {
            .nav-item:hover {
                transform: none;
            }
        }

        /* Print Styles - Hide navigation and sidebar elements */
        @media print {
            /* Hide all navigation and UI elements */
            header,
            nav,
            .navbar,
            #desktop-sidebar,
            #mobile-sidebar,
            .sidebar-glass,
            .sidebar-desktop,
            .sidebar-collapsed,
            .sidebar-overlay,
            .sidebar-toggle,
            .no-print,
            .user-dropdown,
            button,
            .btn,
            [onclick*="print"],
            [onclick*="download"] {
                display: none !important;
            }

            /* Reset page margins and layout */
            body {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Make main content full width */
            #main-content,
            .container {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 20px !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            /* Ensure good print rendering */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Page breaks */
            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            /* Show print-only elements */
            .print-only {
                display: block !important;
            }
        }
    </style>
    @stack('styles')

    <!-- Prevent white flash on dark mode -->
    <script>
        // Additional setup - add dark-mode class to body when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</head>
<body class="font-sans antialiased @guest hero-gradient @else bg-gray-50 dark:bg-gray-900 @endguest">
    <div id="app">
        <!-- Mobile Sidebar Overlay -->
        @auth
        <div id="sidebar-overlay" class="sidebar-overlay hidden" onclick="toggleSidebar()"></div>
        @endauth

        <nav class="navbar-glass fixed w-full z-50 h-16 no-print">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo and Sidebar Toggle -->
                    <div class="flex items-center">
                        @auth
                        <button id="sidebar-toggle" class="sidebar-toggle mr-2 sm:mr-3 p-2 text-gray-700 hover:text-indigo-600 transition-colors" onclick="toggleSidebar()">
                            <i class="fas fa-chevron-right text-base sm:text-lg"></i>
                        </button>
                        @endauth

                        <a href="{{ url('/') }}" class="flex items-center">
                            <h1 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-heartbeat mr-1 sm:mr-2 text-red-500 logo-icon text-sm sm:text-base"></i>
                                <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                    Rehamed
                                </span>
                                @auth
                                    <span class="hidden sm:inline ml-2 text-xs md:text-sm text-gray-600">Dashboard</span>
                                @endauth
                            </h1>
                        </a>
                    </div>

                    <!-- Desktop Navigation & Mobile Menu Toggle -->
                    <div class="flex items-center space-x-1 sm:space-x-2 lg:space-x-4">
                        @guest
                            <!-- Desktop Guest Navigation -->
                            <div class="hidden md:flex items-center space-x-4">
                                <a href="/" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-home mr-2"></i>
                                    <span class="hidden lg:inline">Strona główna</span>
                                    <span class="lg:hidden">Główna</span>
                                </a>
                                <a href="/#contact" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-phone mr-2"></i>
                                    <span class="hidden lg:inline">Kontakt</span>
                                    <span class="lg:hidden">Kontakt</span>
                                </a>
                            </div>

                            <button class="md:hidden p-2 text-gray-700 hover:text-indigo-600" onclick="toggleMobileMenu()">
                                <i class="fas fa-bars text-base sm:text-lg"></i>
                            </button>
                        @else
                            <!-- Authenticated User Menu -->
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
                            <a href="{{ route('reservation.index') }}" class="p-1 sm:p-2 text-gray-600 hover:text-indigo-600 transition-colors {{ request()->routeIs('reservation.*') ? 'text-indigo-600' : '' }}" title="Rezerwacja wizyty">
                                <i class="fas fa-stethoscope text-base sm:text-lg lg:text-xl"></i>
                            </a>
                            @endif

                            <a href="{{ route('chat.index') }}" class="relative p-1 sm:p-2 text-gray-600 hover:text-indigo-600 transition-colors {{ request()->routeIs('chat.*') ? 'text-indigo-600' : '' }}">
                                <i class="fas fa-comments text-base sm:text-lg lg:text-xl"></i>
                                <span class="absolute -top-1 -right-1 h-3 w-3 sm:h-4 sm:w-4 lg:h-5 lg:w-5 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center chat-notification-badge" style="display: none;">
                                    <span class="text-xs">0</span>
                                </span>
                            </a>

                            <div class="relative">
                                <button type="button" id="notification-button" class="relative p-1 sm:p-2 text-gray-600 hover:text-indigo-600 transition-colors">
                                    <i class="fas fa-bell text-base sm:text-lg lg:text-xl"></i>
                                    <span id="notification-badge" class="absolute -top-1 -right-1 h-3 w-3 sm:h-4 sm:w-4 lg:h-5 lg:w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" style="display: none;">
                                        <span id="notification-count" class="text-xs">0</span>
                                    </span>
                                </button>

                                <!-- Notification Dropdown -->
                                <div id="notification-dropdown" class="user-dropdown origin-top-right absolute right-0 mt-3 hidden w-80 sm:w-96 max-h-96 overflow-y-auto bg-white dark:bg-gray-800 border dark:border-gray-700">
                                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Powiadomienia</h3>
                                            <button id="mark-all-read" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                                                Oznacz wszystkie
                                            </button>
                                        </div>
                                    </div>
                                    <div id="notifications-list" class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <!-- Powiadomienia załadują się tutaj przez AJAX -->
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                            <i class="fas fa-spinner fa-spin mb-2"></i>
                                            <p>Ładowanie powiadomień...</p>
                                        </div>
                                    </div>
                                    <div class="p-3 border-t border-gray-200 dark:border-gray-700 text-center">
                                        <a href="{{ route('notifications.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                                            Zobacz wszystkie powiadomienia
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <button type="button" class="user-menu-button flex items-center space-x-1 sm:space-x-2 lg:space-x-3" id="user-menu-button">
                                    <img src="{{ Auth::user()->avatar_url }}"
                                         alt="Avatar uÅ¼ytkownika {{ Auth::user()->full_name }}"
                                         class="user-avatar w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 rounded-full object-cover border border-gray-200">
                                    <div class="hidden sm:block">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-800 truncate max-w-24 lg:max-w-full">
                                            {{ Auth::user()->firstname ?? Auth::user()->name }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            @if(Auth::user()->role === 'admin')
                                                <span class="hidden lg:inline">Administrator</span>
                                                <span class="lg:hidden">Admin</span>
                                            @elseif(Auth::user()->role === 'doctor')
                                                <span class="hidden lg:inline">Fizjoterapeuta</span>
                                                <span class="lg:hidden">Fizjo</span>
                                            @else
                                                Pacjent
                                            @endif
                                        </p>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"></i>
                                </button>
                                <div class="user-dropdown origin-top-right absolute right-0 mt-3 hidden" id="dropdown-menu">
                                    <div class="user-dropdown-content p-2">
                                        <div class="sm:hidden">
                                            <div class="user-dropdown-item">
                                                <i class="fas fa-user"></i>
                                                <span>{{ Auth::user()->firstname ?? Auth::user()->name }}</span>
                                            </div>
                                            <div class="user-dropdown-divider"></div>
                                        </div>
                                        <a href="{{ route('profile.show') }}" class="user-dropdown-item">
                                            <i class="fas fa-user-edit"></i>
                                            <span>Edytuj profil</span>
                                        </a>
                                        <div class="user-dropdown-divider"></div>
                                        <a href="{{ route('settings.index') }}" class="user-dropdown-item">
                                            <i class="fas fa-cog"></i>
                                            <span>Ustawienia</span>
                                        </a>
                                        <div class="user-dropdown-divider"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="user-dropdown-item w-full text-left">
                                                <i class="fas fa-sign-out-alt text-red-500"></i>
                                                <span class="text-red-600">Wyloguj się</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>

                @guest
                <div id="mobile-menu" class="mobile-menu md:hidden bg-white border-t border-gray-200">
                    <div class="px-4 py-2 space-y-2">
                        <a href="/" class="block px-3 py-2 text-sm text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded">
                            <i class="fas fa-home mr-2"></i>Strona gÅ‚Ã³wna
                        </a>
                        <a href="/#contact" class="block px-3 py-2 text-sm text-gray-700 hover:text-indigo-600 hover:bg-gray-50 rounded">
                            <i class="fas fa-phone mr-2"></i>Kontakt
                        </a>
                    </div>
                </div>
                @endguest
            </div>
        </nav>

        <div class="flex pt-16">
            @auth
            <!-- Desktop Sidebar -->
            <div id="desktop-sidebar" class="sidebar-glass sidebar-desktop fixed left-0 top-16 min-h-screen overflow-y-auto hidden md:block z-30">
                <div class="sidebar-content p-6">
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Dashboard">
                            <i class="fas fa-chart-line mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>

                        @if(Auth::user()->role !== 'user')
                            @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.users.index', ['role' => 'user']) }}" class="nav-item {{ request()->routeIs('admin.users.*') && request()->get('role') === 'user' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Użytkownicy">
                                <i class="fas fa-users mr-3 w-5 shrink-0 text-lg"></i>
                                <span class="nav-text">Użytkownicy</span>
                            </a>
                            @elseif(Auth::user()->role === 'doctor')
                            <a href="{{ route('doctor.patients.index') }}" class="nav-item {{ request()->routeIs('doctor.patients.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Pacjenci">
                                <i class="fas fa-users mr-3 w-5 shrink-0 text-lg"></i>
                                <span class="nav-text">Pacjenci</span>
                            </a>
                            @endif
                        @endif

                        <a href="{{ route('calendar.index') }}" class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Terminarz">
                            <i class="fas fa-calendar-alt mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Terminarz</span>
                        </a>

                        @if(Auth::user()->isDoctor() || Auth::user()->isAdmin())
                        <a href="{{ route('schedules.index') }}" class="nav-item {{ request()->routeIs('schedules.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Harmonogramy">
                            <i class="fas fa-clock mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Harmonogramy</span>
                        </a>

                        <a href="{{ route('reservation.my-list') }}" class="nav-item {{ request()->routeIs('reservation.my-list') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Moje Rezerwacje">
                            <i class="fas fa-calendar-check mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Moje Rezerwacje</span>
                        </a>

                        <a href="{{ route('doctor.reservations.pending') }}" class="nav-item {{ request()->routeIs('doctor.reservations.pending') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Do przejęcia">
                            <i class="fas fa-hand-paper mr-3 w-5 shrink-0 text-lg text-orange-500"></i>
                            <span class="nav-text">Do przejęcia</span>
                            <span class="ml-auto pending-reservations-badge bg-orange-500 text-white text-xs rounded-full px-2 py-1" style="display: none;">0</span>
                        </a>

                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.clinic-hours.index') }}" class="nav-item {{ request()->routeIs('admin.clinic-hours.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Godziny kliniki">
                            <i class="fas fa-business-time mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Godziny kliniki</span>
                        </a>
                        @endif
                        @else
                        <a href="{{ route('reservation.index') }}" class="nav-item {{ request()->routeIs('reservation.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Rezerwacje">
                            <i class="fas fa-calendar-check mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Rezerwacje</span>
                        </a>
                        @endif

                        <a href="{{ route('medical-documents.index') }}" class="nav-item {{ request()->routeIs('medical-documents.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Dokumentacja">
                            <i class="fas fa-file-medical mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Dokumentacja</span>
                        </a>

                        <a href="{{ route('chat.index') }}" class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Wiadomości">
                            <i class="fas fa-comments mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Wiadomości</span>
                            <span class="ml-auto chat-sidebar-badge bg-blue-500 text-white text-xs rounded-full px-2 py-1" style="display: none;">0</span>
                        </a>

                        <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Płatności">
                            <i class="fas fa-credit-card mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Płatności</span>
                        </a>

                        @if(Auth::user()->role !== 'user')
                            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                            <div class="relative">
                                <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Raporty">
                                    <i class="fas fa-chart-bar mr-3 w-5 shrink-0 text-lg"></i>
                                    <span class="nav-text">Raporty</span>
                                    <i class="fas fa-chevron-down ml-auto text-xs transition-transform duration-200" id="reports-chevron"></i>
                                </a>

                                <div class="ml-8 mt-2 space-y-1 hidden" id="reports-submenu">
                                    <a href="{{ route('reports.patients') }}" class="nav-item {{ request()->routeIs('reports.patients') ? 'active' : '' }} flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 font-medium">
                                        <i class="fas fa-users mr-2 w-4 text-sm"></i>
                                        <span class="nav-text">Pacjenci</span>
                                    </a>
                                    <a href="{{ route('reports.documents') }}" class="nav-item {{ request()->routeIs('reports.documents') ? 'active' : '' }} flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 font-medium">
                                        <i class="fas fa-file-medical mr-2 w-4 text-sm"></i>
                                        <span class="nav-text">Dokumenty</span>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                    <a href="{{ route('reports.statistics') }}" class="nav-item {{ request()->routeIs('reports.statistics') ? 'active' : '' }} flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 font-medium">
                                        <i class="fas fa-chart-pie mr-2 w-4 text-sm"></i>
                                        <span class="nav-text">Statystyki</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endif

                        <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-gray-700 font-medium" data-tooltip="Ustawienia">
                            <i class="fas fa-cog mr-3 w-5 shrink-0 text-lg"></i>
                            <span class="nav-text">Ustawienia</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Mobile Sidebar -->
            <div id="mobile-sidebar" class="sidebar-glass sidebar-collapsed w-full sm:w-96 fixed left-0 top-16 bottom-0 overflow-y-auto md:hidden z-50">
                <div class="h-full flex flex-col">
                    <!-- Mobile Tiles Grid (All Navigation Items as Tiles) -->
                    <div class="grid grid-cols-3 gap-4 p-4 flex-1 min-h-0" style="grid-auto-rows: 1fr;">
                        <!-- Dashboard Tile -->
                        <a href="{{ route('dashboard') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-indigo-50 dark:bg-indigo-900 hover:bg-indigo-100 dark:hover:bg-indigo-800 transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? 'ring-2 ring-indigo-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-chart-line text-3xl text-indigo-600 dark:text-indigo-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Dashboard</span>
                        </a>

                        @if(Auth::user()->role !== 'user')
                            @if(Auth::user()->role === 'admin')
                            <!-- Users Tile (Admin) -->
                            <a href="{{ route('admin.users.index', ['role' => 'user']) }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.users.*') && request()->get('role') === 'user' ? 'ring-2 ring-blue-600' : '' }}" onclick="closeSidebar()">
                                <i class="fas fa-users text-3xl text-blue-600 dark:text-blue-400 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Użytkownicy</span>
                            </a>
                            @elseif(Auth::user()->role === 'doctor')
                            <!-- Patients Tile (Doctor) -->
                            <a href="{{ route('doctor.patients.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors {{ request()->routeIs('doctor.patients.*') ? 'ring-2 ring-blue-600' : '' }}" onclick="closeSidebar()">
                                <i class="fas fa-users text-3xl text-blue-600 dark:text-blue-400 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Pacjenci</span>
                            </a>
                            @endif
                        @endif

                        <!-- Calendar Tile -->
                        <a href="{{ route('calendar.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-cyan-50 dark:bg-cyan-900 hover:bg-cyan-100 dark:hover:bg-cyan-800 transition-colors {{ request()->routeIs('calendar.*') ? 'ring-2 ring-cyan-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-calendar-alt text-3xl text-cyan-600 dark:text-cyan-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Terminarz</span>
                        </a>

                        @if(Auth::user()->isDoctor() || Auth::user()->isAdmin())
                        <!-- Schedules Tile -->
                        <a href="{{ route('schedules.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-rose-50 dark:bg-rose-900 hover:bg-rose-100 dark:hover:bg-rose-800 transition-colors {{ request()->routeIs('schedules.*') ? 'ring-2 ring-rose-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-clock text-3xl text-rose-600 dark:text-rose-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Harmonogramy</span>
                        </a>

                        <!-- My Reservations Tile -->
                        <a href="{{ route('reservation.my-list') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-teal-50 dark:bg-teal-900 hover:bg-teal-100 dark:hover:bg-teal-800 transition-colors {{ request()->routeIs('reservation.my-list') ? 'ring-2 ring-teal-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-calendar-check text-3xl text-teal-600 dark:text-teal-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Moje Rezerwacje</span>
                        </a>

                        <!-- Pending Reservations Tile (Do przejęcia) -->
                        <a href="{{ route('doctor.reservations.pending') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-orange-50 dark:bg-orange-900 hover:bg-orange-100 dark:hover:bg-orange-800 transition-colors {{ request()->routeIs('doctor.reservations.pending') ? 'ring-2 ring-orange-600' : '' }} relative" onclick="closeSidebar()">
                            <i class="fas fa-hand-paper text-3xl text-orange-600 dark:text-orange-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Do przejęcia</span>
                            <span class="pending-reservations-badge-mobile absolute top-2 right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5" style="display: none;">0</span>
                        </a>

                        @if(Auth::user()->isAdmin())
                        <!-- Clinic Hours Tile (Admin only) -->
                        <a href="{{ route('admin.clinic-hours.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-violet-50 dark:bg-violet-900 hover:bg-violet-100 dark:hover:bg-violet-800 transition-colors {{ request()->routeIs('admin.clinic-hours.*') ? 'ring-2 ring-violet-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-business-time text-3xl text-violet-600 dark:text-violet-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Godziny kliniki</span>
                        </a>
                        @endif
                        @else
                        <!-- Reservations Tile (for users) -->
                        <a href="{{ route('reservation.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-teal-50 dark:bg-teal-900 hover:bg-teal-100 dark:hover:bg-teal-800 transition-colors {{ request()->routeIs('reservation.*') ? 'ring-2 ring-teal-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-calendar-check text-3xl text-teal-600 dark:text-teal-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Rezerwacje</span>
                        </a>
                        @endif

                        <!-- Documents Tile -->
                        <a href="{{ route('medical-documents.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-purple-50 dark:bg-purple-900 hover:bg-purple-100 dark:hover:bg-purple-800 transition-colors {{ request()->routeIs('medical-documents.*') ? 'ring-2 ring-purple-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-file-medical text-3xl text-purple-600 dark:text-purple-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Dokumenty</span>
                        </a>

                        <!-- Chat Tile -->
                        <a href="{{ route('chat.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-pink-50 dark:bg-pink-900 hover:bg-pink-100 dark:hover:bg-pink-800 transition-colors {{ request()->routeIs('chat.*') ? 'ring-2 ring-pink-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-comments text-3xl text-pink-600 dark:text-pink-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Wiadomości</span>
                        </a>

                        <!-- Payments Tile -->
                        <a href="{{ route('payments.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-green-50 dark:bg-green-900 hover:bg-green-100 dark:hover:bg-green-800 transition-colors {{ request()->routeIs('payments.*') ? 'ring-2 ring-green-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-credit-card text-3xl text-green-600 dark:text-green-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Płatności</span>
                        </a>

                        @if(Auth::user()->role !== 'user')
                            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                            <!-- Reports Tile -->
                            <a href="{{ route('reports.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-orange-50 dark:bg-orange-900 hover:bg-orange-100 dark:hover:bg-orange-800 transition-colors {{ request()->routeIs('reports.*') ? 'ring-2 ring-orange-600' : '' }}" onclick="closeSidebar()">
                                <i class="fas fa-chart-bar text-3xl text-orange-600 dark:text-orange-400 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Raporty</span>
                            </a>
                            @endif
                        @endif

                        <!-- Notifications Tile -->
                        <a href="{{ route('notifications.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-amber-50 dark:bg-amber-900 hover:bg-amber-100 dark:hover:bg-amber-800 transition-colors {{ request()->routeIs('notifications.*') ? 'ring-2 ring-amber-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-bell text-3xl text-amber-600 dark:text-amber-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Powiadomienia</span>
                        </a>

                        <!-- Settings Tile -->
                        <a href="{{ route('settings.index') }}" class="h-full w-full flex flex-col items-center justify-center p-6 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors {{ request()->routeIs('settings.*') ? 'ring-2 ring-gray-600' : '' }}" onclick="closeSidebar()">
                            <i class="fas fa-cog text-3xl text-gray-600 dark:text-gray-400 mb-2"></i>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Ustawienia</span>
                        </a>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Main Content -->
            <main id="main-content" class="main-content @auth expanded @endauth w-full min-h-screen">
                <div class="@auth p-2 sm:p-4 md:p-6 lg:p-8 @endauth">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="max-w-7xl mx-auto mb-4">
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
                                <button type="button" class="ml-auto text-green-500 hover:text-green-600" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="max-w-7xl mx-auto mb-4">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="ml-3 text-sm font-medium text-red-800">{{ session('error') }}</p>
                                <button type="button" class="ml-auto text-red-500 hover:text-red-600" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Apply saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            @auth
            const userPreferences = @json(Auth::user()->preferences_with_defaults);
            const savedTheme = userPreferences.theme || 'light';

            // Najpierw usuń wszystkie klasy motywu
            document.body.classList.remove('dark-mode', 'dark');
            document.documentElement.classList.remove('dark-mode', 'dark');

            // Następnie zastosuj wybrany motyw
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                document.documentElement.classList.add('dark-mode', 'dark');
                console.log('Layout: Zastosowano ciemny motyw');
            } else {
                console.log('Layout: Zastosowano jasny motyw');
            }
            @endauth
        });

        // User menu dropdown
        const userMenuBtn = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('dropdown-menu');
        if (userMenuBtn && userMenu) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const chevron = userMenuBtn.querySelector('.fa-chevron-down');
                if (userMenu.classList.contains('hidden')) {
                    userMenu.classList.remove('hidden');
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    userMenu.classList.add('hidden');
                    chevron.style.transform = 'rotate(0deg)';
                }
            });

            document.addEventListener('click', function(event) {
                if (!userMenuBtn.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                    const chevron = userMenuBtn.querySelector('.fa-chevron-down');
                    chevron.style.transform = 'rotate(0deg)';
                }
            });
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('open');
            }
        }

        // Reports submenu
        document.addEventListener('DOMContentLoaded', function() {
            const reportsMainLink = document.querySelector('[data-tooltip="Raporty"]');
            const reportsSubmenu = document.getElementById('reports-submenu');
            const reportsChevron = document.getElementById('reports-chevron');
            const desktopSidebar = document.getElementById('desktop-sidebar');

            if (reportsMainLink && reportsMainLink.classList.contains('active') && reportsSubmenu && desktopSidebar) {
                if (!desktopSidebar.classList.contains('collapsed')) {
                    reportsSubmenu.classList.remove('hidden');
                    if (reportsChevron) {
                        reportsChevron.style.transform = 'rotate(180deg)';
                    }
                }
            }

            if (reportsMainLink && reportsSubmenu && desktopSidebar) {
                reportsMainLink.addEventListener('click', function(e) {
                    const isCollapsed = desktopSidebar.classList.contains('collapsed');
                    if (isCollapsed) {
                        window.location.href = reportsMainLink.getAttribute('href');
                        return;
                    }
                    if (e.target === reportsMainLink || reportsMainLink.contains(e.target)) {
                        e.preventDefault();
                        reportsSubmenu.classList.toggle('hidden');
                        if (reportsChevron) {
                            const isOpen = !reportsSubmenu.classList.contains('hidden');
                            reportsChevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
                        }
                    }
                });
            }
        });

        // Mobile reports submenu
        function toggleMobileReportsMenu() {
            const submenu = document.getElementById('mobile-reports-submenu');
            const chevron = document.getElementById('mobile-reports-chevron');
            if (submenu && chevron) {
                submenu.classList.toggle('hidden');
                const isOpen = !submenu.classList.contains('hidden');
                chevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }

        // Sidebar functions
        function toggleSidebar() {
            const isDesktop = window.innerWidth >= 768;
            if (isDesktop) {
                toggleDesktopSidebar();
            } else {
                toggleMobileSidebar();
            }
        }

        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('desktop-sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('sidebar-toggle');

            if (sidebar && mainContent && toggleBtn) {
                const isCollapsed = sidebar.classList.contains('collapsed');
                if (isCollapsed) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                    mainContent.classList.add('expanded');
                    toggleBtn.classList.remove('rotated');
                } else {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.remove('expanded');
                    mainContent.classList.add('collapsed');
                    toggleBtn.classList.add('rotated');
                }
            }
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('sidebar-toggle');

            if (sidebar && overlay && toggleBtn) {
                const isOpen = sidebar.classList.contains('open');
                if (isOpen) {
                    closeMobileSidebar();
                } else {
                    sidebar.classList.add('open');
                    overlay.classList.remove('hidden');
                    toggleBtn.classList.add('rotated');
                    document.body.style.overflow = 'hidden';
                }
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('sidebar-toggle');

            if (sidebar && overlay && toggleBtn) {
                sidebar.classList.remove('open');
                overlay.classList.add('hidden');
                toggleBtn.classList.remove('rotated');
                document.body.style.overflow = '';
            }
        }

        function closeSidebar() {
            if (window.innerWidth < 768) {
                closeMobileSidebar();
            }
        }

        // Update unread count
        function updateUnreadCount() {
            fetch('/chat/api/unread-count')
                .then(response => response.json())
                .then(data => {
                    const chatBadge = document.querySelector('.chat-notification-badge span');
                    const chatBadgeContainer = document.querySelector('.chat-notification-badge');
                    if (chatBadge && chatBadgeContainer) {
                        if (data.unread_count > 0) {
                            chatBadge.textContent = data.unread_count;
                            chatBadgeContainer.style.display = 'flex';
                        } else {
                            chatBadgeContainer.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Notification system
        function updateNotificationCount() {
            fetch('/notifications/api/unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    const count = document.getElementById('notification-count');
                    if (badge && count) {
                        if (data.count > 0) {
                            count.textContent = data.count > 99 ? '99+' : data.count;
                            badge.style.display = 'flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error fetching notification count:', error));
        }

        // Pending reservations badge (for doctors and admins)
        function updatePendingReservationsBadge() {
            @if(Auth::check() && (Auth::user()->isDoctor() || Auth::user()->isAdmin()))
            fetch('/reservation/pending')
                .then(response => response.json())
                .then(data => {
                    const badges = document.querySelectorAll('.pending-reservations-badge, .pending-reservations-badge-mobile');
                    const count = data.pending ? data.pending.length : 0;
                    badges.forEach(badge => {
                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    });
                })
                .catch(error => console.error('Error fetching pending reservations:', error));
            @endif
        }

        function loadNotifications() {
            fetch('/notifications/api/get?limit=10')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('notifications-list');
                    if (!list) return;

                    // Check if dark mode is active based on user preference
                    const isDarkMode = document.body.classList.contains('dark-mode');

                    if (data.notifications.length === 0) {
                        const emptyTextColor = isDarkMode ? 'text-gray-400' : 'text-gray-500';
                        list.innerHTML = `<div class="p-4 text-center ${emptyTextColor} text-sm">Brak powiadomień</div>`;
                        return;
                    }

                    list.innerHTML = data.notifications.map(notif => {
                        // Background colors based on user's theme preference
                        let bgClass, iconBgClass, iconTextClass, titleTextClass, messageTextClass, timeTextClass, dotBgClass;

                        if (isDarkMode) {
                            bgClass = notif.is_read
                                ? 'bg-gray-800 hover:bg-gray-700'
                                : 'bg-blue-900/30 hover:bg-blue-900/50';
                            titleTextClass = 'text-white';
                            messageTextClass = 'text-gray-400';
                            timeTextClass = 'text-gray-500';
                            dotBgClass = 'bg-blue-400';
                        } else {
                            bgClass = notif.is_read
                                ? 'bg-white hover:bg-gray-50'
                                : 'bg-blue-50 hover:bg-blue-100';
                            titleTextClass = 'text-gray-900';
                            messageTextClass = 'text-gray-600';
                            timeTextClass = 'text-gray-500';
                            dotBgClass = 'bg-blue-600';
                        }

                        const textWeight = notif.is_read ? 'font-normal' : 'font-semibold';
                        const iconColor = getNotificationColor(notif.type);

                        // Icon colors based on theme
                        if (isDarkMode) {
                            iconBgClass = `bg-${iconColor}-900`;
                            iconTextClass = `text-${iconColor}-400`;
                        } else {
                            iconBgClass = `bg-${iconColor}-100`;
                            iconTextClass = `text-${iconColor}-600`;
                        }

                        return `
                            <div class="${bgClass} p-3 cursor-pointer transition-colors notification-item"
                                 data-id="${notif.id}"
                                 data-url="${notif.action_url}"
                                 data-read="${notif.is_read}">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full ${iconBgClass} flex items-center justify-center">
                                            <i class="${notif.icon} ${iconTextClass}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm ${textWeight} ${titleTextClass}">${notif.title}</p>
                                        <p class="text-xs ${messageTextClass} mt-1 line-clamp-2">${notif.message}</p>
                                        <p class="text-xs ${timeTextClass} mt-1">${notif.created_at}</p>
                                    </div>
                                    ${!notif.is_read ? `<div class="w-2 h-2 ${dotBgClass} rounded-full"></div>` : ''}
                                </div>
                            </div>
                        `;
                    }).join('');

                    // Dodaj event listeners do powiadomień
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const id = this.dataset.id;
                            const url = this.dataset.url;
                            const isRead = this.dataset.read === 'true';

                            if (!isRead) {
                                markNotificationAsRead(id);
                            }

                            // Przekieruj na odpowiedni URL
                            window.location.href = url;
                        });
                    });
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    const list = document.getElementById('notifications-list');
                    if (list) {
                        list.innerHTML = '<div class="p-4 text-center text-red-500 dark:text-red-400 text-sm">Błąd ładowania powiadomień</div>';
                    }
                });
        }

        function markNotificationAsRead(id) {
            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationCount();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllNotificationsAsRead() {
            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationCount();
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        }

        function getNotificationColor(type) {
            const colors = {
                'appointment_created': 'blue',
                'appointment_updated': 'yellow',
                'appointment_cancelled': 'red',
                'appointment_reminder': 'orange',
                'document_created': 'green',
                'document_updated': 'yellow',
                'message_received': 'purple',
                'user_registered': 'indigo',
                'system': 'gray'
            };
            return colors[type] || 'gray';
        }

        @auth
        document.addEventListener('DOMContentLoaded', function() {
            // Notification dropdown toggle
            const notifButton = document.getElementById('notification-button');
            const notifDropdown = document.getElementById('notification-dropdown');
            const markAllReadBtn = document.getElementById('mark-all-read');

            if (notifButton && notifDropdown) {
                notifButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isHidden = notifDropdown.classList.contains('hidden');

                    // Zamknij user menu jeśli otwarte
                    const userMenu = document.getElementById('dropdown-menu');
                    if (userMenu) userMenu.classList.add('hidden');

                    if (isHidden) {
                        notifDropdown.classList.remove('hidden');
                        loadNotifications();
                    } else {
                        notifDropdown.classList.add('hidden');
                    }
                });

                // Zamknij dropdown po kliknięciu poza nim
                document.addEventListener('click', function(event) {
                    if (!notifButton.contains(event.target) && !notifDropdown.contains(event.target)) {
                        notifDropdown.classList.add('hidden');
                    }
                });
            }

            // Mark all as read button
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    markAllNotificationsAsRead();
                });
            }

            // Update counts periodically
            updateNotificationCount();
            updateUnreadCount();
            updatePendingReservationsBadge();

            setInterval(() => {
                updateNotificationCount();
                updateUnreadCount();
                updatePendingReservationsBadge();
            }, 30000); // Co 30 sekund
        });
        @endauth

        // CSRF token
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        if (window.jQuery) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* SweetAlert2 Dark Mode */
        body.dark-mode .swal2-popup {
            background-color: #1f2937 !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .swal2-title {
            color: #e2e8f0 !important;
        }
        body.dark-mode .swal2-html-container {
            color: #cbd5e0 !important;
        }
        body.dark-mode .swal2-close {
            color: #9ca3af !important;
        }
        body.dark-mode .swal2-close:hover {
            color: #e2e8f0 !important;
        }
    </style>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
