{{-- resources/views/auth/verification-success.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center pt-20 pb-8 px-4">
    <div class="form-container max-w-md w-full rounded-3xl p-8 relative">
        <div class="text-white text-center">
            <div class="mb-8">
                <div class="w-20 h-20 bg-green-500 bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-300"></i>
                </div>
                <h2 class="text-2xl font-bold mb-2">Email zweryfikowany!</h2>
                <p class="text-gray-200">Twoje konto zostało pomyślnie aktywowane.</p>
            </div>

            <div class="bg-white bg-opacity-10 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Co możesz teraz robić:</h3>
                <ul class="space-y-2 text-left text-sm">
                    <li class="flex items-center">
                        <i class="fas fa-calendar-check mr-3 text-green-300"></i>
                        Umówić wizytę u specjalisty
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-file-medical mr-3 text-blue-300"></i>
                        Przeglądać dokumentację medyczną
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-comments mr-3 text-purple-300"></i>
                        Komunikować się z lekarzami
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-chart-line mr-3 text-yellow-300"></i>
                        Śledzić postępy w rehabilitacji
                    </li>
                </ul>
            </div>

            <a href="{{ route('dashboard') }}" class="btn-primary w-full text-white px-6 py-3 rounded-lg font-semibold text-lg flex items-center justify-center transition-all hover:transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Przejdź do panelu głównego
            </a>

            <div class="mt-6">
                <p class="text-gray-300 text-sm">
                    Witamy w klinice Rehamed!
                    <span class="text-yellow-300">🎉</span>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.form-container {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9));
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #fcd34d, #f59e0b);
    border: none;
    box-shadow: 0 4px 15px rgba(252, 211, 77, 0.3);
}

.btn-primary:hover {
    box-shadow: 0 8px 25px rgba(252, 211, 77, 0.4);
}
</style>
@endsection
