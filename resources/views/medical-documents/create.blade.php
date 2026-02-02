{{-- resources/views/medical-documents/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('medical-documents.index') }}"
               class="text-gray-600 hover:text-gray-900 transition-colors mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Nowy dokument medyczny</h1>
                <p class="text-gray-600 text-sm sm:text-base">Utwórz nowy dokument medyczny dla pacjenta</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <form method="POST" action="{{ route('medical-documents.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Patient Selection -->
                    <div class="lg:col-span-2">
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pacjent <span class="text-red-500">*</span>
                        </label>
                        <select name="patient_id"
                                id="patient_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('patient_id') @enderror"
                                required>
                            <option value="">Wybierz pacjenta</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}"
                                        {{ (old('patient_id', $selectedPatientId) == $patient->id) ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tytuł dokumentu <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title') }}"
                               placeholder="np. Konsultacja fizjoterapeutyczna"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') @enderror"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Typ dokumentu <span class="text-red-500">*</span>
                        </label>
                        <select name="type"
                                id="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('type') @enderror"
                                required>
                            <option value="">Wybierz typ</option>
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Ogólny</option>
                            <option value="diagnosis" {{ old('type') == 'diagnosis' ? 'selected' : '' }}>Diagnoza</option>
                            <option value="treatment" {{ old('type') == 'treatment' ? 'selected' : '' }}>Leczenie</option>
                            <option value="examination" {{ old('type') == 'examination' ? 'selected' : '' }}>Badanie</option>
                            <option value="prescription" {{ old('type') == 'prescription' ? 'selected' : '' }}>Recepta</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Document Date -->
                    <div>
                        <label for="document_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Data dokumentu <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="document_date"
                               id="document_date"
                               value="{{ old('document_date', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('document_date') @enderror"
                               required>
                        @error('document_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status"
                                id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') @enderror"
                                required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Szkic</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Ukończony</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Zarchiwizowany</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Treść dokumentu <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content"
                              id="content"
                              rows="8"
                              placeholder="Opisz szczegóły konsultacji, diagnozy lub leczenia..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('content') @enderror"
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Dodatkowe notatki
                    </label>
                    <textarea name="notes"
                              id="notes"
                              rows="4"
                              placeholder="Dodatkowe informacje, uwagi..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('notes') @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Metadata Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                        Dodatkowe informacje medyczne
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Symptoms -->
                        <div>
                            <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">
                                Objawy
                            </label>
                            <textarea name="symptoms"
                                      id="symptoms"
                                      rows="4"
                                      placeholder="Każdy objaw w nowej linii&#10;Ból pleców&#10;Sztywność mięśni&#10;Ograniczona ruchomość"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('symptoms') @enderror">{{ old('symptoms') }}</textarea>
                            @error('symptoms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Medications -->
                        <div>
                            <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                                Leki / Terapie
                            </label>
                            <textarea name="medications"
                                      id="medications"
                                      rows="4"
                                      placeholder="Każdy lek w nowej linii&#10;Ibuprofen 400mg&#10;Masaż leczniczy&#10;Ćwiczenia rozciągające"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('medications') @enderror">{{ old('medications') }}</textarea>
                            @error('medications')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recommendations -->
                        <div>
                            <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                                Zalecenia
                            </label>
                            <textarea name="recommendations"
                                      id="recommendations"
                                      rows="4"
                                      placeholder="Każde zalecenie w nowej linii&#10;Unikać dźwigania ciężkich przedmiotów&#10;Regularne ćwiczenia&#10;Kontrola za 2 tygodnie"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('recommendations') @enderror">{{ old('recommendations') }}</textarea>
                            @error('recommendations')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Upload -->
                <div>
                    <label for="document_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Załącznik
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <div class="flex text-gray-600">
                                <label for="document_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Prześlij plik</span>
                                    <input id="document_file"
                                           name="document_file"
                                           type="file"
                                           class="sr-only"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           onchange="updateFileName(this)">
                                </label>
                                <p class="pl-1">lub przeciągnij i upuść</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PDF, DOC, DOCX, JPG, JPEG, PNG do 10MB
                            </p>
                            <p id="file-name" class="text-sm text-gray-900 font-medium hidden"></p>
                        </div>
                    </div>
                    @error('document_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Privacy Settings -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_private"
                                   name="is_private"
                                   type="checkbox"
                                   value="1"
                                   {{ old('is_private') ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                        </div>
                        <div class="ml-3">
                            <label for="is_private" class="text-sm font-medium text-gray-700">
                                Dokument prywatny
                            </label>
                            <p class="text-sm text-gray-500">
                                Dokument będzie widoczny tylko dla doktora. Pacjent nie będzie mógł go przeglądać.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('medical-documents.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Anuluj
                </a>

                <button type="submit"
                        name="action"
                        value="draft"
                        class="inline-flex items-center justify-center px-4 py-2 border border-yellow-600 text-sm font-medium rounded-lg text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Zapisz jako szkic
                </button>

                <button type="submit"
                        name="action"
                        value="completed"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-check mr-2"></i>
                    Utwórz dokument
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Update file name display
function updateFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');

    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        fileNameDisplay.textContent = fileName;
        fileNameDisplay.classList.remove('hidden');
    } else {
        fileNameDisplay.classList.add('hidden');
    }
}

// Auto-resize textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');

    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });

    // Handle form submission with action buttons
    const form = document.querySelector('form');
    const statusSelect = document.getElementById('status');

    form.addEventListener('submit', function(e) {
        const clickedButton = document.activeElement;

        if (clickedButton.name === 'action') {
            if (clickedButton.value === 'draft') {
                statusSelect.value = 'draft';
            } else if (clickedButton.value === 'completed') {
                statusSelect.value = 'completed';
            }
        }
    });
});

// Patient search functionality
document.addEventListener('DOMContentLoaded', function() {
    const patientSelect = document.getElementById('patient_id');

    // Add search functionality if there are many patients
    if (patientSelect.options.length > 10) {
        // Convert select to searchable dropdown (you can implement this with a library like Select2)
        console.log('Consider implementing searchable dropdown for patient selection');
    }
});
</script>
@endsection
