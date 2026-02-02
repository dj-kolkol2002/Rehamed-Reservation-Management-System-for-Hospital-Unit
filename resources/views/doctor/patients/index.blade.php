{{-- resources/views/doctor/patients/index.blade.php --}}
@extends('layouts.app')

@section('styles')
<style>
/* Napraw problem z ukrywaniem ikon */
.table-actions {
    min-width: 120px;
}

.table-actions .fas {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    font-size: 14px;
}

.action-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.action-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Wymuś wyświetlanie przycisków */
button.action-icon {
    border: none;
    background: transparent;
    cursor: pointer;
}

button.action-icon:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}
</style>
@endsection

@section('content')
<div class="flex-1 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Moi pacjenci</h1>
                <p class="text-gray-600">Zarządzaj swoimi pacjentami i przeglądaj ich dane medyczne.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('doctor.patients.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-user-plus mr-2"></i>
                    Dodaj pacjenta
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Wszyscy</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Aktywni</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Nieaktywni</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Nowi w tym miesiącu</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['new_this_month'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-8">
        <form method="GET" action="{{ route('doctor.patients.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Wyszukaj</label>
                    <div class="relative">
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ $request->get('search') }}"
                               placeholder="Imię, nazwisko lub email..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Wszystkie statusy</option>
                        <option value="active" {{ $request->get('status') === 'active' ? 'selected' : '' }}>Aktywni</option>
                        <option value="inactive" {{ $request->get('status') === 'inactive' ? 'selected' : '' }}>Nieaktywni</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sortuj według</label>
                    <select id="sort" name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="created_at" {{ $request->get('sort') === 'created_at' ? 'selected' : '' }}>Data dodania</option>
                        <option value="firstname" {{ $request->get('sort') === 'firstname' ? 'selected' : '' }}>Imię</option>
                        <option value="lastname" {{ $request->get('sort') === 'lastname' ? 'selected' : '' }}>Nazwisko</option>
                        <option value="email" {{ $request->get('sort') === 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                    <input type="hidden" name="direction" value="{{ $request->get('direction', 'desc') }}">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filtruj
                </button>
                <a href="{{ route('doctor.patients.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść filtry
                </a>
            </div>
        </form>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('doctor.patients.index', array_merge(request()->query(), ['sort' => 'firstname', 'direction' => request('sort') === 'firstname' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center hover:text-gray-700">
                                Pacjent
                                @if(request('sort') === 'firstname')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('doctor.patients.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center hover:text-gray-700">
                                Email
                                @if(request('sort') === 'email')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wiek</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumenty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('doctor.patients.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center hover:text-gray-700">
                                Data dodania
                                @if(request('sort') === 'created_at')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Pacjent -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img src="{{ $patient->avatar_url }}"
                                         alt="Avatar pacjenta {{ $patient->full_name }}"
                                         class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $patient->full_name }}
                                    </div>
                                    @if($patient->phone)
                                    <div class="text-sm text-gray-500">
                                        {{ $patient->phone }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $patient->email }}</div>
                            @if($patient->email_verified_at)
                                <div class="text-xs text-green-600 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Zweryfikowany
                                </div>
                            @else
                                <div class="text-xs text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Niezweryfikowany
                                </div>
                            @endif
                        </td>

                        <!-- Wiek -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($patient->date_of_birth)
                                {{ $patient->date_of_birth->age }} lat
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Dokumenty -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                                $documentsCount = $patient->patientDocuments()->count();
                            @endphp
                            @if($documentsCount > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-file-medical mr-1"></i>
                                    {{ $documentsCount }} {{ $documentsCount == 1 ? 'dokument' : 'dokumentów' }}
                                </span>
                            @else
                                <span class="text-gray-400">Brak dokumentów</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($patient->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle mr-1 text-green-500"></i>
                                    Aktywny
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-circle mr-1 text-red-500"></i>
                                    Nieaktywny
                                </span>
                            @endif
                        </td>

                        <!-- Data dodania -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $patient->created_at->format('d.m.Y') }}
                            <div class="text-xs text-gray-500">
                                {{ $patient->created_at->format('H:i') }}
                            </div>
                        </td>

                        <!-- Akcje -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium table-actions">
                            <div style="display: flex !important; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('doctor.patients.show', $patient) }}"
                                   style="display: inline-flex !important; width: 32px; height: 32px; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; color: #4f46e5; text-decoration: none;"
                                   title="Zobacz szczegóły"
                                   onmouseover="this.style.background='#e5e7eb'"
                                   onmouseout="this.style.background='#f3f4f6'">
                                    <i class="fas fa-eye" style="display: inline-block !important; font-size: 14px;"></i>
                                </a>
                                <a href="{{ route('doctor.patients.edit', $patient) }}"
                                   style="display: inline-flex !important; width: 32px; height: 32px; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; color: #059669; text-decoration: none;"
                                   title="Edytuj"
                                   onmouseover="this.style.background='#e5e7eb'"
                                   onmouseout="this.style.background='#f3f4f6'">
                                    <i class="fas fa-edit" style="display: inline-block !important; font-size: 14px;"></i>
                                </a>
                                <button onclick="deletePatient({{ $patient->id }}, '{{ addslashes($patient->full_name) }}')"
                                        style="display: inline-flex !important; width: 32px; height: 32px; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; color: #dc2626; border: none; cursor: pointer;"
                                        title="Usuń"
                                        onmouseover="this.style.background='#e5e7eb'"
                                        onmouseout="this.style.background='#f3f4f6'">
                                    <i class="fas fa-trash" style="display: inline-block !important; font-size: 14px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Brak pacjentów</h3>
                                <p class="text-gray-500 mb-4">Nie znaleziono pacjentów spełniających kryteria wyszukiwania.</p>
                                <a href="{{ route('doctor.patients.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Dodaj pierwszego pacjenta
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($patients->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Pokazano {{ $patients->firstItem() }} - {{ $patients->lastItem() }} z {{ $patients->total() }} wyników
                </div>
                <div>
                    {{ $patients->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Potwierdź usunięcie</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="deleteMessage">
                    Czy na pewno chcesz usunąć tego pacjenta? Ta akcja nie może zostać cofnięta.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                        Usuń
                    </button>
                </form>
                <button onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Anuluj
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Delete patient
    function deletePatient(patientId, patientName) {
        document.getElementById('deleteMessage').textContent =
            `Czy na pewno chcesz usunąć pacjenta "${patientName}"? Ta akcja nie może zostać cofnięta.`;
        document.getElementById('deleteForm').action = `/doctor/patients/${patientId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal on outside click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endsection
