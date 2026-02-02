@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Moje Wizyty</h1>
                <p class="text-gray-600 dark:text-gray-400">Przeglądaj i zarządzaj swoimi wizytami</p>
            </div>
            <a href="/reservation/patient/available-slots" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Nowa Rezerwacja
            </a>
        </div>

        @if(session('success') || request('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('success') ?? request('success') }}</span>
        </div>
        @endif

        <!-- Filtry -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-wrap gap-3">
                <button onclick="filterAppointments('all')" class="filter-btn active px-4 py-2 rounded-lg transition" data-status="all">
                    Wszystkie
                </button>
                <button onclick="filterAppointments('pending')" class="filter-btn px-4 py-2 rounded-lg transition" data-status="pending">
                    <i class="fas fa-clock mr-1"></i>Oczekujące
                </button>
                <button onclick="filterAppointments('confirmed')" class="filter-btn px-4 py-2 rounded-lg transition" data-status="confirmed">
                    <i class="fas fa-check-circle mr-1"></i>Potwierdzone
                </button>
                <button onclick="filterAppointments('rejected')" class="filter-btn px-4 py-2 rounded-lg transition" data-status="rejected">
                    <i class="fas fa-times-circle mr-1"></i>Odrzucone
                </button>
                <button onclick="filterAppointments('completed')" class="filter-btn px-4 py-2 rounded-lg transition" data-status="completed">
                    <i class="fas fa-check-double mr-1"></i>Zakończone
                </button>
            </div>
        </div>

        <!-- Lista wizyt -->
        <div id="appointmentsContainer">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Ładowanie wizyt...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal odrzucenia -->
<div id="rejectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Powód Odrzucenia</h3>
            <button onclick="closeRejectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="rejectionReason" class="text-gray-700 dark:text-gray-300"></div>
        <div class="mt-4 flex justify-end">
            <button onclick="closeRejectionModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Zamknij
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', function() {
    loadAppointments();
});

async function loadAppointments() {
    const container = document.getElementById('appointmentsContainer');
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i><p class="text-gray-600 dark:text-gray-400">Ładowanie wizyt...</p></div>';

    try {
        const response = await fetch('/reservation/my-appointments');
        const data = await response.json();

        if (data.appointments && data.appointments.length > 0) {
            displayAppointments(data.appointments);
        } else {
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">Nie masz jeszcze żadnych wizyt</p>
                    <a href="/reservation/patient/available-slots" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i>Zarezerwuj Wizytę
                    </a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Błąd ładowania wizyt:', error);
        container.innerHTML = `
            <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                <p>Wystąpił błąd podczas ładowania wizyt. Spróbuj ponownie.</p>
            </div>
        `;
    }
}

function displayAppointments(appointments) {
    const container = document.getElementById('appointmentsContainer');

    // Grupowanie po statusie rezerwacji
    const grouped = {
        pending: [],
        confirmed: [],
        rejected: [],
        completed: []
    };

    appointments.forEach(apt => {
        const status = apt.reservation_status || (apt.status === 'completed' ? 'completed' : 'confirmed');
        if (grouped[status]) {
            grouped[status].push(apt);
        }
    });

    let html = '';

    // Najpierw oczekujące
    if (grouped.pending.length > 0) {
        html += renderGroup('Oczekujące na Potwierdzenie', grouped.pending, 'pending');
    }

    // Potem potwierdzone
    if (grouped.confirmed.length > 0) {
        html += renderGroup('Potwierdzone', grouped.confirmed, 'confirmed');
    }

    // Odrzucone
    if (grouped.rejected.length > 0) {
        html += renderGroup('Odrzucone', grouped.rejected, 'rejected');
    }

    // Zakończone
    if (grouped.completed.length > 0) {
        html += renderGroup('Zakończone', grouped.completed, 'completed');
    }

    container.innerHTML = html || '<div class="text-center py-8 text-gray-600 dark:text-gray-400">Brak wizyt dla wybranego filtru</div>';
}

function renderGroup(title, appointments, status) {
    let html = `
        <div class="appointment-group mb-6" data-group="${status}">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">${title}</h2>
            <div class="space-y-4">
    `;

    appointments.forEach(apt => {
        const statusConfig = getStatusConfig(apt.reservation_status || 'confirmed');
        const date = new Date(apt.start_time);
        const dateFormatted = date.toLocaleDateString('pl-PL', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const timeFormatted = date.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });

        html += `
            <div class="appointment-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 border-l-4 ${statusConfig.borderClass}" data-status="${status}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${apt.title}</h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full ${statusConfig.badgeClass}">
                                <i class="${statusConfig.icon} mr-1"></i>${statusConfig.label}
                            </span>
                            ${apt.priority && apt.priority !== 'normal' ? `<span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <i class="fas fa-exclamation-triangle mr-1"></i>${apt.priority === 'urgent' ? 'Pilne' : 'Nagłe'}
                            </span>` : ''}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <div>
                                <i class="fas fa-calendar mr-2"></i>${dateFormatted}
                            </div>
                            <div>
                                <i class="fas fa-clock mr-2"></i>${timeFormatted}
                            </div>
                            <div>
                                <i class="fas fa-user-md mr-2"></i>${apt.doctor_name || 'Brak danych'}
                            </div>
                            <div>
                                <i class="fas fa-stethoscope mr-2"></i>${apt.type || 'Brak typu'}
                            </div>
                        </div>

                        ${apt.notes ? `
                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                                <i class="fas fa-sticky-note mr-2"></i>${apt.notes}
                            </div>
                        ` : ''}

                        ${apt.reservation_status === 'confirmed' && apt.confirmed_at ? `
                            <div class="mt-2 text-xs text-green-600 dark:text-green-400">
                                <i class="fas fa-check-circle mr-1"></i>Potwierdzone: ${new Date(apt.confirmed_at).toLocaleString('pl-PL')}
                            </div>
                        ` : ''}

                        ${apt.reservation_status === 'rejected' && apt.rejected_at ? `
                            <div class="mt-2 text-xs text-red-600 dark:text-red-400">
                                <i class="fas fa-times-circle mr-1"></i>Odrzucone: ${new Date(apt.rejected_at).toLocaleString('pl-PL')}
                                ${apt.rejection_reason ? `<button onclick="showRejectionReason('${apt.rejection_reason.replace(/'/g, "\\'")}')" class="ml-2 underline">Zobacz powód</button>` : ''}
                            </div>
                        ` : ''}
                    </div>

                    <div class="flex flex-col gap-2 ml-4">
                        ${apt.reservation_status === 'pending' || apt.reservation_status === 'confirmed' ? `
                            ${apt.patient_can_cancel && new Date(apt.start_time) > new Date() ? `
                                <button onclick="cancelAppointment(${apt.id})" class="px-4 py-2 text-sm bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition">
                                    <i class="fas fa-times mr-1"></i>Anuluj
                                </button>
                            ` : ''}
                        ` : ''}
                        <a href="/reservation/${apt.id}" class="px-4 py-2 text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition text-center">
                            <i class="fas fa-eye mr-1"></i>Szczegóły
                        </a>
                    </div>
                </div>
            </div>
        `;
    });

    html += `
            </div>
        </div>
    `;

    return html;
}

