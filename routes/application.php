<?php

use App\Http\Controllers\Api\V1\Application\Auth\UserAuthController;
use App\Http\Controllers\Api\V1\Application\ProfileManagment\ProfileManagmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['locale'])
    ->prefix('application/v1')
    ->group(function () {
        Route::middleware('throttle:30,1')
            ->prefix('auth')
            ->group(function () {
                Route::post('otp', [UserAuthController::class, 'sendOtp']);
                Route::post('login', [UserAuthController::class, 'login']);
                Route::post('logout', [UserAuthController::class, 'logout'])
                    ->middleware(['auth:sanctum']);
            });

        Route::middleware(['throttle:60,1', 'auth:sanctum'])
            ->prefix('profile')
            ->group(function () {
                Route::get('', [ProfileManagmentController::class, 'getAuthenticatedUser']);
                Route::patch('', [ProfileManagmentController::class, 'update']);
            });
    });
