{{-- resources/views/medical-documents/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <div class="flex items-center mb-4 sm:mb-0">
                <a href="{{ route('medical-documents.index') }}"
                   class="text-gray-600 hover:text-gray-900 transition-colors mr-4">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $medicalDocument->title }}</h1>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600">
                        <span>{{ $medicalDocument->document_date->format('d.m.Y') }}</span>
                        <span>•</span>
                        <span>{{ $medicalDocument->type_display }}</span>
                        <span>•</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $medicalDocument->status_color }}-100 text-{{ $medicalDocument->status_color }}-800">
                            {{ $medicalDocument->status_display }}
                        </span>
                        @if($medicalDocument->is_private)
                        <span>•</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-lock mr-1"></i>
                            Prywatny
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            @if($medicalDocument->canBeEditedBy(Auth::user()))
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="{{ route('medical-documents.edit', $medicalDocument) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edytuj
                </a>

                <form method="POST"
                      action="{{ route('medical-documents.destroy', $medicalDocument) }}"
                      class="inline-block"
                      onsubmit="return confirm('Czy na pewno chcesz usunąć ten dokument?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Usuń
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-6 sm:space-y-8">
        <!-- Patient and Doctor Information -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Informacje podstawowe</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient Info -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Pacjent</h3>
                        <div class="flex items-center">
                            <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                 src="{{ $medicalDocument->patient->avatar_url }}"
                                 alt="Avatar {{ $medicalDocument->patient->full_name }}">
                            <div class="ml-4">
                                <div class="text-lg font-medium text-gray-900">{{ $medicalDocument->patient->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $medicalDocument->patient->email }}</div>
                                @if($medicalDocument->patient->phone)
                                <div class="text-sm text-gray-500">{{ $medicalDocument->patient->phone }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Info -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Fizjoterapeuta</h3>
                        <div class="flex items-center">
                            <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                 src="{{ $medicalDocument->doctor->avatar_url }}"
                                 alt="Avatar {{ $medicalDocument->doctor->full_name }}">
                            <div class="ml-4">
                                <div class="text-lg font-medium text-gray-900">{{ $medicalDocument->doctor->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $medicalDocument->doctor->email }}</div>
                                @if($medicalDocument->doctor->phone)
                                <div class="text-sm text-gray-500">{{ $medicalDocument->doctor->phone }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Content -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Treść dokumentu</h2>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    <div class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $medicalDocument->content }}</div>
                </div>
            </div>
        </div>

        @if($medicalDocument->notes)
        <!-- Notes -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Dodatkowe notatki</h2>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    <div class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $medicalDocument->notes }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Medical Information -->
        @if($medicalDocument->metadata && (isset($medicalDocument->metadata['symptoms']) || isset($medicalDocument->metadata['medications']) || isset($medicalDocument->metadata['recommendations'])))
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Informacje medyczne</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @if(isset($medicalDocument->metadata['symptoms']) && !empty($medicalDocument->metadata['symptoms']))
                    <!-- Symptoms -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Objawy</h3>
                        <ul class="space-y-2">
                            @foreach($medicalDocument->metadata['symptoms'] as $symptom)
                            <li class="flex items-start">
                                <i class="fas fa-circle text-red-400 text-xs mt-2 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-900">{{ $symptom }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($medicalDocument->metadata['medications']) && !empty($medicalDocument->metadata['medications']))
                    <!-- Medications -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Leki / Terapie</h3>
                        <ul class="space-y-2">
                            @foreach($medicalDocument->metadata['medications'] as $medication)
                            <li class="flex items-start">
                                <i class="fas fa-circle text-blue-400 text-xs mt-2 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-900">{{ $medication }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($medicalDocument->metadata['recommendations']) && !empty($medicalDocument->metadata['recommendations']))
                    <!-- Recommendations -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Zalecenia</h3>
                        <ul class="space-y-2">
                            @foreach($medicalDocument->metadata['recommendations'] as $recommendation)
                            <li class="flex items-start">
                                <i class="fas fa-circle text-green-400 text-xs mt-2 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-900">{{ $recommendation }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- File Attachment -->
        @if($medicalDocument->hasFile())
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Załączniki</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $medicalDocument->file_name }}</div>
                            <div class="text-sm text-gray-500">Załącznik do dokumentu</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('medical-documents.download', $medicalDocument) }}"
                           class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Pobierz
                        </a>

                        @if($medicalDocument->canBeEditedBy(Auth::user()))
                        <button onclick="deleteFile({{ $medicalDocument->id }})"
                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Usuń
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Document Metadata -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Informacje o dokumencie</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Data utworzenia</div>
                        <div class="text-sm text-gray-900 mt-1">{{ $medicalDocument->created_at->format('d.m.Y H:i') }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Ostatnia modyfikacja</div>
                        <div class="text-sm text-gray-900 mt-1">{{ $medicalDocument->updated_at->format('d.m.Y H:i') }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $medicalDocument->status_color }}-100 text-{{ $medicalDocument->status_color }}-800">
                                {{ $medicalDocument->status_display }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Prywatność</div>
                        <div class="mt-1">
                            @if($medicalDocument->is_private)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-lock mr-1"></i>
                                    Prywatny
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-unlock mr-1"></i>
                                    Publiczny
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
@endsection

@section('scripts')
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
});

// Delete file function
function deleteFile(documentId) {
    if (confirm('Czy na pewno chcesz usunąć ten plik?')) {
        fetch(`/medical-documents/${documentId}/file`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to show updated document
                location.reload();
            } else {
                alert(data.message || 'Wystąpił błąd podczas usuwania pliku.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Wystąpił błąd podczas usuwania pliku.');
        });
    }
}

// Print document function
function printDocument() {
    window.print();
}

// Add print styles
const printStyles = `
@media print {
    body * {
        visibility: hidden;
    }
    .max-w-4xl, .max-w-4xl * {
        visibility: visible;
    }
    .max-w-4xl {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    /* Hide navigation and action buttons */
    .fas.fa-arrow-left,
    button,
    .bg-green-600,
    .bg-red-600 {
        display: none !important;
    }
}`;

// Add print styles to document
const styleSheet = document.createElement('style');
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);
</script>
@endsection
