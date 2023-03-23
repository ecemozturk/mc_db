<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CeteleController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'getUserData']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Change password
Route::post('/change-password', [AuthController::class, 'changePassword']);

//Two factor
Route::post('/enable-two-factor', [AuthController::class, 'enableTwoFactor']);
Route::post('/disable-two-factor', [AuthController::class, 'disableTwoFactor']);
Route::post('/verify-two-factor', [AuthController::class, 'verifyTwoFactor']);

//QR
Route::middleware([\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, \Illuminate\Routing\Middleware\SubstituteBindings::class])
    ->post('/generateQRCode', [\App\Http\Controllers\AuthController::class, 'generateQRCode']);
//Enable 2FA
Route::post('/enable2FA', 'TwoFactorAuthController@enableTwoFactorAuthentication');



////Api start
Route::get('/cities', [\App\Http\Controllers\Api\CityController::class, 'index']);
Route::get('/kurumlar', [\App\Http\Controllers\Api\KurumController::class, 'index']);
Route::apiResource('countries', CountryController::class)->only(['index']);
////Api end


Route::apiResource('calendars', \App\Http\Controllers\Api\CalendarController::class);

Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::post('/events', [\App\Http\Controllers\EventController::class, 'store'])->name('events.store');
Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');
Route::put('/events/{event}', [\App\Http\Controllers\EventController::class, 'update'])->name('events.update');
Route::delete('/events/{event}', [\App\Http\Controllers\EventController::class, 'destroy'])->name('events.destroy');


Route::get('/ceteles', [CeteleController::class, 'index']);
Route::post('/ceteles', [CeteleController::class, 'store']);
Route::get('/ceteles/{id}', [CeteleController::class, 'show']);
Route::put('/ceteles/{id}', [CeteleController::class, 'update']);
Route::delete('/ceteles/{id}', [CeteleController::class, 'destroy']);

