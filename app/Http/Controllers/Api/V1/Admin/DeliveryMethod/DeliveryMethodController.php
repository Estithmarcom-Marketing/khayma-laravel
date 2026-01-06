<?php

namespace App\Http\Controllers\Api\V1\Admin\DeliveryMethod;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryMethod\StoreDeliveryMethodRequest;
use App\Http\Requests\DeliveryMethod\UpdateDeliveryMethodRequest;
use App\Http\Resources\DeliveryMethod\DeliveryMethodResource;
use App\Models\DeliveryMethod;
use App\Services\V1\Admin\DeliveryMethod\DeliveryMethodService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class DeliveryMethodController extends Controller
{
    public function __construct(protected DeliveryMethodService $service) {}

    public function index()
    {
        try {
            $deliveryMethods = $this->service->list();

            return ApiResponse::successResponse(['delivery_methods' => DeliveryMethodResource::collection($deliveryMethods)], 'Delivery methods retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch delivery methods: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch delivery methods.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getActive()
    {
        try {
            $deliveryMethod = $this->service->getActive();

            return ApiResponse::successResponse(['delivery_methods' => DeliveryMethodResource::collection($deliveryMethod)], 'Active delivery methods retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch active delivery methods: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch active delivery methods.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreDeliveryMethodRequest $request)
    {
        try {
            $delivery_method = $this->service->store($request->validated());

            return ApiResponse::successResponse(['delivery_method' => DeliveryMethodResource::make($delivery_method)], 'Delivery method created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create delivery method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create delivery method.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateDeliveryMethodRequest $request, DeliveryMethod $deliveryMethod)
    {
        try {
            $deliveryMethod = $this->service->update($deliveryMethod, $request->validated());

            return ApiResponse::successResponse(['delivery_method' => DeliveryMethodResource::make($deliveryMethod)], 'Delivery method updated successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update delivery method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update delivery method.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(DeliveryMethod $deliveryMethod)
    {
        try {
            $this->service->delete($deliveryMethod);

            return ApiResponse::successResponse([], 'Delivery method deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete delivery method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete delivery method.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(DeliveryMethod $deliveryMethod)
    {
        try {
            $deliveryMethod = $this->service->show($deliveryMethod);

            return ApiResponse::successResponse(['delivery_method' => DeliveryMethodResource::make($deliveryMethod)], 'Delivery method retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch delivery method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch delivery method.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
