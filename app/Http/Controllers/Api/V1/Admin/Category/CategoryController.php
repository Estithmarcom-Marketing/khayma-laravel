<?php

namespace App\Http\Controllers\Api\V1\Admin\Category;

use App\Exports\CategoriesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\ImportCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Imports\CategoriesImport;
use App\Models\Category;
use App\Services\V1\Admin\Category\CategoryService;
use App\Services\V1\Admin\SpreadsheetImportExport\SpreadsheetImportExportService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service, protected SpreadsheetImportExportService $spreadsheetService) {}

    public function index()
    {
        try {
            $categories = $this->service->list();
            $categories = CategoryResource::collection($categories)->response()->getData(true);

            return ApiResponse::successResponse([
                'categories' => $categories['data'],
                'meta' => $categories['meta'],
                'links' => $categories['links'],
            ],
                'Categories retrieved successfully',
                status: Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch categories', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Category $category)
    {
        try {
            $category = $this->service->show($category);

            return ApiResponse::successResponse(['category' => CategoryResource::make($category)],
                'Category retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch category', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {

            $category = $this->service->store($request->validated());

            return ApiResponse::successResponse(['category' => CategoryResource::make($category)],
                'Category created successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to create category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create category', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category = $this->service->update($category, $request->validated());

            return ApiResponse::successResponse(['category' => CategoryResource::make($category)],
                'Category updated successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update category', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $this->service->delete($category);

            return ApiResponse::successResponse(null,
                'Category deleted successfully',
                Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete category', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeSubCategory(StoreCategoryRequest $request, Category $category)
    {
        try {
            $subCategory = $this->service->storeSubCategory($category, $request->validated());

            return ApiResponse::successResponse(['sub_category' => CategoryResource::make($subCategory)],
                'Sub-category created successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to create sub-category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create sub-category', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateSubCategory(UpdateCategoryRequest $request, Category $category, Category $subCategory)
    {
        try {
            $subCategory = $this->service->updateSubCategory($category, $subCategory, $request->validated());

            return ApiResponse::successResponse(['sub_category' => CategoryResource::make($subCategory)],
                'Sub-category updated successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update sub-category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update sub-category', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroySubCategory(Category $category, Category $subCategory)
    {
        try {
            $this->service->deleteSubCategory($category, $subCategory);

            return ApiResponse::successResponse(null,
                'Sub-category deleted successfully',
                Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete sub-category', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete sub-category', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listSubCategories(Category $category)
    {
        try {
            $subCategories = $this->service->listSubCategories($category);
            $subCategories = CategoryResource::collection($subCategories)->response()->getData(true);

            return ApiResponse::successResponse([
                'sub_categories' => $subCategories['data'],
                'meta' => $subCategories['meta'],
                'links' => $subCategories['links'],
            ],
                'Sub-categories retrieved successfully',
                status: Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch sub-categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch sub-categories', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportCsv()
    {
        try {
            return $this->spreadsheetService->exportCsv(new CategoriesExport, 'categories');
        } catch (\Exception $e) {
            Log::error('Failed to export categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to export categories', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportExcel()
    {
        try {
            return $this->spreadsheetService->exportExcel(new CategoriesExport, 'categories');
        } catch (\Exception $e) {
            Log::error('Failed to export categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to export categories', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function importSpreadsheet(ImportCategoryRequest $request)
    {
        try {
            $file = $request->file('file');

            $this->spreadsheetService->import(new CategoriesImport, $file);

            return ApiResponse::successResponse(null, 'Import started. Processing in background.', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error('Failed to import categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to import categories', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
