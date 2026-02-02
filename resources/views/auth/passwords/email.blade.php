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

        body.dark-mode .bg-white {
            background-color: #1e293b !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;
        }

        body.dark-mode .hero-gradient {
            background: linear-gradient(135deg, #4338ca 0%, #5b21b6 100%);
        }

        body.dark-mode .text-gray-900 { color: #f1f5f9 !important; }
        body.dark-mode .text-gray-600 { color: #94a3b8 !important; }
        body.dark-mode .text-gray-500 { color: #64748b !important; }
        body.dark-mode .border-gray-300 { border-color: #334155 !important; }

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

        body.dark-mode .bg-red-50 {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: #dc2626 !important;
        }

        body.dark-mode .text-red-700 { color: #fca5a5 !important; }

        body.dark-mode .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.1) !important;
            border-color: #22c55e !important;
        }

        body.dark-mode .text-green-700 { color: #86efac !important; }

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

        body.dark-mode .theme-toggle .fa-sun { display: none; }
        .theme-toggle .fa-moon { display: none; }
        body.dark-mode .theme-toggle .fa-moon { display: block; }

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

        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        input, .bg-white {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
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


    <div class="relative w-full max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">


                <div class="lg:w-2/5 hero-gradient p-12 flex flex-col justify-center items-center text-white relative overflow-hidden">
                    <div class="absolute top-10 right-10 opacity-20 float-animation">
                        <i class="fas fa-key text-6xl"></i>
                    </div>
                    <div class="absolute bottom-10 left-10 opacity-20 float-animation" style="animation-delay: 1s;">
                        <i class="fas fa-lock text-6xl"></i>
                    </div>

                    <div class="relative z-10 text-center animate-fadeInUp">
                        <div class="mb-8">
                            <div class="inline-block p-6 bg-white bg-opacity-20 rounded-full backdrop-blur-sm">
                                <i class="fas fa-unlock-alt text-6xl"></i>
                            </div>
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                            Resetuj Hasło
                        </h1>
                        <p class="text-xl mb-8 text-purple-100">
                            Odzyskaj dostęp do swojego konta
                        </p>

                        <div class="space-y-4 text-left max-w-sm mx-auto">
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-envelope text-xl"></i>
                                </div>
                                <span class="text-lg">Otrzymasz link na email</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <span class="text-lg">Bezpieczne resetowanie</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-xl"></i>
                                </div>
                                <span class="text-lg">Link ważny 60 minut</span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="lg:w-3/5 p-12 flex flex-col justify-center">
                    <div class="max-w-xl mx-auto w-full animate-fadeInUp">

                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                                Zapomniałeś hasła?
                            </h2>
                            <p class="text-gray-600">
                                Podaj swój adres email, a wyślemy Ci link do resetowania hasła
                            </p>
                        </div>


                        @if (session('status'))
                            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                                    <p class="text-green-700">{{ session('status') }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                                    <div>
                                        <h3 class="text-red-800 font-semibold mb-1">Błąd</h3>
                                        <ul class="text-red-700 text-sm space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-purple-500"></i>
                                    Adres e-mail
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all duration-200 text-gray-900"
                                    placeholder="twoj@email.com"
                                >
                            </div>

                            <button
                                type="submit"
                                class="w-full bg-linear-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2"
                            >
                                <i class="fas fa-paper-plane"></i>
                                <span>Wyślij link resetujący</span>
                            </button>
                        </form>


                        <div class="mt-8 text-center">
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 font-medium inline-flex items-center space-x-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Powrót do logowania</span>
                            </a>
                        </div>


                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-sm text-gray-500 mb-3">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Twoje dane są bezpieczne i chronione
                                </p>
                                <div class="flex justify-center space-x-4 text-xs text-gray-400">
                                    <a href="#" class="hover:text-gray-600 transition-colors">Pomoc</a>
                                    <span>•</span>
                                    <a href="#" class="hover:text-gray-600 transition-colors">Kontakt</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        }
    </script>

</body>
</html>
