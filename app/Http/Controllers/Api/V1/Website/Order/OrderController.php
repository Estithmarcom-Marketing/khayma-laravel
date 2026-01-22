<?php

namespace App\Http\Controllers\Api\V1\Website\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Services\V1\Website\Order\OrderService;
use App\Traits\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->service->store($request->validated());

            return ApiResponse::successResponse(['order' => OrderResource::make($order)], 'Order created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error('Failed to create order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create order', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $order = $this->service->show($id);

            return ApiResponse::successResponse(['order' => OrderResource::make($order)], 'Order Fetched Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch order', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index()
    {
        try {
            $orders = $this->service->list();

            return ApiResponse::successResponse(['orders' => OrderResource::collection($orders)], 'Orders Fetched Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch orders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch orders', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
