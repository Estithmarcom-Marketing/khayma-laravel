<?php

namespace App\Http\Controllers\Api\V1\User\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Banner\BannerResource;
use App\Services\V1\User\Home\HomeService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function __construct(protected HomeService $service) {}

    public function getHomeBanners()
    {
        try {
            $sliders = $this->service->getHomeBanners();
            $sliders = $sliders->map(function ($items) {
                return BannerResource::collection($items);
            });

            return ApiResponse::successResponse(
                ['sliders' => $sliders],
                'sliders listed successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list sliders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to list sliders', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
