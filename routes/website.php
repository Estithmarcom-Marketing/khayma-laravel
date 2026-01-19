<?php

use App\Http\Controllers\Api\V1\User\Address\AddressController;
use App\Http\Controllers\Api\V1\User\Auth\UserAuthController;
use App\Http\Controllers\Api\V1\User\Cart\CartController;
use App\Http\Controllers\Api\V1\User\Favourite\FavouriteController;
use App\Http\Controllers\Api\V1\User\Home\HomeController;
use App\Http\Controllers\Api\V1\User\ProductReminder\ProductReminderController;
use App\Http\Controllers\Api\V1\User\ProfileManagment\ProfileManagmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/user')
    ->group(function () {

        Route::middleware('throttle:10,1')
            ->prefix('auth')
            ->group(function () {
                Route::post('otp', [UserAuthController::class, 'sendOtp']);
                Route::post('login', [UserAuthController::class, 'login']);
                Route::post('logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');

            });
        Route::middleware(['throttle:10,1', 'auth:sanctum'])
            ->prefix('profile')
            ->group(function () {
                Route::get('', [ProfileManagmentController::class, 'getAuthenticatedUser']);
                Route::patch('', [ProfileManagmentController::class, 'update']);
            });

        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('addresses')
            ->group(function () {
                Route::get('', [AddressController::class, 'index']);
                Route::post('', [AddressController::class, 'store']);
                Route::patch('{address}', [AddressController::class, 'update']);
                Route::get('{address}', [AddressController::class, 'show']);
                Route::delete('{address}', [AddressController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('cart')
            ->scopeBindings()
            ->group(function () {
                Route::get('', [CartController::class, 'getCart']);
                Route::get('{cart}', [CartController::class, 'getCartItems']);
                Route::post('items/{productVariation}', [CartController::class, 'addToCart']);
                Route::patch('items/{cartProduct}', [CartController::class, 'updateCartItem']);
                Route::delete('items/{cartProduct}', [CartController::class, 'removeCartItem']);
                Route::delete('', [CartController::class, 'clearCart']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('favourites')
            ->group(function () {
                Route::get('', [FavouriteController::class, 'index']);
                Route::get('{favourite}', [FavouriteController::class, 'show']);
                Route::post('products/{product}', [FavouriteController::class, 'store']);
                Route::delete('{favourite}', [FavouriteController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('product-reminders')
            ->group(function () {
                Route::get('', [ProductReminderController::class, 'index']);
                Route::get('{productReminder}', [ProductReminderController::class, 'show']);
                Route::post('variations/{productVariation}', [ProductReminderController::class, 'store']);
                Route::delete('{productReminder}', [ProductReminderController::class, 'destroy']);
            });
        Route::middleware('throttle:60,1')
            ->prefix('home')
            ->group(function () {
                Route::get('', [HomeController::class, 'getHomeBanners']);
                
            });
    });
