<?php

use App\Http\Controllers\Api\V1\Website\Address\AddressController;
use App\Http\Controllers\Api\V1\Website\Auth\UserAuthController;
use App\Http\Controllers\Api\V1\Website\Cart\CartController;
use App\Http\Controllers\Api\V1\Website\Favourite\FavouriteController;
use App\Http\Controllers\Api\V1\Website\Home\HomeController;
use App\Http\Controllers\Api\V1\Website\Product\ProductController;
use App\Http\Controllers\Api\V1\Website\ProductReminder\ProductReminderController;
use App\Http\Controllers\Api\V1\Website\ProfileManagment\ProfileManagmentController;
use App\Http\Controllers\Api\V1\Website\Review\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('website/v1')
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
        Route::middleware(['throttle:60,1', 'auth:sanctum'])
            ->prefix('reviews')
            ->group(function () {
                Route::get('', [ReviewController::class, 'getMyReviews']);
                Route::get('products/{product}', [ReviewController::class, 'getProductReviews']);
                Route::post('', [ReviewController::class, 'store']);
                Route::patch('{review}', [ReviewController::class, 'update']);
                Route::delete('{review}', [ReviewController::class, 'destroy']);
            });
        Route::middleware('throttle:60,1')
            ->prefix('home')
            ->group(function () {
                Route::get('banners', [HomeController::class, 'getHomeBanners']);
                Route::get('categories', [HomeController::class, 'getHomeCategories']);
                Route::get('latest-products', [HomeController::class, 'getLatestProducts']);
                Route::get('common-products', [HomeController::class, 'getCommonProducts']);
                Route::get('most-ordered-products', [HomeController::class, 'getMostOrderdProducts']);
                Route::get('reviews', [HomeController::class, 'getHomeReviews']);
                Route::get('common-questions', [HomeController::class, 'getCommonQuestions']);
            });
        Route::middleware('throttle:60,1')
            ->prefix('products')
            ->group(function () {
                Route::get('', [ProductController::class, 'filter']);
                Route::get('{id}', [ProductController::class, 'show']);
            });
    });
