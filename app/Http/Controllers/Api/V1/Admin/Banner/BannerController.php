<?php

namespace App\Http\Controllers\Api\V1\Admin\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\UpdateBannerRequest;
use App\Http\Resources\Banner\BannerResource;
use App\Models\Banner;
use App\Services\V1\Admin\Banner\BannerService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Banners')]
class BannerController extends Controller
{
    public function __construct(protected BannerService $bannerService) {}

    public function index()
    {
        try {
            $banners = $this->bannerService->list();
            return ApiResponse::successResponse([
                BannerResource::collection($banners),
            ], __('banners.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('banners.retrieve_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('banners.retrieve_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Banner $banner, UpdateBannerRequest $request)
    {
        try {
            $banner = $this->bannerService->update($banner, $request->validated());
            return ApiResponse::successResponse([
                BannerResource::make($banner),
            ], __('banners.updated'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update banner', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('banners.update_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
