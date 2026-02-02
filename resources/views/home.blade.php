<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<script>

    (function() {
        const theme = localStorage.getItem('theme') || 'light';
        const html = document.documentElement;
        const isDark = theme === 'dark';

        if (isDark) {
            html.classList.add('dark-mode');

            const style = document.createElement('style');
            style.innerHTML = 'body { background-color: #0f172a !important; }';
            document.head.appendChild(style);
        }
    })();
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Rehamed') }} - Nowoczesna Klinika Rehabilitacji</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }


        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .pulse-dot {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }


        .service-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }


        .navbar-glass {
            backdrop-filter: blur(20px);
            background: rgba(30, 41, 59, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .navbar-scrolled {
            background: rgba(30, 41, 59, 0.98);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }


        .nav-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::before,
        .nav-link.active::before {
            width: 100%;
        }
        .nav-link:hover {
            color: #667eea;
            transform: translateY(-2px);
        }


        .cta-button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .cta-button:hover::before {
            left: 100%;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }


        .mobile-menu {
            background: rgba(30, 41, 59, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 0 0 20px 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .mobile-menu-item {
            transition: all 0.3s ease;
            border-radius: 12px;
        }


        .mobile-menu-item:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateX(10px);
        }


        .logo-icon {
            animation: heartbeat 2s infinite;
        }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }


        .hamburger {
            width: 30px;
            height: 20px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .hamburger span {
            display: block;
            position: absolute;
            height: 3px;
            width: 100%;
            background: #cbd5e0;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .hamburger span:first-child { top: 0; }
        .hamburger span:nth-child(2) { top: 50%; transform: translateY(-50%); }
        .hamburger span:last-child { bottom: 0; }
        .hamburger.active span:first-child { transform: rotate(45deg); top: 50%; margin-top: -1.5px; }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:last-child { transform: rotate(-45deg); bottom: 50%; margin-bottom: -1.5px; }


        .social-icon:hover {
            transform: translateY(-4px);
            color: #4f46e5;
        }


        .dark-mode {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .dark-mode .navbar-glass {
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid #334155;
        }

        .dark-mode .navbar-scrolled {
            background: rgba(15, 23, 42, 0.98);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .dark-mode .mobile-menu {
            background: rgba(30, 41, 59, 0.98);
            border: 1px solid #334155;
        }

        .dark-mode .mobile-menu-item {
            color: #cbd5e0;
        }

        .dark-mode .mobile-menu-item:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }

        .dark-mode .hamburger span {
            background: #a5b4fc;
        }

        .dark-mode .bg-white {
            background-color: #1e293b !important;
        }

        .dark-mode .text-gray-900 {
            color: #f1f5f9 !important;
        }

        .dark-mode .text-gray-800 {
            color: #e2e8f0 !important;
        }

        .dark-mode .text-gray-600 {
            color: #cbd5e0 !important;
        }

        .dark-mode .bg-gray-50 {
            background-color: #0f172a !important;
        }

        .dark-mode .bg-gradient-to-br.from-purple-50 {
            background: linear-gradient(to bottom right, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1)) !important;
        }

        .dark-mode .card-hover {
            border: 1px solid #334155;
        }

        .dark-mode .card-hover:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .dark-mode .border-gray-100 {
            border-color: #334155 !important;
        }

        .dark-mode footer {
            background-color: #020617;
            border-color: #1e293b;
        }


        .theme-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .theme-toggle:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.5);
        }

        .theme-toggle i {
            color: white;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .theme-toggle:hover i {
            transform: rotate(20deg);
        }


        .dark-mode .theme-toggle .fa-sun {
            display: none;
        }

        .theme-toggle .fa-moon {
            display: none;
        }

        .dark-mode .theme-toggle .fa-moon {
            display: block;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <nav id="navbar" class="navbar-glass fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">
                <div class="flex items-center">
                    <div class="shrink-0 group">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white flex items-center">
                            <i class="fas fa-heartbeat mr-2 text-red-500 logo-icon"></i>
                            <span class="bg-gradient-to-r from-purple-400 to-indigo-300 bg-clip-text text-transparent">
                                Rehamed
                            </span>
                        </h1>
                    </div>
                </div>

                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="nav-link text-gray-300 hover:text-white px-3 py-2 text-sm font-semibold"><i class="fas fa-home mr-2"></i>Strona główna</a>
                    <a href="#services" class="nav-link text-gray-300 hover:text-white px-3 py-2 text-sm font-semibold"><i class="fas fa-stethoscope mr-2"></i>Usługi</a>
                    <a href="#about" class="nav-link text-gray-300 hover:text-white px-3 py-2 text-sm font-semibold"><i class="fas fa-info-circle mr-2"></i>O nas</a>
                    <a href="#contact" class="nav-link text-gray-300 hover:text-white px-3 py-2 text-sm font-semibold"><i class="fas fa-envelope mr-2"></i>Kontakt</a>
                    <a href="{{ route('login') }}" class="cta-button text-white px-6 py-3 rounded-full text-sm font-semibold ml-4 bg-gradient-to-r from-purple-700 to-indigo-800 hover:from-purple-800 hover:to-indigo-900"><i class="fas fa-calendar-check mr-2"></i>Umów wizytę</a>
                </div>

                <div class="lg:hidden">
                    <button id="mobile-menu-button" class="hamburger p-2">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="lg:hidden hidden mobile-menu">
            <div class="px-4 pt-4 pb-6 space-y-2">
                <a href="#home" class="mobile-menu-item flex items-center px-4 py-3 text-gray-200 font-medium"><i class="fas fa-home mr-3 w-5"></i>Strona główna</a>
                <a href="#services" class="mobile-menu-item flex items-center px-4 py-3 text-gray-200 font-medium"><i class="fas fa-stethoscope mr-3 w-5"></i>Usługi</a>
                <a href="#about" class="mobile-menu-item flex items-center px-4 py-3 text-gray-200 font-medium"><i class="fas fa-info-circle mr-3 w-5"></i>O nas</a>
                <a href="#contact" class="mobile-menu-item flex items-center px-4 py-3 text-gray-200 font-medium"><i class="fas fa-envelope mr-3 w-5"></i>Kontakt</a>
                <div class="px-4 pt-2">
                    <a href="{{ route('login') }}" class="mobile-menu-item block text-center text-white px-6 py-3 rounded-full font-semibold bg-gradient-to-r from-purple-700 to-indigo-800 hover:from-purple-800 hover:to-indigo-900"><i class="fas fa-calendar-check mr-2"></i>Umów wizytę</a>
                </div>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-gradient pt-16 lg:pt-20 min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="text-white order-2 xl:order-1">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                        Twoje zdrowie w
                        <span class="text-yellow-300">najlepszych rękach</span>
                    </h1>
                    <p class="text-lg lg:text-xl mb-8 text-gray-100 leading-relaxed">
                        Nowoczesna klinika rehabilitacji oferująca kompleksową opiekę medyczną
                        z wykorzystaniem najnowszych technologii i metod leczenia.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8">
                        <a href="{{ route('login') }}" class="bg-gradient-to-r from-purple-700 to-indigo-800 text-white px-8 py-4 rounded-xl font-semibold hover:from-purple-800 hover:to-indigo-900 transition duration-300 shadow-lg inline-flex items-center justify-center">
                            <i class="fas fa-calendar-check mr-2"></i>Umów wizytę
                        </a>
                        <a href="#services" class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-purple-700 transition duration-300 inline-flex items-center justify-center">
                            <i class="fas fa-play-circle mr-2"></i>Zobacz więcej
                        </a>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-6">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full pulse-dot mr-2"></div>
                            <span class="text-sm">Dostępni 24/7</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-green-400"></i>
                            <span class="text-sm">Licencjonowani specjaliści</span>
                        </div>
                    </div>
                </div>
                <div class="relative order-1 xl:order-2">
                    <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-6 lg:p-8 shadow-2xl">
                        <div class="text-center mb-6">
                            <i class="fas fa-user-md text-5xl lg:text-6xl text-yellow-300 mb-4"></i>
                            <h3 class="text-xl lg:text-2xl font-bold mb-2 text-white">Twój specjalista czeka</h3>
                            <p class="text-gray-100">Dostępny już dziś</p>
                        </div>
                        <div class="bg-white rounded-2xl p-4 lg:p-6 text-gray-800 mb-4">
                            <div class="flex items-center justify-between mb-3 pb-3 border-b">
                                <span class="text-sm text-gray-600">Najbliższy wolny termin</span>
                                <span class="text-sm font-bold text-purple-700">Dziś, 15:30</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-slate-700 p-3 rounded-xl text-center border border-slate-600">
                                    <i class="fas fa-clock text-purple-400 mb-2 text-xl"></i>
                                    <p class="text-xs text-slate-300 mb-1">Czas wizyty</p>
                                    <p class="font-bold text-white text-lg">45 min</p>
                                </div>
                                <div class="bg-slate-700 p-3 rounded-xl text-center border border-slate-600">
                                    <i class="fas fa-star text-purple-400 mb-2 text-xl"></i>
                                    <p class="text-xs text-slate-300 mb-1">Ocena</p>
                                    <p class="font-bold text-white text-lg">4.9/5</p>
                                </div>
                            </div>
                            <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-purple-600 to-indigo-700 text-white py-3 rounded-xl font-semibold text-center hover:shadow-lg transition duration-300">
                                Zarezerwuj termin
                            </a>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-white/20 backdrop-blur rounded-lg p-2 text-center">
                                <p class="text-2xl font-bold text-white">500+</p>
                                <p class="text-xs text-gray-100">Pacjentów</p>
                            </div>
                            <div class="bg-white/20 backdrop-blur rounded-lg p-2 text-center">
                                <p class="text-2xl font-bold text-white">15+</p>
                                <p class="text-xs text-gray-100">Specjalistów</p>
                            </div>
                            <div class="bg-white/20 backdrop-blur rounded-lg p-2 text-center">
                                <p class="text-2xl font-bold text-white">10</p>
                                <p class="text-xs text-gray-100">Lat doświadczenia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Nasze usługi</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Oferujemy szeroki zakres usług rehabilitacyjnych dostosowanych do Twoich potrzeb</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="card-hover bg-white rounded-2xl p-6 lg:p-8 shadow-lg border border-gray-100">
                    <div class="bg-gradient-to-br from-purple-100 to-indigo-100 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-dumbbell service-icon text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fizjoterapia</h3>
                    <p class="text-gray-600 mb-4">Kompleksowa rehabilitacja ruchowa z wykorzystaniem najnowszych metod terapeutycznych.</p>
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700 inline-flex items-center">
                        Dowiedz się więcej <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="card-hover bg-white rounded-2xl p-6 lg:p-8 shadow-lg border border-gray-100">
                    <div class="bg-gradient-to-br from-purple-100 to-indigo-100 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-spa service-icon text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Masaż</h3>
                    <p class="text-gray-600 mb-4">Profesjonalne masaże lecznicze i relaksacyjne wykonywane przez doświadczonych terapeutów.</p>
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700 inline-flex items-center">
                        Dowiedz się więcej <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="card-hover bg-white rounded-2xl p-6 lg:p-8 shadow-lg border border-gray-100">
                    <div class="bg-gradient-to-br from-purple-100 to-indigo-100 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-heartbeat service-icon text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Rehabilitacja kardiologiczna</h3>
                    <p class="text-gray-600 mb-4">Specjalistyczna opieka nad pacjentami z chorobami układu krążenia.</p>
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700 inline-flex items-center">
                        Dowiedz się więcej <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="card-hover bg-white rounded-2xl p-6 lg:p-8 shadow-lg border border-gray-100">
                    <div class="bg-gradient-to-br from-purple-100 to-indigo-100 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-brain service-icon text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Neurorehabilitacja</h3>
                    <p class="text-gray-600 mb-4">Terapia dla pacjentów z problemami neurologicznymi i poudarowymi.</p>
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700 inline-flex items-center">
                        Dowiedz się więcej <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="card-hover bg-white rounded-2xl p-6 lg:p-8 shadow-lg border border-gray-100">
                    <div class="bg-gradient-to-br from-purple-100 to-indigo-100 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-hand-holding-medical service-icon text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Rehabilitacja ortopedyczna</h3>
                    <p class="text-gray-600 mb-4">Leczenie schorzeń i urazów narządu ruchu przy użyciu nowoczesnego sprzętu.</p>
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700 inline-flex items-center">
                        Dowiedz się więcej <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="card-hover bg-white rounded-2xl p-6 lg:p-8 shadow-lg border border-gray-100">
                    <div class="bg-gradient-to-br from-purple-100 to-indigo-100 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-child service-icon text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fizjoterapia dziecięca</h3>
                    <p class="text-gray-600 mb-4">Specjalistyczna opieka nad najmłodszymi pacjentami z zaburzeniami rozwoju.</p>
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700 inline-flex items-center">
                        Dowiedz się więcej <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-16 lg:py-24 bg-gradient-to-br from-purple-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">Dlaczego warto wybrać Rehamed?</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Jesteśmy nowoczesną kliniką rehabilitacji, która łączy tradycyjne metody leczenia z najnowszymi osiągnięciami medycyny.
                        Nasz zespół składa się z doświadczonych specjalistów, którzy dbają o każdy aspekt Twojego zdrowia.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Wykwalifikowana kadra</h4>
                                <p class="text-gray-600">Nasi specjaliści posiadają wieloletnie doświadczenie i ciągle podnoszą swoje kwalifikacje.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Nowoczesny sprzęt</h4>
                                <p class="text-gray-600">Dysponujemy najnowocześniejszym sprzętem medycznym i rehabilitacyjnym.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Indywidualne podejście</h4>
                                <p class="text-gray-600">Każdy pacjent otrzymuje spersonalizowany plan terapii dopasowany do jego potrzeb.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-white rounded-3xl p-8 shadow-2xl">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center">
                                <div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-6 mb-3">
                                    <i class="fas fa-users text-4xl text-white"></i>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">500+</p>
                                <p class="text-gray-600">Zadowolonych pacjentów</p>
                            </div>
                            <div class="text-center">
                                <div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-6 mb-3">
                                    <i class="fas fa-award text-4xl text-white"></i>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">10+</p>
                                <p class="text-gray-600">Lat doświadczenia</p>
                            </div>
                            <div class="text-center">
                                <div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-6 mb-3">
                                    <i class="fas fa-user-md text-4xl text-white"></i>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">15+</p>
                                <p class="text-gray-600">Specjalistów</p>
                            </div>
                            <div class="text-center">
                                <div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-6 mb-3">
                                    <i class="fas fa-star text-4xl text-white"></i>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">4.9/5</p>
                                <p class="text-gray-600">Średnia ocen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-16 lg:py-24 bg-gradient-to-br from-purple-600 to-indigo-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 lg:p-12 shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="text-white">
                        <h2 class="text-3xl lg:text-4xl font-bold mb-6">Skontaktuj się z nami</h2>
                        <p class="text-lg text-gray-100 mb-8">
                            Masz pytania? Chcesz umówić wizytę? Skontaktuj się z nami już dziś!
                        </p>
                        <div class="space-y-4 text-gray-100">
                            <p class="flex items-start"><i class="fas fa-map-marker-alt mr-4 text-yellow-300 w-5 text-center mt-1"></i><span>ul. Zdrowotna 123<br>42-310 Żarki, Polska</span></p>
                            <p class="flex items-center"><i class="fas fa-phone-alt mr-4 text-yellow-300 w-5 text-center"></i><span>+48 123 456 789</span></p>
                            <p class="flex items-center"><i class="fas fa-envelope mr-4 text-yellow-300 w-5 text-center"></i><span>kontakt@rehamed.pl</span></p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold mb-4 text-white">Godziny otwarcia</h3>
                        <div class="space-y-2 text-gray-100 font-light">
                            <p class="flex justify-between border-b border-white/20 pb-2"><span>Poniedziałek - Piątek</span><strong class="font-semibold text-yellow-300">8:00 - 20:00</strong></p>
                            <p class="flex justify-between border-b border-white/20 pb-2"><span>Sobota</span><strong class="font-semibold text-yellow-300">9:00 - 15:00</strong></p>
                            <p class="flex justify-between"><span>Niedziela</span><strong class="font-semibold text-red-300">Zamknięte</strong></p>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('login') }}" class="inline-flex items-center bg-gradient-to-r from-purple-700 to-indigo-800 text-white px-8 py-4 rounded-xl font-semibold hover:from-purple-800 hover:to-indigo-900 transition duration-300 shadow-lg">
                                <i class="fas fa-calendar-check mr-2"></i>Umów wizytę online
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 border-t border-gray-800">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500">
            <p>&copy; 2025 Rehamed. Wszelkie prawa zastrzeżone.</p>
        </div>
    </footer>


    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Dark Mode">
        <i class="fas fa-sun"></i>
        <i class="fas fa-moon"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navbar = document.getElementById('navbar');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuItems = document.querySelectorAll('#mobile-menu a');
            const themeToggle = document.getElementById('theme-toggle');


            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });


            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenuButton.classList.toggle('active');
            });

            mobileMenuItems.forEach(item => {
                item.addEventListener('click', () => {
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.classList.remove('active');
                    }
                });
            });


            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }


            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark-mode');

                const isDarkMode = document.documentElement.classList.contains('dark-mode');
                localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');


                themeToggle.style.transform = 'rotate(360deg) scale(1.1)';
                setTimeout(() => {
                    themeToggle.style.transform = '';
                }, 300);
            });
        });
    </script>
</body>
</html>
