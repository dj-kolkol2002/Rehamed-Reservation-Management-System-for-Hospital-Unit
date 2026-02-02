<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Obsługa ról rozdzielonych przecinkiem (np. 'role:admin,doctor')
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }

        // Sprawdź czy użytkownik ma jedną z wymaganych ról
        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        // Jeśli nie ma uprawnień, przekieruj do odpowiedniego dashboardu
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Nie masz uprawnień do tej akcji.'
            ], 403);
        }

        // Przekieruj do odpowiedniego dashboardu na podstawie roli
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Nie masz uprawnień do tej akcji.');
            case 'doctor':
                return redirect()->route('doctor.dashboard')
                    ->with('error', 'Nie masz uprawnień do tej akcji.');
            case 'user':
                return redirect()->route('user.dashboard')
                    ->with('error', 'Nie masz uprawnień do tej akcji.');
            default:
                return redirect()->route('dashboard')
                    ->with('error', 'Nie masz uprawnień do tej akcji.');
        }

    }


}
