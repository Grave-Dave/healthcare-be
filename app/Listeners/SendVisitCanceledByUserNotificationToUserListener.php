<?php

namespace App\Listeners;

use App\Events\AfterVisitCanceledByUser;
use App\Jobs\SendNotificationJob;
use App\Models\Location;
use App\Models\Visit;
use App\Notifications\VisitCanceledByUserNotificationToUser;

class SendVisitCanceledByUserNotificationToUserListener
{
    /**
     * Handle the event.
     */
    public function handle(AfterVisitCanceledByUser $event): void
    {
        $visit = Visit::where(Visit::ID_COLUMN, $event->getVisitId())
            ->withTrashed()
            ->with(Visit::AVAILABLE_TERM_RELATION, Visit::USER_RELATION)->first();

        $patient = $visit->getRelationValue(Visit::USER_RELATION);

        $visitTerm = $visit->getRelationValue(Visit::AVAILABLE_TERM_RELATION);

        $location = Location::where(Location::ID_COLUMN, $visitTerm->getLocationId())->first();

        $notifiable = $patient;

        $notification = new VisitCanceledByUserNotificationToUser($patient->getUserFullName(), $location->getName(), $visitTerm->getDate(), $visitTerm->getTime());

        SendNotificationJob::dispatch($notification, $notifiable, VisitCanceledByUserNotificationToUser::class);
    }
}
