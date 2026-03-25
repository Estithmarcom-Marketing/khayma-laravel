<?php

namespace App\Http\Controllers\Api\V1\Admin\ProductReminder;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductReminder\ProductVariationResource;
use App\Services\V1\Admin\ProductReminder\ProductReminderService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductReminderController extends Controller
{
    public function __construct(protected ProductReminderService $productReminderService){}
    

    public function index()
    {
        try {
            $products = $this->productReminderService->getProducts();
            return ApiResponse::successResponse(
                ProductVariationResource::collection($products),
                __('reminders.fetched_successfully'),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            Log::error( __('reminders.fetch_fail'), ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse('Failed to update product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
