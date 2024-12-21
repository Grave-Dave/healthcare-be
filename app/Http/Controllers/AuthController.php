<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ], 201);
    }

    /**
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            $refreshToken = base64_encode(random_bytes(64));

            $user->update([
                'refresh_token' => $refreshToken,
                'refresh_token_expires_at' => Carbon::now()->addDays(7),
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ])->cookie('refresh_token', $refreshToken, 60 * 24 * 7, '/', null, true, true);
        }

        return response()->json(['message' => 'Nieprawidłowe dane'], 401);
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

        return response()->json(['user' => $user, 'isAdmin' => $isAdmin]);
    }

    /**
     * @throws Exception
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $refreshToken = $request->cookie('refresh_token');

        $user = User::where(User::REFRESH_TOKEN, $refreshToken)
            ->where(User::REFRESH_TOKEN_EXPIRE, '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        $newAccessToken = $user->createToken('access-token')->plainTextToken;

        $newRefreshToken = base64_encode(random_bytes(64));
        $user->update([
            'refresh_token' => $newRefreshToken,
            'refresh_token_expires_at' => Carbon::now()->addDays(7),
        ]);

        return response()->json([
            'access_token' => $newAccessToken,
            'token_type' => 'Bearer',
        ])->cookie('refresh_token', $newRefreshToken, 60 * 24 * 7, '/', null, true, true);
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
