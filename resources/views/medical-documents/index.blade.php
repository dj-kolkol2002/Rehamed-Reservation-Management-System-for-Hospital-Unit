{{-- resources/views/medical-documents/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Dokumentacja medyczna</h1>
                <p class="text-gray-600 text-sm sm:text-base">
                    @if(Auth::user()->role === 'user')
                        Przeglądaj swoją dokumentację medyczną
                    @elseif(Auth::user()->role === 'doctor')
                        Zarządzaj dokumentacją swoich pacjentów
                    @else
                        Zarządzaj dokumentacją medyczną wszystkich pacjentów
                    @endif
                </p>
            </div>

            @if(Auth::user()->role !== 'user')
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="{{ route('medical-documents.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>
                    <span class="hidden sm:inline">Dodaj dokument</span>
                    <span class="sm:hidden">Dodaj</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl p-3 sm:p-6 shadow-lg border border-gray-100 card-hover">
            <div class="flex items-center">
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3 sm:mr-4">
                    <i class="fas fa-file-medical text-blue-600 text-sm sm:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Wszystkie</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3 sm:p-6 shadow-lg border border-gray-100 card-hover">
            <div class="flex items-center">
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-3 sm:mr-4">
                    <i class="fas fa-edit text-yellow-600 text-sm sm:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Szkice</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3 sm:p-6 shadow-lg border border-gray-100 card-hover">
            <div class="flex items-center">
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center mr-3 sm:mr-4">
                    <i class="fas fa-check-circle text-green-600 text-sm sm:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Ukończone</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3 sm:p-6 shadow-lg border border-gray-100 card-hover">
            <div class="flex items-center">
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-purple-100 rounded-full flex items-center justify-center mr-3 sm:mr-4">
                    <i class="fas fa-calendar text-purple-600 text-sm sm:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Ten miesiąc</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg mb-6 sm:mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-filter mr-2"></i>Filtry
            </h2>
            <button type="button" id="toggleFilters" class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-chevron-down" id="filterChevron"></i>
            </button>
        </div>
        <form method="GET" action="{{ route('medical-documents.index') }}" id="filterForm" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-1 xl:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Szukaj</label>
                    <input type="text"
                           name="search"
                           value="{{ $request->search }}"
                           placeholder="Tytuł, treść, notatki..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                @if(Auth::user()->role !== 'user' && $patients->count() > 0)
                <!-- Patient Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pacjent</label>
                    <select name="patient_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Wszyscy pacjenci</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ $request->patient_id == $patient->id ? 'selected' : '' }}>
                                {{ $patient->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(Auth::user()->role === 'admin' && $doctors->count() > 0)
                <!-- Doctor Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Doktor</label>
                    <select name="doctor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Wszyscy doktorzy</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ $request->doctor_id == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typ</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Wszystkie typy</option>
                        <option value="general" {{ $request->type == 'general' ? 'selected' : '' }}>Ogólny</option>
                        <option value="diagnosis" {{ $request->type == 'diagnosis' ? 'selected' : '' }}>Diagnoza</option>
                        <option value="treatment" {{ $request->type == 'treatment' ? 'selected' : '' }}>Leczenie</option>
                        <option value="examination" {{ $request->type == 'examination' ? 'selected' : '' }}>Badanie</option>
                        <option value="prescription" {{ $request->type == 'prescription' ? 'selected' : '' }}>Recepta</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Wszystkie statusy</option>
                        <option value="draft" {{ $request->status == 'draft' ? 'selected' : '' }}>Szkic</option>
                        <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Ukończony</option>
                        <option value="archived" {{ $request->status == 'archived' ? 'selected' : '' }}>Zarchiwizowany</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data od</label>
                    <input type="date"
                           name="date_from"
                           value="{{ $request->date_from }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data do</label>
                    <input type="date"
                           name="date_to"
                           value="{{ $request->date_to }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4 border-t">
                <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Szukaj
                </button>

                <a href="{{ route('medical-documents.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść
                </a>
            </div>
        </form>
    </div>

    <!-- Documents List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if($documents->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('medical-documents.index', array_merge($request->all(), ['sort' => 'title', 'direction' => $request->sort === 'title' && $request->direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Dokument</span>
                                    @if($request->sort === 'title')
                                        <i class="fas fa-sort-{{ $request->direction === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            @if(Auth::user()->role !== 'user')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pacjent
                            </th>
                            @endif
                            @if(Auth::user()->role === 'admin')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Doktor
                            </th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('medical-documents.index', array_merge($request->all(), ['sort' => 'type', 'direction' => $request->sort === 'type' && $request->direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Typ</span>
                                    @if($request->sort === 'type')
                                        <i class="fas fa-sort-{{ $request->direction === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('medical-documents.index', array_merge($request->all(), ['sort' => 'status', 'direction' => $request->sort === 'status' && $request->direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Status</span>
                                    @if($request->sort === 'status')
                                        <i class="fas fa-sort-{{ $request->direction === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('medical-documents.index', array_merge($request->all(), ['sort' => 'document_date', 'direction' => $request->sort === 'document_date' && $request->direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Data</span>
                                    @if($request->sort === 'document_date')
                                        <i class="fas fa-sort-{{ $request->direction === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Akcje
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($documents as $document)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-{{ $document->status_color }}-100 flex items-center justify-center">
                                            <i class="fas fa-file-medical text-{{ $document->status_color }}-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $document->title }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($document->content, 50) }}</div>
                                        @if($document->hasFile())
                                            <div class="flex items-center mt-1">
                                                <i class="fas fa-paperclip text-gray-400 text-xs mr-1"></i>
                                                <span class="text-xs text-gray-500">{{ $document->file_name }}</span>
                                            </div>
                                        @endif
                                        @if($document->is_private)
                                            <div class="flex items-center mt-1">
                                                <i class="fas fa-lock text-red-500 text-xs mr-1"></i>
                                                <span class="text-xs text-red-600">Prywatny</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @if(Auth::user()->role !== 'user')
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full object-cover"
                                             src="{{ $document->patient->avatar_url }}"
                                             alt="Avatar {{ $document->patient->full_name }}">
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $document->patient->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $document->patient->email }}</div>
                                    </div>
                                </div>
                            </td>
                            @endif
                            @if(Auth::user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full object-cover"
                                             src="{{ $document->doctor->avatar_url }}"
                                             alt="Avatar {{ $document->doctor->full_name }}">
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $document->doctor->full_name }}</div>
                                    </div>
                                </div>
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $document->type_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $document->status_color }}-100 text-{{ $document->status_color }}-800">
                                    {{ $document->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $document->document_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('medical-documents.show', $document) }}"
                                       class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($document->canBeEditedBy(Auth::user()))
                                    <a href="{{ route('medical-documents.edit', $document) }}"
                                       class="text-green-600 hover:text-green-900 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form method="POST"
                                          action="{{ route('medical-documents.destroy', $document) }}"
                                          class="inline-block"
                                          onsubmit="return confirm('Czy na pewno chcesz usunąć ten dokument?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if($document->hasFile())
                                    <a href="{{ route('medical-documents.download', $document) }}"
                                       class="text-purple-600 hover:text-purple-900 transition-colors">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden">
                @foreach($documents as $document)
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-900 mb-1">{{ $document->title }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ Str::limit($document->content, 60) }}</p>

                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $document->type_display }}
                                </span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $document->status_color }}-100 text-{{ $document->status_color }}-800">
                                    {{ $document->status_display }}
                                </span>
                                @if($document->is_private)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Prywatny
                                </span>
                                @endif
                            </div>

                            @if(!Auth::user()->isPatient())
                            <div class="flex items-center mb-2">
                                <img class="h-6 w-6 rounded-full object-cover mr-2"
                                     src="{{ $document->patient->avatar_url }}"
                                     alt="Avatar {{ $document->patient->full_name }}">
                                <span class="text-xs text-gray-600">{{ $document->patient->full_name }}</span>
                            </div>
                            @endif

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $document->document_date->format('d.m.Y') }}</span>
                                @if($document->hasFile())
                                <span class="flex items-center">
                                    <i class="fas fa-paperclip mr-1"></i>
                                    Załącznik
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('medical-documents.show', $document) }}"
                               class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($document->canBeEditedBy(Auth::user()))
                            <a href="{{ route('medical-documents.edit', $document) }}"
                               class="text-green-600 hover:text-green-900 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif

                            @if($document->hasFile())
                            <a href="{{ route('medical-documents.download', $document) }}"
                               class="text-purple-600 hover:text-purple-900 transition-colors">
                                <i class="fas fa-download"></i>
                            </a>
                            @endif
                        </div>

                        @if($document->canBeEditedBy(Auth::user()))
                        <form method="POST"
                              action="{{ route('medical-documents.destroy', $document) }}"
                              class="inline-block"
                              onsubmit="return confirm('Czy na pewno chcesz usunąć ten dokument?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-900 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $documents->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <i class="fas fa-file-medical text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Brak dokumentów</h3>
                <p class="text-gray-500 mb-6">
                    @if(Auth::user()->role === 'user')
                        Nie masz jeszcze żadnych dokumentów medycznych.
                    @else
                        Nie znaleziono dokumentów spełniających kryteria wyszukiwania.
                    @endif
                </p>
                @if(Auth::user()->role !== 'user')
                <a href="{{ route('medical-documents.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj pierwszy dokument
                </a>
                @endif
            </div>
        @endif
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="success-toast">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="error-toast">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
    </div>
