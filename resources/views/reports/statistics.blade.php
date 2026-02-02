@extends('layouts.app')

@section('title', 'Statystyki Systemu')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Statystyki Systemu</h1>
            <p class="text-gray-600">Zaawansowane statystyki i analiza danych systemu medycznego</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 mt-4 lg:mt-0">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Drukuj
            </button>

            <button onclick="downloadPDF()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Pobierz PDF
            </button>

            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Powrót
            </a>
        </div>
    </div>


    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 no-print">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Okres analizy</h3>
        <form method="GET" action="{{ route('reports.statistics') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data początkowa</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from', now()->subMonths(6)->format('Y-m-d')) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data końcowa</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Aktualizuj
                </button>
            </div>
        </form>
    </div>


    <div class="print-only mb-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Statystyki Systemu</h1>
            <p class="text-gray-600">Wygenerowano: {{ now()->format('d.m.Y H:i') }}</p>
            <p class="text-gray-600">Okres: {{ request('date_from', now()->subMonths(6)->format('d.m.Y')) }} - {{ request('date_to', now()->format('d.m.Y')) }}</p>
        </div>
    </div>


    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Statystyki użytkowników</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Łączna liczba użytkowników</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($userStats['total_users']) }}</p>
                        <p class="text-xs text-green-600">{{ $userStats['active_users'] }} aktywnych</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Doktorzy</p>
                        <p class="text-2xl font-semibold text-green-600">{{ number_format($userStats['doctors']) }}</p>
                        <p class="text-xs text-gray-500">Aktywni lekarze</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pacjenci</p>
                        <p class="text-2xl font-semibold text-purple-600">{{ number_format($userStats['patients']) }}</p>
                        <p class="text-xs text-blue-600">{{ $userStats['new_users_this_month'] }} nowych w tym miesiącu</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Administratorzy</p>
                        <p class="text-2xl font-semibold text-red-600">{{ number_format($userStats['admins']) }}</p>
                        <p class="text-xs text-gray-500">Uprawnieni użytkownicy</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aktywność</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ round(($userStats['active_users'] / max($userStats['total_users'], 1)) * 100, 1) }}%</p>
                        <p class="text-xs text-gray-500">Procent aktywnych</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Nowi w tym miesiącu</p>
                        <p class="text-2xl font-semibold text-indigo-600">{{ number_format($userStats['new_users_this_month']) }}</p>
                        <p class="text-xs text-gray-500">Nowi użytkownicy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Statystyki dokumentów</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Łączna liczba dokumentów</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($documentStats['total_documents']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ukończone</p>
                        <p class="text-2xl font-semibold text-green-600">{{ number_format($documentStats['by_status']['completed'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Szkice</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ number_format($documentStats['by_status']['draft'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4V7a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v4a2 2 0 002 2h4a2 2 0 002-2v-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">W okresie</p>
                        <p class="text-2xl font-semibold text-purple-600">{{ number_format($documentStats['documents_period']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Statystyki płatności</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Razem przychód</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($paymentStats['total_amount'], 2, ',', ' ') }} PLN</p>
                        <p class="text-xs text-green-600">{{ $paymentStats['completed_payments'] }} opłacone</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Opłacone</p>
                        <p class="text-2xl font-semibold text-emerald-600">{{ number_format($paymentStats['completed_amount'], 2, ',', ' ') }} PLN</p>
                        <p class="text-xs text-gray-500">{{ $paymentStats['completed_payments'] }} płatności</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Oczekujące</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ number_format($paymentStats['pending_amount'], 2, ',', ' ') }} PLN</p>
                        <p class="text-xs text-gray-500">{{ $paymentStats['pending_payments'] }} płatności</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Nieudane</p>
                        <p class="text-2xl font-semibold text-red-600">{{ $paymentStats['failed_payments'] }}</p>
                        <p class="text-xs text-gray-500">płatności w okresie</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumenty według typu</h3>
            <div class="space-y-3">
                @php
                    $typeLabels = [
                        'general' => 'Ogólne',
                        'diagnosis' => 'Diagnozy',
                        'treatment' => 'Leczenie',
                        'examination' => 'Badania',
                        'prescription' => 'Recepty'
                    ];
                    $typeColors = [
                        'general' => '#6B7280',
                        'diagnosis' => '#EF4444',
                        'treatment' => '#3B82F6',
                        'examination' => '#F59E0B',
                        'prescription' => '#10B981'
                    ];
                    $totalByType = $documentStats['by_type']->sum();
                @endphp

                @foreach($typeLabels as $type => $label)
                    @php
                        $count = $documentStats['by_type'][$type] ?? 0;
                        $percentage = $totalByType > 0 ? round(($count / $totalByType) * 100, 1) : 0;
                        $colorHex = $typeColors[$type] ?? '#6B7280';
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 chart-dot" style="background-color: {{ $colorHex }}; min-width: 12px; min-height: 12px;"></div>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $percentage }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumenty według statusu</h3>
            <div class="space-y-3">
                @php
                    $statusLabels = [
                        'completed' => 'Ukończone',
                        'draft' => 'Szkice',
                        'archived' => 'Zarchiwizowane'
                    ];
                    $statusColors = [
                        'completed' => '#10B981',
                        'draft' => '#F59E0B',
                        'archived' => '#6B7280'
                    ];
                    $totalByStatus = $documentStats['by_status']->sum();
                @endphp

                @foreach($statusLabels as $status => $label)
                    @php
                        $count = $documentStats['by_status'][$status] ?? 0;
                        $percentage = $totalByStatus > 0 ? round(($count / $totalByStatus) * 100, 1) : 0;
                        $colorHex = $statusColors[$status] ?? '#6B7280';
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 chart-dot" style="background-color: {{ $colorHex }}; min-width: 12px; min-height: 12px;"></div>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $percentage }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Aktywność miesięczna (dokumenty)</h3>
        <div class="space-y-4">
            @foreach($monthlyActivity as $activity)
                @php
                    $monthName = [
                        1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
                        5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
                        9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
                    ][$activity->month] ?? '';
                    $maxCount = $monthlyActivity->max('count');
                    $percentage = $maxCount > 0 ? ($activity->count / $maxCount) * 100 : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $monthName }} {{ $activity->year }}</span>
                        <span class="font-medium">{{ $activity->count }} dokumentów</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Najpopularniejsze typy dokumentów w analizowanym okresie</h3>
        <div class="space-y-3">
            @foreach($popularTypes as $index => $typeData)
                @php
                    $typeLabels = [
                        'general' => 'Ogólne',
                        'diagnosis' => 'Diagnozy',
                        'treatment' => 'Leczenie',
                        'examination' => 'Badania',
                        'prescription' => 'Recepty'
                    ];
                    $popularColors = [
                        0 => '#3B82F6',
                        1 => '#10B981',
                        2 => '#F59E0B',
                        3 => '#8B5CF6',
                        4 => '#EF4444'
                    ];
                    $colorHex = $popularColors[$index] ?? '#6B7280';
                    $label = $typeLabels[$typeData->type] ?? $typeData->type;
                    $maxCount = $popularTypes->first()->count ?? 1;
                    $percentage = ($typeData->count / $maxCount) * 100;
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="w-3 h-3 rounded-full mr-3 chart-dot" style="background-color: {{ $colorHex }}; min-width: 12px; min-height: 12px;"></div>
                        <span class="text-sm text-gray-700 mr-4">{{ $label }}</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="h-2 rounded-full" style="background-color: {{ $colorHex }}; width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-medium text-gray-900">{{ $typeData->count }}</span>
                        <span class="text-xs text-gray-500 ml-1">dokumentów</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie statystyk</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($userStats['total_users']) }}</div>
                <div class="text-sm text-gray-600">Łączna liczba użytkowników</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($documentStats['total_documents']) }}</div>
                <div class="text-sm text-gray-600">Łączna liczba dokumentów</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ round(($userStats['active_users'] / max($userStats['total_users'], 1)) * 100, 1) }}%</div>
                <div class="text-sm text-gray-600">Aktywność użytkowników</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ round($documentStats['total_documents'] / max($userStats['doctors'], 1), 1) }}</div>
                <div class="text-sm text-gray-600">Dokumentów na doktora</div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    body { -webkit-print-color-adjust: exact; font-size: 12px; }
    .container { max-width: none; margin: 0; padding: 0; }
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
}

.print-only { display: none; }


.chart-dot {
    min-width: 12px;
    min-height: 12px;
    border-radius: 9999px;
    margin-right: 0.75rem;
    flex-shrink: 0;
}
</style>
@endpush

@push('scripts')
<script>
function downloadPDF() {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('download', 'pdf');
    window.open(currentUrl.toString(), '_blank');
}
</script>
@endpush
@endsection
