@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Zarządzanie Dostępnością</h1>
                <p class="text-gray-600 dark:text-gray-400">Zarządzaj swoimi slotami i dostępnością</p>
            </div>
            <div class="flex gap-3">
                <a href="/reservation/pending" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-clock mr-2"></i>Oczekujące Wnioski
                </a>
                <button onclick="openGenerateModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Generuj Sloty
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                        <i class="fas fa-calendar-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Wszystkie sloty</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalSlots">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                        <i class="fas fa-eye text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Publiczne</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="publicSlots">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                        <i class="fas fa-users text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Zarezerwowane</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="bookedSlots">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                        <i class="fas fa-ban text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Zablokowane</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="blockedCount">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtry -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Filtruj Sloty</h2>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                    <input type="date" id="end_date" name="end_date" value="{{ now()->addDays(30)->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="visibility_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Widoczność
                    </label>
                    <select id="visibility_filter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Wszystkie</option>
                        <option value="public">Publiczne</option>
                        <option value="restricted">Ograniczone</option>
                        <option value="hidden">Ukryte</option>
                    </select>
                </div>

                <div>
                    <label for="availability_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select id="availability_filter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Wszystkie</option>
                        <option value="1">Dostępne</option>
                        <option value="0">Pełne</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        <i class="fas fa-search mr-2"></i>Szukaj
                    </button>
                </div>
            </form>
        </div>

        <!-- Akcje grupowe -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6 hidden" id="bulkActionsPanel">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    <span id="selectedCount">0</span> slotów zaznaczonych
                </span>
                <div class="flex gap-2">
                    <button onclick="bulkUpdateVisibility('public')" class="px-4 py-2 text-sm bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg transition">
                        <i class="fas fa-eye mr-1"></i>Ustaw publiczne
                    </button>
                    <button onclick="bulkUpdateVisibility('hidden')" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded-lg transition">
                        <i class="fas fa-eye-slash mr-1"></i>Ukryj
                    </button>
                    <button onclick="bulkDelete()" class="px-4 py-2 text-sm bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition">
                        <i class="fas fa-trash mr-1"></i>Usuń
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista slotów -->
        <div id="slotsContainer">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Ładowanie slotów...</p>
            </div>
        </div>

        <!-- Zablokowane okresy -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Zablokowane Okresy</h2>
                <button onclick="openBlockModal()" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-ban mr-2"></i>Zablokuj Okres
                </button>
            </div>
            <div id="blockedSlotsContainer">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400">Ładowanie zablokowanych okresów...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal generowania slotów -->
<div id="generateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">Generuj Sloty</h3>
            <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="generateForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="gen_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data od <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="gen_start_date" name="start_date" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="gen_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data do <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="gen_end_date" name="end_date" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div>
                <label for="gen_visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Widoczność <span class="text-red-500">*</span>
                </label>
                <select id="gen_visibility" name="visibility" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="public">Publiczne</option>
                    <option value="restricted">Ograniczone</option>
                    <option value="hidden">Ukryte</option>
                </select>
            </div>

            <div>
                <label for="gen_max_patients" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Maksymalna liczba pacjentów na slot <span class="text-red-500">*</span>
                </label>
                <input type="number" id="gen_max_patients" name="max_patients" value="1" min="1" max="10" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    Sloty zostaną wygenerowane na podstawie Twojego harmonogramu pracy w wybranym okresie.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeGenerateModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Anuluj
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-cogs mr-2"></i>Generuj Sloty
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal blokowania okresu -->
<div id="blockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">Zablokuj Okres</h3>
            <button onclick="closeBlockModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="blockForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="block_start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data i czas od <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="block_start_time" name="start_time" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="block_end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data i czas do <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="block_end_time" name="end_time" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div>
                <label for="block_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Powód <span class="text-red-500">*</span>
                </label>
                <select id="block_reason" name="reason" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="vacation">Urlop</option>
                    <option value="sick_leave">Zwolnienie lekarskie</option>
                    <option value="break">Przerwa</option>
                    <option value="meeting">Spotkanie</option>
                    <option value="training">Szkolenie</option>
                    <option value="other">Inne</option>
                </select>
            </div>

            <div>
                <label for="block_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notatki (opcjonalne)
                </label>
                <textarea id="block_notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Dodatkowe informacje..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeBlockModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Anuluj
                </button>
                <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="fas fa-ban mr-2"></i>Zablokuj Okres
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal edycji widoczności -->
<div id="editVisibilityModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Zmień Widoczność</h3>
            <button onclick="closeEditVisibilityModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editVisibilityForm">
            <input type="hidden" id="edit_slot_id">
            <div class="mb-4">
                <label for="edit_visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Widoczność <span class="text-red-500">*</span>
                </label>
                <select id="edit_visibility" name="visibility" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="public">Publiczne</option>
                    <option value="restricted">Ograniczone</option>
                    <option value="hidden">Ukryte</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditVisibilityModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Anuluj
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Zapisz
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedSlots = new Set();

document.addEventListener('DOMContentLoaded', function() {
    loadSlots();
    loadBlockedSlots();
    loadStatistics();

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadSlots();
    });

    document.getElementById('generateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateSlots();
    });

    document.getElementById('blockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        blockPeriod();
    });

    document.getElementById('editVisibilityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitVisibilityChange();
    });
});

