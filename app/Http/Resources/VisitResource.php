<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            Visit::ID_COLUMN => $this->getId(),
            Visit::STATUS => $this->getStatus(),
            Visit::USER_RELATION => $this->whenLoaded(
                Visit::USER_RELATION, function () {
                return new UserResource($this->user);
            }),
            Visit::AVAILABLE_TERM_RELATION => $this->whenLoaded(
                Visit::AVAILABLE_TERM_RELATION, function () {
                return new AvailableTermResource($this->availableTerm);
            }),
        ];
    }
}
