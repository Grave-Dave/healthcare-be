<?php

namespace App\Providers;

use App\Events\AfterVisitCanceledByAdmin;
use App\Events\AfterVisitCanceledByUser;
use App\Events\AfterVisitConfirmed;
use App\Events\AfterVisitCreated;
use App\Listeners\SendNewUserJoinedNotificationListener;
use App\Listeners\SendNewVisitNotificationToTherapistListener;
use App\Listeners\SendNewVisitNotificationToUserListener;
use App\Listeners\SendVisitCanceledByAdminNotificationToUserListener;
use App\Listeners\SendVisitCanceledByUserNotificationToTherapistListener;
use App\Listeners\SendVisitCanceledByUserNotificationToUserListener;
use App\Listeners\SendVisitConfirmedNotificationToUserListener;
use App\Listeners\SendWelcomeNotificationListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            SendWelcomeNotificationListener::class,
            SendNewUserJoinedNotificationListener::class,
        ],
        AfterVisitCreated::class => [
            SendNewVisitNotificationToTherapistListener::class,
            SendNewVisitNotificationToUserListener::class,
        ],
        AfterVisitCanceledByUser::class => [
            SendVisitCanceledByUserNotificationToTherapistListener::class,
            SendVisitCanceledByUserNotificationToUserListener::class,
        ],
        AfterVisitConfirmed::class => [
            SendVisitConfirmedNotificationToUserListener::class,
        ],

        AfterVisitCanceledByAdmin::class => [
            SendVisitCanceledByAdminNotificationToUserListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
