<?php

namespace App\Http\Controllers\Api\V1\Website\ProductReminder;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductReminders\ProductRemindersResource;
use App\Models\ProductReminder;
use App\Models\ProductVariation;
use App\Services\V1\Website\ProductReminder\ProductReminderService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Dedoc\Scramble\Attributes\Group;
#[Group('Website Product Reminders')]
class ProductReminderController extends Controller
{
    public function __construct(protected ProductReminderService $service) {}

    public function index()
    {
        try {
            $productReminders = $this->service->list();
            $productReminders = ProductRemindersResource::collection($productReminders)->response()->getData(true);

            return ApiResponse::successResponse([
                'product_reminders' => $productReminders['data'],
                'meta' => $productReminders['meta'],
                'links' => $productReminders['links'],
            ], 'Product Reminders fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch product reminders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(ProductReminder $productReminder)
    {
        try {
            $productReminder = $this->service->show($productReminder);

            return ApiResponse::successResponse([
                'product_reminder' => ProductRemindersResource::make($productReminder),
            ], 'Product Reminder fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch product reminder', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ProductVariation $productVariation)
    {
        try {
            $productReminder = $this->service->store($productVariation);
            if (! $productReminder) {
                return ApiResponse::errorResponse('Product is in stock', Response::HTTP_BAD_REQUEST);
            }

            return ApiResponse::successResponse([
                'product_reminder' => ProductRemindersResource::make($productReminder),
            ], 'Product Reminder created successfully',
             Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create product reminder', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(ProductVariation $productVariation)
    {
        try {
            $this->service->delete($productVariation);
            return ApiResponse::successResponse([], 'Product Reminder deleted successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to delete product reminder', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete product reminder', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
