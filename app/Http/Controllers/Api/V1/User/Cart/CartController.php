<?php

namespace App\Http\Controllers\Api\V1\User\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
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
}
