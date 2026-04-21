<?php

namespace App\Http\Controllers\Api\V1\Admin\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\Brand\BrandResource;
use App\Models\Brand;
use App\Services\V1\Admin\Brand\BrandService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
#[Group('Admin Brands')]
class BrandController extends Controller
{
    public function __construct(protected BrandService $service) {}

    public function index()
    {
        try {
            $brands = $this->service->list();
            $brands = BrandResource::collection($brands)->response()->getData(true);

            return ApiResponse::successResponse([
                'brands' => $brands['data'],
                'meta' => $brands['meta'],
                'links' => $brands['links'],
            ],
                'Brands retrieved successfully',
                status: Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch brands', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch brands', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = $this->service->store($request->validated());

            return ApiResponse::successResponse(['brand' => BrandResource::make($brand)], 'Brand created successfully', status: Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create brand', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create brand', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Brand $brand, UpdateBrandRequest $request)
    {
        try {
            $brand = $this->service->update($brand, $request->validated());

            return ApiResponse::successResponse(['brand' => BrandResource::make($brand)], 'Brand updated successfully', status: Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update brand', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update brand', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Brand $brand)
    {
        try {
            $brand = $this->service->show($brand);

            return ApiResponse::successResponse(['brand' => BrandResource::make($brand)], 'Brand retrieved successfully', status: Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch brand', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch brand', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            $this->service->delete($brand);

            return ApiResponse::successResponse(null, 'Brand deleted successfully', status: Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete brand', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete brand', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function brandsNoPagination()
    {
        try {
            $brands = $this->service->brandsNoPagination();
            $brands = BrandResource::collection($brands);

            return ApiResponse::successResponse(['brands' => $brands], 'Brands retrieved successfully', status: Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch brands', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch brands', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
