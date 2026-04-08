<?php

namespace App\Http\Controllers\Api\V1\Application\Fcm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fcm\UpdateFcmTokenRequest;
use App\Services\V1\Website\Fcm\FcmService;
use App\Traits\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

class FcmController extends Controller
{
    use ApiResponse;
    public function __construct(public FcmService $service) {}
    public function update(UpdateFcmTokenRequest $request)
    {
        try {
            $this->service->updateFcmToken($request->validated());
            return ApiResponse::successResponse([], __('fcm_token.updated_successfully'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(
                'Failed to update fcm token',
                ['error' => $e->getMessage(), 'request' => $request->validated(), 'method' => __METHOD__]
            );
            return ApiResponse::errorResponse(__('fcm_token.updated_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
