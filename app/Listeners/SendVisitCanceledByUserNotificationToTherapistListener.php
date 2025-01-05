<?php

namespace App\Listeners;

use App\Events\AfterVisitCanceledByUser;
use App\Events\AfterVisitCreated;
use App\Jobs\SendNotificationJob;
use App\Models\Location;
use App\Models\Therapist;
use App\Models\User;
use App\Models\Visit;
use App\Notifications\NewVisitNotificationToTherapist;
use App\Notifications\VisitCanceledByUserNotificationToTherapist;

class SendVisitCanceledByUserNotificationToTherapistListener
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

        $therapist = Therapist::where(Therapist::ID_COLUMN, $visitTerm->getTherapistId())->first();

        $notifiable = User::where(User::ID_COLUMN, $therapist->getUserId())->first();

        $notification = new VisitCanceledByUserNotificationToTherapist($patient->getUserFullName(), $patient->getUserPhone(), $location->getName(), $visitTerm->getDate(), $visitTerm->getTime());

        SendNotificationJob::dispatch($notification, $notifiable, VisitCanceledByUserNotificationToTherapist::class);
    }
}
