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

            return ApiResponse::successResponse(['order' => OrderResource::make($order)], __('orders.created'), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error('Failed to create order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_create'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancel($id)
    {
        try {
            $order = $this->service->cancel($id);

            return ApiResponse::successResponse(['order' => OrderResource::make($order)], __('orders.canceled'), Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to cancel order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_cancel'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $order = $this->service->show($id);

            return ApiResponse::successResponse(['order' => OrderResource::make($order)], __('orders.shown'), Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_show'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index()
    {
        try {
            $orders = $this->service->list();
            $orders = OrderResource::collection($orders)->response()->getData(true);

            return ApiResponse::successResponse(
                ['orders' =>$orders['data'],
                    'meta' => $orders['meta'],
                    'links' => $orders['links']],
                __('orders.fetched'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch orders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listCanceled()
    {
        try {
            $orders = $this->service->listCanceled();
            $orders = OrderResource::collection($orders)->response()->getData(true);

            return ApiResponse::successResponse(
                ['orders' => $orders['data'],
                    'meta' => $orders['meta'],
                    'links' => $orders['links']], __('orders.fetched'), Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch orders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
