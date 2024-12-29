<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisitCollectionResource;
use App\Models\Therapist;
use App\Traits\Authorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\AvailableTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminVisitController extends Controller
{
    use Authorization;

    /**
     * Show all incoming visits for the therapist.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $currentDateTime = now();

        $therapistId = Therapist::where(Therapist::USER_ID, $user->getId())->first()->getId();

        $incomingPendingVisits = Visit::select(
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
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, '>', $currentDateTime)
            ->where(Visit::TABLE_NAME . "." . Visit::STATUS, Visit::STATUS_PENDING)
            ->with([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION])
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE)
            ->get();

        $incomingConfirmedVisits = Visit::select(
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
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, '>', $currentDateTime)
            ->where(Visit::TABLE_NAME . "." . Visit::STATUS, Visit::STATUS_CONFIRMED)
            ->with([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION])
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE)
            ->get();

        return response()->json([
            'message' => 'Visits retrieved successfully.',
            'incoming_pending_visits' => new VisitCollectionResource($incomingPendingVisits),
            'incoming_confirmed_visits' => new VisitCollectionResource($incomingConfirmedVisits),
        ]);
    }

    /**
     * Confirm visit by the therapist.
     *
     * @param $visitId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update($visitId): JsonResponse
    {
        $user = Auth::user();

        $visit = Visit::find($visitId);

        if (!$visit) {
            return response()->json(['error' => 'Visit not found.'], 404);
        }

        $this->authorizeAdmin($user, $visit->availableTerm);

        if ($visit->status !== Visit::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Visit status can only be updated from pending to confirmed.',
            ], 400);
        }

        $visit->status = Visit::STATUS_CONFIRMED;
        $visit->save();

        return response()->json([
            'message' => 'Visit status updated successfully.',
        ]);
    }

    /**
     * Cancel a visit and update the available term status.
     *
     * @param Request $request
     * @param int $visitId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(Request $request, int $visitId): JsonResponse
    {
        $user = Auth::user();

        $data = array_merge($request->all(), ['visitId' => $visitId]);

        $validator = Validator::make($data, [
            'withTerm' => ['required', 'boolean'],
            'visitId' => ['required', 'integer', Rule::exists(Visit::TABLE_NAME, Visit::ID_COLUMN)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $visit = Visit::find($visitId);
        $withTerm = $request->withTerm;

        $this->authorizeAdmin($user, $visit->availableTerm);

        try {
            DB::beginTransaction();

            $availableTerm = $visit->availableTerm;
            if (!$availableTerm || $availableTerm->date <= now()) {
                return response()->json(['error' => 'Only incoming visits can be canceled.'], 422);
            }

            $visit->status = Visit::STATUS_CANCELED;
            $visit->save();
            $visit->delete();

            if ($withTerm) {
                $availableTerm->status = AvailableTerm::STATUS_CANCELED;
                $availableTerm->save();
                $availableTerm->delete();
            } else {
                $availableTerm->status = AvailableTerm::STATUS_AVAILABLE;
                $availableTerm->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Visit canceled successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'An error occurred while canceling the visit.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
