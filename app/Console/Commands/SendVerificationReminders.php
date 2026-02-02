<?php
// app/Console/Commands/SendVerificationReminders.php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendVerificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:send-verification-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send verification reminders to users who haven\'t verified their email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Searching for unverified users...');

        // Znajdź użytkowników, którzy się zarejestrowali ale nie zweryfikowali emaila
        // w ciągu ostatnich 7 dni
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('role', '!=', 'admin')
            ->where('is_active', true)
            ->whereBetween('created_at', [
                now()->subDays(7),
                now()->subHours(24) // Nie wysyłaj jeśli konto ma mniej niż 24h
            ])
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('No unverified users found.');
            return;
        }

        $this->info("Found {$unverifiedUsers->count()} unverified users.");

        $sent = 0;
        foreach ($unverifiedUsers as $user) {
            try {
                $user->notify(new CustomVerifyEmail);
                $sent++;

                $this->line("✓ Reminder sent to: {$user->email}");

                Log::info('Verification reminder sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'days_since_registration' => $user->created_at->diffInDays(now())
                ]);

            } catch (\Exception $e) {
                $this->error("✗ Failed to send reminder to: {$user->email}");
                Log::error('Failed to send verification reminder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Verification reminders sent: {$sent}/{$unverifiedUsers->count()}");
    }
}

// Żeby utworzyć tę komendę, uruchom:
// php artisan make:command SendVerificationReminders
