<?php

namespace App\Http\Controllers\Api\V1\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\SalesReportRequest;
use App\Http\Resources\Reports\SalesByDateResource;
use App\Http\Resources\Reports\SalesByPaymentMethodResource;
use App\Services\V1\Admin\Reports\SalesReportService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Sales Report')]
class SalesReportController extends Controller
{
    public function __construct(protected SalesReportService $salesReportService) {}

    public function byDate(SalesReportRequest $request)
    {
        try {
            $data = $this->salesReportService->getSalesByDate($request->validated('period', 'month'));

            return ApiResponse::successResponse(SalesByDateResource::collection($data), __('stats.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('stats.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('stats.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function byPaymentMethod(SalesReportRequest $request)
    {
        try {
            $data = $this->salesReportService->getSalesByPaymentMethod($request->validated('period', 'month'));

            return ApiResponse::successResponse(SalesByPaymentMethodResource::collection($data), __('stats.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('stats.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('stats.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
