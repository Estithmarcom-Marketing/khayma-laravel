<?php

namespace App\Http\Controllers\Api\V1\Admin\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\BannerRequest;
use App\Http\Resources\Banner\BannerResource;
use App\Models\Banner;
use App\Services\V1\Admin\Home\HomeService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HomeManagementController extends Controller
{
    public function __construct(protected HomeService $service) {}

    public function storeBanners(BannerRequest $request)
    {
        try {
            $banners = $this->service->storeBanners($request->validated());

            return ApiResponse::successResponse(
                ['slider' => BannerResource::make($banners)],
                'Banners stored successfully',
                Response::HTTP_CREATED);
        } catch (\Exception $e) {

            Log::error('Failed to store banners', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to store banners', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getBanners()
    {
        try {
            $banners = $this->service->listBanners();
            $banners = BannerResource::collection($banners)->response()->getData(true);

            return ApiResponse::successResponse(
                ['slider' => $banners['data'],
                    'meta' => $banners['meta'],
                    'links' => $banners['links']],
                'Banners listed successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list banners', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to list banners', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getHomeBanners()
    {
        try {
            $banners = $this->service->ListHomeBanners();
            $banners = $banners->map(function ($items) {
                return BannerResource::collection($items);
            });

            return ApiResponse::successResponse(
                ['slider' => $banners],
                'Banners listed successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list banners', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to list banners', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Banner $banner)
    {
        try {
            $this->service->deleteBanner($banner);

            return ApiResponse::successResponse([], 'Banner deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete banner', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete banner', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
