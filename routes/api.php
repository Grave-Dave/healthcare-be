<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AdminVisitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailableTermController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/** AUTH */

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/auth/refresh', [AuthController::class, 'refreshToken']);

Route::middleware('throttle:3,1')->group(function () {

    Route::post('password/email', [PasswordResetController::class, 'sendResetLink']);

    Route::post('password/reset', [PasswordResetController::class, 'resetPassword']);
});

/** SECURED */

Route::middleware(['auth:sanctum', 'throttle:3,1'])->post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email is already verified.'], 400);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification email sent.']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    /** AUTH */

    Route::get('/user', [AuthController::class, 'checkAuth']);

    Route::post('/logout', [AuthController::class, 'logout']);

    /** USER DATA */

    Route::delete('/user-delete', [UserController::class, 'delete']);

    Route::post('/user-update', [UserController::class, 'update']);

    Route::get('/users', [UserController::class, 'index']);

    /** LOCATIONS */

    Route::get('/locations', [LocationController::class, 'index']);

    /** AVAILABLE TERMS */

    Route::post('/month-terms', [AvailableTermController::class, 'index']);

    Route::post('/terms', [AvailableTermController::class, 'show']);

    Route::post('/new-terms', [AvailableTermController::class, 'store']);

    Route::delete('/term/{termId}', [AvailableTermController::class, 'delete']);

    /** USER VISITS */

    Route::get('/user-visits', [VisitController::class, 'index']);

    Route::post('/new-visit', [VisitController::class, 'store']);

    Route::delete('/delete-visit/{visitId}', [VisitController::class, 'delete']);

    /** ADMIN VISITS */

    Route::get('/admin-visits', [AdminVisitController::class, 'index']);

    Route::post('/update-visit/{visitId}', [AdminVisitController::class, 'update']);

    Route::delete('/admin-delete-visit/{visitId}', [AdminVisitController::class, 'delete']);

    /** ADMIN PANEL */

    Route::post('/admin-month-terms', [AdminPanelController::class, 'index']);

    Route::post('/admin-visits', [AdminPanelController::class, 'showByDate']);

    Route::get('/admin-user-visits/{userId}', [AdminPanelController::class, 'showByUser']);

});
