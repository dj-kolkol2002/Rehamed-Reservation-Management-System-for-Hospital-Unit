
@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Profil użytkownika</h1>
                <p class="text-gray-600 dark:text-gray-400">Szczegółowe informacje o użytkowniku {{ $user->full_name }}.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edytuj
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Powrót do listy
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-linear-to-r from-indigo-500 to-purple-600 px-6 py-8">
                    <div class="flex items-center">
                        <div class="h-24 w-24 rounded-full overflow-hidden bg-white bg-opacity-20 border-4 border-white border-opacity-30">
                            <img src="{{ $user->avatar_url }}"
                                 alt="Avatar użytkownika {{ $user->full_name }}"
                                 class="h-full w-full object-cover">
                        </div>
                        <div class="ml-6">
                            <h2 class="text-2xl font-bold text-white">{{ $user->full_name }}</h2>
                            <p class="text-indigo-100 text-lg">{{ $user->email }}</p>
                            <div class="flex items-center mt-3 space-x-4">
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
                                    @default
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                            <i class="fas fa-user mr-2"></i>
                                            Pacjent
                                        </span>
                                @endswitch

                                @if($user->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Aktywny
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Nieaktywny
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informacje osobiste</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Imię</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->firstname ?? 'Brak danych' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nazwisko</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->lastname ?? 'Brak danych' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefon</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->phone ?? 'Brak numeru' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Data urodzenia</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->date_of_birth ? $user->date_of_birth->format('d.m.Y') : 'Brak danych' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Płeć</label>
                            <p class="text-gray-900 dark:text-white mt-1">
                                @if($user->gender === 'male')
                                    Mężczyzna
                                @elseif($user->gender === 'female')
                                    Kobieta
                                @else
                                    Nie podano
                                @endif
                            </p>
                        </div>
                    </div>
                </div>


                @if($user->address || $user->city || $user->postal_code || $user->country)
                <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Adres</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Ulica</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->address ?? 'Brak danych' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Miasto</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->city ?? 'Brak danych' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Kod pocztowy</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->postal_code ?? 'Brak danych' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Kraj</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $user->country ?? 'Brak danych' }}</p>
                        </div>
                    </div>
                </div>
                @endif


                @if($user->role === 'user' && $user->medical_history)
                <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Historia medyczna</h3>
                    @if($user->medical_history && count($user->medical_history) > 0)
                        <ul class="space-y-2">
                            @foreach($user->medical_history as $item)
                                <li class="flex items-start">
                                    <i class="fas fa-circle text-green-500 dark:text-green-400 text-xs mt-2 mr-3"></i>
                                    <span class="text-gray-900 dark:text-white">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">Brak informacji w historii medycznej.</p>
                    @endif
                </div>
                @endif


                @if($user->patientAppointments->count() > 0 || $user->doctorAppointments->count() > 0)
                <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ostatnie wizyty</h3>

                    @if($user->role === 'user' && $user->patientAppointments->count() > 0)
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Wizyty jako pacjent</h4>
                        <div class="space-y-3 mb-6">
                            @foreach($user->patientAppointments->take(5) as $appointment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->title ?? 'Wizyta' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Doktor: {{ $appointment->doctor?->full_name ?? 'Nieprzypisany' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->start_time?->format('d.m.Y') ?? 'Brak daty' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment->start_time?->format('H:i') ?? '--:--' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    @if($user->role === 'doctor' && $user->doctorAppointments->count() > 0)
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Wizyty jako doktor</h4>
                        <div class="space-y-3">
                            @foreach($user->doctorAppointments->take(5) as $appointment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->title ?? 'Wizyta' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Pacjent: {{ $appointment->patient->full_name ?? 'Nieznany' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->start_time?->format('d.m.Y') ?? 'Brak daty' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment->start_time?->format('H:i') ?? '--:--' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    @if($user->patientAppointments->count() === 0 && $user->doctorAppointments->count() === 0)
                        <p class="text-gray-500 dark:text-gray-400 italic">Brak wizyt w systemie.</p>
                    @endif
                </div>
                @endif
            </div>
        </div>


        <div class="space-y-8">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-linear-to-r from-purple-500 to-pink-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statystyki
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Łączna liczba wizyt</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $userStats['total_appointments'] }}</span>
                    </div>

                    @if($user->role === 'user')
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Wizyty jako pacjent</span>
                        <span class="text-xl font-semibold text-blue-600 dark:text-blue-400">{{ $userStats['patient_appointments'] }}</span>
                    </div>
                    @endif

                    @if($user->role === 'doctor')
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Wizyty jako doktor</span>
                        <span class="text-xl font-semibold text-green-600 dark:text-green-400">{{ $userStats['doctor_appointments'] }}</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">W systemie od</span>
                        <div class="text-right">
                            <span class="text-xl font-semibold text-purple-600 dark:text-purple-400">
                                {{ $user->formatted_account_age ?? ($userStats['account_age'] == 1 ? '1 dzień' : $userStats['account_age'] . ' dni') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-linear-to-r from-green-500 to-teal-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Szybkie akcje
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($user->role === 'user')
                        <a href="{{ route('medical-documents.index', ['patient_id' => $user->id]) }}"
                           class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-file-medical text-blue-600 dark:text-blue-400 mr-3"></i>
                                <span class="text-gray-900 dark:text-white">Zobacz dokumenty</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                        </a>
                    @endif

                    @if($user->role === 'doctor' || $user->role === 'user')
                        <a href="{{ route('calendar.index', ['doctor_id' => $user->role === 'doctor' ? $user->id : null]) }}"
                           class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-green-600 dark:text-green-400 mr-3"></i>
                                <span class="text-gray-900 dark:text-white">Zobacz kalendarz</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                        </a>
                    @endif

                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-edit text-purple-600 dark:text-purple-400 mr-3"></i>
                            <span class="text-gray-900 dark:text-white">Edytuj profil</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                    </a>

                    @if(!$user->is_active)
                        <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-3"></i>
                                    <span class="text-gray-900 dark:text-white">Aktywuj konto</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.deactivate', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-lg transition-colors"
                                    onclick="return confirm('Czy na pewno chcesz dezaktywować to konto?')">
                                <div class="flex items-center">
                                    <i class="fas fa-times-circle text-red-600 dark:text-red-400 mr-3"></i>
                                    <span class="text-gray-900 dark:text-white">Dezaktywuj konto</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>


            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-linear-to-r from-orange-500 to-red-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informacje o koncie
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Utworzono</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Ostatnia aktualizacja</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                    @if($user->email_verified_at)
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email zweryfikowany</label>
                        <p class="text-gray-900 dark:text-white mt-1 flex items-center">
                            <i class="fas fa-check-circle text-green-500 dark:text-green-400 mr-2"></i>
                            {{ $user->email_verified_at->format('d.m.Y') }}
                        </p>
                    </div>
                    @else
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email niezweryfikowany</label>
                        <p class="text-red-600 dark:text-red-400 mt-1 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Wymaga weryfikacji
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
