<?php

namespace App\Http\Controllers\Api\V1\Application\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Order\CalculateTotalRequest;
use App\Http\Requests\Application\Order\FilterOrderRequest;
use App\Http\Requests\Application\Order\RepayOrderRequest;
use App\Http\Requests\Application\Order\StoreOrderRequest;
use App\Http\Resources\Application\Order\OrderResource;
use App\Models\Order;
use App\Services\V1\Website\Order\OrderReceiptPdfService;
use App\Services\V1\Website\Order\OrderService;
use App\Services\V1\Website\Order\StoreOrderService;
use App\Services\V1\Website\Payment\PaymentService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public $user;

    public function __construct(protected OrderService $service,
        protected StoreOrderService $storeOrderService,
        protected OrderReceiptPdfService $orderReceiptPdfService,
        protected PaymentService $paymentService)
    {
        $this->user = auth('sanctum')->user();
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->storeOrderService->store($request->validated());

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'payment_method' => $order->paymentMethod,
                'user_id' => $this->user->id,
            ]);

            try {
                $dto = $this->paymentService->initiateIfNeeded(
                    $order,
                    $request->gateway
                );
            } catch (\Exception $e) {
                Log::error('Payment failed, rolling back order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'method' => __METHOD__,
                ]);

                $this->storeOrderService->rollbackOrder($order);

                return ApiResponse::errorResponse(
                    __('orders.payment_failed_retry'),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            return ApiResponse::successResponse(
                [
                    'order' => OrderResource::make($order),
                    'redirect_url' => $dto?->redirectUrl,
                ],
                __('orders.created'),
                Response::HTTP_CREATED
            );

        } catch (\LogicException $e) {
            Log::error('Failed to create order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error('Failed to create order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_create'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancel($id)
    {
        try {
            $order = $this->service->cancel($id);

            return ApiResponse::successResponse(['order' => OrderResource::make($order)], __('orders.canceled'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to cancel order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_cancel'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $order = $this->service->show($id);

            return ApiResponse::successResponse(['order' => OrderResource::make($order)], __('orders.shown'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch order', ['error' => $e->getMessage(), 'url' => request()->url(), 'query' => request()->query(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_show'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index()
    {
        try {
            $orders = $this->service->list();
            $orders = OrderResource::collection($orders)->response()->getData(true);

            return ApiResponse::successResponse(
                ['orders' => $orders['data'],
                    'meta' => $orders['meta'],
                    'links' => $orders['links']],
                __('orders.fetched'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function filter(FilterOrderRequest $request)
    {
        try {
            $orders = $this->service->filter($request->validated());
            $orders = OrderResource::collection($orders)->response()->getData(true);

            return ApiResponse::successResponse(
                ['orders' => $orders['data'],
                    'meta' => $orders['meta'],
                    'links' => $orders['links']],
                __('orders.fetched'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders', ['error' => $e->getMessage(), 'url' => request()->url(), 'query' => request()->query(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reorder($id)
    {
        try {
            $items = $this->service->reorder($id);

            return ApiResponse::successResponse([], __('orders.reordered'), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('orders.error_reorder'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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

    public function calculateTotalAmountOfOrder(CalculateTotalRequest $request)
    {
        try {
            $totalAmount = $this->storeOrderService->calculateTotalAmountOfOrder($request->validated());

            return ApiResponse::successResponse($totalAmount, 'order price calculated successfully', Response::HTTP_OK);
        } catch (\LogicException $e) {
            Log::error('Failed to calculate total amount of order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error('Failed to calculate total amount of order', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to calculate total amount of order', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function repay(Order $order, RepayOrderRequest $request)
    {
        try {

            $result = $this->service->repay($order, $request->validated());

            return ApiResponse::successResponse(
                [
                    'order' => OrderResource::make($result['order']),
                    'redirect_url' => $result['redirect_url'],
                ],
                __('orders.repaid'),
                Response::HTTP_OK
            );

        } catch (\LogicException $e) {
            Log::error('Failed to repay order', [
                'error' => $e->getMessage(),
                'method' => __METHOD__,
            ]);

            return ApiResponse::errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {

            Log::error('Failed to repay order', [
                'error' => $e->getMessage(),
                'method' => __METHOD__,
            ]);

            return ApiResponse::errorResponse(
                __('orders.error_repay'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
