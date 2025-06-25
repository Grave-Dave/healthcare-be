<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function generateTokens(User|Authenticatable $user): array
    {
        $accessToken = $user->createToken('access-token');

        $refreshToken = base64_encode(random_bytes(64));

        $user->update([
            'refresh_token' => Hash::make($refreshToken),
            'refresh_token_expires_at' => Carbon::now()->addDays(),
        ]);

        return [
            'accessToken' => $accessToken->plainTextToken,
            'refreshToken' => $refreshToken,
        ];
    }
}
