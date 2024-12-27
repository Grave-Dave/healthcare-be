<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Traits\Authorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use Authorization;
    use SoftDeletes;

    /**
     * Update the specified user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException|ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $this->authorizeUser($user);

        $validator = Validator::make($request->all(), [
            'firstName' => 'nullable|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->getId(),
            'email' => 'nullable|email|unique:users,email,' . $user->getId(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (isset($data[User::PASSWORD])) {
            $data[User::PASSWORD] = Hash::make($data[User::PASSWORD]);
        } else {
            unset($data[User::PASSWORD]);
        }

        $user->update($data);

        return response()->json(['message' => 'User data updated successfully', 'user' => new UserResource($user)]);
    }

    /**
     * Delete the specified user.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete()
    {
        $user = Auth::user();

        $this->authorizeUser($user);

        $user->tokens()->delete();

        $user->update([
            'refresh_token' => null,
            'refresh_token_expires_at' => null,
        ]);

        $user->delete();

        return response()->json(['message' => 'User account deleted successfully'])
            ->cookie('refresh_token', '', -1);
    }
}
