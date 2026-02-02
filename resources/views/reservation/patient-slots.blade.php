@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Dostępne Terminy</h1>
            <p class="text-gray-600 dark:text-gray-400">Wybierz dostępny termin wizyty</p>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Sugerowany termin -->
        <div id="suggestedSlotContainer" class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 border border-green-200 dark:border-green-700 rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Sugerowany Termin
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Najbliższy dostępny termin wizyty</p>
                </div>
                <div id="suggestedSlotContent" class="text-right">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                        <p class="text-sm text-gray-500 mt-1">Szukanie...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtry -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Filtruj Terminy</h2>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data od
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data do
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ now()->addDays(14)->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        <i class="fas fa-search mr-2"></i>Szukaj
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista dostępnych slotów -->
        <div id="slotsContainer">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Ładowanie dostępnych terminów...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal rezerwacji -->
<div id="reservationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">Złóż Wniosek o Wizytę</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <form id="reservationForm" class="space-y-4">
            @csrf
            <input type="hidden" id="slot_id" name="slot_id">

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Termin:</strong> <span id="modal_datetime"></span>
                </p>
            </div>

            <div>
                <label for="reservation_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Sposób zgłoszenia <span class="text-red-500">*</span>
                </label>
                <select id="reservation_type" name="reservation_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required>
                    <option value="online">Online</option>
                    <option value="phone">Telefonicznie</option>
                    <option value="in_person">Osobiście</option>
                </select>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Typ Wizyty <span class="text-red-500">*</span>
                </label>
                <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required>
                    <option value="fizjoterapia">Fizjoterapia</option>
                    <option value="konsultacja">Konsultacja</option>
                    <option value="masaz">Masaż leczniczy</option>
                    <option value="neurorehabilitacja">Neurorehabilitacja</option>
                    <option value="kontrola">Wizyta kontrolna</option>
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tytuł Wizyty <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="np. Ból pleców, Konsultacja urazowa" required>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notatki (opcjonalne)
                </label>
                <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Dodatkowe informacje o dolegliwościach..."></textarea>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <p class="text-sm text-yellow-800 dark:text-yellow-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    Wniosek wymaga potwierdzenia przez fizjoterapeutę. Otrzymasz powiadomienie o statusie rezerwacji.
                </p>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Anuluj
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-paper-plane mr-2"></i>Złóż Wniosek
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSuggestedSlot();
    loadSlots();

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadSlots();
    });

    document.getElementById('reservationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReservation();
    });
});

async function loadSuggestedSlot() {
    const container = document.getElementById('suggestedSlotContent');

    try {
        const response = await fetch('/reservation/suggested-slot');
        const data = await response.json();

        if (data.success && data.slot) {
            const slot = data.slot;
            const dateObj = new Date(slot.date);
            const dateFormatted = dateObj.toLocaleDateString('pl-PL', { weekday: 'long', day: 'numeric', month: 'long' });
            const doctorId = slot.doctor_id || null;

            container.innerHTML = `
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">${dateFormatted}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">${slot.start_time} - ${slot.end_time}</p>
                    <button onclick="openReservationModal('${slot.id}', '${slot.date}', '${slot.start_time}', '${slot.end_time}', [${doctorId}])"
                            class="mt-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">
                        <i class="fas fa-calendar-check mr-2"></i>Zarezerwuj ten termin
                    </button>
                </div>
            `;
        } else {
            container.innerHTML = `
                <p class="text-gray-500 dark:text-gray-400 text-sm">Brak dostępnych terminów</p>
            `;
        }
    } catch (error) {
        console.error('Błąd ładowania sugerowanego terminu:', error);
        container.innerHTML = `
            <p class="text-red-500 text-sm">Błąd ładowania</p>
        `;
    }
}

async function loadSlots() {
    const container = document.getElementById('slotsContainer');
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i><p class="text-gray-600 dark:text-gray-400">Ładowanie dostępnych terminów...</p></div>';

    const params = new URLSearchParams({
        start_date: document.getElementById('start_date').value,
        end_date: document.getElementById('end_date').value
    });

    try {
        const response = await fetch(`/reservation/patient/available-slots?${params}`);
        const data = await response.json();

        if (data.dates && Object.keys(data.dates).length > 0) {
            displaySlots(data.dates);
        } else {
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">Brak dostępnych terminów w wybranym okresie</p>
                    <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Spróbuj zmienić zakres dat</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Błąd ładowania slotów:', error);
        container.innerHTML = `
            <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                <p>Wystąpił błąd podczas ładowania terminów. Spróbuj ponownie.</p>
            </div>
        `;
    }
}

function displaySlots(dates) {
    const container = document.getElementById('slotsContainer');
    let html = '';

    Object.entries(dates).forEach(([date, slots]) => {
        const dateObj = new Date(date);
        const dateFormatted = dateObj.toLocaleDateString('pl-PL', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        html += `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 capitalize">
                    <i class="fas fa-calendar-day mr-2 text-blue-600"></i>${dateFormatted}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        `;

        slots.forEach(slot => {
            const availability = slot.max_patients - slot.current_bookings;
            const startTime = slot.start_time.length > 5 ? slot.start_time.substring(0, 5) : slot.start_time;
            const endTime = slot.end_time.length > 5 ? slot.end_time.substring(0, 5) : slot.end_time;
            // Zapisz doctor_ids jako JSON string
            const doctorIds = JSON.stringify(slot.doctor_ids || []);
            html += `
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-blue-500 dark:hover:border-blue-400 transition cursor-pointer" onclick="openReservationModal('${slot.id}', '${date}', '${slot.start_time}', '${slot.end_time}', ${doctorIds})">
                    <div class="flex justify-between items-start">
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            ${startTime} - ${endTime}
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded ${availability > 3 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : availability > 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'}">
                            ${availability > 0 ? availability + ' miejsc' : 'Pełne'}
                        </span>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Przechowuj dane aktualnie wybranego slotu
let currentSlotData = null;

function openReservationModal(slotId, date, startTime, endTime, doctorIds) {
    document.getElementById('slot_id').value = slotId;

    // Zapisz pełne dane slotu
    currentSlotData = {
        date: date,
        start_time: startTime.length > 5 ? startTime.substring(0, 5) : startTime,
        end_time: endTime.length > 5 ? endTime.substring(0, 5) : endTime,
        doctor_ids: doctorIds
    };

    const dateObj = new Date(date);
    const dateFormatted = dateObj.toLocaleDateString('pl-PL', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('modal_datetime').textContent = `${dateFormatted}, ${currentSlotData.start_time} - ${currentSlotData.end_time}`;

    document.getElementById('reservationModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('reservationModal').classList.add('hidden');
    document.getElementById('reservationForm').reset();
}

async function submitReservation() {
    const form = document.getElementById('reservationForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    // Dodaj pełne dane slotu
    data.slot_data = currentSlotData;

    try {
        const response = await fetch('/reservation/request', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            closeModal();
            window.location.href = '/reservation/my-appointments?success=Wniosek został złożony pomyślnie';
        } else {
            alert(result.error || 'Wystąpił błąd podczas składania wniosku');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas składania wniosku');
    }
}
</script>
@endpush
