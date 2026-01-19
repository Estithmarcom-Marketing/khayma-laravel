<?php

namespace App\Http\Controllers\Api\V1\Admin\AdminManagment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\V1\Admin\AdminManagment\AdminManagmentService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminManagmentController extends Controller
{
    public function __construct(protected AdminManagmentService $service) {}

    public function store(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->service->store($validated);

            Log::info('Admin registered successfully', ['admin_id' => $data['admin']->id]);

            return ApiResponse::successResponse(['admin' => new UserResource($data['admin'])],
                'Admin registered successfully',
                Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Admin registration failed', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Admin registration failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
