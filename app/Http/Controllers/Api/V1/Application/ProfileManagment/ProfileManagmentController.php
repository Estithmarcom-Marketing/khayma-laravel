<?php

namespace App\Http\Controllers\Api\V1\Application\ProfileManagment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\ProfileManagement\UpdatePhoneRequest;
use App\Http\Requests\Application\ProfileManagement\UpdateProfileRequest;
use App\Http\Resources\Application\User\UserResource;
use App\Services\V1\Website\ProfileManagement\ProfileManagementService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProfileManagmentController extends Controller
{
    public function __construct(protected ProfileManagementService $service) {}

    public function getAuthenticatedUser()
    {
        try {
            $user = auth('sanctum')->user();

            return ApiResponse::successResponse([
                'user' => UserResource::make($user),
            ], __('profile.fetch_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user profile', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('profile.fetch_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = $this->service->updateProfile($request->validated());
            return ApiResponse::successResponse([
                'user' => UserResource::make($user),
            ], __('profile.update_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update user profile', ['error' => $e->getMessage(), 'method' => __METHOD__,'request_data' => $request->validated()]);

            return ApiResponse::errorResponse(__('profile.update_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendOtpForPhoneUpdate()
    {
        try {
            $this->service->sendOtpForPhoneUpdate();

            return ApiResponse::successResponse(null, __('profile.otp_sent_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP for phone update', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('profile.otp_sent_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendOtpToNewPhone(UpdatePhoneRequest $request)
    {
        try {
            $this->service->sendOtpToNewPhone($request->validated());

            return ApiResponse::successResponse(null, __('profile.otp_sent_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP for new phone number', ['error' => $e->getMessage(), 'method' => __METHOD__, 'request_data' => $request->only(['phone', 'otp_code'])]);

            return ApiResponse::errorResponse(__('profile.otp_sent_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updatePhoneNumber(UpdatePhoneRequest $request)
    {
        try {
            $user = $this->service->updatePhoneNumber($request->validated());

            return ApiResponse::successResponse([
                'user' => UserResource::make($user),
            ], __('profile.phone_update_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update phone number', ['error' => $e->getMessage(), 'method' => __METHOD__, 'request_data' => $request->only(['new_phone', 'otp_code'])]);

            return ApiResponse::errorResponse(__('profile.phone_update_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
