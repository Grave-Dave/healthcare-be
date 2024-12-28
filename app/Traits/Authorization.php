<?php

namespace App\Traits;

use App\Http\Permissions\PermissionsService;
use Illuminate\Auth\Access\AuthorizationException;

trait Authorization
{
    /**
     * @throws AuthorizationException
     */
    public function authorizeUser($user, $resource = false): void
    {
        /** @var PermissionsService $service */
        $service = app(PermissionsService::class);

        if (!$service->hasUserPermissions($user, $resource)) {

            throw new AuthorizationException("You don't have permissions for that action", 403);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function authorizeAdmin($user, $resource = false): void
    {
        /** @var PermissionsService $service */
        $service = app(PermissionsService::class);

        if (!$service->hasAdminPermissions($user, $resource)) {

            throw new AuthorizationException("You don't have permissions for that action", 403);
        }
    }
}
