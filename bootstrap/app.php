<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Rejestracja middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'active' => \App\Http\Middleware\CheckActiveUser::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class, // Nasz własny middleware
        ]);

        // Middleware priorytet (opcjonalne)
        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\EnsureEmailIsVerified::class, // Nasz własny middleware
            \App\Http\Middleware\CheckActiveUser::class,
            \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Obsługa wygasłego tokenu CSRF (419 Page Expired)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // Jeśli to żądanie wylogowania, po prostu wyloguj i przekieruj
            if ($request->is('logout')) {
                if (auth()->check()) {
                    auth()->logout();
                }
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('home')
                    ->with('success', 'Zostałeś wylogowany.');
            }

            // Dla innych przypadków, przekieruj na login z informacją
            return redirect()->route('login')
                ->with('error', 'Twoja sesja wygasła. Zaloguj się ponownie.');
        });
    })
    ->create();
