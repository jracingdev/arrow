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

Route::middleware('throttle:otp')->group(function () {
    Route::post('/otp/send', [App\Http\Controllers\Api\PhoneOtpController::class, 'send']);
    Route::post('/otp/verify', [App\Http\Controllers\Api\PhoneOtpController::class, 'verify']);
});
