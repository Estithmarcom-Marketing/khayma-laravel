<?php

use App\Http\Controllers\Api\V1\Application\Address\AddressController;
use App\Http\Controllers\Api\V1\Application\Auth\UserAuthController;
use App\Http\Controllers\Api\V1\Application\Brand\BrandController;
use App\Http\Controllers\Api\V1\Application\Cart\CartController;
use App\Http\Controllers\Api\V1\Application\Category\CategoryController;
use App\Http\Controllers\Api\V1\Application\City\CityController;
use App\Http\Controllers\Api\V1\Application\Color\ColorController;
use App\Http\Controllers\Api\V1\Application\CustomTent\CustomTentController;
use App\Http\Controllers\Api\V1\Application\DeliveryMethod\DeliveryMethodController;
use App\Http\Controllers\Api\V1\Application\Favourite\FavouriteController;
use App\Http\Controllers\Api\V1\Application\Home\HomeController;
use App\Http\Controllers\Api\V1\Application\Notification\NotificationController;
use App\Http\Controllers\Api\V1\Application\Order\OrderController;
use App\Http\Controllers\Api\V1\Application\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Api\V1\Application\Product\ProductController;
use App\Http\Controllers\Api\V1\Application\ProductReminder\ProductReminderController;
use App\Http\Controllers\Api\V1\Application\ProfileManagment\ProfileManagmentController;
use App\Http\Controllers\Api\V1\Application\Review\ReviewController;
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
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('favourites')
            ->group(function () {
                Route::get('', [FavouriteController::class, 'index']);
                Route::get('{favourite}', [FavouriteController::class, 'show']);
                Route::post('products/{product}', [FavouriteController::class, 'store']);
                Route::delete('products/{product}', [FavouriteController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('product-reminders')
            ->group(function () {
                Route::get('', [ProductReminderController::class, 'index']);
                Route::get('{productReminder}', [ProductReminderController::class, 'show']);
                Route::post('variations/{productVariation}', [ProductReminderController::class, 'store']);
                Route::delete('variations/{productVariation}', [ProductReminderController::class, 'destroy']);
            });
        Route::middleware(['throttle:60,1'])
            ->prefix('reviews')
            ->group(function () {
                Route::get('', [ReviewController::class, 'getMyReviews'])->middleware(['auth:sanctum']);
                Route::get('products/{product}', [ReviewController::class, 'getProductReviews']);
                Route::get('statistics/{id}', [ReviewController::class, 'getStatistics']);
                Route::post('', [ReviewController::class, 'store'])->middleware(['auth:sanctum']);
                Route::patch('{review}', [ReviewController::class, 'update'])->middleware(['auth:sanctum']);
                Route::delete('{review}', [ReviewController::class, 'destroy'])->middleware(['auth:sanctum']);
            });

        Route::get('delivery-methods', [DeliveryMethodController::class, 'index'])
            ->middleware('throttle:60,1');

        Route::get('payment-methods', [PaymentMethodController::class, 'index'])
            ->middleware('throttle:60,1');

        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('orders')
            ->group(function () {
                Route::post('', [OrderController::class, 'store']);
                Route::get('', [OrderController::class, 'index']);

                Route::get('filter', [OrderController::class, 'filter']);
                Route::post('calculate', [OrderController::class, 'calculateTotalAmountOfOrder']);

                Route::get('{order}/receipt', [OrderController::class, 'receipt']);
                Route::post('{id}/reorder', [OrderController::class, 'reorder']);
                Route::get('{id}', [OrderController::class, 'show']);
                Route::post('{id}/cancel', [OrderController::class, 'cancel']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('notifications')
            ->group(function () {
                Route::get('', [NotificationController::class, 'index']);
                Route::get('unread', [NotificationController::class, 'getUnReadNotifications']);
                Route::post('', [NotificationController::class, 'markAllAsRead']);
            });
            Route::post('tent', [CustomTentController::class, 'store']);

    });
