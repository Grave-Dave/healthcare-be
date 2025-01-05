<?php

namespace App\Listeners;

use App\Events\AfterVisitCreated;
use App\Jobs\SendNotificationJob;
use App\Models\Location;
use App\Models\Therapist;
use App\Models\User;
use App\Models\Visit;
use App\Notifications\NewVisitNotificationToTherapist;

class SendNewVisitNotificationToTherapistListener
{
    /**
     * Handle the event.
     */
    public function handle(AfterVisitCreated $event): void
    {
        $visit = Visit::where(Visit::ID_COLUMN, $event->getVisitId())
            ->with(Visit::AVAILABLE_TERM_RELATION, Visit::USER_RELATION)->first();

        $patient = $visit->getRelationValue(Visit::USER_RELATION);

        $visitTerm = $visit->getRelationValue(Visit::AVAILABLE_TERM_RELATION);

        $location = Location::where(Location::ID_COLUMN, $visitTerm->getLocationId())->first();

        $therapist = Therapist::where(Therapist::ID_COLUMN, $visitTerm->getTherapistId())->first();

        $notifiable = User::where(User::ID_COLUMN, $therapist->getUserId())->first();

        $notification = new NewVisitNotificationToTherapist($patient->getUserFullName(), $patient->getUserPhone(), $location->getName(), $visitTerm->getDate(), $visitTerm->getTime());

        SendNotificationJob::dispatch($notification, $notifiable, NewVisitNotificationToTherapist::class);
    }
}
