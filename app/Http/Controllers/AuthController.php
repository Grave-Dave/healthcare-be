<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Therapist;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Return access token, refresh token, and set refresh token cookie.
     *
     * @param $tokens
     * @return JsonResponse
     */
    protected function generateTokenResponse($tokens): JsonResponse
    {
        return response()->json([
            'access_token' => $tokens['accessToken'],
            'token_type' => 'Bearer',
        ])->cookie('refresh_token', $tokens['refreshToken'], 60 * 24, '/', null, true, true);
    }

    /**
     * @throws Exception
     */
    public function register(Request $request, AuthService $service): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'phone' => 'required|string|min:9|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldUser = User::withTrashed()->where('email', $request->email)->first();

        if ($oldUser) {
            $oldUser->restore();
            $oldUser->update([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $user = $oldUser;
        } else {
            $user = User::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);
        }

        if ($user instanceof MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }

        $tokens = $service->generateTokens($user);

        return $this->generateTokenResponse($tokens);
    }

    /**
     * @throws Exception
     */
    public function login(Request $request, AuthService $service): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $tokens = $service->generateTokens($user);

            return $this->generateTokenResponse($tokens);
        }

        return response()->json(['message' => 'Nieprawidłowe dane'], 403);
    }

    public function checkAuth(Request $request): JsonResponse
    {
        $userId = $request->user()->getId();

        $user = User::where(User::ID_COLUMN, $userId)
            ->firstOrFail();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $isAdmin = Therapist::where(Therapist::USER_ID, $user->getId())->exists();

        return response()->json(['user' => new UserResource($user), 'isAdmin' => $isAdmin]);
    }

    /**
     * @throws Exception
     */
    public function refreshToken(Request $request, AuthService $service): JsonResponse
    {
        $refreshToken = $request->cookie('refresh_token');

        $usersWithTokens = User::where(User::REFRESH_TOKEN_EXPIRE_DATE, '>', Carbon::now())->get();

        $matchingUser = $usersWithTokens->first(fn($user) => Hash::check($refreshToken, $user->refresh_token));

        if (!$matchingUser) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        $matchingUser->tokens()->delete();

        $tokens = $service->generateTokens($matchingUser);

        return $this->generateTokenResponse($tokens);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();

        $user->update([
            'refresh_token' => null,
            'refresh_token_expires_at' => null,
        ]);

        return response()->json(['message' => 'Logged out successfully'])
            ->cookie('refresh_token', '', -1);
    }
}
