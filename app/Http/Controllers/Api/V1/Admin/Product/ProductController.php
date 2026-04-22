<?php

namespace App\Http\Controllers\Api\V1\Admin\Product;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreProductVaritionsRequest;
use App\Http\Requests\Product\StoreVariantPropertyRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\UpdateProductVaritionsRequest;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\ProductVariation\ProductVariationResource;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Property;
use App\Services\V1\Admin\Product\ProductService;
use App\Services\V1\Admin\Product\ProductVariantService;
use App\Services\V1\Admin\Product\VariantPropertyService;
use App\Services\V1\Admin\SpreadsheetImportExport\SpreadsheetImportExportService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Product')]
class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service,
        protected ProductVariantService $productVariantService,
        protected VariantPropertyService $variantPropertyService,
        protected SpreadsheetImportExportService $spreadsheetService
    ) {}

    public function index()
    {
        try {
            $products = $this->service->list();

            return ApiResponse::successResponse(
                [
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
                __('product.list_success'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Product $product)
    {
        try {
            $product = $this->service->show($product);

            return ApiResponse::successResponse(
                [
                    'product' => ProductResource::make($product),
                ],
                __('product.show_success'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.show_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $product = $this->service->store($validated);

            return ApiResponse::successResponse(
                ['product' => ProductResource::make($product)],
                __('product.create_success'),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error('Failed to create product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.create_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        try {
            $product = $this->service->update($product, $request->validated());

            return ApiResponse::successResponse(
                ['product' => ProductResource::make($product)],
                __('product.update_success'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to update product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.update_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->service->delete($product);

            return ApiResponse::successResponse([], __('product.delete_success'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.delete_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeProductVariation(Product $product, StoreProductVaritionsRequest $request)
    {
        try {
            $variation = $this->productVariantService->storeVariations($product, $request->validated());

            return ApiResponse::successResponse(
                ['product_variation' => ProductVariationResource::make($variation)],
                'product Variation stored successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to store product variation', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to store product variation', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateProductVariation(Product $product, ProductVariation $productVariation, UpdateProductVaritionsRequest $request)
    {
        try {
            $variation = $this->productVariantService->updateVariation($productVariation, $request->validated());

            return ApiResponse::successResponse(
                ['product_variation' => ProductVariationResource::make($variation)],
                'Product variation updated successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to update product variation', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update product variation', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyProductVariation(Product $product, ProductVariation $productVariation)
    {
        try {
            $this->productVariantService->deleteVariation($product, $productVariation);

            return ApiResponse::successResponse([], 'Product variation deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete product variation', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete product variation', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showProductVariation(Product $product, ProductVariation $productVariation)
    {
        try {
            $productVariation = $this->productVariantService->showVariation($product, $productVariation);

            return ApiResponse::successResponse(
                ['product_variation' => ProductVariationResource::make($productVariation)],
                'Product variation retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch product variation', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch product variation', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listProductVariations(Product $product)
    {
        try {
            $productVariations = $this->productVariantService->listProductVariations($product);
            $productVariations = ProductVariationResource::collection($productVariations)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'product_variations' => $productVariations['data'],
                    'meta' => $productVariations['meta'],
                    'links' => $productVariations['links'],
                ],
                'Product variations retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch product variations', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch product variations', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listProductVariationProperties(Product $product, ProductVariation $productVariation)
    {
        try {
            $productVariationProperties = $this->variantPropertyService->listVariationProperties($productVariation);
            $productVariationProperties = PropertyResource::collection($productVariationProperties)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'product_variation_properties' => $productVariationProperties['data'],
                    'meta' => $productVariationProperties['meta'],
                    'links' => $productVariationProperties['links'],
                ],
                'Product variation properties retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch product variation properties', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch product variation properties', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeProductVariationProperty(Product $product, ProductVariation $productVariation, StoreVariantPropertyRequest $request)
    {
        try {
            $property = $this->variantPropertyService->storeVariationProperty($productVariation, $request->validated());

            return ApiResponse::successResponse(
                ['property' => PropertyResource::make($property)],
                'Property stored successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to store property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to store property', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showProductVariationProperty(Product $product, ProductVariation $productVariation, Property $property)
    {
        try {
            $property = $this->variantPropertyService->showVariationProperty($productVariation, $property);

            return ApiResponse::successResponse(
                ['property' => PropertyResource::make($property)],
                'Property retrieved successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch property', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyProductVariationProperty(Product $product, ProductVariation $productVariation, Property $property)
    {
        try {
            $this->variantPropertyService->deleteVariationProperty($productVariation, $property);

            return ApiResponse::successResponse([], 'Property deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete property', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete property', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function exportCsv()
    {
        try {
            return $this->spreadsheetService->exportCsv(new ProductExport, 'products');
        } catch (\Exception $e) {
            Log::error('Failed to export products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.export_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function exportExcel()
    {
        try {
            return $this->spreadsheetService->exportExcel(new ProductExport, 'products');
        } catch (\Exception $e) {
            Log::error('Failed to export products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.export_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function listLowStock()
    {
        try {
            $variations = $this->productVariantService->listLowStock();
            $variations = ProductVariationResource::collection($variations)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'variations' => $variations['data'],
                    'meta' => $variations['meta'],
                    'links' => $variations['links'],
                ],
                __('product.low_stock_success'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch low stock products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('product.low_stock_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
