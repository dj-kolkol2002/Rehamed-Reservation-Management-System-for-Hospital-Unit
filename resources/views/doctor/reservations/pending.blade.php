@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Oczekujące Wnioski o Wizyty</h1>
                <p class="text-gray-600 dark:text-gray-400">Zarządzaj wnioskami o wizyty od pacjentów</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" id="statsContainer">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                        <i class="fas fa-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Oczekujące</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="pendingCount">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                        <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dziś potwierdzone</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="confirmedTodayCount">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                        <i class="fas fa-times-circle text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dziś odrzucone</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="rejectedTodayCount">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                        <i class="fas fa-calendar-check text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Najbliższe wizyty</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="upcomingCount">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista oczekujących wniosków -->
        <div id="pendingRequestsContainer">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Ładowanie wniosków...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal potwierdzenia -->
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Potwierdź Wizytę</h3>
            <button onclick="closeConfirmModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mb-4">
            <p class="text-gray-700 dark:text-gray-300">Czy na pewno chcesz potwierdzić tę wizytę?</p>
            <div id="confirmDetails" class="mt-3 bg-gray-50 dark:bg-gray-700/50 rounded p-3 text-sm"></div>

            <!-- Pole ceny wizyty -->
            <div id="priceContainer" class="mt-4" style="display: none;">
                <label for="appointmentPrice" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>Cena wizyty (PLN)
                </label>
                <input type="number" id="appointmentPrice" step="0.01" min="0" max="999999.99"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white"
                    placeholder="np. 150.00">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Podaj cenę, aby pacjent mógł opłacić wizytę online. Pozostaw puste jeśli płatność na miejscu.
                </p>
            </div>

            @if(Auth::user()->isAdmin())
            <!-- Wybór fizjoterapeuty dla admina -->
            <div id="doctorSelectContainer" class="mt-4" style="display: none;">
                <label for="assignDoctorId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-user-md mr-2"></i>Przypisz do fizjoterapeuty
                </label>
                <select id="assignDoctorId" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                    <option value="">-- Pierwszy dostępny --</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Wybierz fizjoterapeutę lub pozostaw "Pierwszy dostępny"
                </p>
            </div>
            @endif
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Anuluj
            </button>
            <button onclick="submitConfirm()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                <i class="fas fa-check mr-2"></i>Potwierdź Wizytę
            </button>
        </div>
    </div>
</div>

<!-- Modal odrzucenia -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Odrzuć Wniosek</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="rejectForm">
            <input type="hidden" id="rejectAppointmentId">
            <div class="mb-4">
                <label for="rejectionReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Powód odrzucenia <span class="text-red-500">*</span>
                </label>
                <textarea id="rejectionReason" name="reason" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" placeholder="Podaj powód odrzucenia wniosku..." required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Anuluj
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Odrzuć Wniosek
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentConfirmId = null;
let currentAppointmentData = null;
const isAdmin = {{ Auth::user()->isAdmin() ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    loadPendingRequests();

    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReject();
    });
});

