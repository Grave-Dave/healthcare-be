<?php

namespace App\Listeners;

use App\Jobs\SendNotificationJob;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;

class SendWelcomeNotificationListener
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $notification = new WelcomeNotification();

        SendNotificationJob::dispatch($notification, $event->user, WelcomeNotification::class);
    }
}
