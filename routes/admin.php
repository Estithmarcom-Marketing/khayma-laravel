<?php

use App\Http\Controllers\Api\V1\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\Brand\BrandController;
use App\Http\Controllers\Api\V1\Admin\Category\CategoryController;
use App\Http\Controllers\Api\V1\Admin\City\CityController;
use App\Http\Controllers\Api\V1\Admin\City\CityShipmentController;
use App\Http\Controllers\Api\V1\Admin\Color\ColorController;
use App\Http\Controllers\Api\V1\Admin\CommonQuestion\CommonQuestionController;
use App\Http\Controllers\Api\V1\Admin\DeliveryMethod\DeliveryMethodController;
use App\Http\Controllers\Api\V1\Admin\Payment\PaymentGatewayController;
use App\Http\Controllers\Api\V1\Admin\Payment\PaymentMethodController;
use App\Http\Controllers\Api\V1\Admin\Product\ProductController;
use App\Http\Controllers\Api\V1\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Api\V1\Admin\Property\PropertyController;
use App\Http\Controllers\Api\V1\Admin\RolesAndPermissions\RolesPermissionsController;
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

            ->group(function () {
                Route::get('roles', [RolesPermissionsController::class, 'getAllRoles']);
                Route::get('permissions', [RolesPermissionsController::class, 'getAllPermissions']);
                Route::post('roles', [RolesPermissionsController::class, 'storeRole']);
                Route::post('permissions', [RolesPermissionsController::class, 'storePermission']);

                Route::post('users/{user}/roles/{role}', [RolesPermissionsController::class, 'assignRoleToUser']);
                Route::delete('users/{user}/roles/{role}', [RolesPermissionsController::class, 'revokeRoleFromUser']);

                Route::post('roles/{role}/permissions/{permission}', [RolesPermissionsController::class, 'assignPermissionToRole']);
                Route::delete('roles/{role}/permissions/{permission}', [RolesPermissionsController::class, 'revokePermissionFromRole']);

                Route::get('users/{user}/roles', [RolesPermissionsController::class, 'getRolesByUser']);

                Route::get('roles/{role}/permissions', [RolesPermissionsController::class, 'getPermissionsByRole']);
                Route::get('roles/{role}/users', [RolesPermissionsController::class, 'getUsersByRole']);

                Route::get('permissions/{permission}/roles', [RolesPermissionsController::class, 'getRolesByPermission']);
            });

        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('cities')
            ->group(function () {
                Route::get('', [CityController::class, 'index']);
                Route::post('', [CityController::class, 'store']);
                Route::get('active', [CityController::class, 'getActive']);
                Route::get('with-shipments', [CityController::class, 'listWithShipments']);
                Route::patch('{city}', [CityController::class, 'update']);
                Route::get('{city}', [CityController::class, 'show']);
                Route::delete('{city}', [CityController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('city-shipments')
            ->group(function () {
                Route::get('', [CityShipmentController::class, 'index']);
                Route::get('{cityShipment}', [CityShipmentController::class, 'show']);
                Route::post('cities/{city}', [CityShipmentController::class, 'store']);
                Route::patch('{cityShipment}', [CityShipmentController::class, 'update']);
                Route::delete('{cityShipment}', [CityShipmentController::class, 'destroy']);
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
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('payment-methods')
            ->group(function () {
                Route::get('', [PaymentMethodController::class, 'index']);
                Route::get('active', [PaymentMethodController::class, 'getActive']);
                Route::post('', [PaymentMethodController::class, 'store']);
                Route::patch('{paymentMethod}', [PaymentMethodController::class, 'update']);
                Route::get('{paymentMethod}', [PaymentMethodController::class, 'show']);
                Route::delete('{paymentMethod}', [PaymentMethodController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('payment-gateways')

            ->group(function () {
                Route::get('', [PaymentGatewayController::class, 'index']);
                Route::get('active', [PaymentGatewayController::class, 'getActive']);
                Route::get('{paymentGateway}', [PaymentGatewayController::class, 'show']);
                Route::post('', [PaymentGatewayController::class, 'store']);
                Route::patch('{paymentGateway}', [PaymentGatewayController::class, 'update']);
                Route::delete('{paymentGateway}', [PaymentGatewayController::class, 'destroy']);
            });
        Route::middleware(['throttle:60,1'])
            ->prefix('categories')
            ->scopeBindings()
            ->group(function () {

                Route::get('', [CategoryController::class, 'index']);
                Route::post('', [CategoryController::class, 'store']);
                Route::get('export-csv', [CategoryController::class, 'exportCsv']);
                Route::get('export-excel', [CategoryController::class, 'exportExcel']);
                Route::get('{category}', [CategoryController::class, 'show']);
                Route::get('{category}/sub-categories', [CategoryController::class, 'listSubCategories']);

                Route::post('import', [CategoryController::class, 'importSpreadsheet']);
                Route::patch('{category}', [CategoryController::class, 'update'])->middleware('auth:sanctum');
                Route::delete('{category}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum');
                Route::post('{category}/sub-categories', [CategoryController::class, 'storeSubCategory'])->middleware('auth:sanctum');
                Route::patch('{category}/sub-categories/{subCategory}', [CategoryController::class, 'updateSubCategory'])->middleware('auth:sanctum');
                Route::delete('{category}/sub-categories/{subCategory}', [CategoryController::class, 'destroySubCategory'])->middleware('auth:sanctum');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('properties')
            ->group(function () {
                Route::get('', [PropertyController::class, 'index']);
                Route::post('', [PropertyController::class, 'store']);
                Route::patch('{property}', [PropertyController::class, 'update']);
                Route::get('{property}', [PropertyController::class, 'show']);
                Route::delete('{property}', [PropertyController::class, 'destroy']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('products')
            ->group(function () {
                Route::get('', [ProductController::class, 'index']);
                Route::get('{product}', [ProductController::class, 'show']);
                Route::post('', [ProductController::class, 'store']);
                Route::patch('{product}', [ProductController::class, 'update']);
                Route::delete('{product}', [ProductController::class, 'destroy']);

            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('products/{product}/variations')
            ->scopeBindings()
            ->group(function () {
                Route::get('', [ProductController::class, 'listProductVariations']);
                Route::get('{productVariation}', [ProductController::class, 'showProductVariation']);
                Route::post('', [ProductController::class, 'storeProductVariation']);
                Route::patch('{productVariation}', [ProductController::class, 'updateProductVariation']);
                Route::delete('{productVariation}', [ProductController::class, 'destroyProductVariation']);

            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('products/{product}/variations/{productVariation}/properties')
            ->scopeBindings()
            ->group(function () {
                Route::get('', [ProductController::class, 'listProductVariationProperties']);
                Route::get('{property}', [ProductController::class, 'showProductVariationProperty']);
                Route::post('', [ProductController::class, 'storeProductVariationProperty']);
                Route::delete('{property}', [ProductController::class, 'destroyProductVariationProperty']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('questions')
            ->group(function () {
                Route::get('', [CommonQuestionController::class, 'index']);
                Route::post('', [CommonQuestionController::class, 'store']);
                Route::patch('{commonQuestion}', [CommonQuestionController::class, 'update']);
                Route::get('{commonQuestion}', [CommonQuestionController::class, 'show']);
                Route::delete('{commonQuestion}', [CommonQuestionController::class, 'destroy']);
            });

    });
