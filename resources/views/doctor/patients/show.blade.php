{{-- resources/views/doctor/patients/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Profil pacjenta</h1>
                <p class="text-gray-600 dark:text-gray-400">Szczegółowe informacje o pacjencie {{ $patient->full_name }}.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
                <a href="{{ route('doctor.patients.edit', $patient) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edytuj
                </a>
                <a href="{{ route('doctor.patients.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Powrót do listy
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Patient Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-8">
                    <div class="flex items-center">
                        <div class="h-24 w-24 rounded-full overflow-hidden bg-white bg-opacity-20 border-4 border-white border-opacity-30">
                            <img src="{{ $patient->avatar_url }}"
                                 alt="Avatar pacjenta {{ $patient->full_name }}"
                                 class="h-full w-full object-cover">
                        </div>
                        <div class="ml-6">
                            <h2 class="text-2xl font-bold text-white">{{ $patient->full_name }}</h2>
                            <p class="text-green-100 text-lg">{{ $patient->email }}</p>
                            <div class="flex items-center mt-3 space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                    <i class="fas fa-user mr-2"></i>
                                    Pacjent
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                    <i class="fas fa-circle mr-2 {{ $patient->is_active ? 'text-green-300' : 'text-red-300' }}"></i>
                                    {{ $patient->is_active ? 'Aktywny' : 'Nieaktywny' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-address-book mr-2 text-green-600 dark:text-green-400"></i>
                        Informacje kontaktowe
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</label>
                            <div class="flex items-center">
                                <p class="text-gray-900 dark:text-white">{{ $patient->email }}</p>
                                @if($patient->email_verified_at)
                                    <i class="fas fa-check-circle text-green-500 dark:text-green-400 ml-2" title="Email zweryfikowany"></i>
                                @else
                                    <i class="fas fa-exclamation-circle text-red-500 dark:text-red-400 ml-2" title="Email niezweryfikowany"></i>
                                @endif
                            </div>
                        </div>

                        @if($patient->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Telefon</label>
                            <p class="text-gray-900 dark:text-white">{{ $patient->phone }}</p>
                        </div>
                        @endif

                        @if($patient->date_of_birth)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Data urodzenia</label>
                            <p class="text-gray-900 dark:text-white">
                                {{ $patient->date_of_birth->format('d.m.Y') }}
                                <span class="text-gray-500 dark:text-gray-400 text-sm">({{ $patient->date_of_birth->age }} lat)</span>
                            </p>
                        </div>
                        @endif

                        @if($patient->gender)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Płeć</label>
                            <p class="text-gray-900 dark:text-white">
                                @switch($patient->gender)
                                    @case('male')
                                        Mężczyzna
                                        @break
                                    @case('female')
                                        Kobieta
                                        @break
                                    @case('other')
                                        Inna
                                        @break
                                    @default
                                        {{ $patient->gender }}
                                @endswitch
                            </p>
                        </div>
                        @endif

                        @if($patient->address)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Adres</label>
                            <p class="text-gray-900 dark:text-white">{{ $patient->address }}</p>
                        </div>
                        @endif

                        @if($patient->emergency_contact)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Kontakt awaryjny</label>
                            <p class="text-gray-900 dark:text-white">{{ $patient->emergency_contact }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Medical History -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-file-medical mr-2"></i>
                        Historia medyczna
                    </h3>
                </div>
                <div class="p-6">
                    @if($patient->medical_history && is_array($patient->medical_history) && count($patient->medical_history) > 0)
                        <ul class="space-y-2">
                            @foreach($patient->medical_history as $item)
                                <li class="flex items-start">
                                    <i class="fas fa-circle text-purple-500 dark:text-purple-400 text-xs mt-2 mr-3"></i>
                                    <span class="text-gray-900 dark:text-white">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">Brak informacji w historii medycznej.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Medical Documents -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-file-medical-alt mr-2"></i>
                        Ostatnie dokumenty medyczne
                    </h3>
                    <a href="{{ route('medical-documents.create', ['patient_id' => $patient->id]) }}" class="bg-white bg-opacity-20 text-white px-3 py-1 rounded-lg text-sm hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-plus mr-1"></i>
                        Nowy dokument
                    </a>
                </div>
                <div class="p-6">
                    @if($patient->patientDocuments->count() > 0)
                        <div class="space-y-4">
                            @foreach($patient->patientDocuments as $document)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-file-medical text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $document->title }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $document->type_display }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            {{ $document->document_date->format('d.m.Y') }}
                                            - {{ $document->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $document->status === 'completed' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : '' }}
                                        {{ $document->status === 'draft' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' : '' }}
                                        {{ $document->status === 'archived' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}">
                                        {{ $document->status_display }}
                                    </span>
                                    @if($document->hasFile())
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            Załącznik
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($patient->patientDocuments->count() >= 10)
                        <div class="text-center mt-6">
                            <a href="{{ route('medical-documents.index', ['patient_id' => $patient->id]) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                                Zobacz wszystkie dokumenty <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-file-medical-alt text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Brak dokumentów medycznych w systemie.</p>
                            <a href="{{ route('medical-documents.create', ['patient_id' => $patient->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Dodaj pierwszy dokument
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Statistics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statystyki
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Łączna liczba dokumentów</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $patientStats['total_documents'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Ukończone dokumenty</span>
                        <span class="text-xl font-semibold text-green-600 dark:text-green-400">{{ $patientStats['completed_documents'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Szkice dokumentów</span>
                        <span class="text-xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $patientStats['draft_documents'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Dokumenty w tym miesiącu</span>
                        <span class="text-xl font-semibold text-blue-600 dark:text-blue-400">{{ $patientStats['documents_this_month'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">W systemie od</span>
                        <div class="text-right">
                            <span class="text-xl font-semibold text-orange-600 dark:text-orange-400">
                                {{ $patientStats['account_age'] == 1 ? '1 dzień' : $patientStats['account_age'] . ' dni' }}
                            </span>
                        </div>
                    </div>

                    @if($patientStats['last_document'])
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Ostatni dokument</span>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $patientStats['last_document']->document_date->format('d.m.Y') }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-500">
                                {{ $patientStats['last_document']->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Szybkie akcje
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('medical-documents.create', ['patient_id' => $patient->id]) }}"
                       class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-file-medical-alt text-purple-600 dark:text-purple-400 mr-3"></i>
                            <span class="text-gray-900 dark:text-white">Nowa dokumentacja</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                    </a>

                    <a href="{{ route('doctor.patients.edit', $patient) }}"
                       class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-edit text-green-600 dark:text-green-400 mr-3"></i>
                            <span class="text-gray-900 dark:text-white">Edytuj dane pacjenta</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                    </a>

                    <a href="{{ route('medical-documents.index', ['patient_id' => $patient->id]) }}"
                       class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-folder-open text-blue-600 dark:text-blue-400 mr-3"></i>
                            <span class="text-gray-900 dark:text-white">Zobacz wszystkie dokumenty</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                    </a>

                    <a href="{{ route('chat.index') }}"
                       class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-yellow-600 dark:text-yellow-400 mr-3"></i>
                            <span class="text-gray-900 dark:text-white">Wyślij wiadomość</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                    </a>

                    <button onclick="deletePatient({{ $patient->id }}, '{{ $patient->full_name }}')"
                            class="w-full flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-trash text-red-600 dark:text-red-400 mr-3"></i>
                            <span class="text-gray-900 dark:text-white">Usuń pacjenta</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                    </button>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-500 to-gray-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informacje o koncie
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Data utworzenia</label>
                        <p class="text-gray-900 dark:text-white">{{ $patient->created_at->format('d.m.Y H:i') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500">{{ $patient->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Ostatnia aktualizacja</label>
                        <p class="text-gray-900 dark:text-white">{{ $patient->updated_at->format('d.m.Y H:i') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500">{{ $patient->updated_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status weryfikacji email</label>
                        @if($patient->email_verified_at)
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 dark:text-green-400 mr-2"></i>
                                <div>
                                    <p class="text-green-600 dark:text-green-400 font-medium">Zweryfikowany</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500">{{ $patient->email_verified_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 dark:text-red-400 mr-2"></i>
                                <p class="text-red-600 dark:text-red-400 font-medium">Niezweryfikowany</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status konta</label>
                        <div class="flex items-center">
                            @if($patient->is_active)
                                <i class="fas fa-circle text-green-500 dark:text-green-400 mr-2"></i>
                                <span class="text-green-600 dark:text-green-400 font-medium">Aktywny</span>
                            @else
                                <i class="fas fa-circle text-red-500 dark:text-red-400 mr-2"></i>
                                <span class="text-red-600 dark:text-red-400 font-medium">Nieaktywny</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
