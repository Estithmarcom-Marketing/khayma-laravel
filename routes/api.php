<?php

use App\Http\Controllers\Api\V1\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\City\CityController;
use App\Http\Controllers\Api\V1\Admin\Color\ColorController;
use App\Http\Controllers\Api\V1\Admin\Size\SizeController;
use App\Http\Controllers\Api\V1\User\Address\AddressController;
use App\Http\Controllers\Api\V1\User\Auth\UserAuthController;
use App\Http\Controllers\Api\V1\User\ProfileManagment\ProfileManagmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')
    ->prefix('v1/admin/auth')
    ->group(function () {
        Route::post('/register', [AdminAuthController::class, 'register']);
        Route::post('/login', [AdminAuthController::class, 'login']);
        Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
    });

Route::middleware('throttle:10,1')
    ->prefix('v1/user/auth')
    ->group(function () {
        Route::post('/otp', [UserAuthController::class, 'sendOtp']);
        Route::post('/login', [UserAuthController::class, 'login']);
        Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
    });

Route::middleware('auth:sanctum')
    ->prefix('v1/profile')
    ->group(function () {
        Route::get('', [ProfileManagmentController::class, 'getAuthenticatedUser']);
        Route::patch('', [ProfileManagmentController::class, 'update']);

    });
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('v1/addresses')
    ->scopeBindings()
    ->controller(AddressController::class)
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('{address}', 'update');
        Route::get('{address}', 'show');
        Route::delete('{address}', 'destroy');
    });

Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('v1/cities')
    ->scopeBindings()
    ->controller(CityController::class)
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('{city}', 'update');
        Route::get('{city}', 'show');
        Route::delete('{city}', 'destroy');
    });
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('v1/colors')
    ->scopeBindings()
    ->controller(ColorController::class)
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('{color}', 'update');
        Route::get('{color}', 'show');
        Route::delete('{color}', 'destroy');
    });

Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('v1/sizes')
    ->scopeBindings()
    ->controller(SizeController::class)
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('{size}', 'update');
        Route::get('{size}', 'show');
        Route::delete('{size}', 'destroy');
    });