async function loadPendingRequests() {
    const container = document.getElementById('pendingRequestsContainer');

    try {
        const response = await fetch('/reservation/pending');
        const data = await response.json();

        // Update stats
        document.getElementById('pendingCount').textContent = data.pending?.length || 0;
        document.getElementById('confirmedTodayCount').textContent = data.confirmed_today || 0;
        document.getElementById('rejectedTodayCount').textContent = data.rejected_today || 0;
        document.getElementById('upcomingCount').textContent = data.upcoming || 0;

        if (data.pending && data.pending.length > 0) {
            displayPendingRequests(data.pending);
        } else {
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">Brak oczekujących wniosków</p>
                    <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Wszystkie wnioski zostały przetworzone</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Błąd ładowania wniosków:', error);
        container.innerHTML = `
            <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                <p>Wystąpił błąd podczas ładowania wniosków. Spróbuj ponownie.</p>
            </div>
        `;
    }
}

function displayPendingRequests(requests) {
    const container = document.getElementById('pendingRequestsContainer');

    // Sortuj po dacie wizyty
    requests.sort((a, b) => new Date(a.start_time) - new Date(b.start_time));

    let html = '<div class="space-y-4">';

    requests.forEach(req => {
        const date = new Date(req.start_time);
        const endDate = new Date(req.end_time);
        const dateFormatted = date.toLocaleDateString('pl-PL', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const timeFormatted = date.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });
        const endTimeFormatted = endDate.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });
        const createdDate = new Date(req.created_at);
        const hoursAgo = Math.floor((new Date() - createdDate) / (1000 * 60 * 60));

        const typeLabels = {
            online: 'Online',
            phone: 'Telefonicznie',
            in_person: 'Osobiście'
        };

        // Sprawdź czy wizyta może być "przejęta" (brak przypisanego lekarza)
        const canClaim = req.can_claim || req.doctor_id === null;
        const availableDoctorsCount = req.available_doctors_count || 0;
        const borderColor = canClaim ? 'border-orange-500' : 'border-yellow-500';
        const confirmButtonText = canClaim ? 'Przejmij wizytę' : 'Potwierdź';
        const confirmButtonIcon = canClaim ? 'fa-hand-paper' : 'fa-check';
        const rejectButtonText = canClaim ? 'Rezygnuję' : 'Odrzuć';

        html += `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 ${borderColor}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">${req.title}</h3>
                            ${canClaim ? `
                                <span class="px-2 py-1 text-xs font-medium rounded bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                    <i class="fas fa-users mr-1"></i>Oczekuje na przejęcie
                                </span>
                            ` : ''}
                            ${req.priority !== 'normal' ? `
                                <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>${req.priority === 'urgent' ? 'Pilne' : 'Nagłe'}
                                </span>
                            ` : ''}
                            <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                ${typeLabels[req.reservation_type] || req.reservation_type}
                            </span>
                        </div>

                        ${canClaim && availableDoctorsCount > 1 ? `
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-3 mb-3">
                                <p class="text-sm text-orange-700 dark:text-orange-400">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Ta wizyta czeka na przejęcie przez jednego z <strong>${availableDoctorsCount}</strong> dostępnych fizjoterapeutów.
                                    Kliknij "Przejmij wizytę" aby przypisać ją do siebie.
                                </p>
                            </div>
                        ` : ''}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                <strong>Termin:</strong> ${dateFormatted}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-clock mr-2 text-blue-600"></i>
                                <strong>Godzina:</strong> ${timeFormatted} - ${endTimeFormatted}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                <strong>Pacjent:</strong> ${req.patient?.full_name || req.patient_name || 'Brak danych'}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-stethoscope mr-2 text-blue-600"></i>
                                <strong>Typ:</strong> ${req.type || 'Brak typu'}
                            </div>
                        </div>

                        ${req.notes ? `
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3 mb-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-sticky-note mr-2"></i><strong>Notatki pacjenta:</strong> ${req.notes}
                                </p>
                            </div>
                        ` : ''}

                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Zgłoszono ${hoursAgo === 0 ? 'mniej niż godzinę temu' : hoursAgo === 1 ? '1 godzinę temu' : `${hoursAgo} godzin temu`}
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 ml-4">
                        <button onclick='confirmAppointment(${JSON.stringify(req).replace(/'/g, "\\'")})'  class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition whitespace-nowrap">
                            <i class="fas ${confirmButtonIcon} mr-2"></i>${confirmButtonText}
                        </button>
                        <button onclick="openRejectModal(${req.id}, ${canClaim})" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition whitespace-nowrap">
                            <i class="fas fa-times mr-2"></i>${rejectButtonText}
                        </button>
                        <a href="/reservation/${req.id}" class="px-6 py-2 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded-lg transition whitespace-nowrap">
                            <i class="fas fa-eye mr-2"></i>Szczegóły
                        </a>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

function confirmAppointment(reqData) {
    // reqData może być obiektem lub stringiem JSON
    const req = typeof reqData === 'string' ? JSON.parse(reqData) : reqData;

    currentConfirmId = req.id;
    currentAppointmentData = req;

    const canClaim = req.can_claim || req.doctor_id === null;
    const patientName = req.patient?.full_name || req.patient_name || 'Pacjent';

    // Formatuj datę
    const date = new Date(req.start_time);
    const dateFormatted = date.toLocaleDateString('pl-PL', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    const timeFormatted = date.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });

    // Zaktualizuj tytuł i tekst modalu w zależności od trybu
    const modalTitle = document.querySelector('#confirmModal h3');
    const modalText = document.querySelector('#confirmModal > div > div:nth-child(2) > p');
    const confirmButton = document.querySelector('#confirmModal button[onclick="submitConfirm()"]');

    if (canClaim && !isAdmin) {
        modalTitle.innerHTML = 'Przejmij Wizytę';
        modalText.innerHTML = 'Czy chcesz przejąć tę wizytę? Po potwierdzeniu zostaniesz przypisany jako fizjoterapeuta prowadzący.';
        confirmButton.innerHTML = '<i class="fas fa-hand-paper mr-2"></i>Przejmij Wizytę';
    } else if (isAdmin && canClaim) {
        modalTitle.innerHTML = 'Potwierdź i Przypisz Wizytę';
        modalText.innerHTML = 'Potwierdź wizytę i przypisz ją do wybranego fizjoterapeuty.';
        confirmButton.innerHTML = '<i class="fas fa-check mr-2"></i>Potwierdź i Przypisz';
    } else {
        modalTitle.innerHTML = 'Potwierdź Wizytę';
        modalText.innerHTML = 'Czy na pewno chcesz potwierdzić tę wizytę?';
        confirmButton.innerHTML = '<i class="fas fa-check mr-2"></i>Potwierdź Wizytę';
    }

    document.getElementById('confirmDetails').innerHTML = `
        <strong>Pacjent:</strong> ${patientName}<br>
        <strong>Data:</strong> ${dateFormatted}<br>
        <strong>Godzina:</strong> ${timeFormatted}<br>
        <strong>Typ:</strong> ${req.type || 'Brak'}
        ${canClaim && !isAdmin ? '<br><br><em class="text-orange-600">Po przejęciu wizyty, inni fizjoterapeuci nie będą mogli jej potwierdzić.</em>' : ''}
    `;

    // Pokaż pole ceny dla wizyt do przejęcia
    const priceContainer = document.getElementById('priceContainer');
    const priceInput = document.getElementById('appointmentPrice');
    if (priceContainer && canClaim) {
        priceContainer.style.display = 'block';
        // Ustaw domyślną cenę jeśli wizyta ma już cenę
        if (req.price && req.price > 0) {
            priceInput.value = req.price;
        } else {
            priceInput.value = '';
        }
    } else if (priceContainer) {
        priceContainer.style.display = 'none';
    }

    // Dla admina - pokaż i wypełnij dropdown z dostępnymi fizjoterapeutami
    if (isAdmin && canClaim) {
        const doctorSelectContainer = document.getElementById('doctorSelectContainer');
        const doctorSelect = document.getElementById('assignDoctorId');

        if (doctorSelectContainer && doctorSelect) {
            // Wyczyść poprzednie opcje
            doctorSelect.innerHTML = '<option value="">-- Pierwszy dostępny --</option>';

            // Pobierz dostępnych lekarzy z metadata
            const availableDoctorIds = req.metadata?.available_doctor_ids || [];

            if (availableDoctorIds.length > 0) {
                // Pobierz dane lekarzy z API
                fetchAvailableDoctors(availableDoctorIds, doctorSelect);
            }

            doctorSelectContainer.style.display = 'block';
        }
    } else {
        const doctorSelectContainer = document.getElementById('doctorSelectContainer');
        if (doctorSelectContainer) {
            doctorSelectContainer.style.display = 'none';
        }
    }

    document.getElementById('confirmModal').classList.remove('hidden');
}

async function fetchAvailableDoctors(doctorIds, selectElement) {
    try {
        // Pobierz listę wszystkich lekarzy z API
        const response = await fetch('/admin/reservations/doctors');
        const data = await response.json();

        if (data.doctors) {
            data.doctors.forEach(doctor => {
                if (doctorIds.includes(doctor.id)) {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = doctor.full_name;
                    selectElement.appendChild(option);
                }
            });
        }
    } catch (error) {
        console.error('Błąd pobierania listy lekarzy:', error);
    }
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentConfirmId = null;
    currentAppointmentData = null;

    // Resetuj pole ceny
    const priceInput = document.getElementById('appointmentPrice');
    if (priceInput) {
        priceInput.value = '';
    }
    const priceContainer = document.getElementById('priceContainer');
    if (priceContainer) {
        priceContainer.style.display = 'none';
    }

    // Resetuj dropdown dla admina
    const doctorSelect = document.getElementById('assignDoctorId');
    if (doctorSelect) {
        doctorSelect.value = '';
    }
    const doctorSelectContainer = document.getElementById('doctorSelectContainer');
    if (doctorSelectContainer) {
        doctorSelectContainer.style.display = 'none';
    }
}

async function submitConfirm() {
    if (!currentConfirmId) return;

    const confirmButton = document.querySelector('#confirmModal button[onclick="submitConfirm()"]');
    const originalButtonText = confirmButton.innerHTML;
    confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Przetwarzanie...';
    confirmButton.disabled = true;

    // Przygotuj body żądania
    const requestBody = {};

    // Dodaj cenę jeśli podana
    const priceInput = document.getElementById('appointmentPrice');
    if (priceInput && priceInput.value) {
        const price = parseFloat(priceInput.value);
        if (!isNaN(price) && price >= 0) {
            requestBody.price = price;
        }
    }

    // Dla admina - dodaj wybranego lekarza
    if (isAdmin) {
        const doctorSelect = document.getElementById('assignDoctorId');
        if (doctorSelect && doctorSelect.value) {
            requestBody.doctor_id = parseInt(doctorSelect.value);
        }
    }

    try {
        const response = await fetch(`/reservation/${currentConfirmId}/confirm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(requestBody)
        });

        const data = await response.json();

        if (response.ok) {
            closeConfirmModal();
            // Pokaż komunikat sukcesu
            showSuccessMessage(data.message || 'Wizyta została potwierdzona');
            // Odśwież listę po krótkim opóźnieniu
            setTimeout(() => loadPendingRequests(), 1000);
        } else {
            if (data.error_type === 'already_taken') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Wizyta już przejęta',
                    text: data.error,
                    confirmButtonColor: '#f0ad4e'
                });
                closeConfirmModal();
                setTimeout(() => loadPendingRequests(), 1000);
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
                    text: data.error || 'Nie udało się potwierdzić wizyty',
                    confirmButtonColor: '#d33'
                });
            }
            confirmButton.innerHTML = originalButtonText;
            confirmButton.disabled = false;
        }
    } catch (error) {
        console.error('Błąd:', error);
        Swal.fire({
            icon: 'error',
            title: 'Błąd',
            text: 'Wystąpił błąd podczas potwierdzania wizyty',
            confirmButtonColor: '#d33'
        });
        confirmButton.innerHTML = originalButtonText;
        confirmButton.disabled = false;
    }
}

