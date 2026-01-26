<?php

namespace App\Http\Controllers\Api\V1\Website\City;

use App\Http\Controllers\Controller;
use App\Http\Resources\City\CityResource;
use App\Services\V1\Website\City\CityService;
use App\Traits\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    public function __construct(protected CityService $service) {}

    public function index()
    {
        try {
            $cities = $this->service->list();

            return ApiResponse::successResponse(['cities' => CityResource::collection($cities)],
                __('city.retrieved_all'));
        } catch (\Exception $e) {
            \Log::error('Failed to fetch cities', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('city.retrieve_all_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $city = $this->service->show($id);

            return ApiResponse::successResponse(['city' => CityResource::make($city)],
                __('city.retrieved_one'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch city', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('city.retrieve_one_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
