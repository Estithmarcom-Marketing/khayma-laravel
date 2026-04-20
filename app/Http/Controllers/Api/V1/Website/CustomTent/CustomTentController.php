<?php

namespace App\Http\Controllers\Api\V1\Website\CustomTent;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomTent\StoreCustomTentRequest;
use App\Http\Resources\CustomTent\CustomTentResource;
use App\Services\V1\Website\CustomTent\CustomTentService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Dedoc\Scramble\Attributes\Group;

#[Group('Website Custom Tent')]
class CustomTentController extends Controller
{
    public function __construct(protected CustomTentService $service) {}
    public function store(StoreCustomTentRequest $request)
    {
        try {
            $customTent = $this->service->store($request->validated());
            return ApiResponse::successResponse(
                ['custom_tent' => CustomTentResource::make($customTent)],
                'Custom Tent Requested Succesfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error('Failed to request custom tent ', ['error' => $e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse('Failed to request custom tent', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
