@extends('layouts.app')

@section('title', 'Raporty')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Raporty</h1>
            <p class="text-gray-600">Generuj i przeglądaj raporty systemu medycznego</p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                {{ now()->format('d.m.Y H:i') }}
            </span>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                    <p class="text-sm font-medium text-gray-600">Pacjenci</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_patients']) }}</p>
                    <p class="text-xs text-green-600">{{ $stats['active_patients'] }} aktywnych</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Dokumenty</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_documents']) }}</p>
                    <p class="text-xs text-blue-600">{{ $stats['documents_today'] }} dzisiaj</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Doktorzy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_doctors']) }}</p>
                    <p class="text-xs text-gray-500">Aktywni lekarze</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ten miesiąc</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['documents_this_month']) }}</p>
                    <p class="text-xs text-indigo-600">Nowych dokumentów</p>
                </div>
            </div>
        </div>

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
                    <p class="text-sm font-medium text-gray-600">Płatności</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($paymentStats['total_payments']) }}</p>
                    <p class="text-xs text-green-600">{{ $paymentStats['completed_payments'] }} opłacone</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Przychód</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($paymentStats['total_amount'], 2, ',', ' ') }} PLN</p>
                    <p class="text-xs text-indigo-600">{{ number_format($paymentStats['this_month_amount'], 2, ',', ' ') }} PLN w tym miesiącu</p>
                </div>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900">Raport Pacjentów</h3>
                    <p class="text-sm text-gray-600">Lista wszystkich pacjentów z podstawowymi informacjami</p>
                </div>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Liczba pacjentów:</span>
                    <span class="font-medium">{{ number_format($stats['total_patients']) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Aktywni:</span>
                    <span class="font-medium text-green-600">{{ number_format($stats['active_patients']) }}</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="{{ route('reports.patients') }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Podgląd
                </a>
                <button onclick="window.open('{{ route('reports.patients', ['download' => 'pdf']) }}', '_blank')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    PDF
                </button>
            </div>
        </div>


        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900">Raport Dokumentów</h3>
                    <p class="text-sm text-gray-600">Analiza dokumentacji medycznej w systemie</p>
                </div>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Dokumenty:</span>
                    <span class="font-medium">{{ number_format($stats['total_documents']) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Ten miesiąc:</span>
                    <span class="font-medium text-blue-600">{{ number_format($stats['documents_this_month']) }}</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="{{ route('reports.documents') }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Podgląd
                </a>
                <button onclick="window.open('{{ route('reports.documents', ['download' => 'pdf']) }}', '_blank')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    PDF
                </button>
            </div>
        </div>


        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900">Raport Płatności</h3>
                    <p class="text-sm text-gray-600">Analiza przychodu i statusów płatności</p>
                </div>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Razem opłacone:</span>
                    <span class="font-medium text-emerald-600">{{ number_format($paymentStats['completed_amount'], 2, ',', ' ') }} PLN</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Ten miesiąc:</span>
                    <span class="font-medium text-blue-600">{{ number_format($paymentStats['this_month_amount'], 2, ',', ' ') }} PLN</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="{{ route('payments.index') }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Podgląd
                </a>
            </div>
        </div>
        @endif
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900">Statystyki Systemu</h3>
                    <p class="text-sm text-gray-600">Zaawansowane statystyki i analiza danych</p>
                </div>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Użytkownicy:</span>
                    <span class="font-medium">{{ number_format($stats['total_patients'] + $stats['total_doctors'] + 1) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Dokumenty dzisiaj:</span>
                    <span class="font-medium text-purple-600">{{ number_format($stats['documents_today']) }}</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="{{ route('reports.statistics') }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Podgląd
                </a>
                <button onclick="window.open('{{ route('reports.statistics', ['download' => 'pdf']) }}', '_blank')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    PDF
                </button>
            </div>
        </div>
        @endif
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumenty według typu</h3>
            <div class="space-y-3">
                @php
                    $typeLabels = [
                        'general' => 'Ogólne',
                        'diagnosis' => 'Diagnozy',
                        'treatment' => 'Leczenie',
                        'examination' => 'Badania',
                        'prescription' => 'Recepty'
                    ];
                    $colors = ['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-red-500'];
                    $totalDocs = array_sum($documentsByType);
                @endphp

                @foreach($typeLabels as $type => $label)
                    @php
                        $count = $documentsByType[$type] ?? 0;
                        $percentage = $totalDocs > 0 ? round(($count / $totalDocs) * 100, 1) : 0;
                        $color = $colors[array_search($type, array_keys($typeLabels))] ?? 'bg-gray-500';
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 {{ $color }} rounded-full mr-3"></div>
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
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktywność w tym miesiącu</h3>
            <div class="space-y-4">
                @foreach($documentsPerDay->take(7) as $day)
                    @php
                        $maxCount = $documentsPerDay->max('count');
                        $percentage = $maxCount > 0 ? ($day->count / $maxCount) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ \Carbon\Carbon::parse($day->date)->format('d.m') }}</span>
                            <span class="font-medium">{{ $day->count }} dok.</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Płatności metodą</h3>
            <div class="space-y-3">
                @php
                    $methodLabels = [
                        'stripe' => ['label' => 'Online (Stripe)', 'icon' => 'credit-card', 'color' => 'bg-blue-500'],
                        'cash' => ['label' => 'Gotówka', 'icon' => 'money-bill-wave', 'color' => 'bg-green-500']
                    ];
                @endphp

                @foreach($paymentsByMethod as $payment)
                    @php
                        $method = $methodLabels[$payment->payment_method] ?? ['label' => $payment->payment_method, 'icon' => 'credit-card', 'color' => 'bg-gray-500'];
                        $totalAmount = $paymentsByMethod->sum('total');
                        $percentage = $totalAmount > 0 ? round(($payment->total / $totalAmount) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 {{ $method['color'] }} rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">{{ $method['label'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900">{{ number_format($payment->total, 2, ',', ' ') }} PLN</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $percentage }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    body { -webkit-print-color-adjust: exact; }
}
</style>
@endpush
@endsection
