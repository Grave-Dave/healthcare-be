<?php

namespace App\Http\Controllers;

use App\Events\AfterVisitCanceledByUser;
use App\Events\AfterVisitCreated;
use App\Http\Resources\VisitCollectionResource;
use App\Http\Resources\VisitResource;
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

class VisitController extends Controller
{
    use Authorization;

    /**
     * Show all incoming and past visits for a user.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeUser($user);

        $currentDateTime = now();

        $incomingVisits = Visit::select(
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
            ->where(Visit::TABLE_NAME . "." . Visit::USER_ID, $user->getId())
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, '>', $currentDateTime)
            ->with([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION])
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE)
            ->get();

        $pastVisits = Visit::select(
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
            ->where(Visit::TABLE_NAME . "." . Visit::USER_ID, $user->getId())
            ->where(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, '<=', $currentDateTime)
            ->with([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION])
            ->orderBy(AvailableTerm::TABLE_NAME . "." . AvailableTerm::DATE, 'desc')
            ->get();

        return response()->json([
            'message' => 'Visits retrieved successfully.',
            'incoming_visits' => new VisitCollectionResource($incomingVisits),
            'past_visits' => new VisitCollectionResource($pastVisits),
        ]);
    }

    /**
     * Create a new visit based on an available term.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException|ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeUser($user);

        $validator = Validator::make($request->all(), [
            'availableTermId' => ['required', Rule::exists(AvailableTerm::TABLE_NAME, AvailableTerm::ID_COLUMN)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();

            $availableTerm = AvailableTerm::where(AvailableTerm::ID_COLUMN, $validatedData['availableTermId'])
                ->where(AvailableTerm::STATUS, AvailableTerm::STATUS_AVAILABLE)
                ->first();

            if (!$availableTerm) {
                return response()->json(['error' => 'The selected term is not available.'], 404);
            }

            $availableTerm->status = AvailableTerm::STATUS_BOOKED;
            $availableTerm->save();

            $visit = Visit::create([
                Visit::USER_ID => $user->getId(),
                Visit::AVAILABLE_TERM_ID => $availableTerm->getId(),
                Visit::STATUS => Visit::STATUS_PENDING
            ]);

            DB::commit();

            $visit->load([Visit::AVAILABLE_TERM_RELATION . "." . AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION]);

            event(new AfterVisitCreated($visit->getId()));

            return response()->json(new VisitResource($visit), 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'An error occurred while creating the visit.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel a visit and update the available term status.
     *
     * @param int $visitId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(int $visitId): JsonResponse
    {
        $user = Auth::user();

        $visit = Visit::find($visitId);

        if (!$visit) {
            return response()->json(['message' => 'Visit not found.'], 404);
        }

        $this->authorizeUser($user, $visit);

        try {
            DB::beginTransaction();

            $availableTerm = $visit->availableTerm;
            if (!$availableTerm || $availableTerm->date <= now()) {
                return response()->json(['error' => 'Only incoming visits can be canceled.'], 422);
            }

            $visit->status = Visit::STATUS_CANCELED;
            $visit->save();
            $visit->delete();

            $availableTerm->status = AvailableTerm::STATUS_AVAILABLE;
            $availableTerm->save();

            DB::commit();

            event(new AfterVisitCanceledByUser($visit->getId()));

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
