<?php

namespace App\Listeners;

use App\Events\AfterVisitCanceledByAdmin;
use App\Jobs\SendNotificationJob;
use App\Models\Location;
use App\Models\Visit;
use App\Notifications\VisitCanceledByAdminNotificationToUser;
use Illuminate\Support\Facades\Log;

class SendVisitCanceledByAdminNotificationToUserListener
{
    /**
     * Handle the event.
     */
    public function handle(AfterVisitCanceledByAdmin $event): void
    {
        $visit = Visit::where(Visit::ID_COLUMN, $event->getVisitId())
            ->withTrashed()
            ->with(Visit::AVAILABLE_TERM_RELATION, Visit::USER_RELATION)->first();

        $patient = $visit->getRelationValue(Visit::USER_RELATION);

        $visitTerm = $visit->getRelationValue(Visit::AVAILABLE_TERM_RELATION);

        if (!$visitTerm) {
            try {
                $visitTerm = $visit->load([Visit::AVAILABLE_TERM_RELATION => function ($query) {
                    $query->withTrashed();
                }])->availableTerm;
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }

        $location = Location::where(Location::ID_COLUMN, $visitTerm->getLocationId())->first();

        $notifiable = $patient;

        $notification = new VisitCanceledByAdminNotificationToUser($patient->getUserFullName(), $location->getName(), $visitTerm->getDate(), $visitTerm->getTime());

        SendNotificationJob::dispatch($notification, $notifiable, VisitCanceledByAdminNotificationToUser::class);
    }
}
