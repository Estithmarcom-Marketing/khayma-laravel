<?php

namespace App\Http\Controllers\Api\V1\Website\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OtpRequest;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Resources\User\UserResource;
use App\Services\V1\Website\Auth\UserAuthService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserAuthController extends Controller
{
    public function __construct(protected UserAuthService $service) {}

    public function sendOtp(OtpRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->service->sendOtp($validated);

            return ApiResponse::successResponse(null, __('auth.otp_sent_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', ['error' => $e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('auth.otp_sent_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function login(UserLoginRequest $request)
    {
        try {
            $data = $this->service->login($request->validated());

            return ApiResponse::successResponse(
                ['user' => UserResource::make($data['user']), 'token' => $data['token']],
                __('auth.logged_in_successfully'),
                Response::HTTP_OK
            );
        } catch (\LogicException $e) {
            Log::error('Failed to login user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            Log::error('Failed to login user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('auth.logged_in_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout()
    {
        try {
            $this->service->logout();
            return ApiResponse::successResponse(null, __('auth.logged_out_successfully'), Response::HTTP_OK);
        } catch (\LogicException $e) {
            Log::error('Failed to logout user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            Log::error('Failed to logout user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('auth.logged_out_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