</div>
@endif
<style>
#filterForm.collapsed {
    display: none;
}
</style>
@endsection

@push('scripts')
<script>
// Auto-hide toast messages
document.addEventListener('DOMContentLoaded', function() {
    const successToast = document.getElementById('success-toast');
    const errorToast = document.getElementById('error-toast');

    if (successToast) {
        setTimeout(() => {
            successToast.style.opacity = '0';
            setTimeout(() => successToast.remove(), 300);
        }, 5000);
    }

    if (errorToast) {
        setTimeout(() => {
            errorToast.style.opacity = '0';
            setTimeout(() => errorToast.remove(), 300);
        }, 5000);
    }

    // Toggle filters
    const toggleButton = document.getElementById('toggleFilters');
    const filterForm = document.getElementById('filterForm');
    const filterChevron = document.getElementById('filterChevron');

    // Check if there are active filters
    const urlParams = new URLSearchParams(window.location.search);
    const hasActiveFilters = Array.from(urlParams.keys()).some(key =>
        key !== 'page' && urlParams.get(key) !== ''
    );

    // Collapse by default if no active filters
    if (!hasActiveFilters) {
        filterForm.classList.add('collapsed');
        filterChevron.classList.remove('fa-chevron-down');
        filterChevron.classList.add('fa-chevron-up');
    }

    toggleButton.addEventListener('click', function() {
        filterForm.classList.toggle('collapsed');

        if (filterForm.classList.contains('collapsed')) {
            filterChevron.classList.remove('fa-chevron-down');
            filterChevron.classList.add('fa-chevron-up');
        } else {
            filterChevron.classList.remove('fa-chevron-up');
            filterChevron.classList.add('fa-chevron-down');
        }
    });
});
</script>
@endpush
