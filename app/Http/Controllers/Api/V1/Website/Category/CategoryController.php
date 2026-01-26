<?php

namespace App\Http\Controllers\Api\V1\Website\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Services\V1\Website\Category\CategoryService;
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
                __('category.listed_all'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to list categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                __('category.list_all_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->service->show($id);

            return ApiResponse::successResponse(
                ['category' => new CategoryResource($category)],
                __('category.listed_one'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to list category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                __('category.list_one_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
