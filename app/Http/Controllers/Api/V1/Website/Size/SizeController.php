<?php

namespace App\Http\Controllers\Api\V1\Website\Size;

use App\Http\Controllers\Controller;
use App\Http\Resources\Size\SizeResource;
use App\Services\V1\Website\Size\SizeService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
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

    public function show($id)
    {
        try {
            $size = $this->service->show($id);

            return ApiResponse::successResponse(['size' => $size], 'Size retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch size', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch size', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
