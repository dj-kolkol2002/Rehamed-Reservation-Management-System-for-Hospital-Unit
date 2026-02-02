<?php
// app/Http/Controllers/Auth/VerificationController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath())->with('info', 'Twój email został już zweryfikowany.');
        }

        if ($request->user()->markEmailAsVerified()) {
            Log::info('Email verified', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email
            ]);

            return redirect($this->redirectPath())->with('success', 'Twój adres email został pomyślnie zweryfikowany!');
        }

        return redirect()->route('verification.notice')->with('error', 'Nie udało się zweryfikować adresu email.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath())->with('info', 'Twój email jest już zweryfikowany.');
        }

        $request->user()->sendEmailVerificationNotification();

        Log::info('Verification email resent', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email
        ]);

        return back()->with('success', 'Link weryfikacyjny został wysłany na Twój adres email!');
    }

    /**
     * Determine where to redirect users after verification.
     */
    protected function redirectPath()
    {
        $user = auth()->user();

        if (!$user) {
            return '/dashboard';
        }

        // Przekieruj na podstawie roli
        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            case 'doctor':
                return route('doctor.dashboard');
            default:
                return route('user.dashboard');
        }
    }
}
