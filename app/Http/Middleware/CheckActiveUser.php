<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Sprawdź czy użytkownik ma właściwość is_active i czy jest aktywny
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Twoje konto zostało dezaktywowane. Skontaktuj się z administratorem.'
                    ], 403);
                }

                return redirect()->route('login')
                    ->withErrors(['email' => 'Twoje konto zostało dezaktywowane. Skontaktuj się z administratorem.']);
            }
        }

        return $next($request);
    }
}
