<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'firstname.required' => 'Imię jest wymagane.',
            'lastname.required' => 'Nazwisko jest wymagane.',
            'phone.required' => 'Numer telefonu jest wymagany.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'email.unique' => 'Ten adres email jest już używany.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'password.confirmed' => 'Potwierdzenie hasła nie pasuje.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user', // domyślna rola
            'is_active' => true, // konto aktywne od razu
            'email_verified_at' => null, // wymagana weryfikacja email
        ]);

        Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->full_name
        ]);

        return $user;
    }

    /**
     * The user has been registered.
     */
    protected function registered(Request $request, $user)
    {
        Log::info('User registration completed', [
            'user_id' => $user->id,
        ]);

        // Wyślij email weryfikacyjny
        $user->sendEmailVerificationNotification();

        Log::info('Verification email sent', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        // Przekieruj na stronę weryfikacji email
        return redirect()->route('verification.notice')
            ->with('success', 'Rejestracja przebiegła pomyślnie! Sprawdź swoją skrzynkę pocztową, aby zweryfikować adres email.');
    }
}
