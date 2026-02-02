@extends('layouts.app')

@section('title', 'Raport Dokumentów Medycznych')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header with Actions -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Raport Dokumentów Medycznych</h1>
            <p class="text-gray-600">Analiza dokumentacji medycznej w systemie</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 mt-4 lg:mt-0">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Drukuj
            </button>

            <button onclick="downloadPDF()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 no-print">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtry raportu</h3>
        <form method="GET" action="{{ route('reports.documents') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @if(auth()->user()->isAdmin())
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Doktor</label>
                <select name="doctor_id" id="doctor_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    <option value="">Wszyscy doktorzy</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Pacjent</label>
                <select name="patient_id" id="patient_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    <option value="">Wszyscy pacjenci</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Typ dokumentu</label>
                <select name="type" id="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    <option value="">Wszystkie typy</option>
                    <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>Ogólny</option>
                    <option value="diagnosis" {{ request('type') == 'diagnosis' ? 'selected' : '' }}>Diagnoza</option>
                    <option value="treatment" {{ request('type') == 'treatment' ? 'selected' : '' }}>Leczenie</option>
                    <option value="examination" {{ request('type') == 'examination' ? 'selected' : '' }}>Badanie</option>
                    <option value="prescription" {{ request('type') == 'prescription' ? 'selected' : '' }}>Recepta</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    <option value="">Wszystkie statusy</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Szkic</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Ukończony</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Zarchiwizowany</option>
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data od</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data do</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
            </div>

            <div class="xl:col-span-2 flex justify-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtruj
                </button>

                <a href="{{ route('reports.documents') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Resetuj
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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
                    <p class="text-sm font-medium text-gray-600">Łącznie dokumentów</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($reportStats['total_documents']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ukończone</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ number_format($reportStats['by_status']['completed'] ?? 0) }}</p>
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
                    <p class="text-2xl font-semibold text-yellow-600">{{ number_format($reportStats['by_status']['draft'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Z załącznikami</p>
                    <p class="text-2xl font-semibold text-purple-600">{{ number_format($reportStats['with_files']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Header for Print -->
    <div class="print-only mb-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Raport Dokumentów Medycznych</h1>
            <p class="text-gray-600">Wygenerowano: {{ now()->format('d.m.Y H:i') }}</p>
            @if(request()->hasAny(['patient_id', 'doctor_id', 'type', 'status', 'date_from', 'date_to']))
                <div class="mt-2 text-sm text-gray-500">
                    <strong>Filtry:</strong>
                    @if(request('type'))
                        Typ: {{ ['general' => 'Ogólny', 'diagnosis' => 'Diagnoza', 'treatment' => 'Leczenie', 'examination' => 'Badanie', 'prescription' => 'Recepta'][request('type')] }};
                    @endif
                    @if(request('status'))
                        Status: {{ ['draft' => 'Szkic', 'completed' => 'Ukończony', 'archived' => 'Zarchiwizowany'][request('status')] }};
                    @endif
                    @if(request('date_from'))
                        Od: {{ request('date_from') }};
                    @endif
                    @if(request('date_to'))
                        Do: {{ request('date_to') }};
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 no-print">
            <h3 class="text-lg font-medium text-gray-900">
                Lista dokumentów ({{ number_format($documents->count()) }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tytuł dokumentu
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pacjent
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Doktor
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Typ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data dokumentu
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Załącznik
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documents as $document)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $document->title }}</div>
                                @if($document->content)
                                    <div class="text-sm text-gray-500">{{ Str::limit($document->content, 100) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $document->patient->avatar_url }}" alt="{{ $document->patient->full_name }}">
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $document->patient->full_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $document->doctor->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeClasses = [
                                        'general' => 'bg-gray-100 text-gray-800',
                                        'diagnosis' => 'bg-red-100 text-red-800',
                                        'treatment' => 'bg-blue-100 text-blue-800',
                                        'examination' => 'bg-yellow-100 text-yellow-800',
                                        'prescription' => 'bg-green-100 text-green-800'
                                    ];
                                    $typeClass = $typeClasses[$document->type] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeClass }}">
                                    {{ $document->type_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'archived' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusClass = $statusClasses[$document->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    {{ $document->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $document->document_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($document->hasFile())
                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Brak dokumentów do wyświetlenia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Documents by Type -->
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
                @endphp

                @foreach($typeLabels as $type => $label)
                    @php
                        $count = $reportStats['by_type'][$type] ?? 0;
                        $percentage = $reportStats['total_documents'] > 0 ? round(($count / $reportStats['total_documents']) * 100, 1) : 0;
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

        <!-- Documents by Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumenty według statusu</h3>
            <div class="space-y-3">
                @php
                    $statusLabels = [
                        'draft' => 'Szkice',
                        'completed' => 'Ukończone',
                        'archived' => 'Zarchiwizowane'
                    ];
                    $statusColors = [
                        'draft' => '#F59E0B',
                        'completed' => '#10B981',
                        'archived' => '#6B7280'
                    ];
                @endphp

                @foreach($statusLabels as $status => $label)
                    @php
                        $count = $reportStats['by_status'][$status] ?? 0;
                        $percentage = $reportStats['total_documents'] > 0 ? round(($count / $reportStats['total_documents']) * 100, 1) : 0;
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

    <!-- Summary Section -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie raportu</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($reportStats['total_documents']) }}</div>
                <div class="text-sm text-gray-600">Łączna liczba dokumentów</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($reportStats['by_status']['completed'] ?? 0) }}</div>
                <div class="text-sm text-gray-600">Dokumenty ukończone</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ number_format($reportStats['by_status']['draft'] ?? 0) }}</div>
                <div class="text-sm text-gray-600">Dokumenty w szkicach</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($reportStats['with_files']) }}</div>
                <div class="text-sm text-gray-600">Z załącznikami</div>
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

/* Zapewnij że kolorowe kółka są zawsze widoczne */
.color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 12px;
    flex-shrink: 0;
}

/* Backup styles for color dots if Tailwind fails */
.chart-dot {
    min-width: 12px;
    min-height: 12px;
    border-radius: 9999px;
    margin-right: 0.75rem;
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
