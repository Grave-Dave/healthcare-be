<?php

namespace App\Http\Permissions;

use App\Models\Therapist;
use App\Models\User;

/**
 * Class Permissions
 */
class PermissionsService
{
    public function findUser($user): ?User
    {
        return User::where(User::ID_COLUMN, $user->id)->first();
    }

    public function hasUserPermissions($user, $resource): bool
    {
        $existingUser = $this->findUser($user);

        if (!$existingUser) {
            return false;
        }

        if ($resource) {
            return $resource->user_id === $existingUser->getId();
        }

        return true;
    }

    public function hasAdminPermissions($user, $resource): bool
    {
        $existingUser = $this->findUser($user);

        if (!$existingUser) {
            return false;
        }

        $therapist = Therapist::where(Therapist::USER_ID, $existingUser->getId())->first();

        if (!$therapist) {
            return false;
        };

        if ($resource) {
            return $resource->therapist_id === $therapist->getId();
        }

        return true;
    }
}
