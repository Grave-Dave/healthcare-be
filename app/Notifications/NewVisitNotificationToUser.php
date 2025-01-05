<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVisitNotificationToUser extends Notification implements ShouldQueue
{
    use Queueable;

    private string $userName;
    private string $location;
    private string $date;
    private string $time;

    public function __construct($userName, $location, $date, $time)
    {
        $this->userName = $userName;
        $this->location = $location;
        $this->date = $date;
        $this->time = $time;
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
            ->view('emails.visits.new-visit-to-user', [
                'user' => $notifiable,
                'userName' => $this->userName,
                'location' => $this->location,
                'date' => $this->date,
                'time' => $this->time,
                'url' => env('APP_URL') . "/moje-wizyty",
            ])
            ->subject('Wizyta została zarezerwowana');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'subject' => 'Wizyta została zarezerwowana',
            'message' => 'Wizyta została zarezerwowana i czeka na zatwierdzenie',
            'user' => $this->userName,
            'location' => $this->location,
            'date' => $this->date,
            'time' => $this->time,
        ];
    }
}
