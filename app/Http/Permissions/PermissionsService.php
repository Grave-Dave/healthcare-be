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

    public function hasUserPermissions($user): bool
    {
        $existingUser = $this->findUser($user);

        if ($existingUser) {
            return true;
        };

        return false;
    }

    public function hasAdminPermissions($user): bool
    {
        $existingUser = $this->findUser($user);

        $isAdmin = Therapist::where(Therapist::USER_ID, $existingUser?->getId())->exists();

        if ($isAdmin) {
            return true;
        };

        return false;
    }
}
