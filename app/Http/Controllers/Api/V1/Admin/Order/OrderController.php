<?php

namespace App\Http\Controllers\Api\V1\Admin\Order;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\GetOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Dashboard\Order\OrderResource;
use App\Models\Order;
use App\Services\V1\Admin\Order\OrderReceiptPdfService;
use App\Services\V1\Admin\Order\OrderService;
use App\Services\V1\Admin\SpreadsheetImportExport\SpreadsheetImportExportService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Orders')]
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private SpreadsheetImportExportService $spreadsheetService,
        private OrderReceiptPdfService $orderReceiptPdfService
    ) {}

    public function index(GetOrderRequest $request)
    {
        $orders = $this->orderService->getOrders($request);
        $data =  OrderResource::collection($orders)->response()->getData(true);
        return ApiResponse::successResponse(
            [
                'orders' => $data['data'],
                'meta' => $data['meta'],
                'links' => $data['links'],
            ],
            __('orders.fetched'),
            status: Response::HTTP_OK
        );
    }

    public function show($id)
    {
        $order = $this->orderService->getOrderById($id);
        if (!$order) {
            return ApiResponse::errorResponse(__('orders.not_found'), Response::HTTP_NOT_FOUND);
        }
        return ApiResponse::successResponse(new OrderResource($order), __('orders.fetched'), status: Response::HTTP_OK);
    }

    public function updateOrder($id, UpdateOrderRequest $request)
    {
        $order = $this->orderService->updateOrder($id, $request->validated());
        if (!$order) {
            return ApiResponse::errorResponse(__('orders.not_found'), Response::HTTP_NOT_FOUND);
        }
        return ApiResponse::successResponse(new OrderResource($order), __('orders.updated'), status: Response::HTTP_OK);
    }

    public function exportExcel()
    {
        return $this->spreadsheetService->exportExcel(new OrdersExport, 'orders');
    }
    public function receipt(Order $order)
    {
        try {
            $url = $this->orderReceiptPdfService->generate($order);
            return ApiResponse::successResponse([
                'receipt' => $url,
            ], __('orders.fetched'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch receipt', [
                'error' => $e->getMessage(),
                'method' => __METHOD__,
            ]);
            return ApiResponse::errorResponse(__('orders.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
