{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Mój profil</h1>
                <p class="text-gray-600">Przeglądaj i zarządzaj swoimi danymi osobowymi.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edytuj profil
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Avatar and Quick Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
                    <div class="mb-4">
                        <img src="{{ $user->avatar_url }}"
                             alt="Avatar użytkownika {{ $user->full_name }}"
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-lg">
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ $user->full_name }}</h2>
                    <p class="text-indigo-100">{{ $user->email }}</p>
                    <div class="mt-4">
                        @switch($user->role)
                            @case('admin')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                    <i class="fas fa-user-shield mr-2"></i>
                                    Administrator
                                </span>
                                @break
                            @case('doctor')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                    <i class="fas fa-user-md mr-2"></i>
                                    Fizjoterapeuta
                                </span>
                                @break
                            @case('user')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                    <i class="fas fa-user mr-2"></i>
                                    Pacjent
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Szybkie informacje</h3>
                    <div class="space-y-3">
                        @if($user->phone)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone text-gray-400 w-5"></i>
                            <span class="ml-2 text-gray-600">{{ $user->phone }}</span>
                        </div>
                        @endif

                        @if($user->date_of_birth)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-birthday-cake text-gray-400 w-5"></i>
                            <span class="ml-2 text-gray-600">
                                {{ $user->date_of_birth->format('d.m.Y') }}
                                ({{ $user->date_of_birth->age }} lat)
                            </span>
                        </div>
                        @endif

                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar-plus text-gray-400 w-5"></i>
                            <span class="ml-2 text-gray-600">
                                Dołączył {{ $user->created_at->format('d.m.Y') }}
                            </span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-circle text-green-500 w-5"></i>
                            <span class="ml-2 text-gray-600">Konto aktywne</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        Dane osobowe
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Imię</label>
                            <p class="text-gray-900">{{ $user->firstname }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nazwisko</label>
                            <p class="text-gray-900">{{ $user->lastname }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <div class="flex items-center">
                                <p class="text-gray-900">{{ $user->email }}</p>
                                @if($user->email_verified_at)
                                    <i class="fas fa-check-circle text-green-500 ml-2" title="Email zweryfikowany"></i>
                                @else
                                    <i class="fas fa-exclamation-circle text-red-500 ml-2" title="Email niezweryfikowany"></i>
                                @endif
                            </div>
                        </div>

                        @if($user->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Telefon</label>
                            <p class="text-gray-900">{{ $user->phone }}</p>
                        </div>
                        @endif

                        @if($user->date_of_birth)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Data urodzenia</label>
                            <p class="text-gray-900">
                                {{ $user->date_of_birth->format('d.m.Y') }}
                                <span class="text-gray-500 text-sm">({{ $user->date_of_birth->age }} lat)</span>
                            </p>
                        </div>
                        @endif

                        @if($user->gender)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Płeć</label>
                            <p class="text-gray-900">
                                @switch($user->gender)
                                    @case('male')
                                        Mężczyzna
                                        @break
                                    @case('female')
                                        Kobieta
                                        @break
                                    @case('other')
                                        Inna
                                        @break
                                    @default
                                        {{ $user->gender }}
                                @endswitch
                            </p>
                        </div>
                        @endif

                        @if($user->address)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Adres</label>
                            <p class="text-gray-900">{{ $user->address }}</p>
                        </div>
                        @endif

                        @if($user->emergency_contact)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kontakt awaryjny</label>
                            <p class="text-gray-900">{{ $user->emergency_contact }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Medical History (for patients only) -->
            @if($user->role === 'user')
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-file-medical mr-2"></i>
                        Historia medyczna
                    </h3>
                </div>
                <div class="p-6">
                    @if($user->medical_history && is_array($user->medical_history) && count($user->medical_history) > 0)
                        <ul class="space-y-2">
                            @foreach($user->medical_history as $item)
                                <li class="flex items-start">
                                    <i class="fas fa-circle text-green-500 text-xs mt-2 mr-3"></i>
                                    <span class="text-gray-900">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 italic">Brak informacji w historii medycznej.</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Account Security -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Bezpieczeństwo konta
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">Hasło</h4>
                            <p class="text-sm text-gray-500">Ostatnio zmienione: {{ $user->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}#password"
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-key mr-2"></i>
                            Zmień hasło
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
