<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollectionResource;
use App\Http\Resources\VisitCollectionResource;
use App\Models\Therapist;
use App\Models\User;
use App\Traits\Authorization;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\AvailableTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminPanelController extends Controller
{
    use Authorization;

    /**
     * Display a list of all available past visits.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $validatedDate = $request->validate([
            'month' => ['required', 'string', 'size:2', 'regex:/^(0[1-9]|1[0-2])$/'],
            'year' => ['required', 'integer', 'min:2000'],
        ]);

        $month = $validatedDate['month'];
        $year = $validatedDate['year'];
        $today = Carbon::today();

        $therapistId = Therapist::where(Therapist::USER_ID, $user->getId())->first()->getId();

        $pastVisits = Visit::select(
            AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE
        )
            ->join(
                AvailableTerm::TABLE_NAME,
                Visit::TABLE_NAME . "." . Visit::AVAILABLE_TERM_ID,
                "=",
                AvailableTerm::TABLE_NAME . "." . AvailableTerm::ID_COLUMN
            )
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::THERAPIST_ID, $therapistId)
            ->whereYear(AvailableTerm::DATE, $year)
            ->whereMonth(AvailableTerm::DATE, $month)
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, '<=', $today)
            ->with([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION])
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, 'desc')
            ->pluck(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE);

        return response()->json($pastVisits);
    }

    /**
     * Display a list of past therapist visits for a chosen date.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function showByDate(Request $request): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $validatedDate = $request->validate([
            'date' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        $date = $validatedDate;

        $therapistId = Therapist::where(Therapist::USER_ID, $user->getId())->first()->getId();

        $pastDateVisits = Visit::select(
            Visit::TABLE_NAME . "." . Visit::ID_COLUMN,
            Visit::TABLE_NAME . "." . Visit::USER_ID,
            Visit::TABLE_NAME . "." . Visit::AVAILABLE_TERM_ID,
            Visit::TABLE_NAME . "." . Visit::STATUS,
        )
            ->join(
                AvailableTerm::TABLE_NAME,
                Visit::TABLE_NAME . "." . Visit::AVAILABLE_TERM_ID,
                "=",
                AvailableTerm::TABLE_NAME . "." . AvailableTerm::ID_COLUMN
            )
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::THERAPIST_ID, $therapistId)
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, $date)
            ->with([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION])
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::TIME)
            ->get();

        return response()->json(new VisitCollectionResource($pastDateVisits));
    }

    /**
     * Display a list of past therapist visits for a chosen user.
     *
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function showByUser(Request $request, int $userId): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $validator = Validator::make(['userId' => $userId], [
            'userId' => ['required', 'integer', Rule::exists(User::TABLE_NAME, User::ID_COLUMN)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $wantedUser = User::where(User::ID_COLUMN, $userId)->first();

        $therapistId = Therapist::where(Therapist::USER_ID, $user->getId())->first()->getId();

        $allUserVisits = Visit::select(
            Visit::TABLE_NAME . "." . Visit::ID_COLUMN,
            Visit::TABLE_NAME . "." . Visit::USER_ID,
            Visit::TABLE_NAME . "." . Visit::AVAILABLE_TERM_ID,
            Visit::TABLE_NAME . "." . Visit::STATUS,
        )
            ->join(
                AvailableTerm::TABLE_NAME,
                Visit::TABLE_NAME . "." . Visit::AVAILABLE_TERM_ID,
                "=",
                AvailableTerm::TABLE_NAME . "." . AvailableTerm::ID_COLUMN
            )
            ->where(Visit::TABLE_NAME . "." . Visit::USER_ID, $wantedUser->getId())
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::THERAPIST_ID, $therapistId)
            ->with([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION])
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE)
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::TIME)
            ->get();

        return response()->json(new VisitCollectionResource($allUserVisits));
    }
}
