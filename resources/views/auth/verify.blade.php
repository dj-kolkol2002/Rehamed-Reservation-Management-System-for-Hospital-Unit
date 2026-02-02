<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Weryfikacja Email - {{ config('app.name', 'Rehamed') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Gradient Hero */
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Dark Mode Base */
        body.dark-mode {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e293b 100%);
            color: #e2e8f0;
        }

        body.dark-mode .bg-white {
            background-color: #1e293b !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;
        }

        body.dark-mode .hero-gradient {
            background: linear-gradient(135deg, #4338ca 0%, #5b21b6 100%);
        }

        body.dark-mode .text-gray-900 {
            color: #f1f5f9 !important;
        }

        body.dark-mode .text-gray-600 {
            color: #94a3b8 !important;
        }

        body.dark-mode .text-gray-500 {
            color: #64748b !important;
        }

        body.dark-mode .bg-yellow-50 {
            background-color: rgba(234, 179, 8, 0.1) !important;
            border-color: #eab308 !important;
        }

        body.dark-mode .text-yellow-800,
        body.dark-mode .text-yellow-700 {
            color: #fde047 !important;
        }

        body.dark-mode .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.1) !important;
            border-color: #22c55e !important;
        }

        body.dark-mode .text-green-700 {
            color: #86efac !important;
        }

        body.dark-mode .bg-red-50 {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: #dc2626 !important;
        }

        body.dark-mode .text-red-700 {
            color: #fca5a5 !important;
        }

        /* Dark Mode Toggle Button */
        .theme-toggle {
            position: fixed;
            top: 2rem;
            right: 2rem;
            width: 48px;
            height: 48px;
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
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.5);
        }

        .theme-toggle i {
            color: white;
            font-size: 1.25rem;
        }

        body.dark-mode .theme-toggle .fa-sun {
            display: none;
        }

        .theme-toggle .fa-moon {
            display: none;
        }

        body.dark-mode .theme-toggle .fa-moon {
            display: block;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }

        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-pink-50 flex items-center justify-center p-4">

    <!-- Dark Mode Toggle -->
    <div class="theme-toggle" onclick="toggleDarkMode()">
        <i class="fas fa-sun"></i>
        <i class="fas fa-moon"></i>
    </div>

    <!-- Decorative Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-30">
        <div class="absolute top-20 left-10 w-64 h-64 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse delay-700"></div>
        <div class="absolute top-40 right-40 w-64 h-64 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse delay-1000"></div>
    </div>

    <!-- Verification Container -->
    <div class="relative w-full max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">

                <!-- Left Side - Hero Section -->
                <div class="lg:w-2/5 hero-gradient p-12 flex flex-col justify-center items-center text-white relative overflow-hidden">
                    <!-- Decorative icons -->
                    <div class="absolute top-10 right-10 opacity-20 float-animation">
                        <i class="fas fa-envelope-open text-6xl"></i>
                    </div>
                    <div class="absolute bottom-10 left-10 opacity-20 float-animation" style="animation-delay: 1s;">
                        <i class="fas fa-shield-alt text-6xl"></i>
                    </div>

                    <div class="relative z-10 text-center animate-fadeInUp">
                        <!-- Email Icon with pulse effect -->
                        <div class="mb-8 relative inline-block">
                            <div class="absolute inset-0 bg-white rounded-full opacity-20 pulse-ring"></div>
                            <div class="relative p-6 bg-white bg-opacity-20 rounded-full backdrop-blur-sm">
                                <i class="fas fa-envelope-circle-check text-6xl"></i>
                            </div>
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                            Zweryfikuj Email
                        </h1>
                        <p class="text-xl mb-8 text-purple-100">
                            Jeden krok do pełnego dostępu
                        </p>

                        <!-- Security Info -->
                        <div class="space-y-4 text-left max-w-sm mx-auto">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-xl"></i>
                                </div>
                                <span class="text-lg">Zabezpiecz swoje konto</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-shield text-xl"></i>
                                </div>
                                <span class="text-lg">Potwierdź swoją tożsamość</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-unlock text-xl"></i>
                                </div>
                                <span class="text-lg">Odblokuj wszystkie funkcje</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Verification Content -->
                <div class="lg:w-3/5 p-12 flex flex-col justify-center">
                    <div class="max-w-xl mx-auto w-full animate-fadeInUp">

                        <!-- Header -->
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                                Potwierdź swój adres email
                            </h2>
                            <p class="text-gray-600">
                                Wysłaliśmy link weryfikacyjny na adres:
                            </p>
                            <p class="text-purple-600 font-semibold mt-2">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        <!-- Alert Messages -->
                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                                    <p class="text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                                    <p class="text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-yellow-500 text-xl mr-3 mt-0.5"></i>
                                    <p class="text-yellow-700">{{ session('info') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Instructions -->
                        <div class="mb-8 p-6 bg-purple-50 rounded-2xl border border-purple-100">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-lightbulb text-2xl text-purple-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        Jak zweryfikować email?
                                    </h3>
                                    <ol class="text-gray-600 space-y-2 list-decimal list-inside">
                                        <li>Sprawdź swoją skrzynkę pocztową</li>
                                        <li>Znajdź wiadomość od Rehamed</li>
                                        <li>Kliknij link weryfikacyjny w emailu</li>
                                        <li>Gotowe! Możesz korzystać z platformy</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Resend Email Form -->
                        <div class="mb-6">
                            <p class="text-gray-600 mb-4 text-center">
                                Nie otrzymałeś wiadomości?
                            </p>
                            <form method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2"
                                >
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Wyślij ponownie link weryfikacyjny</span>
                                </button>
                            </form>
                        </div>

                        <!-- Help Section -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-sm text-gray-500 mb-4">
                                    Sprawdź folder SPAM jeśli nie widzisz wiadomości
                                </p>
                                <div class="space-y-2">
                                    <a href="{{ route('profile.edit') }}" class="text-purple-600 hover:text-purple-700 font-medium text-sm block">
                                        <i class="fas fa-edit mr-2"></i>
                                        Zmień adres email
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-500 hover:text-gray-700 font-medium text-sm">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Wyloguj się
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Check for saved theme preference
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        }

        // Auto-hide success messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.querySelector('.bg-green-50');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.transition = 'opacity 0.5s ease';
                    successAlert.style.opacity = '0';
                    setTimeout(() => successAlert.remove(), 500);
                }, 5000);
            }
        });
    </script>

</body>
</html>
