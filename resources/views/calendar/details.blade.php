@extends('layouts.app')

@section('title', 'Szczegóły wizyty')

@section('styles')
<style>
    /* Light mode styles */
    .detail-card {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
    }

    .detail-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .detail-value {
        color: #111827;
        font-size: 1rem;
        font-weight: 600;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-scheduled {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-no_show {
        background-color: #fef3c7;
        color: #92400e;
    }

    /* Dark mode overrides */
    body.dark-mode .detail-card {
        background-color: #1f2937;
        border-color: #374151;
    }

    body.dark-mode .detail-label {
        color: #9ca3af;
    }

    body.dark-mode .detail-value {
        color: #f9fafb;
    }

    body.dark-mode .status-scheduled {
        background-color: #1e3a8a;
        color: #93c5fd;
    }

    body.dark-mode .status-completed {
        background-color: #14532d;
        color: #86efac;
    }

    body.dark-mode .status-cancelled {
        background-color: #7f1d1d;
        color: #fca5a5;
    }

    body.dark-mode .status-no_show {
        background-color: #78350f;
        color: #fde047;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-calendar-alt mr-2 text-indigo-600 dark:text-indigo-400"></i>
                Informacje o zaplanowanej wizycie
            </h1>
        </div>

        <!-- Main Card -->
        <div class="detail-card rounded-lg shadow-lg p-6 mb-6">
            <!-- Title and Status -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $appointment->title }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $appointment->type_display }}
                    </p>
                </div>
                <span class="status-badge status-{{ $appointment->status }}">
                    @if($appointment->status === 'scheduled')
                        <i class="fas fa-clock mr-2"></i>Zaplanowana
                    @elseif($appointment->status === 'completed')
                        <i class="fas fa-check-circle mr-2"></i>Zakończona
                    @elseif($appointment->status === 'cancelled')
                        <i class="fas fa-times-circle mr-2"></i>Anulowana
                    @elseif($appointment->status === 'no_show')
                        <i class="fas fa-user-slash mr-2"></i>Nieobecność
                    @endif
                </span>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Doctor -->
                <div>
                    <p class="detail-label mb-2">Fizjoterapeuta</p>
                    <p class="detail-value">
                        @if($appointment->doctor)
                            <i class="fas fa-user-md mr-2 text-indigo-600 dark:text-indigo-400"></i>
                            Dr {{ $appointment->doctor->firstname }} {{ $appointment->doctor->lastname }}
                        @else
                            <span class="text-gray-400">Nie przypisano</span>
                        @endif
                    </p>
                </div>

                <!-- Patient -->
                <div>
                    <p class="detail-label mb-2">Pacjent</p>
                    <p class="detail-value">
                        @if($appointment->patient)
                            <i class="fas fa-user mr-2 text-green-600 dark:text-green-400"></i>
                            {{ $appointment->patient->firstname }} {{ $appointment->patient->lastname }}
                        @else
                            <span class="text-gray-400">Blokada czasu</span>
                        @endif
                    </p>
                </div>

                <!-- Start Time -->
                <div>
                    <p class="detail-label mb-2">Data i godzina rozpoczęcia</p>
                    <p class="detail-value">
                        <i class="fas fa-calendar mr-2 text-blue-600 dark:text-blue-400"></i>
                        {{ $appointment->start_time->setTimezone('Europe/Warsaw')->format('d.m.Y H:i') }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $appointment->start_time->setTimezone('Europe/Warsaw')->diffForHumans() }}
                    </p>
                </div>

                <!-- End Time -->
                <div>
                    <p class="detail-label mb-2">Data i godzina zakończenia</p>
                    <p class="detail-value">
                        <i class="fas fa-calendar mr-2 text-blue-600 dark:text-blue-400"></i>
                        {{ $appointment->end_time->setTimezone('Europe/Warsaw')->format('d.m.Y H:i') }}
                    </p>
                </div>

                <!-- Duration -->
                <div>
                    <p class="detail-label mb-2">Czas trwania</p>
                    <p class="detail-value">
                        <i class="fas fa-hourglass-half mr-2 text-purple-600 dark:text-purple-400"></i>
                        {{ $appointment->duration_in_minutes }} minut
                    </p>
                </div>

                <!-- Type -->
                <div>
                    <p class="detail-label mb-2">Typ terapii</p>
                    <p class="detail-value">
                        <i class="fas fa-heartbeat mr-2 text-red-600 dark:text-red-400"></i>
                        {{ $appointment->type_display }}
                    </p>
                </div>
            </div>

            <!-- Notes -->
            @if($appointment->notes)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <p class="detail-label mb-2">Notatki</p>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $appointment->notes }}</p>
                </div>
            @endif

            <!-- Payment Section -->
            @if($appointment->price && $appointment->price > 0)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-credit-card mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        Płatność
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price -->
                        <div>
                            <p class="detail-label mb-2">Cena wizyty</p>
                            <p class="detail-value text-2xl">
                                <i class="fas fa-tag mr-2 text-green-600 dark:text-green-400"></i>
                                {{ number_format($appointment->price, 2, ',', ' ') }} PLN
                            </p>
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <p class="detail-label mb-2">Status płatności</p>
                            @if($appointment->isPaid())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Opłacone
                                    @if($appointment->payment)
                                        @if($appointment->payment->payment_method === 'cash')
                                            (gotówka)
                                        @elseif($appointment->payment->payment_method === 'stripe')
                                            (online)
                                        @elseif($appointment->payment->payment_method === 'card')
                                            (karta)
                                        @endif
                                    @endif
                                </span>
                            @elseif($appointment->hasPendingPayment())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <i class="fas fa-clock mr-2"></i>
                                    Oczekuje na płatność
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    Nieopłacone
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Actions -->
                    @if(!$appointment->isPaid())
                        <div class="mt-6 flex flex-wrap gap-3">
                            @if(Auth::user()->role === 'user' && $appointment->patient_id === Auth::id())
                                <!-- Patient can pay online -->
                                <form action="{{ route('payments.checkout', $appointment) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors duration-150">
                                        <i class="fas fa-credit-card mr-2"></i>Zapłać online
                                    </button>
                                </form>
                            @endif

                            @if(in_array(Auth::user()->role, ['admin', 'doctor']))
                                <!-- Admin/Doctor can mark as paid with cash -->
                                <form id="mark-cash-form-{{ $appointment->id }}" action="{{ route('payments.mark-cash', $appointment) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="button" onclick="confirmMarkAsCash({{ $appointment->id }})" class="inline-flex items-center px-6 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors duration-150">
                                        <i class="fas fa-money-bill-wave mr-2"></i>Oznacz jako opłacone gotówką
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    <!-- Payment Details -->
                    @if($appointment->isPaid() && $appointment->payment)
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                                <i class="fas fa-file-invoice mr-2 text-indigo-600 dark:text-indigo-400"></i>
                                Szczegóły płatności
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Data płatności</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ $appointment->payment->paid_at ? $appointment->payment->paid_at->format('d.m.Y H:i') : 'Brak danych' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Metoda płatności</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                        @if($appointment->payment->payment_method === 'stripe')
                                            <i class="fas fa-credit-card mr-1 text-blue-600"></i> Płatność online
                                        @elseif($appointment->payment->payment_method === 'cash')
                                            <i class="fas fa-money-bill-wave mr-1 text-green-600"></i> Gotówka
                                        @else
                                            {{ ucfirst($appointment->payment->payment_method) }}
                                        @endif
                                    </p>
                                </div>

                                @if($appointment->payment->invoice)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Numer faktury</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ $appointment->payment->invoice->invoice_number }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Data wystawienia faktury</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ $appointment->payment->invoice->issued_at->format('d.m.Y') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <a href="{{ route('calendar.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 transition-colors duration-150">
                <i class="fas fa-arrow-left mr-2"></i>Powrót do kalendarza
            </a>

            @if($canEdit || $canCancel)
                <div class="flex flex-wrap gap-3">
                    @if($canEdit && $appointment->status === 'scheduled')
                        <a href="{{ route('calendar.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors duration-150">
                            <i class="fas fa-edit mr-2"></i>Edytuj wizytę
                        </a>
                    @endif

                    @if($canCancel && $appointment->status === 'scheduled')
                        <form id="cancel-form-{{ $appointment->id }}" action="{{ route('calendar.cancel', $appointment) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PATCH')
                            <button type="button" onclick="confirmCancelAppointment({{ $appointment->id }})" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition-colors duration-150">
                                <i class="fas fa-times mr-2"></i>Anuluj wizytę
                            </button>
                        </form>
                    @endif

                    @if($canEdit && $appointment->status === 'scheduled')
                        <form id="complete-form-{{ $appointment->id }}" action="{{ route('calendar.complete', $appointment) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PATCH')
                            <button type="button" onclick="confirmCompleteAppointment({{ $appointment->id }})" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors duration-150">
                                <i class="fas fa-check mr-2"></i>Oznacz jako zakończoną
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modal for Appointments -->
<div id="appointmentConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="modal-content rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <!-- Modal Header -->
        <div class="modal-header px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <h3 id="appointmentModalTitle" class="modal-title text-lg font-semibold">
                    Potwierdzenie
                </h3>
                <button onclick="closeAppointmentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4">
            <div class="flex items-start space-x-4">
                <div id="appointmentModalIcon" class="shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="flex-1">
                    <p id="appointmentModalMessage" class="modal-message text-sm">
                        Czy na pewno chcesz kontynuować?
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer px-6 py-4 border-t flex justify-end space-x-3">
            <button onclick="closeAppointmentModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Anuluj
            </button>
            <button id="appointmentModalConfirmBtn" onclick="confirmAppointmentAction()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Usuń
            </button>
        </div>
    </div>
