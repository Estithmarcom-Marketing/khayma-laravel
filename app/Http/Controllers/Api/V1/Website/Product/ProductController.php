<?php

namespace App\Http\Controllers\Api\V1\Website\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\FilterProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\ProductVariation\ProductVariationResource;
use App\Services\V1\Website\Product\ProductService;
use App\Traits\Response\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function filter(FilterProductRequest $request)
    {
        try {
            $products = $this->service->filter($request->validated());
            $products = ProductResource::collection($products)->response()->getData(true);

            return ApiResponse::successResponse(
                ['products' => $products['data'],
                    'meta' => $products['meta'],
                    'links' => $products['links'],
                ],
                'Products Fetched Successfully',
                Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Failed To Fetch Products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed To Fetch Products', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showVariations($identifier)
    {
        try {
            $variations = $this->service->showVariations($identifier);

            return ApiResponse::successResponse(['variations' => ProductVariationResource::collection($variations)], 'Product Variation Fetched Successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Failed To Fetch Product Variation', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed To Fetch Product Variation', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($identifier)
    {
        try {
            $product = $this->service->showProduct($identifier);

            return ApiResponse::successResponse(['product' => ProductResource::make($product)], 'Product Fetched Successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Failed To Fetch Product', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed To Fetch Product', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getRelatedProducts($identifier)
    {
        try {
            $products = $this->service->getRelatedProducts($identifier);

            return ApiResponse::successResponse(['products' => ProductResource::collection($products)], 'Related Products Fetched Successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Failed To Fetch Related Products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed To Fetch Related Products', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
