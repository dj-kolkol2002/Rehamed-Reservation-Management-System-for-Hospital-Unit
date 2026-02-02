@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-credit-card mr-3 text-indigo-600 dark:text-indigo-400"></i>
                Moje płatności
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Historia płatności za wizyty
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


        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">

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


                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metoda płatności</label>
                    <select name="method" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Wszystkie</option>
                        <option value="stripe" {{ request('method') === 'stripe' ? 'selected' : '' }}>Online (Stripe)</option>
                        <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Gotówka</option>
                    </select>
                </div>


                @if(request('status') || request('method'))
                    <div class="flex items-end gap-2">
                        <a href="{{ route('payments.index') }}" class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center">
                            <i class="fas fa-redo mr-1"></i> Wyczyść filtry
                        </a>
                    </div>
                @endif
            </form>
        </div>


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
                                    Fizjoterapeuta
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
                                        {{ $appointment->doctor ? 'Dr ' . $appointment->doctor->firstname . ' ' . $appointment->doctor->lastname : 'Brak danych' }}
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
                                            <form id="paymentForm-{{ $appointment->id }}" action="{{ route('payments.checkout', $appointment) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="button" onclick="showPaymentModal({{ $appointment->id }}, '{{ $appointment->title }}', {{ $appointment->price }})" style="color: #16a34a; cursor: pointer; background: none; border: none; font-size: 14px; font-weight: 500; text-decoration: none; padding: 0;">
                                                    <i class="fas fa-credit-card" style="margin-right: 4px;"></i> Zapłać
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


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


<div id="paymentConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="modal-content rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">

        <div class="modal-header px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <h3 class="modal-title text-lg font-semibold">
                    Potwierdzenie płatności
                </h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>


        <div class="px-6 py-4">
            <div class="flex items-start space-x-4">
                <div class="shrink-0 w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                    <i class="fas fa-credit-card text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="modal-message text-sm text-gray-600 dark:text-gray-400 mb-2">
                        Czy chcesz przejść do płatności online za wizytę?
                    </p>
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <p class="text-sm font-medium text-gray-900 dark:text-white" id="paymentTitle">-</p>
                        <p class="text-sm text-indigo-600 dark:text-indigo-400 font-semibold mt-1" id="paymentAmount">-</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal-footer px-6 py-4 border-t flex justify-end space-x-3">
            <button onclick="closePaymentModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Anuluj
            </button>
            <button onclick="confirmPayment()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-credit-card mr-2"></i>
                Zapłać
            </button>
        </div>
    </div>
</div>

<style>

    #paymentConfirmationModal {
        backdrop-filter: blur(4px);
    }

    #paymentConfirmationModal .modal-content {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
    }

    #paymentConfirmationModal .modal-header {
        border-bottom-color: #e5e7eb;
    }

    #paymentConfirmationModal .modal-title {
        color: #111827;
    }

    #paymentConfirmationModal .modal-message {
        color: #6b7280;
    }

    #paymentConfirmationModal .modal-footer {
        border-top-color: #e5e7eb;
    }


    body.dark-mode #paymentConfirmationModal .modal-content {
        background-color: #1f2937;
        border-color: #374151;
    }

    body.dark-mode #paymentConfirmationModal .modal-header {
        border-bottom-color: #374151;
    }

    body.dark-mode #paymentConfirmationModal .modal-title {
        color: #f9fafb;
    }

    body.dark-mode #paymentConfirmationModal .modal-message {
        color: #d1d5db;
    }

    body.dark-mode #paymentConfirmationModal .modal-footer {
        border-top-color: #374151;
    }
</style>

<script>
let currentPaymentId = null;

function showPaymentModal(appointmentId, title, amount) {
    const modal = document.getElementById('paymentConfirmationModal');
    const paymentTitle = document.getElementById('paymentTitle');
    const paymentAmount = document.getElementById('paymentAmount');

    currentPaymentId = appointmentId;
    paymentTitle.textContent = title;
    paymentAmount.textContent = amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' PLN';

    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentConfirmationModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    currentPaymentId = null;
}

function confirmPayment() {
    if (currentPaymentId) {
        const form = document.getElementById('paymentForm-' + currentPaymentId);
        if (form) {
            form.submit();
        }
    }
    closePaymentModal();
}


document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
    }
});


document.getElementById('paymentConfirmationModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>
@endsection
