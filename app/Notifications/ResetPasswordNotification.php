<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    private string $token;
    private string $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $baseUrl = env('APP_URL');
        $resetUrl = $baseUrl . "/password/reset/{$this->token}?email={$this->email}";

        return (new MailMessage)
            ->subject('Zmiana hasła')
            ->view(
                'emails.password-reset',
                [
                    'resetUrl' => $resetUrl,
                    'user' => $notifiable,
                ]
            );
    }
}
