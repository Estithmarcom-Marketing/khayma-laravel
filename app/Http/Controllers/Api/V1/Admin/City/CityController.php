<?php

namespace App\Http\Controllers\Api\V1\Admin\City;

use App\Http\Controllers\Controller;
use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Http\Resources\City\CityResource;
use App\Models\City;
use App\Services\V1\Admin\City\CityService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

#[Group('Admin City')]

class CityController extends Controller
{
    public function __construct(protected CityService $service) {}

    public function index()
    {
        try {
            $cities = $this->service->list();
            $cities = CityResource::collection($cities)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'cities' => $cities['data'],
                    'meta' => $cities['meta'],
                    'links' => $cities['links'],
                ],
                'Cities retrieved successfully',
                status: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch cities', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch cities', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getActive()
    {
        try {
            $cities = $this->service->getActive();
            $cities = CityResource::collection($cities)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'cities' => $cities['data'],
                    'meta' => $cities['meta'],
                    'links' => $cities['links'],
                ],
                'Active cities retrieved successfully',
                status: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch active cities', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch active cities', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listWithShipments()
    {
        try {
            $cities = $this->service->listWithShipments();
            $cities = CityResource::collection($cities)->response()->getData(true);

            return ApiResponse::successResponse(
                [
                    'cities' => $cities['data'],
                    'meta' => $cities['meta'],
                    'links' => $cities['links'],
                ],
                'Cities with shipments retrieved successfully',
                status: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch cities with shipments', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch cities with shipments', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreCityRequest $request)
    {
        try {
            $validated = $request->validated();
            $city = $this->service->store($validated);

            return ApiResponse::successResponse(
                ['city' => CityResource::make($city)],
                'City created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error('Failed to create city', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create city', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        try {
            $validated = $request->validated();
            $city = $this->service->update($city, $validated);

            return ApiResponse::successResponse(
                ['city' => CityResource::make($city)],
                'City updated successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to update city', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update city', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(City $city)
    {
        try {
            $this->service->delete($city);

            return ApiResponse::successResponse(
                null,
                'City deleted successfully',
                Response::HTTP_NO_CONTENT
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete city', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete city', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(City $city)
    {
        try {
            $city = $this->service->show($city);

            return ApiResponse::successResponse(
                ['city' => CityResource::make($city)],
                'City fetched successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch city', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch city', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
