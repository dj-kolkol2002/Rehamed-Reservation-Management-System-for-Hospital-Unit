<?php
// app/Jobs/SendEmailVerificationJob.php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            if (!$this->user->hasVerifiedEmail()) {
                $this->user->notify(new CustomVerifyEmail);

                Log::info('Email verification sent via job', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email verification', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Email verification job failed', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage()
        ]);
    }
}
