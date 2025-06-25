<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Requests\CustomEmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/** EMAIL VERIFICATION */

Route::get('/email/verify/{id}/{hash}', function (CustomEmailVerificationRequest $request) {
    if ($request->fulfill()) {
        return view('verification.success', ['message' => 'Sukces!']);
    }

    return view('verification.failed', ['message' => 'Coś poszło nie tak...']);
})->middleware(['signed'])->name('verification.verify');

/** GOOGLE AUTH */

Route::prefix('api/auth/google')->group(function () {

    Route::get('/redirect', [GoogleAuthController::class, 'redirectToGoogle']);

    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});
