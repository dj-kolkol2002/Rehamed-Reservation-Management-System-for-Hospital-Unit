<?php
// app/Http/Controllers/Auth/ConfirmPasswordController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the password confirmation validation rules.
     */
    protected function rules()
    {
        return [
            'password' => 'required',
        ];
    }

    /**
     * Get the password confirmation validation error messages.
     */
    protected function validationErrorMessages()
    {
        return [
            'password.required' => 'Hasło jest wymagane.',
        ];
    }

    /**
     * Get the response for a failed password confirmation.
     */
    protected function sendConfirmationFailedResponse(Request $request)
    {
        return redirect()->back()
                         ->withErrors(['password' => 'Nieprawidłowe hasło.']);
    }
}
