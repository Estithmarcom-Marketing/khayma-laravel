<?php

namespace App\Http\Controllers\Api\V1\Application\Color;

use App\Http\Controllers\Controller;
use App\Http\Resources\Application\Color\ColorResource;
use App\Services\V1\Website\Color\ColorService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends Controller
{
    public function __construct(protected ColorService $service) {}

    public function index()
    {
        try {
            $colors = $this->service->list();

            return ApiResponse::successResponse(
                ['colors' => ColorResource::collection($colors)],
                __('color.list_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch colors', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('color.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $color = $this->service->show($id);

            return ApiResponse::successResponse(['color' => ColorResource::make($color)],
                __('color.show_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch color', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('color.show_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
