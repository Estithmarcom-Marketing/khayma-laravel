<?php

use App\Http\Controllers\Api\V1\Application\Address\AddressController;
use App\Http\Controllers\Api\V1\Application\Auth\UserAuthController;
use App\Http\Controllers\Api\V1\Application\Brand\BrandController;
use App\Http\Controllers\Api\V1\Application\Cart\CartController;
use App\Http\Controllers\Api\V1\Application\Category\CategoryController;
use App\Http\Controllers\Api\V1\Application\City\CityController;
use App\Http\Controllers\Api\V1\Application\Color\ColorController;
use App\Http\Controllers\Api\V1\Application\Home\HomeController;
use App\Http\Controllers\Api\V1\Application\Product\ProductController;
use App\Http\Controllers\Api\V1\Application\ProfileManagment\ProfileManagmentController;
use App\Http\Controllers\Api\V1\Application\Size\SizeController;
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
        Route::middleware(['throttle:60,1'])
            ->prefix('cities')
            ->group(function () {
                Route::get('', [CityController::class, 'index']);
                Route::get('{id}', [CityController::class, 'show']);
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
        Route::middleware('throttle:60,1')
            ->prefix('home')
            ->group(function () {
                Route::get('banners', [HomeController::class, 'getHomeBanners']);
                Route::get('categories', [HomeController::class, 'getHomeCategories']);
                Route::get('products/latest', [HomeController::class, 'getLatestProducts']);
                Route::get('products/common', [HomeController::class, 'getCommonProducts']);
                Route::get('products/most-orderd', [HomeController::class, 'getMostOrderdProducts']);
                Route::get('products/suggested', [HomeController::class, 'getSuggestedProducts']);
                Route::get('reviews', [HomeController::class, 'getHomeReviews']);
                Route::get('questions/common', [HomeController::class, 'getCommonQuestions']);
            });
        Route::middleware('throttle:60,1')
            ->prefix('products')
            ->group(function () {
                Route::get('', [ProductController::class, 'filter']);
                Route::get('{identifier}/variations', [ProductController::class, 'showVariations']);
                Route::get('{identifier}/related', [ProductController::class, 'getRelatedProducts']);
                Route::get('{identifier}', [ProductController::class, 'show']);
            });
        Route::middleware(['throttle:60,1'])
            ->prefix('categories')
            ->group(function () {
                Route::get('', [CategoryController::class, 'index']);
                Route::get('{id}', [CategoryController::class, 'show']);
            });
        Route::middleware(['throttle:60,1'])
            ->prefix('brands')
            ->group(function () {
                Route::get('', [BrandController::class, 'index']);
                Route::get('{id}', [BrandController::class, 'show']);
            });
        Route::middleware(['throttle:60,1'])
            ->prefix('colors')
            ->group(function () {
                Route::get('', [ColorController::class, 'index']);
                Route::get('{id}', [ColorController::class, 'show']);
            });
        Route::middleware(['throttle:60,1'])
            ->prefix('sizes')
            ->group(function () {
                Route::get('', [SizeController::class, 'index']);
                Route::get('{id}', [SizeController::class, 'show']);
            });
            Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('cart')
            ->group(function () {
                Route::get('', [CartController::class, 'getCartItems']);
                Route::post('', [CartController::class, 'addToCart']);
                Route::patch('{variationId}', [CartController::class, 'updateCartItem']);
                Route::delete('{variationId}', [CartController::class, 'removeCartItem']);
                Route::delete('', [CartController::class, 'clearCart']);
            });
    });
