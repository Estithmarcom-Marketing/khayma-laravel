<?php

namespace App\Http\Controllers\Api\V1\Website\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use App\Services\V1\Website\Address\AddressService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    public function __construct(protected AddressService $service) {}

    public function index()
    {
        try {
            $user = auth()->user();
            $addresses = $this->service->list($user);

            return ApiResponse::successResponse([
                'addresses' => AddressResource::collection($addresses),
            ], __('address.fetched_all'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch addresses', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('address.fetch_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreAddressRequest $request)
    {
        try {

            $validated = $request->validated();
            $user = auth()->user();
            $address = $this->service->store($user, $validated);
            $address->load('city');

            return ApiResponse::successResponse(
                [
                    'address' => AddressResource::make($address)],
                __('address.store_success'),
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to store address', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('address.store_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        try {
            $validated = $request->validated();
            $address = $this->service->update($address, $validated);

            return ApiResponse::successResponse([
                'address' => AddressResource::make($address),
            ], __('address.updated_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update address', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('address.update_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Address $address)
    {
        try {
            $this->service->delete($address);

            return ApiResponse::successResponse(
                null,
                __('address.delete_success'),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete address', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('address.delete_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Address $address)
    {
        try {
            $address = $this->service->show($address);

            return ApiResponse::successResponse([
                'address' => AddressResource::make($address),
            ], __('address.fetched_one'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch address', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('address.fetch_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
