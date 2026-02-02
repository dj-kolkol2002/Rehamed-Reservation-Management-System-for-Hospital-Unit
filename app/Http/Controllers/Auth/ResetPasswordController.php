<?php
// app/Http/Controllers/Auth/ResetPasswordController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
     * Get the password reset validation rules.
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * Get the password reset validation error messages.
     */
    protected function validationErrorMessages()
    {
        return [
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'password.confirmed' => 'Potwierdzenie hasła nie pasuje.',
        ];
    }

    /**
     * Get the response for a successful password reset.
     */
    protected function sendResetResponse(Request $request, $response)
    {
        Log::info('Password reset successful', [
            'email' => $request->email
        ]);

        return redirect($this->redirectPath())
            ->with('success', 'Hasło zostało pomyślnie zresetowane! Możesz się teraz zalogować.');
    }

    /**
     * Get the response for a failed password reset.
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        Log::warning('Password reset failed', [
            'email' => $request->email,
            'response' => $response
        ]);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $this->getResetFailedMessage($response)]);
    }

    /**
     * Get the password reset failed message.
     */
    protected function getResetFailedMessage($response)
    {
        switch ($response) {
            case Password::INVALID_TOKEN:
                return 'Link do resetowania hasła jest nieprawidłowy lub wygasł. Spróbuj ponownie.';
            case Password::INVALID_USER:
                return 'Nie można znaleźć użytkownika z tym adresem email.';
            default:
                return 'Nie udało się zresetować hasła. Spróbuj ponownie.';
        }
    }

    /**
     * Determine where users should be redirected after resetting password.
     */
    protected function redirectPath()
    {
        return route('login');
    }
}
