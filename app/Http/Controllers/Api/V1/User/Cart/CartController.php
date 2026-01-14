<?php

namespace App\Http\Controllers\Api\V1\User\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreItemRequest;
use App\Http\Resources\Cart\CartItemResource;
use App\Http\Resources\Cart\CartResource;
use App\Models\CartProduct;
use App\Models\ProductVariation;
use App\Services\V1\User\Cart\CartService;
use App\Traits\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct(protected CartService $service) {}

    public function getCart()
    {
        try {
            $cart = $this->service->getCart();

            return ApiResponse::successResponse(['cart' => CartResource::make($cart)], 'Cart retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch cart', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch cart', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCartItems()
    {
        try {
            $cartItems = $this->service->getCartItems();

            return ApiResponse::successResponse(['cart_items' => CartItemResource::collection($cartItems)], 'Cart items retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch cart items', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch cart items', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addToCart(ProductVariation $productVariation, StoreItemRequest $request)
    {
        try {
            $cartItem = $this->service->addItem($productVariation, $request->validated());

            return ApiResponse::successResponse(
                ['cart_item' => $cartItem],
                'Cart item added successfully',
                Response::HTTP_CREATED);
        } catch (\Exception $e) {

            \Log::error('Failed to add cart item', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to add cart item', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCartItem(CartProduct $cartProduct, StoreItemRequest $request)
    {
        try {
            $cartItem = $this->service->updateItem($cartProduct, $request->validated());

            return ApiResponse::successResponse(
                ['cart_item' => CartItemResource::make($cartItem)],
                'Cart item updated successfully',
                Response::HTTP_CREATED);
        } catch (\Exception $e) {

            \Log::error('Failed to update cart item', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update cart item', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function removeCartItem(CartProduct $cartProduct)
    {
        try {
            $this->service->removeItem($cartProduct);

            return ApiResponse::successResponse([], 'Cart item removed successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {

            \Log::error('Failed to remove cart item', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to remove cart item', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function clearCart()
    {
        try {
            $this->service->clearCart();

            return ApiResponse::successResponse([], 'Cart cleared successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {

            \Log::error('Failed to clear cart', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to clear cart', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
