<?php

namespace App\Http\Controllers\Api\V1\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductPropertyRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreProductVaritionsRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\UpdateProductVaritionsRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\V1\Admin\Product\ProductService;
use App\Traits\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function index()
    {
        try {
            $products = $this->service->list();

            return ApiResponse::successResponse([
                'products' => ProductResource::collection($products),
                'meta' => [
                    'per_page' => $products->perPage(),
                    'next_cursor' => optional($products->nextCursor())->encode(),
                    'prev_cursor' => optional($products->previousCursor())->encode(),
                ],
                'links' => [
                    'next' => $products->nextPageUrl(),
                    'prev' => $products->previousPageUrl(),
                ],
            ],
                'Products retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch products', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Product $product)
    {
        try {
            $product = $this->service->show($product);

            return ApiResponse::successResponse([
                'product' => ProductResource::make($product),
            ],
                'Product retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $product = $this->service->store($validated);

            return ApiResponse::successResponse(['product' => ProductResource::make($product)],
                'Product created successfully',
                Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error('Failed to create product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        try {
            $product = $this->service->update($product, $request->validated());

            return ApiResponse::successResponse(['product' => ProductResource::make($product)],
                'Product updated successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to update product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->service->delete($product);

            return ApiResponse::successResponse([], 'Product deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            \Log::error('Failed to delete product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function StoreVariation(Product $product, StoreProductVaritionsRequest $request)
    {
        try {
            $product = $this->service->storeVariations($product, $request->validated());

            return ApiResponse::successResponse(['product' => ProductResource::make($product)],
                'Product updated successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to update product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateVariation(Product $product, ProductVariation $productVariation, UpdateProductVaritionsRequest $request)
    {
        try {
            $product = $this->service->updateVariation($product, $productVariation, $request->validated());

            return ApiResponse::successResponse(['product' => ProductResource::make($product)],
                'Product updated successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to update product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyVariation(Product $product, ProductVariation $productVariation)
    {
        try {
            $this->service->deleteVariation($product, $productVariation);

            return ApiResponse::successResponse([], 'Product deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            \Log::error('Failed to delete product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

   
}
