<?php

namespace App\Http\Controllers\Api\V1\Website\StaticPage;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaticPage\StaticPageResource;
use App\Services\V1\Website\StaticPage\StaticPageService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Website Static Pages')]
class StaticPageController extends Controller
{
    public function __construct(public StaticPageService $service) {}
    public function index()
    {
        try {
            $pages = $this->service->list();
            $pages = StaticPageResource::collection($pages);
            return ApiResponse::successResponse([
                'pages' => $pages
            ], __('static_page.listed_successfully'), Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to list static pages', [$th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('static_page.listed_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function show($identifier)
    {
        try {
            $page = $this->service->show($identifier);
            $page = StaticPageResource::make($page);
            return ApiResponse::successResponse([
                'page' => $page
            ], __('static_page.showed_successfully'), Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to retrieve static page', [$th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('static_page.showed_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
