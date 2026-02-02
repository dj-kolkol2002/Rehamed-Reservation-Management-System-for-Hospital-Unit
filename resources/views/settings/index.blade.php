

@extends('layouts.app')

@push('styles')
<style>

.max-w-7xl.mx-auto.mb-4 {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Ustawienia</h1>
        <p class="text-gray-600">Zarządzaj swoimi preferencjami konta</p>
    </div>


    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6" id="success-message">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
                <button onclick="document.getElementById('success-message').remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-medium">Wystąpiły błędy:</span>
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">
                <i class="fas fa-palette mr-2"></i>
                Motyw
            </h2>
        </div>
        <form method="POST" action="{{ route('settings.update.theme') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <p class="text-gray-600 mb-4">Wybierz motyw interfejsu</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <label class="relative cursor-pointer">
                        <input type="radio" name="theme" value="light"
                               {{ $currentTheme === 'light' ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="border-2 border-gray-300 bg-gradient-to-br from-orange-50 to-amber-50 rounded-lg p-6 transition-all peer-checked:border-indigo-600 peer-checked:bg-gradient-to-br peer-checked:from-indigo-50 peer-checked:to-blue-50 hover:border-indigo-400">
                            <div class="flex items-center justify-between mb-3">
                                <i class="fas fa-sun text-3xl text-yellow-500"></i>
                                <i class="fas fa-check-circle text-2xl text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">Jasny</h3>
                            <p class="text-sm text-gray-600">Klasyczny jasny motyw</p>
                        </div>
                    </label>

                    <!-- Dark Theme -->
                    <label class="relative cursor-pointer">
                        <input type="radio" name="theme" value="dark"
                               {{ $currentTheme === 'dark' ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="border-2 border-gray-600 bg-gray-800 rounded-lg p-6 transition-all peer-checked:border-indigo-400 peer-checked:bg-gray-700 hover:border-indigo-400">
                            <div class="flex items-center justify-between mb-3">
                                <i class="fas fa-moon text-3xl text-indigo-400"></i>
                                <i class="fas fa-check-circle text-2xl text-indigo-400 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <h3 class="font-semibold text-white mb-1">Ciemny</h3>
                            <p class="text-sm text-gray-300">Motyw ciemny dla oczu</p>
                        </div>
                    </label>
                </div>

                @error('theme')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Zapisz motyw
                </button>
            </div>
        </form>
    </div>


    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">
                <i class="fas fa-download mr-2"></i>
                Eksport danych
            </h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">Pobierz kopię wszystkich swoich danych w wybranym formacie</p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-400 text-xl mr-3 mt-1"></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-1">Co zostanie wyeksportowane?</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Dane profilu użytkownika</li>
                            @if(Auth::user()->isPatient())
                                <li>Twoje dokumenty medyczne</li>
                                <li>Historia wizyt</li>
                            @endif
                            @if(Auth::user()->isDoctor())
                                <li>Utworzone dokumenty pacjentów</li>
                                <li>Twoje wizyty z pacjentami</li>
                            @endif
                            <li>Data eksportu</li>
                        </ul>
                    </div>
                </div>
            </div>


            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Wybierz format eksportu:</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">


                    <div class="export-format-option">
                        <a href="{{ route('settings.export.data', ['format' => 'json']) }}"
                           class="flex flex-col items-center p-6 bg-white dark:bg-slate-700 border border-gray-200 dark:border-transparent rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group">
                            <i class="fas fa-code text-4xl text-blue-500 dark:text-blue-400 mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-300 transition-colors"></i>
                            <span class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-300 transition-colors">JSON</span>
                            <span class="text-xs text-gray-600 dark:text-gray-300 text-center mt-1">Dla deweloperów</span>
                        </a>
                    </div>


                    <div class="export-format-option">
                        <a href="{{ route('settings.export.data', ['format' => 'csv']) }}"
                           class="flex flex-col items-center p-6 bg-white dark:bg-slate-700 border border-gray-200 dark:border-transparent rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group">
                            <i class="fas fa-file-csv text-4xl text-green-500 dark:text-green-400 mb-3 group-hover:text-green-600 dark:group-hover:text-green-300 transition-colors"></i>
                            <span class="font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-300 transition-colors">CSV</span>
                            <span class="text-xs text-gray-600 dark:text-gray-300 text-center mt-1">Arkusze kalkulacyjne</span>
                        </a>
                    </div>


                    <div class="export-format-option">
                        <a href="{{ route('settings.export.data', ['format' => 'excel']) }}"
                           class="flex flex-col items-center p-6 bg-white dark:bg-slate-700 border border-gray-200 dark:border-transparent rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group">
                            <i class="fas fa-file-excel text-4xl text-emerald-500 dark:text-emerald-400 mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors"></i>
                            <span class="font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors">Excel</span>
                            <span class="text-xs text-gray-600 dark:text-gray-300 text-center mt-1">Microsoft Excel</span>
                        </a>
                    </div>


                    <div class="export-format-option">
                        <a href="{{ route('settings.export.data', ['format' => 'sql']) }}"
                           class="flex flex-col items-center p-6 bg-white dark:bg-slate-700 border border-gray-200 dark:border-transparent rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group">
                            <i class="fas fa-database text-4xl text-purple-500 dark:text-purple-400 mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-300 transition-colors"></i>
                            <span class="font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-300 transition-colors">SQL</span>
                            <span class="text-xs text-gray-600 dark:text-gray-300 text-center mt-1">Baza danych</span>
                        </a>
                    </div>

                </div>
            </div>


            <div class="border-t pt-4">
                <a href="{{ route('settings.export.data') }}"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-file-download mr-2"></i>
                    Szybki eksport (JSON)
                </a>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-red-200">
        <div class="bg-gradient-to-r from-red-600 to-red-800 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">
                <i class="fas fa-trash mr-2"></i>
                Usuń konto
            </h2>
        </div>
        <div class="p-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl mr-3 mt-1"></i>
                    <div class="text-sm text-red-700">
                        <p class="font-medium mb-2">Uwaga! Tej operacji nie można cofnąć.</p>
                        <p>Usunięcie konta spowoduje trwałe usunięcie:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Profilu użytkownika</li>
                            <li>Wszystkich dokumentów</li>
                            <li>Historii wizyt</li>
                            <li>Przesłanych plików</li>
                        </ul>
                        @if(Auth::user()->isAdmin())
                        <p class="mt-3 font-semibold text-red-800">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Administrator: Upewnij się, że istnieje inny aktywny administrator przed usunięciem konta.
                        </p>
                        @endif
                        @if(Auth::user()->isDoctor())
                        <p class="mt-3 font-semibold text-red-800">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Lekarz: Przed usunięciem konta należy przenieść lub zarchiwizować wszystkie dokumenty pacjentów.
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.delete.account') }}"
                  onsubmit="return confirm('Czy na pewno chcesz usunąć swoje konto? Tej operacji nie można cofnąć!')"
                  class="space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Potwierdź hasłem:
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Twoje hasło">
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Wpisz "DELETE" aby potwierdzić:
                    </label>
                    <input type="text"
                           name="confirmation"
                           id="confirmation"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="DELETE">
                    @error('confirmation')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors shadow-lg hover:shadow-xl">
                        <i class="fas fa-trash mr-2"></i>
                        Usuń konto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            successMessage.style.opacity = '0';
            successMessage.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                successMessage.remove();
            }, 500);
        }, 4000);
    }


    const exportLinks = document.querySelectorAll('.export-format-option a');
    exportLinks.forEach(link => {
        link.addEventListener('click', function(e) {

            const icon = this.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin text-3xl text-gray-400 mb-2';


            setTimeout(() => {
                icon.className = originalClass;
            }, 2000);
        });
    });
});
</script>

<style>

.export-format-option a {
    transition: all 0.3s ease;
}

.export-format-option a:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}


input[type="text"], input[type="password"] {
    transition: all 0.3s ease;
}

input[type="text"]:focus, input[type="password"]:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}


button, .inline-flex {
    transition: all 0.3s ease;
}

button:hover, .inline-flex:hover {
    transform: translateY(-1px);
}


@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection
