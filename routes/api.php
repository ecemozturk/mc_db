<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('calendars', \App\Http\Controllers\Api\CalendarController::class);

Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::post('/events', [\App\Http\Controllers\EventController::class, 'store'])->name('events.store');
Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');
Route::put('/events/{event}', [\App\Http\Controllers\EventController::class, 'update'])->name('events.update');
Route::delete('/events/{event}', [\App\Http\Controllers\EventController::class, 'destroy'])->name('events.destroy');
