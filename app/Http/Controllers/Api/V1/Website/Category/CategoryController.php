<?php

namespace App\Http\Controllers\Api\V1\Website\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Services\V1\Website\CategoryService;
use App\Traits\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service) {}

    public function index()
    {
        try {
            $categories = $this->service->list();

            return ApiResponse::successResponse(
                ['categories' => CategoryResource::collection($categories)],
                'Categories listed successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to list categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to list categories',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->service->show($id);

            return ApiResponse::successResponse(
                ['category' => new CategoryResource($category)],
                'Category listed successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to list category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to list category',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
