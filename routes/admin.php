<?php

use App\Http\Controllers\Api\V1\Admin\AdminManagment\AdminManagmentController;
use App\Http\Controllers\Api\V1\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\Brand\BrandController;
use App\Http\Controllers\Api\V1\Admin\Category\CategoryController;
use App\Http\Controllers\Api\V1\Admin\City\CityController;
use App\Http\Controllers\Api\V1\Admin\City\CityShipmentController;
use App\Http\Controllers\Api\V1\Admin\Color\ColorController;
use App\Http\Controllers\Api\V1\Admin\CommonQuestion\CommonQuestionController;
use App\Http\Controllers\Api\V1\Admin\DeliveryMethod\DeliveryMethodController;
use App\Http\Controllers\Api\V1\Admin\Home\HomeManagementController;
use App\Http\Controllers\Api\V1\Admin\Payment\PaymentGatewayController;
use App\Http\Controllers\Api\V1\Admin\Payment\PaymentMethodController;
use App\Http\Controllers\Api\V1\Admin\Product\ProductController;
use App\Http\Controllers\Api\V1\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Api\V1\Admin\Property\PropertyController;
use App\Http\Controllers\Api\V1\Admin\RolesAndPermissions\RolesPermissionsController;
use App\Http\Controllers\Api\V1\Admin\Size\SizeController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/v1')
    ->group(function () {
        Route::post('store', [AdminManagmentController::class, 'store'])->middleware(['permission:store-admin', 'throttle:10,1']);
        Route::prefix('auth')
            ->group(function () {
                Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');
                Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1', 'role:super-admin'])
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

                Route::post('roles-with-permissions', [RolesPermissionsController::class, 'storeRoleWithPermissions']); // the url is temporary for testing and will be updated when be accepted
                Route::patch('roles/{role}', [RolesPermissionsController::class, 'updateRoleWithPermissions']);
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('cities')
            ->group(function () {
                Route::get('', [CityController::class, 'index'])->middleware('permission:read-city');
                Route::post('', [CityController::class, 'store'])->middleware('permission:store-city');
                Route::get('active', [CityController::class, 'getActive'])->middleware('permission:read-city');
                Route::get('shipments', [CityController::class, 'listWithShipments'])->middleware('permission:read-city');
                Route::patch('{city}', [CityController::class, 'update'])->middleware('permission:store-city');
                Route::get('{city}', [CityController::class, 'show'])->middleware('permission:read-city');
                Route::delete('{city}', [CityController::class, 'destroy'])->middleware('permission:delete-city');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('city-shipments')
            ->group(function () {
                Route::get('', [CityShipmentController::class, 'index'])->middleware('permission:read-city-shipment');
                Route::get('{cityShipment}', [CityShipmentController::class, 'show'])->middleware('permission:read-city-shipment');
                Route::post('cities/{city}', [CityShipmentController::class, 'store'])->middleware('permission:store-city-shipment');
                Route::patch('{cityShipment}', [CityShipmentController::class, 'update'])->middleware('permission:store-city-shipment');
                Route::delete('{cityShipment}', [CityShipmentController::class, 'destroy'])->middleware('permission:delete-city-shipment');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('colors')
            ->group(function () {
                Route::get('', [ColorController::class, 'index'])->middleware('permission:read-colors');
                Route::post('', [ColorController::class, 'store'])->middleware('permission:store-color');
                Route::patch('{color}', [ColorController::class, 'update'])->middleware('permission:store-color');
                Route::get('{color}', [ColorController::class, 'show'])->middleware('permission:read-colors');
                Route::delete('{color}', [ColorController::class, 'destroy'])->middleware('permission:delete-color');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('sizes')
            ->group(function () {
                Route::get('', [SizeController::class, 'index'])->middleware('permission:read-sizes');
                Route::post('', [SizeController::class, 'store'])->middleware('permission:store-size');
                Route::patch('{size}', [SizeController::class, 'update'])->middleware('permission:store-size');
                Route::get('{size}', [SizeController::class, 'show'])->middleware('permission:read-sizes');
                Route::delete('{size}', [SizeController::class, 'destroy'])->middleware('permission:delete-size');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('brands')
            ->group(function () {
                Route::get('', [BrandController::class, 'index'])->middleware('permission:read-brands');
                Route::post('', [BrandController::class, 'store'])->middleware('permission:store-brand');
                Route::patch('{brand}', [BrandController::class, 'update'])->middleware('permission:store-brand');
                Route::get('{brand}', [BrandController::class, 'show'])->middleware('permission:read-brands');
                Route::delete('{brand}', [BrandController::class, 'destroy'])->middleware('permission:delete-brand');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('promo-codes')
            ->group(function () {
                Route::get('', [PromoCodeController::class, 'index'])->middleware('permission:read-promo-codes');
                Route::get('active', [PromoCodeController::class, 'getActive'])->middleware('permission:read-promo-codes');
                Route::post('', [PromoCodeController::class, 'store'])->middleware('permission:store-promo-code');
                Route::patch('{promoCode}', [PromoCodeController::class, 'update'])->middleware('permission:store-promo-code');
                Route::get('{promoCode}', [PromoCodeController::class, 'show'])->middleware('permission:read-promo-codes');
                Route::delete('{promoCode}', [PromoCodeController::class, 'destroy'])->middleware('permission:delete-promo-code');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('delivery-methods')
            ->group(function () {
                Route::get('', [DeliveryMethodController::class, 'index'])->middleware('permission:read-delivery-methods');
                Route::get('active', [DeliveryMethodController::class, 'getActive'])->middleware('permission:read-delivery-methods');
                Route::post('', [DeliveryMethodController::class, 'store'])->middleware('permission:store-delivery-method');
                Route::patch('{deliveryMethod}', [DeliveryMethodController::class, 'update'])->middleware('permission:store-delivery-method');
                Route::get('{deliveryMethod}', [DeliveryMethodController::class, 'show'])->middleware('permission:read-delivery-methods');
                Route::delete('{deliveryMethod}', [DeliveryMethodController::class, 'destroy'])->middleware('permission:delete-delivery-method');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('payment-methods')
            ->group(function () {
                Route::get('', [PaymentMethodController::class, 'index'])->middleware('permission:read-payment-methods');
                Route::get('active', [PaymentMethodController::class, 'getActive'])->middleware('permission:read-payment-methods');
                Route::post('', [PaymentMethodController::class, 'store'])->middleware('permission:store-payment-method');
                Route::patch('{paymentMethod}', [PaymentMethodController::class, 'update'])->middleware('permission:store-payment-method');
                Route::get('{paymentMethod}', [PaymentMethodController::class, 'show'])->middleware('permission:read-payment-methods');
                Route::delete('{paymentMethod}', [PaymentMethodController::class, 'destroy'])->middleware('permission:delete-payment-method');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('payment-gateways')
            ->group(function () {
                Route::get('', [PaymentGatewayController::class, 'index'])->middleware('permission:read-payment-gateways');
                Route::get('active', [PaymentGatewayController::class, 'getActive'])->middleware('permission:read-payment-gateways');
                Route::get('{paymentGateway}', [PaymentGatewayController::class, 'show'])->middleware('permission:read-payment-gateways');
                Route::post('', [PaymentGatewayController::class, 'store'])->middleware('permission:store-payment-gateway');
                Route::patch('{paymentGateway}', [PaymentGatewayController::class, 'update'])->middleware('permission:store-payment-gateway');
                Route::delete('{paymentGateway}', [PaymentGatewayController::class, 'destroy'])->middleware('permission:delete-payment-gateway');
            });
        Route::middleware(['throttle:60,1', 'auth:sanctum'])
            ->prefix('categories')
            ->scopeBindings()
            ->group(function () {
                Route::get('', [CategoryController::class, 'index'])->middleware('permission:read-categories');
                Route::post('', [CategoryController::class, 'store'])->middleware('permission:store-category');
                Route::get('export-csv', [CategoryController::class, 'exportCsv'])->middleware('permission:read-categories');
                Route::get('export-excel', [CategoryController::class, 'exportExcel'])->middleware('permission:read-categories');
                Route::get('{category}', [CategoryController::class, 'show']);
                Route::get('{category}/sub-categories', [CategoryController::class, 'listSubCategories'])->middleware('permission:read-categories');

                Route::post('import', [CategoryController::class, 'importSpreadsheet'])->middleware('permission:store-category');
                Route::patch('{category}', [CategoryController::class, 'update'])->middleware('permission:store-category');
                Route::delete('{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete-category');
                Route::post('{category}/sub-categories', [CategoryController::class, 'storeSubCategory'])->middleware('permission:store-category');
                Route::patch('{category}/sub-categories/{subCategory}', [CategoryController::class, 'updateSubCategory'])->middleware('permission:store-category');
                Route::delete('{category}/sub-categories/{subCategory}', [CategoryController::class, 'destroySubCategory'])->middleware('permission:delete-category');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('properties')
            ->group(function () {
                Route::get('', [PropertyController::class, 'index'])->middleware('permission:read-properties');
                Route::post('', [PropertyController::class, 'store'])->middleware('permission:store-property');
                Route::patch('{property}', [PropertyController::class, 'update'])->middleware('permission:store-property');
                Route::get('{property}', [PropertyController::class, 'show'])->middleware('permission:read-properties');
                Route::delete('{property}', [PropertyController::class, 'destroy'])->middleware('permission:delete-property');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('products')
            ->group(function () {
                Route::get('', [ProductController::class, 'index'])->middleware('permission:read-products');
                Route::get('{product}', [ProductController::class, 'show'])->middleware('permission:read-products');
                Route::post('', [ProductController::class, 'store'])->middleware('permission:store-product');
                Route::patch('{product}', [ProductController::class, 'update'])->middleware('permission:store-product');
                Route::delete('{product}', [ProductController::class, 'destroy'])->middleware('permission:delete-product');

            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('products/{product}/variations')
            ->scopeBindings()
            ->group(function () {
                Route::get('', [ProductController::class, 'listProductVariations'])->middleware('permission:read-products');
                Route::get('{productVariation}', [ProductController::class, 'showProductVariation'])->middleware('permission:read-products');
                Route::post('', [ProductController::class, 'storeProductVariation'])->middleware('permission:store-product');
                Route::patch('{productVariation}', [ProductController::class, 'updateProductVariation'])->middleware('permission:store-product');
                Route::delete('{productVariation}', [ProductController::class, 'destroyProductVariation'])->middleware('permission:delete-product');

            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('products/{product}/variations/{productVariation}/properties')
            ->scopeBindings()
            ->group(function () {
                Route::get('', [ProductController::class, 'listProductVariationProperties'])->middleware('permission:read-products');
                Route::get('{property}', [ProductController::class, 'showProductVariationProperty'])->middleware('permission:read-products');
                Route::post('', [ProductController::class, 'storeProductVariationProperty'])->middleware('permission:store-product');
                Route::delete('{property}', [ProductController::class, 'destroyProductVariationProperty'])->middleware('permission:delete-product');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('questions')
            ->group(function () {
                Route::get('', [CommonQuestionController::class, 'index'])->middleware('permission:read-questions');
                Route::post('', [CommonQuestionController::class, 'store'])->middleware('permission:store-question');
                Route::patch('{commonQuestion}', [CommonQuestionController::class, 'update'])->middleware('permission:store-question');
                Route::get('{commonQuestion}', [CommonQuestionController::class, 'show'])->middleware('permission:read-questions');
                Route::delete('{commonQuestion}', [CommonQuestionController::class, 'destroy'])->middleware('permission:delete-question');
            });
        Route::middleware(['auth:sanctum', 'throttle:60,1'])
            ->prefix('banners')
            ->group(function () {
                Route::get('', [HomeManagementController::class, 'getBanners']);
                Route::get('home', [HomeManagementController::class, 'getHomeBanners']);
                Route::post('', [HomeManagementController::class, 'storeBanners']);
                Route::delete('{banner}', [HomeManagementController::class, 'destroy']);
            });
    });
