<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailableTermController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
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

/** SECURED */

Route::middleware(['auth:sanctum'])->group(function () {

    /** AUTH */

    Route::get('/user', [AuthController::class, 'checkAuth']);

    Route::post('/logout', [AuthController::class, 'logout']);

    /** USER DATA */

    Route::delete('/user-delete', [UserController::class, 'delete']);

    Route::post('/user-update', [UserController::class, 'update']);

    /** LOCATIONS */

    Route::get('/locations', [LocationController::class, 'index']);

    /** AVAILABLE TERMS */

    Route::post('/month-terms', [AvailableTermController::class, 'index']);

    Route::post('/terms', [AvailableTermController::class, 'show']);

    Route::post('/new-terms', [AvailableTermController::class, 'store']);

    Route::delete('/term/{termId}', [AvailableTermController::class, 'delete']);

    /** USER VISITS */

    Route::post('/new-visit', [VisitController::class, 'store']);

});
