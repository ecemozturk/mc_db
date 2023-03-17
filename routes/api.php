<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CeteleController;
use App\Http\Controllers\Api\CountryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

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

Route::apiResource('users', \App\Http\Controllers\UserController::class);



