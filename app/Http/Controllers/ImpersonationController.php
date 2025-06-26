<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Authorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImpersonationController extends Controller
{
    use Authorization;

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function impersonate($userId): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeAdmin($user);

        $validator = Validator::make(
            compact('userId'),
            [
                'userId' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::findOrFail($userId);

        $userRefreshToken = base64_encode(random_bytes(64));

        $user->update([
            'refresh_token' => Hash::make($userRefreshToken),
            'refresh_token_expires_at' => Carbon::now()->addDays(),
        ]);

        return response()->json(['message' => 'Impersonation started'])
            ->cookie('refresh_token', $userRefreshToken, 60 * 24, '/', null, true, true);
    }
}
