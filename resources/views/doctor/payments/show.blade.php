@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back button -->
        <div class="mb-6">
            <a href="{{ route('payments.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Powrót do listy płatności
            </a>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-indigo-600 dark:bg-indigo-700">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-file-invoice mr-3"></i>
                    Szczegóły płatności
                </h1>
            </div>

            <div class="px-6 py-6">
                <!-- Status Badge -->
                <div class="mb-6">
                    @if($payment->status === 'completed')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            <i class="fas fa-check-circle mr-2"></i> Płatność zrealizowana
                        </span>
                    @elseif($payment->status === 'pending')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                            <i class="fas fa-clock mr-2"></i> Oczekuje na płatność
                        </span>
                    @elseif($payment->status === 'failed')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                            <i class="fas fa-times-circle mr-2"></i> Płatność nieudana
                        </span>
                    @endif
                </div>

                <!-- Payment Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Kwota</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($payment->amount, 2, ',', ' ') }} PLN
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Metoda płatności</h3>
                        <p class="text-lg text-gray-900 dark:text-white">
                            @if($payment->payment_method === 'stripe')
                                <i class="fas fa-credit-card mr-2 text-blue-600"></i> Płatność online
                            @elseif($payment->payment_method === 'cash')
                                <i class="fas fa-money-bill-wave mr-2 text-green-600"></i> Gotówka
                            @else
                                {{ ucfirst($payment->payment_method) }}
                            @endif
                        </p>
                    </div>

                    @if($payment->paid_at)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Data płatności</h3>
                        <p class="text-lg text-gray-900 dark:text-white">
                            {{ $payment->paid_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Data utworzenia</h3>
                        <p class="text-lg text-gray-900 dark:text-white">
                            {{ $payment->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Appointment Details -->
                @if($payment->appointment)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-calendar-check mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        Informacje o wizycie
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tytuł</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $payment->appointment->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Typ terapii</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $payment->appointment->type_display }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Data i godzina</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $payment->appointment->start_time->format('d.m.Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Fizjoterapeuta</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">
                                {{ $payment->appointment->doctor ? 'Dr ' . $payment->appointment->doctor->firstname . ' ' . $payment->appointment->doctor->lastname : 'Brak danych' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('calendar.details', $payment->appointment) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                            <i class="fas fa-arrow-right mr-2"></i>Zobacz szczegóły wizyty
                        </a>
                    </div>
                </div>
                @endif

                <!-- Invoice -->
                @if($payment->invoice)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-file-invoice mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        Faktura
                    </h3>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $payment->invoice->invoice_number }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Wystawiona: {{ $payment->invoice->issued_at->format('d.m.Y') }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
