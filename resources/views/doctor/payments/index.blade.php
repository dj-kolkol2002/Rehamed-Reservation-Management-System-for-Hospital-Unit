@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-credit-card mr-3 text-indigo-600 dark:text-indigo-400"></i>
                Płatności pacjentów
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Płatności od Twoich pacjentów
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status płatności</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Wszystkie</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Opłacone</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Oczekujące</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Nieudane</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Nieopłacone</option>
                    </select>
                </div>

                <!-- Payment Method Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metoda płatności</label>
                    <select name="method" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Wszystkie</option>
                        <option value="stripe" {{ request('method') === 'stripe' ? 'selected' : '' }}>Online (Stripe)</option>
                        <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Gotówka</option>
                    </select>
                </div>

                <!-- Search by Patient -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Szukaj pacjenta</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" placeholder="Imię lub nazwisko..." value="{{ request('search') }}" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Reset Filters -->
                @if(request('status') || request('method') || request('search'))
                    <div class="md:col-span-3 flex gap-2">
                        <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-redo mr-1"></i> Wyczyść filtry
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Payments Table -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            @if($appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Wizyta
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Pacjent
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Data wizyty
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Kwota
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Metoda
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Akcje
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($appointments as $appointment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $appointment->title ?? 'Brak tytułu' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $appointment->type_display ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $appointment->patient ? $appointment->patient->firstname . ' ' . $appointment->patient->lastname : 'Brak danych' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $appointment->start_time->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($appointment->price, 2, ',', ' ') }} PLN
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($appointment->payment && $appointment->payment->payment_method === 'stripe')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                <i class="fas fa-credit-card mr-1"></i> Online
                                            </span>
                                        @elseif($appointment->payment && $appointment->payment->payment_method === 'cash')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                <i class="fas fa-money-bill-wave mr-1"></i> Gotówka
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                <i class="fas fa-minus mr-1"></i> Nie ustawiono
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($appointment->payment && $appointment->payment->status === 'completed')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                <i class="fas fa-check-circle mr-1"></i> Opłacone
                                            </span>
                                        @elseif($appointment->payment && $appointment->payment->status === 'pending')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                <i class="fas fa-clock mr-1"></i> Oczekuje
                                            </span>
                                        @elseif($appointment->payment && $appointment->payment->status === 'failed')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> Nieudane
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Nieopłacone
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($appointment->payment)
                                            <a href="{{ route('payments.show', $appointment->payment) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                                <i class="fas fa-eye mr-1"></i> Zobacz
                                            </a>
                                        @endif
                                        @if(!$appointment->payment || $appointment->payment->status !== 'completed')
                                            <button type="button" onclick="openCashPaymentModal({{ $appointment->id }}, '{{ $appointment->title }}', '{{ $appointment->patient ? $appointment->patient->firstname . ' ' . $appointment->patient->lastname : 'Pacjent' }}', {{ $appointment->price }})" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                <i class="fas fa-money-bill-wave mr-1"></i> Gotówka
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $appointments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-receipt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Brak wizyt do opłacenia</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Nie masz jeszcze żadnych wizyt z ceną</p>
                    <a href="{{ route('calendar.index') }}" class="mt-6 inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-calendar mr-2"></i>Przejdź do kalendarza
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cash Payment Confirmation Modal -->
<div id="cashPaymentModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeCashPaymentModal()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="bg-linear-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-money-bill-wave mr-3"></i>
                        Potwierdź płatność gotówką
                    </h3>
                    <button type="button" onclick="closeCashPaymentModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-6">
                <!-- Warning Icon -->
                <div class="flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>

                <!-- Message -->
                <div class="text-center mb-6">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Czy na pewno chcesz oznaczyć wizytę jako opłaconą gotówką?
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Ta akcja spowoduje oznaczenie wizyty jako opłaconej
                    </p>
                </div>

                <!-- Payment Details -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 mb-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Wizyta:</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white" id="modalAppointmentTitle"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Pacjent:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white" id="modalPatientName"></span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-600">
                        <span class="text-base font-medium text-gray-600 dark:text-gray-400">Kwota:</span>
                        <span class="text-xl font-bold text-green-600 dark:text-green-400" id="modalAmount"></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button type="button" onclick="closeCashPaymentModal()" class="w-full sm:w-auto px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Anuluj
                </button>
                <form id="cashPaymentForm" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-linear-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        Potwierdź płatność
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentAppointmentId = null;

function openCashPaymentModal(appointmentId, title, patientName, amount) {
    currentAppointmentId = appointmentId;

    // Update modal content
    document.getElementById('modalAppointmentTitle').textContent = title;
    document.getElementById('modalPatientName').textContent = patientName;
    document.getElementById('modalAmount').textContent = parseFloat(amount).toFixed(2) + ' PLN';

    // Update form action
    const form = document.getElementById('cashPaymentForm');
    form.action = `/payments/appointment/${appointmentId}/mark-cash`;

    // Show modal
    const modal = document.getElementById('cashPaymentModal');
    modal.classList.remove('hidden');

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeCashPaymentModal() {
    const modal = document.getElementById('cashPaymentModal');
    modal.classList.add('hidden');

    // Restore body scroll
    document.body.style.overflow = '';

    currentAppointmentId = null;
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCashPaymentModal();
    }
});
</script>
@endsection
