<!DOCTYPE html>
<html lang="pl">
<script>

    (function() {
        const theme = localStorage.getItem('theme') || 'light';
        const html = document.documentElement;
        const isDark = theme === 'dark';

        if (isDark) {
            html.classList.add('dark');

            const style = document.createElement('style');
            style.innerHTML = 'body { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e293b 100%) !important; }';
            document.head.appendChild(style);
        }
    })();
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rejestracja - {{ config('app.name', 'Rehamed') }}</title>

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


        body.dark-mode input[type="checkbox"] {
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        body.dark-mode input[type="checkbox"]:checked {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
        }


        body.dark-mode .bg-red-50 {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: #dc2626 !important;
        }

        body.dark-mode .text-red-800,
        body.dark-mode .text-red-700 {
            color: #fca5a5 !important;
        }

        body.dark-mode .text-red-500 {
            color: #ef4444 !important;
        }

        body.dark-mode .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.1) !important;
            border-color: #22c55e !important;
        }

        body.dark-mode .text-green-700 {
            color: #86efac !important;
        }

        body.dark-mode .text-green-500 {
            color: #22c55e !important;
        }


        body.dark-mode .password-strength {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        body.dark-mode .match-success {
            color: #86efac !important;
        }

        body.dark-mode .match-error {
            color: #fca5a5 !important;
        }


        body.dark-mode .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;
        }

        body.dark-mode .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3) !important;
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


        .password-strength {
            height: 4px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
            width: 0%;
        }

        .strength-weak { background: #ef4444; width: 25%; }
        .strength-fair { background: #f97316; width: 50%; }
        .strength-good { background: #eab308; width: 75%; }
        .strength-strong { background: #22c55e; width: 100%; }

        .match-success { color: #22c55e; font-weight: 500; }
        .match-error { color: #ef4444; font-weight: 500; }
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


                <div class="lg:w-2/5 hero-gradient p-12 flex flex-col justify-center items-center text-white relative overflow-hidden">

                    <div class="absolute top-10 right-10 opacity-20 float-animation">
                        <i class="fas fa-user-md text-6xl"></i>
                    </div>
                    <div class="absolute bottom-10 left-10 opacity-20 float-animation" style="animation-delay: 1s;">
                        <i class="fas fa-heartbeat text-6xl"></i>
                    </div>

                    <div class="relative z-10 text-center animate-fadeInUp">
                    >
                        <div class="mb-8">
                            <div class="inline-block p-6 bg-white bg-opacity-20 rounded-full backdrop-blur-sm">
                                <i class="fas fa-user-plus text-6xl"></i>
                            </div>
                        </div>


                        <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                            Dołącz do Rehamed
                        </h1>
                        <p class="text-xl mb-8 text-purple-100">
                            Zacznij swoją podróż do zdrowia już dziś
                        </p>


                        <div class="space-y-4 text-left max-w-sm mx-auto">
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-xl"></i>
                                </div>
                                <span class="text-lg">Bezpłatne założenie konta</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <span class="text-lg">Bezpieczne dane medyczne</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-xl"></i>
                                </div>
                                <span class="text-lg">Dostęp 24/7 do platformy</span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="lg:w-3/5 p-12 flex flex-col justify-center">
                    <div class="max-w-2xl mx-auto w-full animate-fadeInUp">


                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                                Utwórz konto
                            </h2>
                            <p class="text-gray-600">
                                Wypełnij formularz, aby rozpocząć swoją rehabilitację
                            </p>
                        </div>


                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                                    <div>
                                        <h3 class="text-red-800 font-semibold mb-1">Błędy w formularzu</h3>
                                        <ul class="text-red-700 text-sm space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                                    <p class="text-green-700">{{ session('status') }}</p>
                                </div>
                            </div>
                        @endif


                        <form method="POST" action="{{ route('register') }}" class="space-y-5">
                            @csrf


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="firstname" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user mr-2 text-purple-500"></i>
                                        Imię
                                    </label>
                                    <input
                                        type="text"
                                        id="firstname"
                                        name="firstname"
                                        value="{{ old('firstname') }}"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all duration-200 text-gray-900"
                                        placeholder="Jan"
                                    >
                                </div>
                                <div>
                                    <label for="lastname" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user mr-2 text-purple-500"></i>
                                        Nazwisko
                                    </label>
                                    <input
                                        type="text"
                                        id="lastname"
                                        name="lastname"
                                        value="{{ old('lastname') }}"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all duration-200 text-gray-900"
                                        placeholder="Kowalski"
                                    >
                                </div>
                            </div>


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
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all duration-200 text-gray-900"
                                    placeholder="twoj@email.com"
                                >
                            </div>


                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-2 text-purple-500"></i>
                                    Numer telefonu
                                </label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all duration-200 text-gray-900"
                                    placeholder="+48 123 456 789"
                                >
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-purple-500"></i>
                                    Hasło
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        required
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all duration-200 text-gray-900"
                                        placeholder="Minimum 8 znaków"
                                    >
                                    <button
                                        type="button"
                                        id="toggle-password"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                                    >
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="password-strength-fill" id="password-strength-fill"></div>
                                </div>
                                <div id="password-strength-text" class="text-xs text-gray-500 mt-2"></div>
                            </div>


                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-purple-500"></i>
                                    Potwierdź hasło
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        required
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all duration-200 text-gray-900"
                                        placeholder="Powtórz hasło"
                                    >
                                    <button
                                        type="button"
                                        id="toggle-confirm-password"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                                    >
                                        <i class="fas fa-eye" id="confirm-password-icon"></i>
                                    </button>
                                </div>
                                <div id="password-match-indicator" class="text-xs mt-2"></div>
                            </div>

                            <div class="flex items-start">
                                <input
                                    id="terms"
                                    name="terms"
                                    type="checkbox"
                                    required
                                    class="w-5 h-5 mt-0.5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 cursor-pointer"
                                >
                                <label for="terms" class="ml-3 text-sm text-gray-600 cursor-pointer">
                                    Akceptuję <a href="#" class="font-medium gradient-text hover:underline">regulamin</a> i <a href="#" class="font-medium gradient-text hover:underline">politykę prywatności</a>
                                </label>
                            </div>


                            <button
                                type="submit"
                                id="submit-btn"
                                class="w-full bg-linear-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <span>Zarejestruj się</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>


                        <div class="mt-8 text-center">
                            <p class="text-gray-600">
                                Masz już konto?
                                <a href="{{ route('login') }}" class="font-semibold gradient-text hover:underline ml-1">
                                    Zaloguj się teraz
                                </a>
                            </p>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-sm text-gray-500 mb-3">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Twoje dane są bezpieczne i chronione
                                </p>
                                <div class="flex justify-center space-x-4 text-xs text-gray-400">
                                    <a href="#" class="hover:text-gray-600 transition-colors">Regulamin</a>
                                    <span>•</span>
                                    <a href="#" class="hover:text-gray-600 transition-colors">Polityka prywatności</a>
                                    <span>•</span>
                                    <a href="#" class="hover:text-gray-600 transition-colors">Pomoc</a>
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

        document.addEventListener('DOMContentLoaded', function() {

            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const togglePassword = document.getElementById('toggle-password');
            const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
            const passwordIcon = document.getElementById('password-icon');
            const confirmPasswordIcon = document.getElementById('confirm-password-icon');
            const passwordStrengthFill = document.getElementById('password-strength-fill');
            const passwordStrengthText = document.getElementById('password-strength-text');
            const passwordMatchIndicator = document.getElementById('password-match-indicator');
            const phoneInput = document.getElementById('phone');
            const termsCheckbox = document.getElementById('terms');
            const submitBtn = document.getElementById('submit-btn');


            if (togglePassword && passwordInput && passwordIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    passwordIcon.className = type === 'text' ? 'fas fa-eye-slash' : 'fas fa-eye';
                });
            }

            if (toggleConfirmPassword && confirmPasswordInput && confirmPasswordIcon) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    confirmPasswordIcon.className = type === 'text' ? 'fas fa-eye-slash' : 'fas fa-eye';
                });
            }


            function checkPasswordStrength(password) {
                let strength = 0;

                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                const strengthClasses = ['', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
                const strengthTexts = ['', 'Słabe', 'Średnie', 'Dobre', 'Bardzo silne'];

                passwordStrengthFill.className = `password-strength-fill ${strengthClasses[strength]}`;

                if (password.length > 0) {
                    passwordStrengthText.textContent = `Siła hasła: ${strengthTexts[strength]}`;
                } else {
                    passwordStrengthText.textContent = '';
                }
            }


            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        passwordMatchIndicator.textContent = '✓ Hasła są identyczne';
                        passwordMatchIndicator.className = 'text-xs mt-2 match-success';
                    } else {
                        passwordMatchIndicator.textContent = '✗ Hasła nie są identyczne';
                        passwordMatchIndicator.className = 'text-xs mt-2 match-error';
                    }
                } else {
                    passwordMatchIndicator.textContent = '';
                }
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    checkPasswordMatch();
                });
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);
            }


            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.startsWith('48')) {
                        value = value.substring(2);
                    }
                    if (value.length > 0) {
                        value = '+48 ' + value.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
                    }
                    this.value = value;
                });
            }


            if (termsCheckbox && submitBtn) {
                termsCheckbox.addEventListener('change', function() {
                    submitBtn.disabled = !this.checked;
                });
            }
        });
    </script>

</body>
</html>
