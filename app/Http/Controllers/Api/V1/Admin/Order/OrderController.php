<?php

namespace App\Http\Controllers\Api\V1\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\GetOrderRequest;
use App\Http\Resources\Order\AdminOrderResource;
use App\Services\V1\Admin\Order\OrderService;
use App\Traits\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService){}

    public function index(GetOrderRequest $request)
    {
        $orders = $this->orderService->getOrders($request);
        $data =  AdminOrderResource::collection($orders)->response()->getData(true);
        return ApiResponse::successResponse([
                'orders' => $data['data'],
                'meta' => $data['meta'],
                'links' => $data['links'],
            ],
            __('orders.fetched'),
            status: Response::HTTP_OK);
    }
}
