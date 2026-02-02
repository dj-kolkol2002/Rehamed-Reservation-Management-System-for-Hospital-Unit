<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Adres email jest wymagany.',
            'password.required' => 'Hasło jest wymagane.',
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     */
    public function username()
    {
        return 'email';
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // Sprawdź czy użytkownik jest aktywny
        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                $this->username() => ['Twoje konto zostało dezaktywowane. Skontaktuj się z administratorem.'],
            ]);
        }

        // Przekieruj na podstawie roli
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Witamy z powrotem w panelu administratora!');
            case 'doctor':
                return redirect()->route('doctor.dashboard')
                    ->with('success', 'Witamy z powrotem w panelu lekarskim!');
            default:
                return redirect()->route('user.dashboard')
                    ->with('success', 'Witamy z powrotem!');
        }
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => ['Nieprawidłowy adres email lub hasło.'],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Wyloguj użytkownika jeśli jest zalogowany
        if (Auth::check()) {
            $this->guard()->logout();
        }

        // Unieważnij sesję i wygeneruj nowy token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Zostałeś pomyślnie wylogowany.');
    }

    /**
     * Pokaż formularz logowania.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }
}
