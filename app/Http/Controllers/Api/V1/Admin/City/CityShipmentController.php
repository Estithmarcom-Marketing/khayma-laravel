<?php

namespace App\Http\Controllers\Api\V1\Admin\City;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityShipment\storeCityShipmentRequest;
use App\Http\Requests\CityShipment\UpdateCityShipmentRequest;
use App\Http\Resources\CityShipment\CityShipmentResource;
use App\Models\City;
use App\Models\CityShipment;
use App\Services\V1\Admin\City\CityShipmentService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class CityShipmentController extends Controller
{
    public function __construct(protected CityShipmentService $service) {}

    public function index()
    {
        try {
            $cityShipments = $this->service->list();
            $cityShipments = CityShipmentResource::collection($cityShipments)->response()->getData(true);

            return ApiResponse::successResponse([
                'city_shipments' => $cityShipments['data'],
                'meta' => $cityShipments['meta'],
                'links' => $cityShipments['links']],
                'City shipments retrieved successfully.',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error retrieving city shipments: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve city shipments.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(CityShipment $cityShipment)
    {
        try {
            $cityShipment = $this->service->show($cityShipment);

            return ApiResponse::successResponse(
                ['city_shipment' => CityShipmentResource::make($cityShipment)],
                'City shipment retrieved successfully.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving city shipment: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve city shipment.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(storeCityShipmentRequest $request, City $city)
    {
        try {
            $cityShipment = $this->service->store($city, $request->validated());

            return ApiResponse::successResponse(
                ['city_shipment' => CityShipmentResource::make($cityShipment)],
                'City shipment created successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error('Error creating city shipment: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create city shipment.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateCityShipmentRequest $request, CityShipment $cityShipment)
    {
        try {
            $cityShipment = $this->service->update($cityShipment, $request->validated());

            return ApiResponse::successResponse(
                ['city_shipment' => CityShipmentResource::make($cityShipment)],
                'City shipment updated successfully.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Error updating city shipment: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update city shipment.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(City $city, CityShipment $cityShipment)
    {
        try {
            $this->service->delete($cityShipment);

            return ApiResponse::successResponse(
                null,
                'City shipment deleted successfully.',
                Response::HTTP_NO_CONTENT
            );
        } catch (\Exception $e) {
            Log::error('Error deleting city shipment: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete city shipment.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
