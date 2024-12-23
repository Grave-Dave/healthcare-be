<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationCollectionResource;
use App\Models\Location;
use App\Models\Therapist;
use App\Traits\Authorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\String\u;

class LocationController extends Controller
{
    use Authorization;
    use SoftDeletes;

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $locations = Location::whereHas(Location::THERAPIST_RELATION, function ($therapistQuery) use ($user) {
            $therapistQuery->where(Therapist::USER_ID, $user->getId());
        })->get();

        return response()->json(new LocationCollectionResource($locations));
    }
}
