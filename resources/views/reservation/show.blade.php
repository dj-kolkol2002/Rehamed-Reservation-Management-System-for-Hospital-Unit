@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Szczegóły Rezerwacji</h1>
                <p class="text-gray-600 dark:text-gray-400">Rezerwacja ID: #{{ $appointment->id }}</p>
            </div>
            <a href="{{ route('reservation.index') }}" class="flex items-center text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                <i class="fas fa-arrow-left mr-2"></i>Wróć
            </a>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Nazwa Rezerwacji</h3>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $appointment->title }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Status Rezerwacji</h3>
                    <div class="flex items-center gap-2">
                        @if($appointment->status === 'scheduled')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                <i class="fas fa-calendar-check mr-2"></i>Zaplanowana
                            </span>
                        @elseif($appointment->status === 'completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                <i class="fas fa-check-circle mr-2"></i>Zakończona
                            </span>
                        @elseif($appointment->status === 'cancelled')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                <i class="fas fa-times-circle mr-2"></i>Anulowana
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                {{ $appointment->status_display }}
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Typ Wizyty</h3>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $appointment->type_display }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Fizjoterapeuta</h3>
                    @if($appointment->doctor)
                    <div class="flex items-center gap-3">
                        <img src="{{ $appointment->doctor->avatar_url }}" alt="{{ $appointment->doctor->full_name }}" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->doctor->full_name }}</p>
                            @if($appointment->doctor->phone)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $appointment->doctor->phone }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-3">
                        <p class="text-orange-700 dark:text-orange-400 font-medium">
                            <i class="fas fa-hourglass-half mr-2"></i>Oczekuje na przejęcie
                        </p>
                        <p class="text-sm text-orange-600 dark:text-orange-500 mt-1">
                            Wizyta czeka na potwierdzenie przez jednego z dostępnych fizjoterapeutów.
                        </p>
                        @if(isset($availableDoctors) && $availableDoctors->isNotEmpty())
                        <p class="text-sm text-orange-600 dark:text-orange-500 mt-2">
                            Dostępnych fizjoterapeutów: {{ $availableDoctors->count() }}
                        </p>
                        @endif
                    </div>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Czas Trwania</h3>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $appointment->duration_in_minutes }} minut</p>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Termin Wizyty</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Początek</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $appointment->start_time->format('H:i') }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $appointment->start_time->format('d.m.Y') }}
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Koniec</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $appointment->end_time->format('H:i') }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $appointment->end_time->format('d.m.Y') }}
                        </p>
                    </div>
                </div>
            </div>

            @if($appointment->notes)
            <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Notatki</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $appointment->notes }}</p>
            </div>
            @endif

            @if($appointment->payment)
            <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Płatność</h3>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 dark:text-gray-300">
                        @if($appointment->payment->status === 'paid')
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Opłacono
                        @elseif($appointment->payment->status === 'pending')
                            <i class="fas fa-hourglass-half text-yellow-500 mr-2"></i>
                            Oczekująca
                        @else
                            <i class="fas fa-times-circle text-red-500 mr-2"></i>
                            Nieopłacona
                        @endif
                    </span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($appointment->payment->amount, 2) }} zł</span>
                </div>
            </div>
            @elseif($appointment->price)
            <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Cena Wizyty</h3>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($appointment->price, 2) }} zł</span>
            </div>
            @endif
        </div>



        @if(Auth::id() === $appointment->doctor_id || Auth::user()->isAdmin())
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle mr-2"></i>
                Jesteś lekarzem przypisanym do tej rezerwacji. Możesz wyświetlić dodatkowe informacje w sekcji kalendarza.
            </p>
        </div>
        @endif

        @php
            $canClaim = false;
            $metadata = $appointment->metadata ?? [];
            $availableDoctorIds = $metadata['available_doctor_ids'] ?? [];
            if ($appointment->doctor_id === null && Auth::user()->isDoctor() && in_array(Auth::id(), $availableDoctorIds)) {
                $canClaim = true;
            }
        @endphp

        @if($canClaim && $appointment->isPending())
        <div class="mt-6 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-medium text-orange-800 dark:text-orange-300">
                        <i class="fas fa-hand-paper mr-2"></i>Możesz przejąć tę wizytę
                    </p>
                    <p class="text-sm text-orange-700 dark:text-orange-400 mt-1">
                        Ta wizyta czeka na potwierdzenie. Jeśli chcesz się nią zająć, kliknij "Przejmij wizytę".
                    </p>
                </div>
                <div class="flex gap-2 ml-4">
                    <button onclick="claimAppointment({{ $appointment->id }})" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition whitespace-nowrap">
                        <i class="fas fa-check mr-2"></i>Przejmij
                    </button>
                    <button onclick="declineAppointment({{ $appointment->id }})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition whitespace-nowrap">
                        <i class="fas fa-times mr-2"></i>Rezygnuję
                    </button>
                </div>
            </div>
        </div>

        <script>
        async function claimAppointment(id) {
            if (!confirm('Czy na pewno chcesz przejąć tę wizytę? Zostaniesz przypisany jako fizjoterapeuta prowadzący.')) return;

            try {
                const response = await fetch(`/reservation/${id}/confirm`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (response.ok) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Sukces',
                        text: data.message || 'Wizyta została przypisana do Ciebie',
                        confirmButtonColor: '#28a745'
                    });
                    window.location.reload();
                } else {
                    if (data.error_type === 'already_taken') {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Wizyta już przejęta',
                            text: data.error,
                            confirmButtonColor: '#f0ad4e'
                        });
                        window.location.reload();
                    } else if (data.error_type === 'patient_conflict') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Konflikt terminu pacjenta',
                            text: data.error,
                            confirmButtonColor: '#d33'
                        });
                    } else if (data.error_type === 'doctor_conflict') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Konflikt terminu fizjoterapeuty',
                            text: data.error,
                            confirmButtonColor: '#d33'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Operacja niedozwolona',
                            text: data.error || 'Nie udało się przejąć wizyty',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Błąd',
                    text: 'Wystąpił błąd: ' + error.message,
                    confirmButtonColor: '#d33'
                });
            }
        }

        async function declineAppointment(id) {
            const reason = prompt('Podaj powód rezygnacji z tej wizyty:');
            if (!reason) return;

            try {
                const response = await fetch(`/reservation/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ reason })
                });

                const data = await response.json();
                if (response.ok) {
                    alert(data.message || 'Zrezygnowałeś z tej wizyty');
                    window.location.href = '/doctor/reservations/pending';
                } else {
                    alert(data.error || 'Nie udało się przetworzyć żądania');
                }
            } catch (error) {
                alert('Wystąpił błąd: ' + error.message);
            }
        }
        </script>
        @endif
    </div>
</div>

<!-- Modal: Wiadomość do Doktora -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Wyślij wiadomość do doktora</h3>
            <button onclick="closeMessageModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="messageForm" onsubmit="sendMessage(event)">
            <textarea id="messageText" name="message" placeholder="Wpisz wiadomość..." class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 mb-4 text-gray-900 dark:text-white dark:bg-gray-700" rows="4" required></textarea>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeMessageModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Anuluj
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Wyślij
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Notatki Doktora -->
<div id="doctorNotesModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notatki z wizyty</h3>
            <button onclick="closeDoctorNotesModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="doctorNotesForm" onsubmit="saveDoctorNotes(event)">
            <textarea id="doctorNotes" name="doctor_notes" placeholder="Wpisz notatki z wizyty..." class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 mb-4 text-gray-900 dark:text-white dark:bg-gray-700" rows="5" required></textarea>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeDoctorNotesModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Anuluj
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Zapisz
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Wiadomość do Pacjenta -->
<div id="patientMessageModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Wyślij wiadomość do pacjenta</h3>
            <button onclick="closePatientMessageModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="patientMessageForm" onsubmit="sendPatientMessage(event)">
            <textarea id="patientMessageText" name="message" placeholder="Wpisz wiadomość..." class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 mb-4 text-gray-900 dark:text-white dark:bg-gray-700" rows="4" required></textarea>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closePatientMessageModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Anuluj
                </button>
                <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                    Wyślij
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const appointmentId = {{ $appointment->id }};

    // Funkcje modali
    function openMessageModal() {
        document.getElementById('messageModal').style.display = 'flex';
    }

    function closeMessageModal() {
        document.getElementById('messageModal').style.display = 'none';
        document.getElementById('messageForm').reset();
    }

    function openDoctorNotesModal() {
        document.getElementById('doctorNotesModal').style.display = 'flex';
    }

    function closeDoctorNotesModal() {
        document.getElementById('doctorNotesModal').style.display = 'none';
        document.getElementById('doctorNotesForm').reset();
    }

    function openPatientMessageModal() {
        document.getElementById('patientMessageModal').style.display = 'flex';
    }

    function closePatientMessageModal() {
        document.getElementById('patientMessageModal').style.display = 'none';
        document.getElementById('patientMessageForm').reset();
    }

    // Wysłanie wiadomości do doktora
    async function sendMessage(event) {
        event.preventDefault();
        const message = document.getElementById('messageText').value;

        try {
            const response = await fetch(`/reservation/${appointmentId}/send-message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();

            if (data.success) {
                alert('Wiadomość została wysłana');
                closeMessageModal();
            } else {
                alert('Błąd: ' + (data.error || 'Nie udało się wysłać wiadomości'));
            }
        } catch (error) {
            alert('Błąd: ' + error.message);
        }
    }

    // Zapis notatek doktora
    async function saveDoctorNotes(event) {
        event.preventDefault();
        const notes = document.getElementById('doctorNotes').value;

        try {
            const response = await fetch(`/reservation/${appointmentId}/doctor-notes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ doctor_notes: notes })
            });

            const data = await response.json();

            if (data.success) {
                alert('Notatki zostały zapisane');
                closeDoctorNotesModal();
            } else {
                alert('Błąd: ' + (data.error || 'Nie udało się zapisać notatek'));
            }
        } catch (error) {
            alert('Błąd: ' + error.message);
        }
    }

    // Wysłanie wiadomości do pacjenta
    async function sendPatientMessage(event) {
        event.preventDefault();
        const message = document.getElementById('patientMessageText').value;

        try {
            const response = await fetch(`/reservation/${appointmentId}/patient-message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();

            if (data.success) {
                alert('Wiadomość została wysłana do pacjenta');
                closePatientMessageModal();
            } else {
                alert('Błąd: ' + (data.error || 'Nie udało się wysłać wiadomości'));
            }
        } catch (error) {
            alert('Błąd: ' + error.message);
        }
    }

    // Ustaw przypomnienie
    function setReminder(appointmentId) {
        const reminderTimes = [
            { value: '15', label: '15 minut przed' },
            { value: '30', label: '30 minut przed' },
            { value: '60', label: '1 godzinę przed' },
            { value: '1440', label: '1 dzień przed' }
        ];

        let options = reminderTimes.map(t => `<option value="${t.value}">${t.label}</option>`).join('');

        const choice = prompt('Kiedy wysłać przypomnienie?\n15 - 15 minut przed\n30 - 30 minut przed\n60 - 1 godzinę przed\n1440 - 1 dzień przed\n\nWpisz liczbę minut:', '15');

        if (choice !== null) {
            const minutes = parseInt(choice);
            if (isNaN(minutes) || minutes < 1) {
                alert('Proszę wpisać prawidłową liczbę minut');
                return;
            }

            fetch(`/reservation/${appointmentId}/set-reminder`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ minutes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Przypomnienie ustawiono na ' + choice + ' minut(y) przed wizytą');
                } else {
                    alert('Błąd: ' + (data.error || 'Nie udało się ustawić przypomnienia'));
                }
            })
            .catch(error => alert('Błąd: ' + error.message));
        }
    }

    // Zamknij modal po kliknięciu poza nim
    document.addEventListener('click', function(event) {
        const messageModal = document.getElementById('messageModal');
        const doctorNotesModal = document.getElementById('doctorNotesModal');
        const patientMessageModal = document.getElementById('patientMessageModal');

        if (event.target === messageModal) {
            closeMessageModal();
        }
        if (event.target === doctorNotesModal) {
            closeDoctorNotesModal();
        }
        if (event.target === patientMessageModal) {
            closePatientMessageModal();
        }
    });
</script>
@endsection
