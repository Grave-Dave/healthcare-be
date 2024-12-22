<?php

namespace App\Traits;

use App\Http\Permissions\PermissionsService;
use Illuminate\Auth\Access\AuthorizationException;

trait Authorization
{
    /**
     * @throws AuthorizationException
     */
    public function authorizeUser($user): void
    {
        /** @var PermissionsService $service */
        $service = app(PermissionsService::class);

        if (!$service->hasUserPermissions($user)) {

            throw new AuthorizationException("You don't have permissions for that action", 403);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function authorizeAdmin($user): void
    {
        /** @var PermissionsService $service */
        $service = app(PermissionsService::class);

        if (!$service->hasAdminPermissions($user)) {

            throw new AuthorizationException("You don't have permissions for that action", 403);
        }
    }
}
