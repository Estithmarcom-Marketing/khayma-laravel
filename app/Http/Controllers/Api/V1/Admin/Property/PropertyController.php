<?php

namespace App\Http\Controllers\Api\V1\Admin\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Property;
use App\Services\V1\Admin\Property\PropertyService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends Controller
{
    public function __construct(protected PropertyService $service) {}

    public function index()
    {
        try {
            $properties = $this->service->list();
            $properties = PropertyResource::collection($properties)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'properties' => $properties['data'],
                    'meta' => $properties['meta'],
                    'links' => $properties['links'],
                ],
                'Properties retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch properties', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch properties', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Property $property)
    {
        try {
            $property = $this->service->show($property);

            return ApiResponse::successResponse(
                [
                    'property' => PropertyResource::make($property),
                ],
                'Property retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch property', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StorePropertyRequest $request)
    {
        try {
            $property = $this->service->store($request->validated());

            return ApiResponse::successResponse(
                [
                    'property' => PropertyResource::make($property),
                ],
                'Property created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error('Failed to create property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create property', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Property $property, UpdatePropertyRequest $request)
    {
        try {
            $property = $this->service->update($property, $request->validated());

            return ApiResponse::successResponse(
                [
                    'property' => PropertyResource::make($property),
                ],
                'Property updated successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to update property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update property', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Property $property)
    {
        try {
            $this->service->delete($property);

            return ApiResponse::successResponse([], 'Property deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete property', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
