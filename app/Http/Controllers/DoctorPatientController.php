<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DoctorPatientController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    /**
     * Display a listing of the patients.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user'); // Only patients

        // Filtry
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', '%' . $search . '%')
                  ->orWhere('lastname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Sortowanie
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['firstname', 'lastname', 'email', 'created_at', 'is_active'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $patients = $query->paginate(15)->withQueryString();

        // Statystyki
        $stats = [
            'total' => User::where('role', 'user')->count(),
            'active' => User::where('role', 'user')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'user')->where('is_active', false)->count(),
            'new_this_month' => User::where('role', 'user')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('doctor.patients.index', compact('patients', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        return view('doctor.patients.create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validatePatient($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $patientData = $request->only([
            'firstname', 'lastname', 'email', 'phone',
            'address', 'date_of_birth', 'gender', 'emergency_contact'
        ]);

        $patientData['password'] = Hash::make($request->password);
        $patientData['role'] = 'user'; // Force patient role
        $patientData['is_active'] = true; // Auto-activate
        // Usunięto auto-verify - konto jest od razu gotowe do użycia

        // Handle medical_history
        if ($request->filled('medical_history')) {
            $patientData['medical_history'] = array_filter(explode("\n", $request->medical_history));
        }

        $patient = User::create($patientData);

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            $patient->saveAvatar($request->file('avatar'));
        }

        return redirect()->route('doctor.patients.index')
            ->with('success', 'Pacjent został pomyślnie dodany do systemu.');
    }

    /**
     * Display the specified patient.
     */
    public function show(User $patient)
    {
        // Sprawdź czy to pacjent
        if ($patient->role !== 'user') {
            abort(404, 'Pacjent nie został znaleziony.');
        }

        // Załaduj dokumenty medyczne pacjenta
        $patient->load(['patientDocuments' => function($query) {
            $query->latest()->limit(10);
        }]);

        // Statystyki pacjenta
        $patientStats = [
            'total_documents' => $patient->patientDocuments()->count(),
            'completed_documents' => $patient->patientDocuments()->where('status', 'completed')->count(),
            'draft_documents' => $patient->patientDocuments()->where('status', 'draft')->count(),
            'documents_this_month' => $patient->patientDocuments()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'account_age' => (int) $patient->created_at->diffInDays(now()),
            'last_document' => $patient->patientDocuments()->latest()->first(),
        ];

        return view('doctor.patients.show', compact('patient', 'patientStats'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(User $patient)
    {
        // Sprawdź czy to pacjent
        if ($patient->role !== 'user') {
            abort(404);
        }

        return view('doctor.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, User $patient)
    {
        // Sprawdź czy to pacjent
        if ($patient->role !== 'user') {
            abort(404);
        }

        $validator = $this->validatePatient($request, $patient->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $patientData = $request->only([
            'firstname', 'lastname', 'email', 'phone',
            'address', 'date_of_birth', 'gender', 'emergency_contact'
        ]);

        // Update password only if provided
        if ($request->filled('password')) {
            $patientData['password'] = Hash::make($request->password);
        }

        // Handle medical_history
        if ($request->filled('medical_history')) {
            $patientData['medical_history'] = array_filter(explode("\n", $request->medical_history));
        } else {
            $patientData['medical_history'] = null;
        }

        $patient->update($patientData);

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            $patient->saveAvatar($request->file('avatar'));
        }

        return redirect()->route('doctor.patients.index')
            ->with('success', 'Dane pacjenta zostały pomyślnie zaktualizowane.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(User $patient)
    {
        // Sprawdź czy to pacjent
        if ($patient->role !== 'user') {
            abort(404);
        }

        // Check if patient has medical documents
        $documentsCount = $patient->patientDocuments()->count();

        if ($documentsCount > 0) {
            return redirect()->back()
                ->with('error', 'Nie można usunąć pacjenta posiadającego dokumenty medyczne w systemie.');
        }

        $patientName = $patient->full_name;
        $patient->delete();

        return redirect()->route('doctor.patients.index')
            ->with('success', "Pacjent {$patientName} został usunięty z systemu.");
    }

    /**
     * Upload patient avatar by doctor.
     */
    public function uploadAvatar(Request $request, User $patient)
    {
        // Sprawdź czy to pacjent
        if ($patient->role !== 'user') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Wybierz zdjęcie do przesłania.',
            'avatar.image' => 'Plik musi być obrazem.',
            'avatar.mimes' => 'Dozwolone formaty: JPEG, PNG, JPG, GIF.',
            'avatar.max' => 'Rozmiar pliku nie może przekraczać 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($patient->saveAvatar($request->file('avatar'))) {
                $freshPatient = $patient->fresh();
                return response()->json([
                    'success' => true,
                    'message' => 'Zdjęcie profilowe pacjenta zostało zaktualizowane.',
                    'avatar_url' => $freshPatient->avatar_url
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Wystąpił błąd podczas zapisywania zdjęcia.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas przetwarzania zdjęcia.'
            ], 500);
        }
    }

    /**
     * Delete patient avatar by doctor.
     */
    public function deleteAvatar(User $patient)
    {
        // Sprawdź czy to pacjent
        if ($patient->role !== 'user') {
            abort(404);
        }

        if ($patient->deleteAvatar()) {
            $freshPatient = $patient->fresh();
            return response()->json([
                'success' => true,
                'message' => 'Zdjęcie profilowe pacjenta zostało usunięte.',
                'avatar_url' => $freshPatient->avatar_url
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nie można usunąć zdjęcia profilowego.'
            ], 400);
        }
    }

    /**
     * Get patients list for AJAX requests.
     */
    public function getPatients(Request $request)
    {
        $query = User::where('role', 'user')->where('is_active', true);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', '%' . $search . '%')
                  ->orWhere('lastname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $patients = $query->select('id', 'firstname', 'lastname', 'email', 'phone')
                         ->limit(20)
                         ->get();

        return response()->json($patients);
    }

    /**
     * Validate patient data.
     */
    private function validatePatient(Request $request, $patientId = null)
    {
        return Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($patientId)
            ],
            'password' => $patientId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'firstname.required' => 'Imię jest wymagane.',
            'lastname.required' => 'Nazwisko jest wymagane.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'email.unique' => 'Ten adres email jest już używany.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'password.confirmed' => 'Potwierdzenie hasła nie pasuje.',
            'date_of_birth.before' => 'Data urodzenia musi być wcześniejsza niż dzisiaj.',
            'gender.in' => 'Wybierz prawidłową płeć.',
            'avatar.image' => 'Plik musi być obrazem.',
            'avatar.mimes' => 'Dozwolone formaty zdjęcia: JPEG, PNG, JPG, GIF.',
            'avatar.max' => 'Rozmiar zdjęcia nie może przekraczać 2MB.',
        ]);
    }
}