function showSuccessMessage(message) {
    // Dodaj komunikat sukcesu na górze strony
    const container = document.querySelector('.container');
    const existingAlert = container.querySelector('.success-alert');
    if (existingAlert) existingAlert.remove();

    const alertDiv = document.createElement('div');
    alertDiv.className = 'success-alert bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-6';
    alertDiv.innerHTML = `<span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i>${message}</span>`;

    const firstChild = container.querySelector('.max-w-7xl');
    firstChild.insertBefore(alertDiv, firstChild.children[1]);

    // Usuń komunikat po 5 sekundach
    setTimeout(() => alertDiv.remove(), 5000);
}

function openRejectModal(id, canClaim = false) {
    document.getElementById('rejectAppointmentId').value = id;

    // Zaktualizuj tytuł i tekst modalu w zależności od trybu
    const modalTitle = document.querySelector('#rejectModal h3');
    const reasonLabel = document.querySelector('#rejectModal label[for="rejectionReason"]');
    const submitButton = document.querySelector('#rejectModal button[type="submit"]');

    if (canClaim) {
        modalTitle.innerHTML = 'Rezygnacja z Wizyty';
        reasonLabel.innerHTML = 'Powód rezygnacji <span class="text-red-500">*</span>';
        submitButton.innerHTML = '<i class="fas fa-times mr-2"></i>Zrezygnuj';
        document.getElementById('rejectionReason').placeholder = 'Podaj powód rezygnacji z tej wizyty (wizyta trafi do innych fizjoterapeutów)...';
    } else {
        modalTitle.innerHTML = 'Odrzuć Wniosek';
        reasonLabel.innerHTML = 'Powód odrzucenia <span class="text-red-500">*</span>';
        submitButton.innerHTML = '<i class="fas fa-times mr-2"></i>Odrzuć Wniosek';
        document.getElementById('rejectionReason').placeholder = 'Podaj powód odrzucenia wniosku...';
    }

    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').reset();
}

async function submitReject() {
    const id = document.getElementById('rejectAppointmentId').value;
    const reason = document.getElementById('rejectionReason').value;

    if (!reason.trim()) {
        alert('Podaj powód odrzucenia/rezygnacji');
        return;
    }

    const submitButton = document.querySelector('#rejectModal button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Przetwarzanie...';
    submitButton.disabled = true;

    try {
        const response = await fetch(`/reservation/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason })
        });

        const data = await response.json();

        if (response.ok) {
            closeRejectModal();
            // Pokaż komunikat sukcesu
            showSuccessMessage(data.message || 'Operacja zakończona pomyślnie');
            // Odśwież listę po krótkim opóźnieniu
            setTimeout(() => loadPendingRequests(), 1000);
        } else {
            alert(data.error || 'Nie udało się przetworzyć żądania');
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas przetwarzania żądania');
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    }
}
</script>
@endpush
