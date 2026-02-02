<?php
// app/Notifications/CustomResetPassword.php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPasswordNotification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Resetowanie hasła - Rehamed')
            ->greeting("Witaj {$notifiable->firstname}!")
            ->line('Otrzymujesz ten email, ponieważ otrzymaliśmy prośbę o zresetowanie hasła dla Twojego konta.')
            ->line('Kliknij poniższy przycisk, aby zresetować hasło:')
            ->action('Resetuj hasło', $url)
            ->line('Link do resetowania hasła wygaśnie za 60 minut.')
            ->line('Jeśli nie prosiłeś o zresetowanie hasła, zignoruj tę wiadomość.')
            ->salutation('Pozdrawiamy,<br>Zespół Rehamed');
    }
}
