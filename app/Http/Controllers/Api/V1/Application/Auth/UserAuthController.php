<?php

namespace App\Http\Controllers\Api\V1\Application\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Auth\OtpRequest;
use App\Http\Requests\Application\Auth\UserLoginRequest;
use App\Http\Resources\Application\UserResource;
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

            return ApiResponse::successResponse(null, 'OTP sent successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to send OTP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(UserLoginRequest $request)
    {
        try {
            $data = $this->service->login($request->validated());

            return ApiResponse::successResponse(
                ['user' => UserResource::make($data['user']), 'token' => $data['token']],
                'User logged in successfully',
                Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Failed to login user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to login user', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout()
    {
        try {
            $this->service->logout();

            return ApiResponse::successResponse(null, 'User logged out successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to logout user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to logout user', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
