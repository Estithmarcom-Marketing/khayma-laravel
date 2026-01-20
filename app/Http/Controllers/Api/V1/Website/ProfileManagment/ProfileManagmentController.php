<?php

namespace App\Http\Controllers\Api\V1\Website\ProfileManagment;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileManagement\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use App\Services\V1\Website\ProfileManagement\ProfileManagementService;
use App\Traits\Response\ApiResponse;
use Auth;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;

class ProfileManagmentController extends Controller
{
    public function __construct(protected ProfileManagementService $service) {}

    public function getAuthenticatedUser(Request $request)
    {
        try {
            $user = Auth::user();

            return ApiResponse::successResponse([
                'user' => new UserResource($user),
            ], 'User profile fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user profile', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch user profile', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $this->service->updateProfile($validated);

            return ApiResponse::successResponse([
                'user' => UserResource::make($user),
            ], 'Profile updated successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update user profile', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update user profile', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
