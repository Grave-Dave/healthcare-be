<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * @throws Exception
     */
    public function handleGoogleCallback(AuthService $service): RedirectResponse
    {
        $baseUrl = env('APP_URL');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect($baseUrl . "/login/google");
        }

        try {
            $oldUser = User::onlyTrashed()->where('email', $googleUser->getEmail())->first();

            if ($oldUser) {
                $oldUser->restore();
                $oldUser->update([
                    'firstName' => $googleUser->user['given_name'] ?? null,
                    'lastName' => $googleUser->user['family_name'] ?? null,
                    'phone' => null,
                    'password' => bcrypt(uniqid()),
                ]);

                $user = $oldUser;
            } else {
                $user = User::firstOrCreate(
                    ['email' => $googleUser->getEmail()],
                    [
                        'firstName' => $googleUser->user['given_name'] ?? null,
                        'lastName' => $googleUser->user['family_name'] ?? null,
                        'avatar' => $googleUser->getAvatar(),
                        'password' => bcrypt(uniqid()),
                        'phone' => null,
                    ]
                );
            }

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();

                event(new Verified($user));
            }
        } catch (\Exception $e) {
            return redirect($baseUrl . "/login/google");
        }

        $tokens = $service->generateTokens($user);

        $refreshToken = $tokens['refreshToken'];

        return redirect($baseUrl . "/login/google?success=true")->cookie('refresh_token', $refreshToken, 60 * 24, '/', null, true, true);
    }
}
