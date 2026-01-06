<?php

use App\Http\Controllers\Api\V1\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\Brand\BrandController;
use App\Http\Controllers\Api\V1\Admin\City\CityController;
use App\Http\Controllers\Api\V1\Admin\Color\ColorController;
use App\Http\Controllers\Api\V1\Admin\DeliveryMethod\DeliveryMethodController;
use App\Http\Controllers\Api\V1\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Api\V1\Admin\Size\SizeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/admin')
    ->group(function () {

        Route::prefix('auth')
            ->group(function () {
                Route::post('store', [AdminAuthController::class, 'store']);
                Route::post('login', [AdminAuthController::class, 'login']);
                Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
            });

        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('cities')
            ->group(function () {
                Route::get('', [CityController::class, 'index']);
                Route::post('', [CityController::class, 'store']);
                Route::patch('{city}', [CityController::class, 'update']);
                Route::get('{city}', [CityController::class, 'show']);
                Route::delete('{city}', [CityController::class, 'destroy']);
            });

        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('colors')
            ->group(function () {
                Route::get('', [ColorController::class, 'index']);
                Route::post('', [ColorController::class, 'store']);
                Route::patch('{color}', [ColorController::class, 'update']);
                Route::get('{color}', [ColorController::class, 'show']);
                Route::delete('{color}', [ColorController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('sizes')
            ->group(function () {
                Route::get('', [SizeController::class, 'index']);
                Route::post('', [SizeController::class, 'store']);
                Route::patch('{size}', [SizeController::class, 'update']);
                Route::get('{size}', [SizeController::class, 'show']);
                Route::delete('{size}', [SizeController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('brands')
            ->group(function () {
                Route::get('', [BrandController::class, 'index']);
                Route::post('', [BrandController::class, 'store']);
                Route::patch('{brand}', [BrandController::class, 'update']);
                Route::get('{brand}', [BrandController::class, 'show']);
                Route::delete('{brand}', [BrandController::class, 'destroy']);
            });

        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('promo-codes')
            ->group(function () {
                Route::get('', [PromoCodeController::class, 'index']);
                Route::get('active', [PromoCodeController::class, 'getActive']);
                Route::post('', [PromoCodeController::class, 'store']);
                Route::patch('{promoCode}', [PromoCodeController::class, 'update']);
                Route::get('{promoCode}', [PromoCodeController::class, 'show']);
                Route::delete('{promoCode}', [PromoCodeController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('delivery-methods')
            ->group(function () {
                Route::get('', [DeliveryMethodController::class, 'index']);
                Route::get('active', [DeliveryMethodController::class, 'getActive']);
                Route::post('', [DeliveryMethodController::class, 'store']);
                Route::patch('{deliveryMethod}', [DeliveryMethodController::class, 'update']);
                Route::get('{deliveryMethod}', [DeliveryMethodController::class, 'show']);
                Route::delete('{deliveryMethod}', [DeliveryMethodController::class, 'destroy']);
            });
    });
