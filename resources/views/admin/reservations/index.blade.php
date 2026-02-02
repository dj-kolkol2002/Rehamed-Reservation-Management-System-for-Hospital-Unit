@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Panel Rezerwacji</h1>
                <p class="text-gray-600 dark:text-gray-400">Zarządzaj wszystkimi rezerwacjami w systemie</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Powrót do panelu
                </a>
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
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Potwierdzone nadchodzące</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="upcomingCount">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtry -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filtry</h2>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="doctor_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fizjoterapeuta
                    </label>
                    <select id="doctor_filter" name="doctor_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="all">Wszyscy</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data od
                    </label>
                    <input type="date" id="date_from" name="date_from" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data do
                    </label>
                    <input type="date" id="date_to" name="date_to" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        <i class="fas fa-search mr-2"></i>Filtruj
                    </button>
                    <button type="button" onclick="resetFilters()" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </form>
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
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Anuluj
            </button>
            <button onclick="submitConfirm()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                <i class="fas fa-check mr-2"></i>Potwierdź
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
                    <i class="fas fa-times mr-2"></i>Odrzuć
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal przypisania do innego fizjoterapeuty -->
<div id="reassignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Przypisz do Fizjoterapeuty</h3>
            <button onclick="closeReassignModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="reassignForm">
            <input type="hidden" id="reassignAppointmentId">
            <div class="mb-4">
                <label for="newDoctorId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Wybierz fizjoterapeutę <span class="text-red-500">*</span>
                </label>
                <select id="newDoctorId" name="doctor_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" required>
                    <option value="">-- Wybierz --</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeReassignModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Anuluj
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-user-md mr-2"></i>Przypisz
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentConfirmId = null;
let doctors = [];

document.addEventListener('DOMContentLoaded', function() {
    loadDoctors();
    loadPendingRequests();

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadPendingRequests();
    });

    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReject();
    });

    document.getElementById('reassignForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReassign();
    });
});

async function loadDoctors() {
    try {
        const response = await fetch('/admin/reservations/doctors');
        const data = await response.json();

        if (data.success && data.doctors) {
            doctors = data.doctors;

            const filterSelect = document.getElementById('doctor_filter');
            const reassignSelect = document.getElementById('newDoctorId');

            data.doctors.forEach(doctor => {
                // Dodaj do filtra
                const filterOption = document.createElement('option');
                filterOption.value = doctor.id;
                filterOption.textContent = doctor.name;
                filterSelect.appendChild(filterOption);

                // Dodaj do reassign
                const reassignOption = document.createElement('option');
                reassignOption.value = doctor.id;
                reassignOption.textContent = doctor.name;
                reassignSelect.appendChild(reassignOption);
            });
        }
    } catch (error) {
        console.error('Błąd ładowania lekarzy:', error);
    }
}

