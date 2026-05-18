<?php

namespace App\Http\Controllers\Api\V1\Admin\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Property;
use App\Services\V1\Admin\Property\PropertyService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Property')]
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
                __('property.listed_successfully'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch properties', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('property.listed_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listWithoutPagination()
    {
        try {
            $properties = $this->service->listWithoutPagination();
            $properties = PropertyResource::collection($properties);
            return ApiResponse::successResponse(
                [
                    'properties' => $properties,
                ],
                __('property.listed_successfully'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch properties', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('property.listed_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
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
                __('property.showed_successfully'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('property.showed_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
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
                __('property.stored_successfully'),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error('Failed to create property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('property.stored_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
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
                __('property.updated_successfully'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to update property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('property.updated_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Property $property)
    {
        try {
            $this->service->delete($property);

            return ApiResponse::successResponse([], __('property.deleted_successfully'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('property.deleted_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
