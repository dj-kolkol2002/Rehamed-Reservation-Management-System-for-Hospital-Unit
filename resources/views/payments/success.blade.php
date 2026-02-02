@extends('layouts.app')

@section('title', 'Płatność zrealizowana')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Płatność zrealizowana!</h1>
            <p class="text-lg text-gray-600">Dziękujemy za dokonanie płatności</p>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-green-500 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Szczegóły płatności</h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Kwota</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($payment->amount, 2, ',', ' ') }} {{ strtoupper($payment->currency) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Data płatności</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $payment->paid_at->format('d.m.Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Metoda płatności</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($payment->payment_method === 'stripe')
                                Płatność online (Stripe)
                            @elseif($payment->payment_method === 'cash')
                                Gotówka
                            @elseif($payment->payment_method === 'card')
                                Karta płatnicza
                            @else
                                {{ ucfirst($payment->payment_method) }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Opłacone
                        </span>
                    </div>
                </div>

                @if($payment->appointment)
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Szczegóły wizyty</h3>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-medium text-gray-900">{{ $payment->appointment->title }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $payment->appointment->type_display }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $payment->appointment->start_time->format('d.m.Y H:i') }}
                            </p>
                            @if($payment->appointment->doctor)
                                <p class="text-sm text-gray-600 mt-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Fizjoterapeuta: {{ $payment->appointment->doctor->firstname }} {{ $payment->appointment->doctor->lastname }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                @if($payment->invoice)
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Faktura</h3>
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $payment->invoice->invoice_number }}</p>
                                <p class="text-sm text-gray-600">Wygenerowano: {{ $payment->invoice->issued_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if($payment->appointment)
            <a href="{{ route('calendar.details', $payment->appointment) }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Zobacz szczegóły wizyty
            </a>
            @endif

            <a href="{{ route('calendar.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Przejdź do kalendarza
            </a>
        </div>

        <!-- Email Notification Info -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Potwierdzenie płatności zostało wysłane na Twój adres email
            </p>
        </div>
    </div>
</div>
@endsection
