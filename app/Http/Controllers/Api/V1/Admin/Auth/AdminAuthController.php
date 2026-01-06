<?php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\V1\Admin\Auth\AdminAuthService;
use App\Services\V1\Auth\AuthService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{
    public function __construct(protected AdminAuthService $service) {}

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->service->register($validated);

            Log::info('Admin registered successfully', ['admin_id' => $data['admin']->id]);

            return ApiResponse::successResponse(['admin' => new UserResource($data['admin']), 'token' => $data['token']],
                'Admin registered successfully',
                Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Admin registration failed', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Admin registration failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function login(LoginRequest $request)
    {

        try {
            $validated = $request->validated();
            $data = $this->service->login($validated);
            if ($data === false) {
                return ApiResponse::errorResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
            }
            Log::info('Admin logged in successfully', ['admin_id' => $data['admin']->id]);

            return ApiResponse::successResponse(['admin' => new UserResource($data['admin']), 'token' => $data['token']],
                'Admin logged in successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Admin login failed', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Admin login failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function logout()
    {
        try {
            $this->service->logout();

            Log::info('Admin logged out successfully');

            return ApiResponse::successResponse(null, 'Admin logged out successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Admin logout failed', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Admin logout failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
