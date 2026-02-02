@extends('layouts.app')

@section('title', 'Raport Pacjentów')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Raport Pacjentów</h1>
            <p class="text-gray-600">Lista wszystkich pacjentów z podstawowymi informacjami</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 mt-4 lg:mt-0">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Drukuj
            </button>

            <button onclick="downloadPDF()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtry raportu</h3>
        <form method="GET" action="{{ route('reports.patients') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Wszyscy</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktywni</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nieaktywni</option>
                </select>
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Płeć</label>
                <select name="gender" id="gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Wszystkie</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Mężczyzna</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Kobieta</option>
                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Inna</option>
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Rejestracja od</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Rejestracja do</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>

            <div class="lg:col-span-4 flex justify-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtruj
                </button>

                <a href="{{ route('reports.patients') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Resetuj
                </a>
            </div>
        </form>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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
                    <p class="text-sm font-medium text-gray-600">Wszyscy pacjenci</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($reportStats['total_patients']) }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Aktywni</p>
                    <p class="text-2xl font-semibold text-green-600">{{ number_format($reportStats['active_patients']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Z dokumentacją</p>
                    <p class="text-2xl font-semibold text-purple-600">{{ number_format($reportStats['with_documents']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4V7a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v4a2 2 0 002 2h4a2 2 0 002-2v-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Średni wiek</p>
                    <p class="text-2xl font-semibold text-orange-600">{{ round($reportStats['average_age'] ?? 0) }} lat</p>
                </div>
            </div>
        </div>
    </div>


    <div class="print-only mb-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Raport Pacjentów</h1>
            <p class="text-gray-600">Wygenerowano: {{ now()->format('d.m.Y H:i') }}</p>
            @if(request()->hasAny(['status', 'gender', 'date_from', 'date_to']))
                <div class="mt-2 text-sm text-gray-500">
                    <strong>Filtry:</strong>
                    @if(request('status'))
                        Status: {{ request('status') == 'active' ? 'Aktywni' : 'Nieaktywni' }};
                    @endif
                    @if(request('gender'))
                        Płeć: {{ ['male' => 'Mężczyzna', 'female' => 'Kobieta', 'other' => 'Inna'][request('gender')] }};
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


    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 no-print">
            <h3 class="text-lg font-medium text-gray-900">
                Lista pacjentów ({{ number_format($patients->count()) }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pacjent
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kontakt
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Informacje
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dokumenty
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data rejestracji
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $patient->avatar_url }}" alt="{{ $patient->full_name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $patient->full_name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $patient->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $patient->email }}</div>
                                @if($patient->phone)
                                    <div class="text-sm text-gray-500">{{ $patient->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($patient->date_of_birth)
                                        Wiek: {{ $patient->date_of_birth->age }} lat
                                    @else
                                        Brak daty urodzenia
                                    @endif
                                </div>
                                @if($patient->gender)
                                    <div class="text-sm text-gray-500">
                                        {{ ['male' => 'Mężczyzna', 'female' => 'Kobieta', 'other' => 'Inna'][$patient->gender] ?? $patient->gender }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $patient->patientDocuments->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($patient->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Aktywny
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Nieaktywny
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $patient->created_at->format('d.m.Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Brak pacjentów do wyświetlenia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie raportu</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($reportStats['total_patients']) }}</div>
                <div class="text-sm text-gray-600">Łączna liczba pacjentów</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($reportStats['active_patients']) }}</div>
                <div class="text-sm text-gray-600">Aktywni pacjenci</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($reportStats['with_documents']) }}</div>
                <div class="text-sm text-gray-600">Z dokumentacją medyczną</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ round($reportStats['average_age'] ?? 0) }}</div>
                <div class="text-sm text-gray-600">Średni wiek (lata)</div>
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
