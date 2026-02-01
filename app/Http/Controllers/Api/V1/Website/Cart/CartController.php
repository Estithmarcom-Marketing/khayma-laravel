<?php

namespace App\Http\Controllers\Api\V1\Website\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreItemRequest;
use App\Http\Resources\Cart\CartItemResource;
use App\Http\Resources\Cart\CartResource;
use App\Models\CartProduct;
use App\Services\V1\Website\Cart\CartService;
use App\Traits\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct(protected CartService $service) {}

 

    public function getCartItems()
    {
        try {
            $cartItems = $this->service->getCartItems();

            return ApiResponse::successResponse(['cart_items' => CartItemResource::collection($cartItems)],
                __('cart.items_retrieved'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch cart items', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('cart.items_fetch_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addToCart(StoreItemRequest $request)
    {
        try {
            $cartItems = $this->service->addItems($request->validated());
           

            return ApiResponse::successResponse(
                [],
                __('cart.item_added'),
                Response::HTTP_CREATED);
        } catch (\Exception $e) {

            \Log::error('Failed to add cart item', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('cart.item_add_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCartItem(CartProduct $cartProduct, StoreItemRequest $request)
    {
        try {
            $cartItem = $this->service->updateItem($cartProduct, $request->validated());

            return ApiResponse::successResponse(
                ['cart_item' => CartItemResource::make($cartItem)],
                __('cart.item_updated'),
                Response::HTTP_OK);
        } catch (\Exception $e) {

            \Log::error('Failed to update cart item', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('cart.item_update_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function removeCartItem(CartProduct $cartProduct)
    {
        try {
            $this->service->removeItem($cartProduct);

            return ApiResponse::successResponse([], __('cart.item_removed'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {

            \Log::error('Failed to remove cart item', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('cart.item_remove_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function clearCart()
    {
        try {
            $this->service->clearCart();

            return ApiResponse::successResponse([], __('cart.cart_cleared'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {

            \Log::error('Failed to clear cart', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('cart.cart_clear_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
