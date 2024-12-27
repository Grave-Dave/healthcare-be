<?php

namespace App\Http\Controllers;

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

            $visit->load([Visit::AVAILABLE_TERM_RELATION . "." .  AvailableTerm::LOCATION_RELATION, Visit::USER_RELATION]);

            return response()->json(new VisitResource($visit), 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'An error occurred while creating the visit.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
