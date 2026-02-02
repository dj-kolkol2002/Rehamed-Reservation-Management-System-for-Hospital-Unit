<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user profile.
     */
    public function show(): View
    {
        /** @var User $user */
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user profile.
     */
    public function edit(): View
    {
        /** @var User $user */
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user profile.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
        ], [
            'firstname.required' => 'Imię jest wymagane.',
            'lastname.required' => 'Nazwisko jest wymagane.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'email.unique' => 'Ten adres email jest już używany.',
            'date_of_birth.before' => 'Data urodzenia musi być wcześniejsza niż dzisiaj.',
            'gender.in' => 'Wybierz prawidłową płeć.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->only([
            'firstname', 'lastname', 'email', 'phone',
            'address', 'date_of_birth', 'gender', 'emergency_contact'
        ]);

        // Handle medical_history for patients
        if ($user->role === 'user') {
            if ($request->filled('medical_history')) {
                $userData['medical_history'] = array_filter(explode("\n", $request->medical_history));
            } else {
                $userData['medical_history'] = null;
            }
        }

        $user->update($userData);

        return redirect()->route('profile.show')
            ->with('success', 'Profil został pomyślnie zaktualizowany.');
    }

    /**
     * Update the user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Aktualne hasło jest wymagane.',
            'password.required' => 'Nowe hasło jest wymagane.',
            'password.min' => 'Nowe hasło musi mieć co najmniej 8 znaków.',
            'password.confirmed' => 'Potwierdzenie hasła nie pasuje.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'password')
                ->withInput();
        }

        // Sprawdź aktualne hasło
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Aktualne hasło jest nieprawidłowe.'], 'password')
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Hasło zostało pomyślnie zmienione.');
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
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
            if ($user->saveAvatar($request->file('avatar'))) {
                $freshUser = $user->fresh();
                return response()->json([
                    'success' => true,
                    'message' => 'Zdjęcie profilowe zostało zaktualizowane.',
                    'avatar_url' => $freshUser->avatar_url
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
     * Delete user avatar.
     */
    public function deleteAvatar(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->deleteAvatar()) {
            $freshUser = $user->fresh();
            return response()->json([
                'success' => true,
                'message' => 'Zdjęcie profilowe zostało usunięte.',
                'avatar_url' => $freshUser->avatar_url
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nie można usunąć zdjęcia profilowego.'
            ], 400);
        }
    }
}
