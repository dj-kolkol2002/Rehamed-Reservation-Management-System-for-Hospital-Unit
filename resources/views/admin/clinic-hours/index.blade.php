@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Godziny pracy kliniki</h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Zarządzaj godzinami otwarcia kliniki. Wizyty mogą być rezerwowane tylko w tych godzinach.
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <form action="{{ route('admin.clinic-hours.default') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Czy na pewno chcesz przywrócić domyślne godziny pracy?')"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Przywróć domyślne
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400 rounded-lg">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Wystąpiły błędy:</strong>
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <form action="{{ route('admin.clinic-hours.update') }}" method="POST">
            @csrf

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Dzień tygodnia
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Godzina otwarcia
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Godzina zamknięcia
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Otwarte
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($clinicHours as $index => $hours)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$hours->is_active ? 'opacity-60' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="hidden" name="hours[{{ $index }}][day_of_week]" value="{{ $hours->day_of_week }}">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full
                                            {{ $hours->is_active ? 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-400' }}">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $hours->day_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="time"
                                           name="hours[{{ $index }}][start_time]"
                                           value="{{ $hours->start_time ? $hours->start_time->format('H:i') : '00:00' }}"
                                           class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                  focus:ring-2 focus:ring-teal-500 focus:border-teal-500
                                                  {{ !$hours->is_active ? 'opacity-50' : '' }}"
                                           {{ !$hours->is_active ? 'disabled' : '' }}>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="time"
                                           name="hours[{{ $index }}][end_time]"
                                           value="{{ $hours->end_time ? $hours->end_time->format('H:i') : '00:00' }}"
                                           class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                  focus:ring-2 focus:ring-teal-500 focus:border-teal-500
                                                  {{ !$hours->is_active ? 'opacity-50' : '' }}"
                                           {{ !$hours->is_active ? 'disabled' : '' }}>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               name="hours[{{ $index }}][is_active]"
                                               value="1"
                                               {{ $hours->is_active ? 'checked' : '' }}
                                               class="sr-only peer"
                                               onchange="toggleTimeInputs(this, {{ $index }})">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4
                                                    peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer
                                                    dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white
                                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all dark:border-gray-500
                                                    peer-checked:bg-teal-600"></div>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Zmiany wpłyną na dostępność slotów rezerwacji dla wszystkich lekarzy.
                    </p>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-teal-600 hover:bg-teal-700
                                   text-white font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-save mr-2"></i>
                        Zapisz zmiany
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3">
            <i class="fas fa-lightbulb mr-2"></i>
            Informacje
        </h3>
        <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-2">
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-500"></i>
                <span>Godziny pracy kliniki określają maksymalny zakres czasowy dla rezerwacji wizyt.</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-500"></i>
                <span>Każdy lekarz może mieć własny harmonogram pracy w ramach godzin otwarcia kliniki.</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-500"></i>
                <span>Wyłączenie dnia (np. niedziela) spowoduje, że żadne wizyty nie będą możliwe w tym dniu.</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-exclamation-triangle mr-2 mt-0.5 text-amber-500"></i>
                <span>Zmiana godzin nie wpływa na już zarezerwowane wizyty.</span>
            </li>
        </ul>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleTimeInputs(checkbox, index) {
    const row = checkbox.closest('tr');
    const timeInputs = row.querySelectorAll('input[type="time"]');

    if (checkbox.checked) {
        row.classList.remove('opacity-60');
        timeInputs.forEach(input => {
            input.disabled = false;
            input.classList.remove('opacity-50');
        });
    } else {
        row.classList.add('opacity-60');
        timeInputs.forEach(input => {
            input.disabled = true;
            input.classList.add('opacity-50');
        });
    }
}
</script>
@endsection
