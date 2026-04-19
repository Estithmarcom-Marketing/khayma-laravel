<?php

namespace App\Http\Controllers\Api\V1\Application\Size;


use App\Http\Controllers\Controller;
use App\Http\Resources\Application\Size\SizeResource;
use App\Services\V1\Website\Size\SizeService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Dedoc\Scramble\Attributes\Group;
#[Group('Application Sizes')]
class SizeController extends Controller
{
    public function __construct(protected SizeService $service) {}
    /**
     * @unauthenticated
     */
    public function index()
    {
        try {
            $sizes = $this->service->list();

            return ApiResponse::successResponse(['sizes' => SizeResource::collection($sizes)],
                __('size.list_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch sizes', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('size.list_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @unauthenticated
     */
    public function show($id)
    {
        try {
            $size = $this->service->show($id);

            return ApiResponse::successResponse(['size' => $size], __('size.show_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch size', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('size.show_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