async function loadPendingRequests() {
    const container = document.getElementById('pendingRequestsContainer');
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i><p class="text-gray-600 dark:text-gray-400">Ładowanie wniosków...</p></div>';

    const params = new URLSearchParams();
    const doctorId = document.getElementById('doctor_filter').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;

    if (doctorId && doctorId !== 'all') params.append('doctor_id', doctorId);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    try {
        const response = await fetch(`/admin/reservations/pending?${params}`);
        const data = await response.json();

        // Update stats
        document.getElementById('pendingCount').textContent = data.total_pending || 0;
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

        html += `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">${req.title}</h3>
                            ${req.priority !== 'normal' ? `
                                <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>${req.priority === 'urgent' ? 'Pilne' : 'Nagłe'}
                                </span>
                            ` : ''}
                            <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                ${typeLabels[req.reservation_type] || req.reservation_type}
                            </span>
                        </div>

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
                                <i class="fas fa-user mr-2 text-green-600"></i>
                                <strong>Pacjent:</strong> ${req.patient_name || 'Brak danych'}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-user-md mr-2 text-purple-600"></i>
                                <strong>Fizjoterapeuta:</strong> ${req.doctor_name || 'Nieprzypisany'}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-stethoscope mr-2 text-blue-600"></i>
                                <strong>Typ wizyty:</strong> ${req.type || 'Brak typu'}
                            </div>
                        </div>

                        ${req.notes ? `
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3 mb-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-sticky-note mr-2"></i><strong>Notatki:</strong> ${req.notes}
                                </p>
                            </div>
                        ` : ''}

                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Zgłoszono ${hoursAgo === 0 ? 'mniej niż godzinę temu' : hoursAgo === 1 ? '1 godzinę temu' : `${hoursAgo} godzin temu`}
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 ml-4">
                        <button onclick="confirmAppointment(${req.id}, '${req.patient_name}', '${dateFormatted}', '${timeFormatted}')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition whitespace-nowrap text-sm">
                            <i class="fas fa-check mr-2"></i>Potwierdź
                        </button>
                        <button onclick="openRejectModal(${req.id})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition whitespace-nowrap text-sm">
                            <i class="fas fa-times mr-2"></i>Odrzuć
                        </button>
                        <button onclick="openReassignModal(${req.id}, ${req.doctor_id})" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition whitespace-nowrap text-sm">
                            <i class="fas fa-exchange-alt mr-2"></i>Przypisz
                        </button>
                        <a href="/reservation/${req.id}" class="px-4 py-2 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded-lg transition whitespace-nowrap text-sm">
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

function resetFilters() {
    document.getElementById('doctor_filter').value = 'all';
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    loadPendingRequests();
}

function confirmAppointment(id, patientName, date, time) {
    currentConfirmId = id;
    document.getElementById('confirmDetails').innerHTML = `
        <strong>Pacjent:</strong> ${patientName}<br>
        <strong>Data:</strong> ${date}<br>
        <strong>Godzina:</strong> ${time}
    `;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentConfirmId = null;
}

async function submitConfirm() {
    if (!currentConfirmId) return;

    try {
        const response = await fetch(`/admin/reservations/${currentConfirmId}/confirm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (response.ok) {
            closeConfirmModal();
            loadPendingRequests();
            showNotification('Rezerwacja została potwierdzona', 'success');
        } else {
            alert(data.error || 'Nie udało się potwierdzić wizyty');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas potwierdzania wizyty');
    }
}

function openRejectModal(id) {
    document.getElementById('rejectAppointmentId').value = id;
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
        alert('Podaj powód odrzucenia');
        return;
    }

    try {
        const response = await fetch(`/admin/reservations/${id}/reject`, {
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
            loadPendingRequests();
            showNotification('Rezerwacja została odrzucona', 'success');
        } else {
            alert(data.error || 'Nie udało się odrzucić wniosku');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas odrzucania wniosku');
    }
}

function openReassignModal(id, currentDoctorId) {
    document.getElementById('reassignAppointmentId').value = id;
    document.getElementById('newDoctorId').value = '';
    document.getElementById('reassignModal').classList.remove('hidden');
}

function closeReassignModal() {
    document.getElementById('reassignModal').classList.add('hidden');
    document.getElementById('reassignForm').reset();
}

async function submitReassign() {
    const id = document.getElementById('reassignAppointmentId').value;
    const doctorId = document.getElementById('newDoctorId').value;

    if (!doctorId) {
        alert('Wybierz fizjoterapeutę');
        return;
    }

    try {
        const response = await fetch(`/admin/reservations/${id}/reassign`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ doctor_id: doctorId })
        });

        const data = await response.json();

        if (response.ok) {
            closeReassignModal();
            loadPendingRequests();
            showNotification(data.message || 'Rezerwacja została przypisana', 'success');
        } else {
            alert(data.error || 'Nie udało się przypisać rezerwacji');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas przypisywania rezerwacji');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-2"></i>${message}`;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
