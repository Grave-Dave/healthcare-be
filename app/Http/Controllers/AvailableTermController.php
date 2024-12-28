<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvailableTermCollectionResource;
use App\Models\AvailableTerm;
use App\Models\Location;
use App\Models\Therapist;
use App\Traits\Authorization;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AvailableTermController extends Controller
{
    use Authorization;
    use SoftDeletes;

    /**
     * Display a list of all available future terms.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeUser($user);

        $validatedDate = $request->validate([
            'month' => ['required', 'string', 'size:2', 'regex:/^(0[1-9]|1[0-2])$/'],
            'year' => ['required', 'integer', 'min:2000'],
        ]);

        $month = $validatedDate['month'];
        $year = $validatedDate['year'];
        $today = Carbon::today();

        $futureTerms = AvailableTerm::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereDate('date', '>=', $today)
            ->where(AvailableTerm::STATUS, AvailableTerm::STATUS_AVAILABLE)
            ->distinct()
            ->orderBy(AvailableTerm::DATE)
            ->pluck(AvailableTerm::DATE);

        return response()->json($futureTerms);
    }

    /**
     * Display a list of available terms for a chosen date.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Request $request): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeUser($user);

        $validatedDate = $request->validate([
            'date' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        $date = $validatedDate;

        $availableTerms = AvailableTerm::where(AvailableTerm::THERAPIST_ID, Therapist::MAIN_THERAPIST_ID)
            ->whereDate(AvailableTerm::DATE, $date)
            ->where(AvailableTerm::STATUS, AvailableTerm::STATUS_AVAILABLE)
            ->with(AvailableTerm::LOCATION_RELATION)
            ->orderBy(AvailableTerm::TIME)
            ->get();

        return response()->json(new AvailableTermCollectionResource($availableTerms));
    }

    /**
     * Add new available terms.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $validator = Validator::make($request->all(), [
            'locationId' => ['required', 'integer', Rule::exists(Location::TABLE_NAME, Location::ID_COLUMN)],
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|array|size:2',
            'time.0' => 'required|integer|between:0,23',
            'time.1' => 'required|integer|between:0,23|gte:time.0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $locationId = $data['locationId'];
        $date = $data['date'];
        $startHour = $data['time'][0];
        $endHour = $data['time'][1];

        $therapistId = Therapist::where(Therapist::USER_ID, $user->getId())->first()->getId();

        $timeSlots = range($startHour, $endHour);

        $existingTerms = AvailableTerm::where(AvailableTerm::THERAPIST_ID, $therapistId)
            ->whereDate(AvailableTerm::DATE, $date)
            ->whereIn(AvailableTerm::TIME, $timeSlots)
            ->pluck(AvailableTerm::TIME)
            ->toArray();

        $newTimeSlots = array_diff($timeSlots, $existingTerms);

        $newTerms = [];

        foreach ($newTimeSlots as $time) {
            $newTerms[] = AvailableTerm::create([
                AvailableTerm::LOCATION_ID => $locationId,
                AvailableTerm::THERAPIST_ID => $therapistId,
                AvailableTerm::DATE => $date,
                AvailableTerm::TIME => $time,
                AvailableTerm::STATUS => AvailableTerm::STATUS_AVAILABLE,
            ])->load(AvailableTerm::LOCATION_RELATION);
        }

        return response()->json(new AvailableTermCollectionResource($newTerms), 201);
    }

    /**
     * Delete available term.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function delete($termId): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $validator = Validator::make(['id' => $termId], [
            'id' => ['required', 'integer', Rule::exists(AvailableTerm::TABLE_NAME, AvailableTerm::ID_COLUMN)]
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            $availableTerm = AvailableTerm::findOrFail($termId);
            $availableTermStatus = $availableTerm->getStatus();

            if ($availableTermStatus !== AvailableTerm::STATUS_AVAILABLE) {
                throw new AuthorizationException('Only available visits can be deleted.', 403);
            }

            $availableTerm->status = AvailableTerm::STATUS_CANCELED;
            $availableTerm->save();
            $availableTerm->delete();

            return response()->json([
                'message' => 'The visit has been successfully deleted.',
            ]);

        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Term not found.',
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the term.',
            ], 500);
        }
    }
}