async function loadStatistics() {
    try {
        const response = await fetch('/doctor/slots/statistics');
        const data = await response.json();

        document.getElementById('totalSlots').textContent = data.total || 0;
        document.getElementById('publicSlots').textContent = data.public || 0;
        document.getElementById('bookedSlots').textContent = data.booked || 0;
    } catch (error) {
        console.error('Błąd ładowania statystyk:', error);
    }
}

async function loadSlots() {
    const container = document.getElementById('slotsContainer');
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i><p class="text-gray-600 dark:text-gray-400">Ładowanie slotów...</p></div>';

    const params = new URLSearchParams({
        start_date: document.getElementById('start_date').value,
        end_date: document.getElementById('end_date').value,
        visibility: document.getElementById('visibility_filter').value || '',
        is_available: document.getElementById('availability_filter').value || ''
    });

    try {
        const response = await fetch(`/doctor/slots/list?${params}`);
        const data = await response.json();

        if (data.slots && data.slots.length > 0) {
            displaySlots(data.slots);
        } else {
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">Brak slotów w wybranym okresie</p>
                    <button onclick="openGenerateModal()" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i>Generuj Sloty
                    </button>
                </div>
            `;
        }
    } catch (error) {
        console.error('Błąd ładowania slotów:', error);
        container.innerHTML = `
            <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                <p>Wystąpił błąd podczas ładowania slotów. Spróbuj ponownie.</p>
            </div>
        `;
    }
}

function displaySlots(slots) {
    // Group by date
    const grouped = {};
    slots.forEach(slot => {
        if (!grouped[slot.date]) {
            grouped[slot.date] = [];
        }
        grouped[slot.date].push(slot);
    });

    let html = '';
    Object.entries(grouped).forEach(([date, dateSlots]) => {
        const dateObj = new Date(date);
        const dateFormatted = dateObj.toLocaleDateString('pl-PL', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        html += `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 capitalize">
                    <i class="fas fa-calendar-day mr-2 text-blue-600"></i>${dateFormatted}
                </h3>
                <div class="space-y-3">
        `;

        dateSlots.forEach(slot => {
            const visibilityConfig = getVisibilityConfig(slot.visibility);
            const availability = slot.max_patients - slot.current_bookings;

            html += `
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="slot-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="${slot.id}" onchange="toggleSlotSelection(${slot.id})">
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            ${slot.start_time.substring(0, 5)} - ${slot.end_time.substring(0, 5)}
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full ${visibilityConfig.badgeClass}">
                            <i class="${visibilityConfig.icon} mr-1"></i>${visibilityConfig.label}
                        </span>
                        <span class="px-2 py-1 text-xs font-medium rounded ${availability > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'}">
                            ${slot.current_bookings}/${slot.max_patients}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openEditVisibilityModal(${slot.id}, '${slot.visibility}')" class="px-3 py-2 text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition">
                            <i class="fas fa-eye mr-1"></i>Widoczność
                        </button>
                        <button onclick="deleteSlot(${slot.id})" class="px-3 py-2 text-sm bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition">
                            <i class="fas fa-trash mr-1"></i>Usuń
                        </button>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    document.getElementById('slotsContainer').innerHTML = html;
}

function getVisibilityConfig(visibility) {
    const configs = {
        public: {
            label: 'Publiczne',
            icon: 'fas fa-eye',
            badgeClass: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
        },
        restricted: {
            label: 'Ograniczone',
            icon: 'fas fa-user-lock',
            badgeClass: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
        },
        hidden: {
            label: 'Ukryte',
            icon: 'fas fa-eye-slash',
            badgeClass: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
        }
    };

    return configs[visibility] || configs.public;
}

async function loadBlockedSlots() {
    const container = document.getElementById('blockedSlotsContainer');

    try {
        const response = await fetch('/doctor/slots/blocked');
        const data = await response.json();

        // Update blocked count
        document.getElementById('blockedCount').textContent = data.blocked?.length || 0;

        if (data.blocked && data.blocked.length > 0) {
            displayBlockedSlots(data.blocked);
        } else {
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-calendar-check text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400">Brak zablokowanych okresów</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Błąd ładowania zablokowanych okresów:', error);
    }
}

function displayBlockedSlots(blocked) {
    const reasonLabels = {
        vacation: 'Urlop',
        sick_leave: 'Zwolnienie',
        break: 'Przerwa',
        meeting: 'Spotkanie',
        training: 'Szkolenie',
        other: 'Inne'
    };

    let html = '<div class="space-y-3">';
    blocked.forEach(block => {
        const startDate = new Date(block.start_time);
        const endDate = new Date(block.end_time);

        html += `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border-l-4 border-red-500 flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            ${reasonLabels[block.reason] || block.reason}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-calendar mr-2"></i>
                        <strong>Od:</strong> ${startDate.toLocaleString('pl-PL')}
                        <strong class="ml-3">Do:</strong> ${endDate.toLocaleString('pl-PL')}
                    </p>
                    ${block.notes ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-2"><i class="fas fa-sticky-note mr-2"></i>${block.notes}</p>` : ''}
                </div>
                <button onclick="deleteBlockedSlot(${block.id})" class="px-3 py-2 text-sm bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition">
                    <i class="fas fa-trash mr-1"></i>Usuń
                </button>
            </div>
        `;
    });
    html += '</div>';

    document.getElementById('blockedSlotsContainer').innerHTML = html;
}

function toggleSlotSelection(id) {
    if (selectedSlots.has(id)) {
        selectedSlots.delete(id);
    } else {
        selectedSlots.add(id);
    }

    updateBulkActionsPanel();
}

function updateBulkActionsPanel() {
    const panel = document.getElementById('bulkActionsPanel');
    const count = selectedSlots.size;

    if (count > 0) {
        panel.classList.remove('hidden');
        document.getElementById('selectedCount').textContent = count;
    } else {
        panel.classList.add('hidden');
    }
}

async function bulkUpdateVisibility(visibility) {
    if (selectedSlots.size === 0) return;

    if (!confirm(`Czy na pewno chcesz zmienić widoczność ${selectedSlots.size} slotów?`)) {
        return;
    }

    try {
        const response = await fetch('/doctor/slots/bulk-update-visibility', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                slot_ids: Array.from(selectedSlots),
                visibility: visibility
            })
        });

        if (response.ok) {
            selectedSlots.clear();
            loadSlots();
            loadStatistics();
        } else {
            const data = await response.json();
            alert(data.error || 'Nie udało się zaktualizować widoczności');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas aktualizacji widoczności');
    }
}

async function bulkDelete() {
    if (selectedSlots.size === 0) return;

    if (!confirm(`Czy na pewno chcesz usunąć ${selectedSlots.size} slotów?`)) {
        return;
    }

    for (const slotId of selectedSlots) {
        await deleteSlot(slotId, false);
    }

    selectedSlots.clear();
    loadSlots();
    loadStatistics();
}

function openGenerateModal() {
    document.getElementById('gen_start_date').value = document.getElementById('start_date').value;
    document.getElementById('gen_end_date').value = document.getElementById('end_date').value;
    document.getElementById('generateModal').classList.remove('hidden');
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
    document.getElementById('generateForm').reset();
}

async function generateSlots() {
    const form = document.getElementById('generateForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('/doctor/slots/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            closeGenerateModal();
            alert(result.message || 'Sloty zostały wygenerowane pomyślnie');
            loadSlots();
            loadStatistics();
        } else {
            alert(result.error || 'Nie udało się wygenerować slotów');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas generowania slotów');
    }
}

function openBlockModal() {
    document.getElementById('blockModal').classList.remove('hidden');
}

function closeBlockModal() {
    document.getElementById('blockModal').classList.add('hidden');
    document.getElementById('blockForm').reset();
}

async function blockPeriod() {
    const form = document.getElementById('blockForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('/doctor/slots/block', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            closeBlockModal();
            loadBlockedSlots();
            loadStatistics();
        } else {
            const result = await response.json();
            alert(result.error || 'Nie udało się zablokować okresu');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas blokowania okresu');
    }
}

function openEditVisibilityModal(slotId, currentVisibility) {
    document.getElementById('edit_slot_id').value = slotId;
    document.getElementById('edit_visibility').value = currentVisibility;
    document.getElementById('editVisibilityModal').classList.remove('hidden');
}

function closeEditVisibilityModal() {
    document.getElementById('editVisibilityModal').classList.add('hidden');
}

async function submitVisibilityChange() {
    const slotId = document.getElementById('edit_slot_id').value;
    const visibility = document.getElementById('edit_visibility').value;

    try {
        const response = await fetch(`/doctor/slots/${slotId}/visibility`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ visibility })
        });

        if (response.ok) {
            closeEditVisibilityModal();
            loadSlots();
            loadStatistics();
        } else {
            const data = await response.json();
            alert(data.error || 'Nie udało się zaktualizować widoczności');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas aktualizacji widoczności');
    }
}

async function deleteSlot(slotId, confirm = true) {
    if (confirm && !window.confirm('Czy na pewno chcesz usunąć ten slot?')) {
        return;
    }

    try {
        const response = await fetch(`/doctor/slots/${slotId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            if (confirm) {
                loadSlots();
                loadStatistics();
            }
        } else {
            const data = await response.json();
            if (confirm) alert(data.error || 'Nie udało się usunąć slotu');
        }
    } catch (error) {
        console.error('Błąd:', error);
        if (confirm) alert('Wystąpił błąd podczas usuwania slotu');
    }
}

async function deleteBlockedSlot(blockId) {
    if (!confirm('Czy na pewno chcesz odblokować ten okres?')) {
        return;
    }

    try {
        const response = await fetch(`/doctor/slots/blocked/${blockId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            loadBlockedSlots();
            loadStatistics();
        } else {
            const data = await response.json();
            alert(data.error || 'Nie udało się usunąć blokady');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas usuwania blokady');
    }
}
</script>
@endpush
