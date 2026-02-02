<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resetowanie Hasła - {{ config('app.name', 'Rehamed') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }


        body.dark-mode {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e293b 100%);
            color: #e2e8f0;
        }


        body.dark-mode .fixed.inset-0 .bg-purple-300 {
            background-color: #4c1d95 !important;
            opacity: 0.2;
        }

        body.dark-mode .fixed.inset-0 .bg-blue-300 {
            background-color: #1e3a8a !important;
            opacity: 0.2;
        }

        body.dark-mode .fixed.inset-0 .bg-pink-300 {
            background-color: #831843 !important;
            opacity: 0.2;
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

        body.dark-mode .text-gray-800 {
            color: #e2e8f0 !important;
        }

        body.dark-mode .text-gray-700 {
            color: #cbd5e0 !important;
        }

        body.dark-mode .text-gray-600 {
            color: #94a3b8 !important;
        }

        body.dark-mode .text-gray-500 {
            color: #64748b !important;
        }

        body.dark-mode .text-gray-400 {
            color: #475569 !important;
        }


        body.dark-mode .border-gray-200,
        body.dark-mode .border-gray-300 {
            border-color: #334155 !important;
        }


        body.dark-mode input {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        body.dark-mode input:focus {
            background-color: #1e293b !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        }

        body.dark-mode input::placeholder {
            color: #475569 !important;
        }


        body.dark-mode .alert-danger {
            background-color: rgba(239, 68, 68, 0.2) !important;
            border-color: #dc2626 !important;
            color: #fca5a5 !important;
        }

        body.dark-mode .alert-success {
            background-color: rgba(34, 197, 94, 0.2) !important;
            border-color: #16a34a !important;
            color: #86efac !important;
        }


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
            transition: transform 0.3s ease;
        }

        .theme-toggle:hover i {
            transform: rotate(20deg);
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


        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        input, .bg-white {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }


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


        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen bg-linear-to-br from-purple-50 via-blue-50 to-pink-50 flex items-center justify-center p-4">


    <div class="theme-toggle" onclick="toggleDarkMode()">
        <i class="fas fa-sun"></i>
        <i class="fas fa-moon"></i>
    </div>


    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-30">
        <div class="absolute top-20 left-10 w-64 h-64 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse delay-700"></div>
        <div class="absolute top-40 right-40 w-64 h-64 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse delay-1000"></div>
    </div>


    <div class="relative w-full max-w-6xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">


                <div class="lg:w-1/2 hero-gradient p-12 flex flex-col justify-center items-center text-white relative overflow-hidden">

                    <div class="absolute top-10 right-10 opacity-20 float-animation">
                        <i class="fas fa-shield-alt text-6xl"></i>
                    </div>
                    <div class="absolute bottom-10 left-10 opacity-20 float-animation" style="animation-delay: 1s;">
                        <i class="fas fa-key text-6xl"></i>
                    </div>

                    <div class="relative z-10 text-center animate-fadeInUp">

                        <div class="mb-8">
                            <div class="inline-block p-6 bg-white bg-opacity-20 rounded-full backdrop-blur-sm">
                                <i class="fas fa-lock-open text-6xl"></i>
                            </div>
                        </div>


                        <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                            Resetowanie Hasła
                        </h1>
                        <p class="text-xl mb-8 text-purple-100">
                            Utwórz nowe, bezpieczne hasło do swojego konta
                        </p>


                        <div class="space-y-4 text-left max-w-sm mx-auto">
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <span class="text-lg">Bezpieczne szyfrowanie</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-lock text-xl"></i>
                                </div>
                                <span class="text-lg">Ochrona danych osobowych</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check-circle text-xl"></i>
                                </div>
                                <span class="text-lg">Weryfikacja dwuetapowa</span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="lg:w-1/2 p-12 flex flex-col justify-center">
                    <div class="max-w-md mx-auto w-full animate-fadeInUp">


                        <div class="mb-8 text-center lg:text-left">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                                Nowe Hasło
                            </h2>
                            <p class="text-gray-600">
                                Wprowadź swój adres email i nowe hasło
                            </p>
                        </div>


                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold mb-1">Wystąpiły błędy:</p>
                                        <ul class="list-disc list-inside space-y-1 text-sm">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-xl mr-3"></i>
                                    <p>{{ session('status') }}</p>
                                </div>
                            </div>
                        @endif


                        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">


                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-indigo-600"></i>Adres Email
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ $email ?? old('email') }}"
                                    required
                                    autocomplete="email"
                                    autofocus
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                                    placeholder="twoj@email.pl"
                                >
                            </div>


                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-indigo-600"></i>Nowe Hasło
                                </label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="new-password"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                                        placeholder="••••••••"
                                    >
                                    <button
                                        type="button"
                                        onclick="togglePassword('password')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                                    >
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Hasło powinno mieć min. 8 znaków
                                </p>
                            </div>


                            <div>
                                <label for="password-confirm" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-indigo-600"></i>Potwierdź Hasło
                                </label>
                                <div class="relative">
                                    <input
                                        id="password-confirm"
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                                        placeholder="••••••••"
                                    >
                                    <button
                                        type="button"
                                        onclick="togglePassword('password-confirm')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                                    >
                                        <i class="fas fa-eye" id="password-confirm-eye"></i>
                                    </button>
                                </div>
                            </div>


                            <button
                                type="submit"
                                class="w-full bg-linear-to-r from-indigo-600 to-purple-600 text-white font-bold py-3 px-6 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl"
                            >
                                <i class="fas fa-key mr-2"></i>
                                Zresetuj Hasło
                            </button>


                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Powrót do logowania
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }


        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            }
        });


        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');

            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }


        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

</body>
</html>
