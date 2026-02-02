@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto">
        <div class="mb-4 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-calendar-check mr-3"></i>{{ $isDoctor ? 'Moje Rezerwacje' : 'Wszystkie Rezerwacje' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $isDoctor ? 'Lista wizyt zarezerwowanych u Ciebie' : 'Zarządzaj wszystkimi rezerwacjami w systemie' }}</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Filtry -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-filter mr-2"></i>Filtry
                </h2>
                <button type="button" id="toggleFilters" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    <i class="fas fa-chevron-down" id="filterChevron"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('reservation.my-list') }}" id="filterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Wyszukiwanie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Szukaj po nazwie</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Wpisz tytuł rezerwacji..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Wszystkie</option>
                            <option value="scheduled" {{ ($filters['status'] ?? '') === 'scheduled' ? 'selected' : '' }}>Zaplanowane</option>
                            <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Potwierdzone</option>
                            <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>W trakcie</option>
                            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Ukończone</option>
                            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Anulowane</option>
                        </select>
                    </div>

                    <!-- Typ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Typ wizyty</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="all" {{ ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' }}>Wszystkie</option>
                            <option value="fizjoterapia" {{ ($filters['type'] ?? '') === 'fizjoterapia' ? 'selected' : '' }}>Fizjoterapia</option>
                            <option value="konsultacja" {{ ($filters['type'] ?? '') === 'konsultacja' ? 'selected' : '' }}>Konsultacja</option>
                            <option value="masaz" {{ ($filters['type'] ?? '') === 'masaz' ? 'selected' : '' }}>Masaż</option>
                            <option value="neurorehabilitacja" {{ ($filters['type'] ?? '') === 'neurorehabilitacja' ? 'selected' : '' }}>Neurorehabilitacja</option>
                            <option value="kontrola" {{ ($filters['type'] ?? '') === 'kontrola' ? 'selected' : '' }}>Wizyta kontrolna</option>
                        </select>
                    </div>

                    <!-- Data od -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data od</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Data do -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data do</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    @if($isAdmin)
                    <!-- Pacjent (tylko dla admina) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pacjent</label>
                        <select name="patient_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="all" {{ ($filters['patient_id'] ?? 'all') === 'all' ? 'selected' : '' }}>Wszyscy pacjenci</option>
                            @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ ($filters['patient_id'] ?? '') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lekarz (tylko dla admina) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fizjoterapeuta</label>
                        <select name="doctor_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="all" {{ ($filters['doctor_id'] ?? 'all') === 'all' ? 'selected' : '' }}>Wszyscy fizjoterapeuci</option>
                            @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ ($filters['doctor_id'] ?? '') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Sortowanie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sortuj według</label>
                        <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="start_time" {{ ($filters['sort_by'] ?? 'start_time') === 'start_time' ? 'selected' : '' }}>Data wizyty</option>
                            <option value="title" {{ ($filters['sort_by'] ?? '') === 'title' ? 'selected' : '' }}>Tytuł</option>
                            <option value="status" {{ ($filters['sort_by'] ?? '') === 'status' ? 'selected' : '' }}>Status</option>
                            <option value="type" {{ ($filters['sort_by'] ?? '') === 'type' ? 'selected' : '' }}>Typ</option>
                        </select>
                    </div>

                    <!-- Kolejność sortowania -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kolejność</label>
                        <select name="sort_order" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="desc" {{ ($filters['sort_order'] ?? 'desc') === 'desc' ? 'selected' : '' }}>Malejąco</option>
                            <option value="asc" {{ ($filters['sort_order'] ?? '') === 'asc' ? 'selected' : '' }}>Rosnąco</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('reservation.my-list') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        <i class="fas fa-times mr-2"></i>Wyczyść
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Filtruj
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Nazwa Rezerwacji</th>
                            @if($isAdmin)
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Pacjent</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Fizjoterapeuta</th>
                            @else
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Pacjent</th>
                            @endif
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Data i Czas</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Typ</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->title }}</p>
                                    @if($appointment->notes)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($appointment->notes, 50) }}</p>
                                    @endif
                                </div>
                            </td>
                            @if($isAdmin)
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->patient?->full_name ?? 'Brak pacjenta' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->patient?->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->doctor?->full_name ?? 'Nieprzypisany' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->doctor?->email ?? '-' }}</p>
                                </div>
                            </td>
                            @else
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->patient?->full_name ?? 'Brak pacjenta' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->patient?->email }}</p>
                                </div>
                            </td>
                            @endif
                            <td class="px-6 py-4 text-sm">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->start_time)->format('d.m.Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    @switch($appointment->type)
                                        @case('fizjoterapia')
                                            <i class="fas fa-hands-helping mr-1"></i>Fizjoterapia
                                            @break
                                        @case('konsultacja')
                                            <i class="fas fa-stethoscope mr-1"></i>Konsultacja
                                            @break
                                        @case('masaz')
                                            <i class="fas fa-spa mr-1"></i>Masaż
                                            @break
                                        @case('neurorehabilitacja')
                                            <i class="fas fa-brain mr-1"></i>Neurorehabilitacja
                                            @break
                                        @case('kontrola')
                                            <i class="fas fa-clipboard-check mr-1"></i>Wizyta kontrolna
                                            @break
                                        @default
                                            {{ ucfirst($appointment->type) }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @switch($appointment->status)
                                    @case('scheduled')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            <i class="fas fa-calendar-alt mr-1"></i>Zaplanowana
                                        </span>
                                        @break
                                    @case('confirmed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            <i class="fas fa-check-circle mr-1"></i>Potwierdzona
                                        </span>
                                        @break
                                    @case('in_progress')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                            <i class="fas fa-hourglass-start mr-1"></i>W trakcie
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            <i class="fas fa-check mr-1"></i>Ukończona
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                            <i class="fas fa-times-circle mr-1"></i>Anulowana
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('reservation.show', $appointment->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition" title="Pokaż szczegóły">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                    <a href="{{ route('calendar.index') }}?edit={{ $appointment->id }}" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition" title="Edytuj w kalendarzu">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-4 block"></i>
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ $isDoctor ? 'Brak rezerwacji u Ciebie' : 'Brak rezerwacji w systemie' }}
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($appointments->hasPages())
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                {{ $appointments->links() }}
            </div>
            @endif
        </div>

        <!-- Statystyka -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Łącznie Rezerwacji</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $appointments->total() }}</p>
                    </div>
                    <div class="text-3xl text-blue-500 opacity-20">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Zaplanowanych</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $appointments->getCollection()->where('status', 'scheduled')->count() }}
                        </p>
                    </div>
                    <div class="text-3xl text-blue-500 opacity-20">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Potwierdzone</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $appointments->getCollection()->where('status', 'confirmed')->count() }}
                        </p>
                    </div>
                    <div class="text-3xl text-green-500 opacity-20">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Ukończone</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $appointments->getCollection()->where('status', 'completed')->count() }}
                        </p>
                    </div>
                    <div class="text-3xl text-green-500 opacity-20">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover\:bg-gray-50:hover {
    background-color: rgb(249, 250, 251);
}

.dark .dark\:hover\:bg-gray-700\/50:hover {
    background-color: rgba(55, 65, 81, 0.5);
}

@media (max-width: 640px) {
    .overflow-x-auto {
        font-size: 0.875rem;
    }

    .px-6 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
}

#filterForm.collapsed {
    display: none;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggleFilters');
    const filterForm = document.getElementById('filterForm');
    const filterChevron = document.getElementById('filterChevron');

    // Sprawdź czy są aktywne filtry
    const hasActiveFilters = {{ (isset($filters) && count(array_filter($filters, function($v) { return $v !== null && $v !== '' && $v !== 'all'; })) > 0) ? 'true' : 'false' }};

    // Jeśli brak aktywnych filtrów, domyślnie zwiń panel
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
