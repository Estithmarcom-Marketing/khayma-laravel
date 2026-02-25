<?php

namespace App\Http\Controllers\Api\V1\Website\Brand;

use App\Http\Controllers\Controller;
use App\Http\Resources\Brand\BrandResource;
use App\Services\V1\Website\Brand\BrandService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    public function __construct(protected BrandService $service) {}

    public function index()
    {
        try {
            $brands = $this->service->list();

            return ApiResponse::successResponse(['brands' => BrandResource::collection($brands)],
                __('brand.list_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch brands', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('brand.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $brand = $this->service->show($id);

            return ApiResponse::successResponse(['brand' => BrandResource::make($brand)],
                __('brand.show_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch brand', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('brand.show_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