function getStatusConfig(status) {
    const configs = {
        pending: {
            label: 'Oczekujące',
            icon: 'fas fa-clock',
            badgeClass: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            borderClass: 'border-yellow-500'
        },
        confirmed: {
            label: 'Potwierdzone',
            icon: 'fas fa-check-circle',
            badgeClass: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            borderClass: 'border-green-500'
        },
        rejected: {
            label: 'Odrzucone',
            icon: 'fas fa-times-circle',
            badgeClass: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            borderClass: 'border-red-500'
        },
        auto_confirmed: {
            label: 'Auto-potwierdzone',
            icon: 'fas fa-check-double',
            badgeClass: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            borderClass: 'border-blue-500'
        }
    };

    return configs[status] || configs.confirmed;
}

function filterAppointments(status) {
    currentFilter = status;

    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });

    const activeBtn = document.querySelector(`[data-status="${status}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
    }

    // Filter appointments
    document.querySelectorAll('.appointment-group').forEach(group => {
        if (status === 'all') {
            group.style.display = 'block';
        } else {
            group.style.display = group.dataset.group === status ? 'block' : 'none';
        }
    });
}

async function cancelAppointment(id) {
    if (!confirm('Czy na pewno chcesz anulować tę wizytę?')) {
        return;
    }

    try {
        const response = await fetch(`/reservation/${id}/cancel`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            window.location.reload();
        } else {
            const data = await response.json();
            alert(data.error || 'Nie udało się anulować wizyty');
        }
    } catch (error) {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas anulowania wizyty');
    }
}

function showRejectionReason(reason) {
    document.getElementById('rejectionReason').textContent = reason;
    document.getElementById('rejectionModal').classList.remove('hidden');
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
}
</script>

<style>
.filter-btn {
    background-color: #e5e7eb;
    color: #374151;
}

.dark .filter-btn {
    background-color: #374151;
    color: #d1d5db;
}

.filter-btn.active {
    background-color: #2563eb;
    color: white;
}

.filter-btn:hover:not(.active) {
    background-color: #d1d5db;
}

.dark .filter-btn:hover:not(.active) {
    background-color: #4b5563;
}
</style>
@endpush
