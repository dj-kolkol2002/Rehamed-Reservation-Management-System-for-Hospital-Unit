<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wysyła testowy email do Mailtrapa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';

        $this->info('🚀 Rozpoczynam wysyłanie testowego emaila...');
        $this->info('');

        // Wyświetl konfigurację
        $this->line('📧 Konfiguracja SMTP:');
        $this->line('   Host: ' . config('mail.mailers.smtp.host'));
        $this->line('   Port: ' . config('mail.mailers.smtp.port'));
        $this->line('   Username: ' . config('mail.mailers.smtp.username'));
        $this->line('   From: ' . config('mail.from.address'));
        $this->line('   To: ' . $email);
        $this->line('');

        try {
            Mail::to($email)->send(new TestMail());

            $this->info('✅ Email został wysłany pomyślnie!');
            $this->line('');
            $this->info('🔍 Sprawdź swoją skrzynkę Mailtrap:');
            $this->line('   https://mailtrap.io/inboxes');
            $this->line('');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Błąd podczas wysyłania emaila:');
            $this->error($e->getMessage());
            $this->line('');
            $this->line('Stack trace:');
            $this->line($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