</div>

<style>
    /* Modal styles for light mode */
    #appointmentConfirmationModal {
        backdrop-filter: blur(4px);
    }

    #appointmentConfirmationModal .modal-content {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
    }

    #appointmentConfirmationModal .modal-header {
        border-bottom-color: #e5e7eb;
    }

    #appointmentConfirmationModal .modal-footer {
        border-top-color: #e5e7eb;
    }

    #appointmentConfirmationModal .modal-title {
        color: #111827;
    }

    #appointmentConfirmationModal .modal-message {
        color: #6b7280;
    }

    /* Dark mode overrides */
    body.dark-mode #appointmentConfirmationModal .modal-content {
        background-color: #1f2937;
        border-color: #374151;
    }

    body.dark-mode #appointmentConfirmationModal .modal-header {
        border-bottom-color: #374151;
    }

    body.dark-mode #appointmentConfirmationModal .modal-footer {
        border-top-color: #374151;
    }

    body.dark-mode #appointmentConfirmationModal .modal-title {
        color: #f9fafb;
    }

    body.dark-mode #appointmentConfirmationModal .modal-message {
        color: #d1d5db;
    }
</style>

<script>
let appointmentModalCallback = null;

function showAppointmentModal(title, message, confirmText, icon, iconBgColor, confirmBtnColor, callback) {
    const modal = document.getElementById('appointmentConfirmationModal');
    const modalTitle = document.getElementById('appointmentModalTitle');
    const modalMessage = document.getElementById('appointmentModalMessage');
    const modalConfirmBtn = document.getElementById('appointmentModalConfirmBtn');
    const modalIconContainer = document.getElementById('appointmentModalIcon');

    modalTitle.textContent = title;
    modalMessage.textContent = message;
    modalConfirmBtn.innerHTML = `<i class="fas ${icon} mr-2"></i>${confirmText}`;

    // Update icon background color
    modalIconContainer.className = `flex-shrink-0 w-12 h-12 rounded-full ${iconBgColor} flex items-center justify-center`;
    const iconElement = modalIconContainer.querySelector('i');
    iconElement.className = `fas ${icon} text-xl`;

    // Update icon and button colors based on type
    if (confirmBtnColor.includes('red')) {
        iconElement.classList.add('text-red-600', 'dark:text-red-400');
    } else if (confirmBtnColor.includes('green')) {
        iconElement.classList.add('text-green-600', 'dark:text-green-400');
    } else if (confirmBtnColor.includes('blue')) {
        iconElement.classList.add('text-blue-600', 'dark:text-blue-400');
    }

    // Update button color
    modalConfirmBtn.className = `px-4 py-2 ${confirmBtnColor} text-white rounded-lg font-medium transition-colors`;

    appointmentModalCallback = callback;
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}

