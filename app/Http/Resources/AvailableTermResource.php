<?php

namespace App\Http\Resources;

use App\Models\AvailableTerm;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableTermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            AvailableTerm::ID_COLUMN => $this->getId(),
            AvailableTerm::DATE => $this->getDate(),
            AvailableTerm::TIME => $this->getTime() .":00",
            AvailableTerm::STATUS => $this->getStatus(),
            AvailableTerm::LOCATION_RELATION => $this->whenLoaded(
                AvailableTerm::LOCATION_RELATION, function () {
                return [
                    Location::ID_COLUMN => $this->location->getId(),
                    Location::LOCATION_NAME => $this->location->getName(),
                ];
            }),
        ];
    }
}
