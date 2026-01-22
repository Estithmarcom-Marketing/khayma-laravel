<?php

namespace App\Http\Controllers\Api\V1\Website\Color;

use App\Http\Controllers\Controller;
use App\Http\Resources\Color\ColorResource;
use App\Services\V1\Website\Color\ColorService;
use App\Traits\Response\ApiResponse;
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
                'Colors retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch colors', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $color = $this->service->show($id);

            return ApiResponse::successResponse(['color' => ColorResource::make($color)],
                'Color retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch color', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
