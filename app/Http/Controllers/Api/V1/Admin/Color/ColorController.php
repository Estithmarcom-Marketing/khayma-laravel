<?php

namespace App\Http\Controllers\Api\V1\Admin\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\StoreColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;
use App\Http\Resources\Color\ColorResource;
use App\Models\Color;
use App\Services\V1\Admin\Color\ColorService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

#[Group('Admin Color')]
class ColorController extends Controller
{
    public function __construct(protected ColorService $service) {}

    public function index()
    {
        try {
            $colors = $this->service->list();
            $colors = ColorResource::collection($colors)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'colors' => $colors['data'],
                    'meta' => $colors['meta'],
                    'links' => $colors['links'],
                ],
                'Colors retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch colors', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch colors', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreColorRequest $request)
    {
        try {
            $validated = $request->validated();
            $color = $this->service->store($validated);

            return ApiResponse::successResponse(
                ['color' => ColorResource::make($color)],
                'Color created successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to create color', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create color', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateColorRequest $request, Color $color)
    {
        try {
            $validated = $request->validated();
            $color = $this->service->update($color, $validated);

            return ApiResponse::successResponse(
                ['color' => ColorResource::make($color)],
                'Color updated successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to update color', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update color', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Color $color)
    {
        try {
            $this->service->destroy($color);

            return ApiResponse::successResponse(
                null,
                'Color deleted successfully',
                Response::HTTP_NO_CONTENT
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete color', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete color', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Color $color)
    {
        try {
            $color = $this->service->show($color);

            return ApiResponse::successResponse(
                ['color' => ColorResource::make($color)],
                'Color retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch color', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch color', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