function closeAppointmentModal() {
    const modal = document.getElementById('appointmentConfirmationModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    appointmentModalCallback = null;
}

function confirmAppointmentAction() {
    if (appointmentModalCallback) {
        appointmentModalCallback();
    }
    closeAppointmentModal();
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAppointmentModal();
    }
});

// Close modal on background click
document.getElementById('appointmentConfirmationModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAppointmentModal();
    }
});

function confirmCancelAppointment(appointmentId) {
    showAppointmentModal(
        'Anuluj wizytę',
        'Czy na pewno chcesz anulować tę wizytę?',
        'Anuluj wizytę',
        'fa-times',
        'bg-red-100 dark:bg-red-900',
        'bg-red-600 hover:bg-red-700',
        function() {
            document.getElementById('cancel-form-' + appointmentId).submit();
        }
    );
}

function confirmCompleteAppointment(appointmentId) {
    showAppointmentModal(
        'Oznacz jako zakończoną',
        'Czy na pewno chcesz oznaczyć tę wizytę jako zakończoną?',
        'Zakończ',
        'fa-check',
        'bg-green-100 dark:bg-green-900',
        'bg-green-600 hover:bg-green-700',
        function() {
            document.getElementById('complete-form-' + appointmentId).submit();
        }
    );
}

function confirmMarkAsCash(appointmentId) {
    showAppointmentModal(
        'Oznacz jako opłacone',
        'Czy na pewno chcesz oznaczyć tę wizytę jako opłaconą gotówką?',
        'Oznacz jako opłacone',
        'fa-check',
        'bg-green-100 dark:bg-green-900',
        'bg-green-600 hover:bg-green-700',
        function() {
            document.getElementById('mark-cash-form-' + appointmentId).submit();
        }
    );
}
</script>
