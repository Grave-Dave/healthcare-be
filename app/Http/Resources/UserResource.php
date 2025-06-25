<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            User::ID_COLUMN => $this->getId(),
            User::FIRST_NAME => $this->getUserFirstName(),
            User::LAST_NAME => $this->getUserLastName(),
            User::PHONE => $this->getUserPhone(),
            User::EMAIL => $this->getUserEmail(),
            User::AVATAR => $this->getUserAvatar(),
        ];
    }
}
