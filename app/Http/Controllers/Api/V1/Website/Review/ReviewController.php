<?php

namespace App\Http\Controllers\Api\V1\Website\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use App\Services\V1\Website\Review\ReviewService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $service) {}

    public function getMyReviews()
    {
        try {
            $reviews = $this->service->getMyReviews();
            $reviews = ReviewResource::collection($reviews)->response()->getData(true);

            return ApiResponse::successResponse(
                ['reviews' => $reviews['data'],
                    'meta' => $reviews['meta'],
                    'links' => $reviews['links']],
                'Reviews fetched successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch reviews', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch reviews', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getProductReviews(Product $product)
    {
        try {
            $reviews = $this->service->getReviews($product);
            $reviews = ReviewResource::collection($reviews)->response()->getData(true);

            return ApiResponse::successResponse(
                ['reviews' => $reviews['data'],
                    'meta' => $reviews['meta'],
                    'links' => $reviews['links']],
                'Reviews fetched successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch reviews', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch reviews', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getStatistics($id)
    {
        try {
        $statistics = $this->service->getStatistics($id);

            return ApiResponse::successResponse(['statistics' => $statistics], 'Statistics fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch statistics', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch statistics', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreReviewRequest $request)
    {
        try {
            $review = $this->service->store($request->validated());

            return ApiResponse::successResponse(['review' => ReviewResource::make($review)], 'Review created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create review', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        try {
            $review = $this->service->update($request->validated(), $review);

            return ApiResponse::successResponse(['review' => ReviewResource::make($review)], 'Review updated successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update review', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Review $review)
    {
        try {
            $this->service->destroy($review);

            return ApiResponse::successResponse([], 'Review deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete review', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
