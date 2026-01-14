<?php

namespace App\Http\Controllers\Api\V1\Admin\CommonQuestion;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommonQuestion\StoreCommonQuestionRequest;
use App\Http\Requests\CommonQuestion\UpdateCommonQuestionRequest;
use App\Http\Resources\CommonQuestion\CommonQuestionResource;
use App\Models\CommonQuestion;
use App\Services\V1\Admin\CommonQuestion\CommonQuestionService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CommonQuestionController extends Controller
{
    public function __construct(protected CommonQuestionService $service) {}

    public function index()
    {
        try {
            $questions = $this->service->list();
            $questions = CommonQuestionResource::collection($questions)->response()->getData(true);

            return ApiResponse::successResponse([
                'questions' => $questions['data'],
                'meta' => $questions['meta'],
                'links' => $questions['links'],
            ],
                'Common Question retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Common Questions not found', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Common Questions not found',
                Response::HTTP_INTERNAL_SERVER_ERROR

            );
        }
    }

    public function store(StoreCommonQuestionRequest $request)
    {
        try {
            $question = $this->service->store($request->validated());
            $question = CommonQuestionResource::make($question);

            return ApiResponse::successResponse(['question' => $question], 'Common Question created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Common Question not created', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(UpdateCommonQuestionRequest $request, CommonQuestion $commonQuestion)
    {
        try {
            $question = $this->service->update($commonQuestion, $request->validated());
            $question = CommonQuestionResource::make($question);

            return ApiResponse::successResponse(['question' => $question], 'Common Question updated successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Common Question not updated', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(CommonQuestion $commonQuestion)
    {
        try {
            $this->service->delete($commonQuestion);

            return ApiResponse::successResponse(null, 'Common Question deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Common Question not deleted', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(CommonQuestion $commonQuestion)
    {
        try {
            $question = $this->service->show($commonQuestion);
            $question = CommonQuestionResource::make($question);

            return ApiResponse::successResponse(['question' => $question], 'Common Question retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Common Question not found', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
