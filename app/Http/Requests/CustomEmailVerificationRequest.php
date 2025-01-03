<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class CustomEmailVerificationRequest extends EmailVerificationRequest
{

    public function authorize(): bool
    {

        $user = User::findOrFail($this->route('id'));

        if (!$user) {
            return false;
        }

        if (!hash_equals((string)$this->route('hash'), sha1($user->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    public function fulfill(): bool
    {
        $user = User::findOrFail($this->route('id'));

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));

            return true;
        }

        return false;
    }
}
