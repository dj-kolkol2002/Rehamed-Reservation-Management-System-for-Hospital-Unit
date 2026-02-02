@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Rezerwacja Wizyty</h1>
            <p class="text-gray-600 dark:text-gray-400">Zarezerwuj wizytę u fizjoterapeuty</p>
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

        <div class="max-w-2xl mx-auto">
            <div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Nowa Rezerwacja</h2>

                    <form id="reservationForm" class="space-y-6">
                        @csrf

                        <div>
                            <label for="appointment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Typ Wizyty <span class="text-red-500">*</span>
                            </label>
                            <select id="appointment_type" name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">-- Wybierz typ wizyty --</option>
                                <option value="fizjoterapia">Fizjoterapia</option>
                                <option value="konsultacja">Konsultacja</option>
                                <option value="masaz">Masaż leczniczy</option>
                                <option value="neurorehabilitacja">Neurorehabilitacja</option>
                                <option value="kontrola">Wizyta kontrolna</option>
                            </select>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nazwa Rezerwacji <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="np. Zapalenie ścięgna, Ból pleców" required>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Opisz krótko swoją dolegliwość lub temat wizyty</p>
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Data Wizyty <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required min="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div>
                            <label for="time_slot" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Godzina Wizyty <span class="text-red-500">*</span>
                            </label>
                            <select id="time_slot" name="time_slot" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required disabled>
                                <option value="">-- Wybierz datę --</option>
                            </select>
                            <p id="slotsLoading" class="text-sm text-gray-500 dark:text-gray-400 mt-2 hidden">Ładowanie dostępnych godzin...</p>
                            <p id="noSlotsMessage" class="text-sm text-red-500 dark:text-red-400 mt-2 hidden">Brak dostępnych godzin w wybranym dniu</p>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notatki (opcjonalne)
                            </label>
                            <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Dodaj informacje o stanie zdrowia, dolegliwościach lub preferencjach..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-6 py-3 rounded-lg transition">
                                Anuluj
                            </a>
                            <button type="submit" id="submitBtn" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg transition">
                                Zarezerwuj Wizytę
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('start_date');
    const timeSlotSelect = document.getElementById('time_slot');
    const slotsLoading = document.getElementById('slotsLoading');
    const noSlotsMessage = document.getElementById('noSlotsMessage');

    // Przechowuj dane slotów dla wybranego dnia
    let currentDaySlots = [];

    // Obsługa zmiany daty
    dateInput.addEventListener('change', function() {
        if (this.value) {
            loadAvailableSlots(this.value);
        } else {
            timeSlotSelect.innerHTML = '<option value="">-- Wybierz datę --</option>';
            timeSlotSelect.disabled = true;
            currentDaySlots = [];
        }
    });

    function loadAvailableSlots(date) {
        timeSlotSelect.innerHTML = '';
        slotsLoading.classList.remove('hidden');
        noSlotsMessage.classList.add('hidden');
        timeSlotSelect.disabled = true;
        currentDaySlots = [];

        const startDateFormatted = new Date(date).toISOString().split('T')[0];

        fetch(`/reservation/patient/available-slots?start_date=${startDateFormatted}&end_date=${startDateFormatted}`)
            .then(response => response.json())
            .then(data => {
                slotsLoading.classList.add('hidden');

                if (data.success && data.dates) {
                    const slots = data.dates[startDateFormatted] || [];
                    currentDaySlots = slots; // Zapisz sloty

                    if (slots.length > 0) {
                        timeSlotSelect.innerHTML = '<option value="">-- Wybierz godzinę --</option>';
                        slots.forEach((slot, index) => {
                            const option = document.createElement('option');
                            option.value = index; // Użyj indeksu jako wartość
                            const startTime = slot.start_time.length > 5 ? slot.start_time.substring(0, 5) : slot.start_time;
                            const endTime = slot.end_time.length > 5 ? slot.end_time.substring(0, 5) : slot.end_time;
                            option.textContent = `${startTime} - ${endTime}`;
                            timeSlotSelect.appendChild(option);
                        });
                        timeSlotSelect.disabled = false;
                        noSlotsMessage.classList.add('hidden');
                    } else {
                        noSlotsMessage.classList.remove('hidden');
                        timeSlotSelect.innerHTML = '<option value="">Brak dostępnych godzin</option>';
                        timeSlotSelect.disabled = true;
                    }
                } else {
                    noSlotsMessage.classList.remove('hidden');
                    timeSlotSelect.innerHTML = '<option value="">Brak dostępnych godzin</option>';
                    timeSlotSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                slotsLoading.classList.add('hidden');
                noSlotsMessage.classList.remove('hidden');
                timeSlotSelect.disabled = true;
            });
    }

    // Obsługa wysyłania formularza
    document.getElementById('reservationForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const slotIndex = document.getElementById('time_slot').value;
        const selectedDate = document.getElementById('start_date').value;

        if (slotIndex === '' || !currentDaySlots[slotIndex]) {
            Swal.fire({
                icon: 'warning',
                title: 'Brak wyboru',
                text: 'Wybierz termin wizyty',
                confirmButtonText: 'OK'
            });
            return;
        }

        const selectedSlot = currentDaySlots[slotIndex];

        const formData = {
            slot_id: selectedSlot.id,
            slot_data: {
                date: selectedDate,
                start_time: selectedSlot.start_time.length > 5 ? selectedSlot.start_time.substring(0, 5) : selectedSlot.start_time,
                end_time: selectedSlot.end_time.length > 5 ? selectedSlot.end_time.substring(0, 5) : selectedSlot.end_time,
                doctor_ids: selectedSlot.doctor_ids || []
            },
            title: document.getElementById('title').value,
            type: document.getElementById('appointment_type').value,
            notes: document.getElementById('notes').value,
            reservation_type: 'online'
        };

        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').textContent = 'Rezerwowanie...';

        try {
            const response = await fetch('/reservation/request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukces!',
                    text: 'Wniosek o wizytę został złożony! Czeka na potwierdzenie.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    document.getElementById('reservationForm').reset();
                    timeSlotSelect.innerHTML = '<option value="">-- Wybierz datę --</option>';
                    timeSlotSelect.disabled = true;
                    currentDaySlots = [];
                    loadMyReservations();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Błąd',
                    text: data.error || 'Nie udało się zarezerwować wizyty',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Błąd:', error);
            Swal.fire({
                icon: 'error',
                title: 'Błąd',
                text: 'Błąd przy rezerwacji wizyty',
                confirmButtonText: 'OK'
            });
        } finally {
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('submitBtn').textContent = 'Zarezerwuj Wizytę';
        }
    });
});
</script>
@endsection
