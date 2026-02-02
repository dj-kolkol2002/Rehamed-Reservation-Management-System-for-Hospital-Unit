@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Mój Harmonogram Pracy</h1>
            <p class="text-gray-600 dark:text-gray-400">Zarządzaj swoimi godzinami pracy w poszczególnych dniach tygodnia</p>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Szybkie akcje</h2>
            <div class="flex flex-wrap gap-3">
                <form action="{{ route('schedules.default', Auth::user()->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        Ustaw domyślny harmonogram (Pon-Pt 8:00-16:00)
                    </button>
                </form>
                <form action="{{ route('schedules.clear', Auth::user()->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz wyczyścić cały harmonogram?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                        Wyczyść harmonogram
                    </button>
                </form>
            </div>
        </div>

        <form action="{{ route('schedules.update', Auth::user()->id) }}" method="POST" id="scheduleForm">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                @php
                    $dayNames = [
                        0 => 'Niedziela',
                        1 => 'Poniedziałek',
                        2 => 'Wtorek',
                        3 => 'Środa',
                        4 => 'Czwartek',
                        5 => 'Piątek',
                        6 => 'Sobota',
                    ];
                @endphp

                @foreach($schedules as $day => $schedule)
                <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="p-6">
                        <input type="hidden" name="schedules[{{ $day }}][day_of_week]" value="{{ $day }}">
                        
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-shrink-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $dayNames[$day] }}
                                </h3>
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="hidden" name="schedules[{{ $day }}][is_active]" value="0">
                                <input
                                    type="checkbox"
                                    name="schedules[{{ $day }}][is_active]"
                                    value="1"
                                    id="is_active_{{ $day }}"
                                    class="toggle-active w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500"
                                    {{ $schedule->is_active ? 'checked' : '' }}
                                    onchange="toggleDayInputs({{ $day }})"
                                >
                                <label for="is_active_{{ $day }}" class="text-sm text-gray-700 dark:text-gray-300">
                                    Dzień czynny
                                </label>
                            </div>

                            <div class="flex-1 flex flex-col sm:flex-row gap-4" id="time_inputs_{{ $day }}">
                                <div class="flex-1">
                                    <label for="start_time_{{ $day }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Od:
                                    </label>
                                    <input
                                        type="time"
                                        name="schedules[{{ $day }}][start_time]"
                                        id="start_time_{{ $day }}"
                                        value="{{ $schedule->start_time?->format('H:i') ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                        {{ !$schedule->is_active ? 'disabled' : '' }}
                                    >
                                </div>
                                <div class="flex-1">
                                    <label for="end_time_{{ $day }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Do:
                                    </label>
                                    <input
                                        type="time"
                                        name="schedules[{{ $day }}][end_time]"
                                        id="end_time_{{ $day }}"
                                        value="{{ $schedule->end_time?->format('H:i') ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                        {{ !$schedule->is_active ? 'disabled' : '' }}
                                    >
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-6 py-3 rounded-lg transition">
                    Anuluj
                </a>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg transition">
                    Zapisz harmonogram
                </button>
            </div>
        </form>
        
        <script>
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            @foreach($schedules as $day => $schedule)
            enableFieldsForSubmit({{ $day }});
            @endforeach
            
            // Ustaw flag w localStorage - poinformuj kalendarz że harmonogram się zmienił
            localStorage.setItem('scheduleUpdated', 'true');
            localStorage.setItem('scheduleUpdatedAt', new Date().toISOString());
        });

        function enableFieldsForSubmit(day) {
            const startTime = document.getElementById('start_time_' + day);
            const endTime = document.getElementById('end_time_' + day);

            if (startTime) startTime.disabled = false;
            if (endTime) endTime.disabled = false;
        }
        </script>
    </div>
</div>

<script>
function toggleDayInputs(day) {
    const checkbox = document.getElementById('is_active_' + day);
    const startTime = document.getElementById('start_time_' + day);
    const endTime = document.getElementById('end_time_' + day);

    if (checkbox.checked) {
        startTime.disabled = false;
        endTime.disabled = false;
        startTime.required = true;
        endTime.required = true;
    } else {
        startTime.disabled = true;
        endTime.disabled = true;
        startTime.required = false;
        endTime.required = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @foreach($schedules as $day => $schedule)
    toggleDayInputs({{ $day }});
    @endforeach
});
</script>
@endsection
