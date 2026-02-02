<?php
// app/Http/Controllers/MedicalDocumentController.php

namespace App\Http\Controllers;

use App\Models\MedicalDocument;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MedicalDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of medical documents.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MedicalDocument::with(['patient', 'doctor']);

        // Filtrowanie według roli użytkownika
        if ($user->role === 'user') {
            $query->forPatient($user->id)->public();
        } elseif ($user->role === 'doctor') {
            $query->byDoctor($user->id);
        }
        // Admin może zobaczyć wszystkie dokumenty

        // Filtry
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($user->role !== 'user' && $request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('document_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('document_date', '<=', $request->date_to);
        }

        // Sortowanie
        $sortBy = $request->get('sort', 'document_date');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['title', 'type', 'status', 'document_date', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $documents = $query->paginate(15)->withQueryString();

        // Dane dla filtrów
        $patients = collect();
        $doctors = collect();

        if ($user->role === 'admin') {
            $patients = User::where('role', 'user')->where('is_active', true)->get();
            $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        } elseif ($user->role === 'doctor') {
            $patients = User::where('role', 'user')->where('is_active', true)->get();
        }

        // Statystyki
        $stats = $this->getDocumentStats($user);

        return view('medical-documents.index', compact(
            'documents', 'patients', 'doctors', 'stats', 'request'
        ));
    }

    /**
     * Show the form for creating a new medical document.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Tylko doktorzy i administratorzy mogą tworzyć dokumenty
        if ($user->role === 'user') {
            abort(403, 'Nie masz uprawnień do tworzenia dokumentów.');
        }

        $patients = User::where('role', 'user')->where('is_active', true)->get();
        $selectedPatientId = $request->get('patient_id');

        return view('medical-documents.create', compact('patients', 'selectedPatientId'));
    }

    /**
     * Store a newly created medical document.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Tylko doktorzy i administratorzy mogą tworzyć dokumenty
        if ($user->role === 'user') {
            abort(403, 'Nie masz uprawnień do tworzenia dokumentów.');
        }

        $validator = $this->validateDocument($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $documentData = $request->only([
            'patient_id', 'title', 'type', 'content', 'notes',
            'status', 'document_date', 'is_private'
        ]);

        $documentData['doctor_id'] = $user->id;
        $documentData['is_private'] = $request->boolean('is_private');

        // Sprawdź action button i ustaw status accordingly
        if ($request->has('action')) {
            if ($request->action === 'draft') {
                $documentData['status'] = 'draft';
            } elseif ($request->action === 'completed') {
                $documentData['status'] = 'completed';
            }
        }

        // Przetwórz metadata
        $metadata = [];
        if ($request->filled('symptoms')) {
            $metadata['symptoms'] = array_filter(explode("\n", $request->symptoms));
        }

        if ($request->filled('medications')) {
            $metadata['medications'] = array_filter(explode("\n", $request->medications));
        }

        if ($request->filled('recommendations')) {
            $metadata['recommendations'] = array_filter(explode("\n", $request->recommendations));
        }

        $documentData['metadata'] = $metadata;

        $document = MedicalDocument::create($documentData);

        // Obsłuż plik jeśli został przesłany
        if ($request->hasFile('document_file')) {
            $document->saveFile($request->file('document_file'));
        }

        // Utwórz powiadomienie o nowym dokumencie
        $document->load(['patient', 'doctor']);
        Notification::documentCreated($document);

        return redirect()->route('medical-documents.index')
            ->with('success', 'Dokument medyczny został pomyślnie utworzony.');
    }

    /**
     * Display the specified medical document.
     */
    public function show(MedicalDocument $medicalDocument)
    {
        $user = Auth::user();

        if (!$medicalDocument->canBeViewedBy($user)) {
            abort(403, 'Nie masz uprawnień do przeglądania tego dokumentu.');
        }

        $medicalDocument->load(['patient', 'doctor']);

        return view('medical-documents.show', compact('medicalDocument'));
    }

    /**
     * Show the form for editing the medical document.
     */
    public function edit(MedicalDocument $medicalDocument)
    {
        $user = Auth::user();

        if (!$medicalDocument->canBeEditedBy($user)) {
            abort(403, 'Nie masz uprawnień do edycji tego dokumentu.');
        }

        $patients = User::where('role', 'user')->where('is_active', true)->get();
        $medicalDocument->load(['patient']);

        return view('medical-documents.edit', compact('medicalDocument', 'patients'));
    }

    /**
     * Update the specified medical document.
     */
    public function update(Request $request, MedicalDocument $medicalDocument)
    {
        $user = Auth::user();

        if (!$medicalDocument->canBeEditedBy($user)) {
            abort(403, 'Nie masz uprawnień do edycji tego dokumentu.');
        }

        $validator = $this->validateDocument($request, $medicalDocument->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $documentData = $request->only([
            'patient_id', 'title', 'type', 'content', 'notes',
            'status', 'document_date', 'is_private'
        ]);

        $documentData['is_private'] = $request->boolean('is_private');

        // Sprawdź action button i ustaw status accordingly
        if ($request->has('action')) {
            if ($request->action === 'draft') {
                $documentData['status'] = 'draft';
            } elseif ($request->action === 'completed') {
                $documentData['status'] = 'completed';
            }
        }

        // Przetwórz metadata
        $metadata = [];
        if ($request->filled('symptoms')) {
            $metadata['symptoms'] = array_filter(explode("\n", $request->symptoms));
        }

        if ($request->filled('medications')) {
            $metadata['medications'] = array_filter(explode("\n", $request->medications));
        }

        if ($request->filled('recommendations')) {
            $metadata['recommendations'] = array_filter(explode("\n", $request->recommendations));
        }

        $documentData['metadata'] = $metadata;

        $medicalDocument->update($documentData);

        // Obsłuż plik jeśli został przesłany
        if ($request->hasFile('document_file')) {
            $medicalDocument->saveFile($request->file('document_file'));
        }

        return redirect()->route('medical-documents.show', $medicalDocument)
            ->with('success', 'Dokument medyczny został pomyślnie zaktualizowany.');
    }

    /**
     * Remove the specified medical document.
     */
    public function destroy(MedicalDocument $medicalDocument)
    {
        $user = Auth::user();

        if (!$medicalDocument->canBeEditedBy($user)) {
            abort(403, 'Nie masz uprawnień do usunięcia tego dokumentu.');
        }

        // Usuń plik jeśli istnieje
        $medicalDocument->deleteFile();

        $patientName = $medicalDocument->patient->full_name;
        $medicalDocument->delete();

        return redirect()->route('medical-documents.index')
            ->with('success', "Dokument medyczny dla pacjenta {$patientName} został usunięty.");
    }

    /**
     * Download document file.
     */
    public function download(MedicalDocument $medicalDocument)
    {
        $user = Auth::user();

        if (!$medicalDocument->canBeViewedBy($user)) {
            abort(403, 'Nie masz uprawnień do pobrania tego pliku.');
        }

        if (!$medicalDocument->hasFile()) {
            abort(404, 'Plik nie został znaleziony.');
        }

        $filePath = storage_path('app/private/' . $medicalDocument->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Plik nie został znaleziony.');
        }

        return response()->download($filePath, $medicalDocument->file_name);
    }

    /**
     * Delete document file.
     */
    public function deleteFile(MedicalDocument $medicalDocument)
    {
        $user = Auth::user();

        if (!$medicalDocument->canBeEditedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz uprawnień do usunięcia tego pliku.'
            ], 403);
        }

        if ($medicalDocument->deleteFile()) {
            return response()->json([
                'success' => true,
                'message' => 'Plik został pomyślnie usunięty.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nie można usunąć pliku.'
        ], 400);
    }

    /**
     * Get medical documents for AJAX requests.
     */
    public function getDocuments(Request $request)
    {
        $user = Auth::user();
        $query = MedicalDocument::with(['patient', 'doctor']);

        // Filtrowanie według roli użytkownika
        if ($user->role === 'user') {
            $query->forPatient($user->id)->public();
        } elseif ($user->role === 'doctor') {
            $query->byDoctor($user->id);
        }

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $documents = $query->latest()->limit(20)->get();

        return response()->json($documents);
    }

    /**
     * Validate document data.
     */
    private function validateDocument(Request $request, $documentId = null)
    {
        return Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:general,diagnosis,treatment,examination,prescription',
            'content' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,completed,archived',
            'document_date' => 'required|date',
            'is_private' => 'boolean',
            'symptoms' => 'nullable|string',
            'medications' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'patient_id.required' => 'Wybierz pacjenta.',
            'patient_id.exists' => 'Wybrany pacjent nie istnieje.',
            'title.required' => 'Tytuł dokumentu jest wymagany.',
            'type.required' => 'Typ dokumentu jest wymagany.',
            'type.in' => 'Wybierz prawidłowy typ dokumentu.',
            'content.required' => 'Treść dokumentu jest wymagana.',
            'status.required' => 'Status dokumentu jest wymagany.',
            'status.in' => 'Wybierz prawidłowy status dokumentu.',
            'document_date.required' => 'Data dokumentu jest wymagana.',
            'document_date.date' => 'Podaj prawidłową datę.',
            'document_file.file' => 'Przesłany plik jest nieprawidłowy.',
            'document_file.mimes' => 'Dozwolone formaty plików: PDF, DOC, DOCX, JPG, JPEG, PNG.',
            'document_file.max' => 'Rozmiar pliku nie może przekraczać 10MB.',
        ]);
    }

    /**
     * Get document statistics for user.
     */
    private function getDocumentStats($user)
    {
        $query = MedicalDocument::query();

        if ($user->role === 'user') {
            $query->forPatient($user->id)->public();
        } elseif ($user->role === 'doctor') {
            $query->byDoctor($user->id);
        }

        return [
            'total' => $query->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'archived' => (clone $query)->where('status', 'archived')->count(),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count(),
        ];
    }
}
