<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * WAŻNE: Ten middleware powinien ZAWSZE być używany RAZEM z middleware 'auth'
     * i 'auth' powinien być PRZED tym middleware w kolejności.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jeśli użytkownik nie jest zalogowany, przekieruj na login
        // (to nie powinno się zdarzyć jeśli middleware 'auth' jest przed tym middleware)
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Musisz być zalogowany, aby uzyskać dostęp do tej strony.');
        }

        // Jeśli użytkownik implementuje MustVerifyEmail i nie ma zweryfikowanego emaila
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Musisz zweryfikować swój adres email, aby uzyskać dostęp do tej strony.');
        }

        return $next($request);
    }
}
