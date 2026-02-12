<?php

namespace App\Http\Controllers\Api\V1\Application\Home;


use App\Http\Controllers\Controller;
use App\Http\Resources\Application\Banner\BannerResource;
use App\Http\Resources\Application\Category\CategoryResource;
use App\Http\Resources\Application\CommonQuestion\CommonQuestionResource;
use App\Http\Resources\Application\Product\ProductResource;
use App\Http\Resources\Application\Review\ReviewResource;
use App\Services\V1\Website\Home\HomeService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function __construct(protected HomeService $service) {}
    public function getHomeBanners()
    {
        try {
            $sliders = $this->service->getHomeBanners();
            $sliders = $sliders->map(function ($items) {
                return BannerResource::collection($items);
            });

            return ApiResponse::successResponse(
                ['sliders' => $sliders],
                __('home.sliders_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list sliders', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('home.sliders_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getHomeCategories()
    {
        try {
            $categories = $this->service->getCategories();

            return ApiResponse::successResponse(
                ['categories' => CategoryResource::collection($categories)],
                __('home.categories_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list categories', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('home.categories_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getLatestProducts()
    {
        try {
            $products = $this->service->getLatestProducts();

            $products = ProductResource::collection($products)->response()->getData(true);

            return ApiResponse::successResponse(
                ['products' => $products['data'],
                    'meta' => $products['meta'],
                    'links' => $products['links']],
                __('home.products_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('home.products_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getCommonProducts()
    {
        try {
            $products = $this->service->getCommonProducts();

            $products = ProductResource::collection($products)->response()->getData(true);

            return ApiResponse::successResponse(
                ['products' => $products['data'],
                    'meta' => $products['meta'],
                    'links' => $products['links']],
                __('home.products_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('home.products_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getMostOrderdProducts()
    {
        try {
            $products = $this->service->getMostOrderdProducts();

            $products = ProductResource::collection($products)->response()->getData(true);

            return ApiResponse::successResponse(
                ['products' => $products['data'],
                    'meta' => $products['meta'],
                    'links' => $products['links']],
                __('home.products_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('home.products_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getSuggestedProducts()
    {
        try {
            $products = $this->service->getSuggestedProducts();

            $products = ProductResource::collection($products)->response()->getData(true);

            return ApiResponse::successResponse(
                ['products' => $products['data'],
                    'meta' => $products['meta'],
                    'links' => $products['links']],
                __('home.products_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list products', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('home.products_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getHomeReviews()
    {
        try {
            $reviews = $this->service->getHomeReviews();

            $reviews = ReviewResource::collection($reviews)->response()->getData(true);

            return ApiResponse::successResponse(
                ['reviews' => $reviews['data'],
                    'meta' => $reviews['meta'],
                    'links' => $reviews['links']],
                __('home.reviews_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list reviews', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('home.reviews_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getCommonQuestions()
    {
        try {
            $questions = $this->service->getCommonQuestions();

            $questions = CommonQuestionResource::collection($questions)->response()->getData(true);

            return ApiResponse::successResponse(
                ['questions' => $questions['data'],
                    'meta' => $questions['meta'],
                    'links' => $questions['links']],
                __('home.questions_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to list questions', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to list questions', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
