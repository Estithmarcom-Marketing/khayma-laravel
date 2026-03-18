<?php

namespace App\Http\Controllers\Api\V1\Admin\Stats;

use App\Http\Controllers\Controller;
use App\Http\Resources\Stats\CustomersStatsResource;
use App\Http\Resources\Stats\OrdersStatsResource;
use App\Http\Resources\Stats\PendingOrdersStatsResource;
use App\Http\Resources\Stats\ProductsStatsResource;
use App\Http\Resources\Stats\RevenueStatsResource;
use App\Services\V1\Admin\Stats\StatsService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends Controller
{
    public function __construct(protected StatsService $statsService) {}

    public function ordersStats()
    {
        try {
            $stats = $this->statsService->getCurrentMonthOrderCount();
            return ApiResponse::successResponse(new OrdersStatsResource($stats), __('stats.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('stats.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('stats.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function revenueStats()
    {
        try {
            $stats = $this->statsService->getCurrentMonthRevenue();
            return ApiResponse::successResponse(new RevenueStatsResource($stats), __('stats.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('stats.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('stats.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function productsStats()
    {
        try {
            $count = $this->statsService->getProductsCount();
            return ApiResponse::successResponse(new ProductsStatsResource($count), __('stats.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('stats.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('stats.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function customersStats()
    {
        try {
            $stats = $this->statsService->getCurrentMonthCustomersCount();
            return ApiResponse::successResponse(new CustomersStatsResource($stats), __('stats.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('stats.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('stats.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function pendingOrdersStats()
    {
        try {
            $count = $this->statsService->getTotalPendingOrders();
            return ApiResponse::successResponse(new PendingOrdersStatsResource($count), __('stats.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('stats.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('stats.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
