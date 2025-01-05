<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AfterVisitCanceledByUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $visitId;

    public function __construct(int $visitId)
    {
        $this->visitId = $visitId;
    }

    public function getVisitId(): int
    {
        return $this->visitId;
    }
}
