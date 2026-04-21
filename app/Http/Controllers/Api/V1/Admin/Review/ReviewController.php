<?php

namespace App\Http\Controllers\Api\V1\Admin\Review;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Review\ReviewResource;
use App\Services\V1\Admin\Review\ReviewService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
#[Group('Admin Review')]
class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService){}

    public function index()
    {
        $reviews = $this->reviewService->list();
        return ApiResponse::successResponse( ReviewResource::collection($reviews),
         __('review.list_success'),
          status: Response::HTTP_OK);
    }

    public function destroy($id)
    {
        try {
            $this->reviewService->delete($id);
            return ApiResponse::successResponse( [],
            __('review.delete_success'),
            status: Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            Log::error(__('review.delete_failed'), ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('review.delete_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);

        }

    }
}
