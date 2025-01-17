<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserJoinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $userName;

    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.new-user-joined',
                [
                    'user' => $notifiable,
                    'newUserName' => $this->userName
                ])
            ->subject('Nowy użytkownik');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'subject' => 'Nowy użytkownik',
            'message' => 'Nowy użytkownik zarejestrował się i właśnie zweryfikował swój adres email',
            'newUserName' => $this->userName
        ];
    }
}
