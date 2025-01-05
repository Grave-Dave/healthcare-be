<?php

namespace App\Listeners;

use App\Jobs\SendNotificationJob;
use App\Models\Therapist;
use App\Models\User;
use App\Notifications\NewUserJoinedNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;

class SendNewUserJoinedNotificationListener
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $notification = new NewUserJoinedNotification($event->user->getUserFullName());

        $notifiables  = User::whereHas(User::THERAPIST_RELATION)->get();

        foreach ($notifiables  as $notifiable) {

            SendNotificationJob::dispatch($notification, $notifiable, NewUserJoinedNotification::class);
        }

    }
}
