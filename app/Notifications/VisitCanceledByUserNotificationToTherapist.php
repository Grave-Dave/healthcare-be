<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitCanceledByUserNotificationToTherapist extends Notification implements ShouldQueue
{
    use Queueable;

    private string $patientName;
    private string $patientPhone;
    private string $location;
    private string $date;
    private string $time;

    public function __construct($patientName, $patientPhone, $location, $date, $time)
    {
        $this->patientName = $patientName;
        $this->patientPhone = $patientPhone;
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
            ->view('emails.visits.visit-canceled-by-user-to-therapist', [
                'user' => $notifiable,
                'patientName' => $this->patientName,
                'patientPhone' => $this->patientPhone,
                'location' => $this->location,
                'date' => $this->date,
                'time' => $this->time,
                'url' => env('APP_URL') . "/wizyty",
            ])
            ->subject('Wizyta została odwołana');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'subject' => 'Wizyta została odwołana',
            'message' => 'Użytkownik odwołał następującą wizytę',
            'patient' => $this->patientPhone,
            'location' => $this->location,
            'date' => $this->date,
            'time' => $this->time,
        ];
    }
}
