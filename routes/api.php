<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

                /** Auth user */

Route::group(
    ["prefix" => "auth"],
    function (){
        Route::controller(AuthController::class)->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
            Route::post('send-otp', 'sendOtp');
            Route::post('verify-otp', 'verifyOtp');
            Route::post('forgot-password', 'forgotPassword');
            Route::post('reset-password', 'resetPassword');
            Route::post('logout', 'logout')->middleware('auth:sanctum');

        });
    });
