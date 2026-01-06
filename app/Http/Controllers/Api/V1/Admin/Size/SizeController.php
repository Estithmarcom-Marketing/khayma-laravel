<?php

namespace App\Http\Controllers\Api\V1\Admin\Size;

use App\Http\Controllers\Controller;
use App\Http\Requests\Size\StoreSizeRequest;
use App\Http\Requests\Size\UpdateSizeRequest;
use App\Http\Resources\Size\SizeResource;
use App\Models\Size;
use App\Services\V1\Admin\Size\SizeService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class SizeController extends Controller
{
    public function __construct(protected SizeService $service) {}

    public function index()
    {
        try {
            $sizes = $this->service->list();

            return ApiResponse::successResponse(['sizes' => SizeResource::collection($sizes)], 'Sizes retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch sizes', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch sizes', Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function store(StoreSizeRequest $request)
    {
        try {
            $size = $this->service->store($request->validated());

            return ApiResponse::successResponse(['size' => SizeResource::make($size)], 'Size created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create size', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create size', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateSizeRequest $request, Size $size)
    {
        try {
            $size = $this->service->update($size, $request->validated());

            return ApiResponse::successResponse(['size' => SizeResource::make($size)], 'Size updated successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update size', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update size', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Size $size)
    {
        try {
            $this->service->delete($size);

            return ApiResponse::successResponse(null, 'Size deleted successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to delete size', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete size', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Size $size)
    {
        try {
            $size = $this->service->show($size);

            return ApiResponse::successResponse(['size' => SizeResource::make($size)], 'Size retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch size', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch size', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
