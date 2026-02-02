<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtry
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', '%' . $search . '%')
                  ->orWhere('lastname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Sortowanie
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['firstname', 'lastname', 'email', 'role', 'created_at', 'is_active'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $users = $query->paginate(15)->withQueryString();

        // Statystyki
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'doctors' => User::where('role', 'doctor')->count(),
            'patients' => User::where('role', 'user')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validateUser($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->only([
            'firstname', 'lastname', 'email', 'role', 'phone',
            'address', 'city', 'postal_code', 'country',
            'date_of_birth', 'gender', 'emergency_contact', 'is_active'
        ]);

        $userData['password'] = Hash::make($request->password);

        // Handle medical_history for patients
        if ($request->role === 'user' && $request->filled('medical_history')) {
            $userData['medical_history'] = array_filter(explode("\n", $request->medical_history));
        }

        $user = User::create($userData);

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            $user->saveAvatar($request->file('avatar'));
        }

        // Powiadomienie dla nowego użytkownika
        Notification::createForUser(
            $user->id,
            'system',
            'Witamy w systemie Rehamed',
            'Twoje konto zostało utworzone przez administratora. Możesz się teraz zalogować.',
            ['action' => 'account_created']
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Użytkownik został pomyślnie utworzony.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['patientAppointments' => function($query) {
            $query->latest()->limit(5);
        }, 'doctorAppointments' => function($query) {
            $query->latest()->limit(5);
        }]);

        // Statystyki użytkownika
        $userStats = [
            'total_appointments' => $user->patientAppointments()->count() + $user->doctorAppointments()->count(),
            'patient_appointments' => $user->patientAppointments()->count(),
            'doctor_appointments' => $user->doctorAppointments()->count(),
            'account_age' => (int) $user->created_at->diffInDays(now()),
            'last_login' => $user->last_activity ? $user->last_activity->diffForHumans() : 'Nigdy',
            'documents_count' => $user->patientDocuments()->count() + $user->doctorDocuments()->count(),
            'unread_notifications' => Notification::forUser($user->id)->unread()->count(),
        ];

        return view('admin.users.show', compact('user', 'userStats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = $this->validateUser($request, $user->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldStatus = $user->is_active;
        $oldRole = $user->role;

        $userData = $request->only([
            'firstname', 'lastname', 'email', 'role', 'phone',
            'address', 'city', 'postal_code', 'country',
            'date_of_birth', 'gender', 'emergency_contact', 'is_active'
        ]);

        // Update password only if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle medical_history for patients
        if ($request->role === 'user') {
            if ($request->filled('medical_history')) {
                $userData['medical_history'] = array_filter(explode("\n", $request->medical_history));
            } else {
                $userData['medical_history'] = null;
            }
        } else {
            $userData['medical_history'] = null;
        }

        $user->update($userData);

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            $user->saveAvatar($request->file('avatar'));
        }

        // Powiadom użytkownika o istotnych zmianach
        if ($oldStatus != $user->is_active || $oldRole != $user->role) {
            $message = '';
            if ($oldStatus != $user->is_active) {
                $message = $user->is_active ?
                    'Twoje konto zostało aktywowane.' :
                    'Twoje konto zostało dezaktywowane.';
            }
            if ($oldRole != $user->role) {
                $message .= " Twoja rola została zmieniona.";
            }

            Notification::createForUser(
                $user->id,
                'system',
                'Aktualizacja konta',
                $message,
                ['action' => 'account_updated']
            );
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Użytkownik został pomyślnie zaktualizowany.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting current admin
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Nie możesz usunąć swojego własnego konta.');
        }

        // Sprawdź czy to nie ostatni administrator
        if ($user->role === 'admin') {
            $activeAdminsCount = User::where('role', 'admin')
                ->where('is_active', true)
                ->count();

            if ($activeAdminsCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Nie można usunąć ostatniego aktywnego administratora.');
            }
        }

        $userName = $user->full_name;

        // Usuń avatar jeśli istnieje
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Użytkownik {$userName} został pomyślnie usunięty.");
    }

    /**
     * Aktywuj użytkownika
     */
    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        Notification::createForUser(
            $user->id,
            'system',
            'Konto aktywowane',
            'Twoje konto zostało aktywowane przez administratora.',
            ['action' => 'account_activated']
        );

        return redirect()->back()
            ->with('success', "Konto użytkownika {$user->full_name} zostało aktywowane.");
    }

    /**
     * Dezaktywuj użytkownika
     */
    public function deactivate(User $user)
    {
        // Sprawdź czy nie dezaktywuje siebie samego
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Nie możesz dezaktywować własnego konta.');
        }

        // Sprawdź czy to nie ostatni administrator
        if ($user->role === 'admin') {
            $activeAdminsCount = User::where('role', 'admin')
                ->where('is_active', true)
                ->count();

            if ($activeAdminsCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Nie można dezaktywować ostatniego administratora.');
            }
        }

        $user->update(['is_active' => false]);

        Notification::createForUser(
            $user->id,
            'system',
            'Konto dezaktywowane',
            'Twoje konto zostało dezaktywowane przez administratora.',
            ['action' => 'account_deactivated']
        );

        return redirect()->back()
            ->with('success', "Konto użytkownika {$user->full_name} zostało dezaktywowane.");
    }

    /**
     * Toggle user active status (AJAX).
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id() && $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Nie możesz dezaktywować swojego własnego konta.'
            ], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
            'message' => 'Status użytkownika został zaktualizowany.'
        ]);
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request, User $user)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->saveAvatar($request->file('avatar'));

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_url,
            'message' => 'Avatar został zaktualizowany.'
        ]);
    }

    /**
     * Delete user avatar
     */
    public function deleteAvatar(User $user)
    {
        $user->deleteAvatar();

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_url,
            'message' => 'Avatar został usunięty.'
        ]);
    }

    /**
     * Ręczna weryfikacja/cofnięcie weryfikacji email użytkownika.
     */
    public function verifyEmail(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'error' => 'Nie możesz zmienić weryfikacji własnego konta.'
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            $user->email_verified_at = null;
            $user->save();

            return response()->json([
                'success' => true,
                'verified' => false,
                'message' => 'Weryfikacja email została cofnięta.'
            ]);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'verified' => true,
            'message' => 'Email użytkownika został zweryfikowany.'
        ]);
    }

    /**
     * Validate user data.
     */
    private function validateUser(Request $request, $userId = null)
    {
        return Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'password' => $userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,user',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
            'is_active' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'firstname.required' => 'Imię jest wymagane.',
            'lastname.required' => 'Nazwisko jest wymagane.',
            'email.required' => 'Email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'email.unique' => 'Ten email jest już używany.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'password.confirmed' => 'Hasła nie są zgodne.',
            'role.required' => 'Rola jest wymagana.',
            'role.in' => 'Wybierz prawidłową rolę.',
            'date_of_birth.before' => 'Data urodzenia musi być wcześniejsza niż dzisiaj.',
            'gender.in' => 'Wybierz prawidłową płeć.',
            'avatar.image' => 'Plik musi być obrazem.',
            'avatar.mimes' => 'Dozwolone formaty: jpeg, png, jpg, gif.',
            'avatar.max' => 'Maksymalny rozmiar pliku to 2MB.',
        ]);
    }
}
