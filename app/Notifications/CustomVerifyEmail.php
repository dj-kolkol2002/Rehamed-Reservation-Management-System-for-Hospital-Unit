<?php
// app/Notifications/CustomVerifyEmail.php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase // Usunięto ShouldQueue dla testów
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Potwierdź swój adres email - Rehamed')
            ->greeting("Witaj {$notifiable->firstname}!")
            ->line('Dziękujemy za rejestrację w klinice Rehamed.')
            ->line('Aby dokończyć proces rejestracji, potwierdź swój adres email klikając poniższy przycisk.')
            ->action('Potwierdź adres email', $verificationUrl)
            ->line('Ten link wygaśnie za 60 minut.')
            ->line('Jeśli nie zakładałeś konta w naszej klinice, zignoruj tę wiadomość.')
            ->salutation('Zespół Rehamed');
    }
}
