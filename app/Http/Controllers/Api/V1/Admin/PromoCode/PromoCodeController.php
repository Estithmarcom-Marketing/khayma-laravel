<?php

namespace App\Http\Controllers\Api\V1\Admin\PromoCode;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromoCode\StorePromoCodeRequest;
use App\Http\Requests\PromoCode\UpdatePromoCodeRequest;
use App\Http\Resources\PromoCode\PromoCodeResource;
use App\Models\PromoCode;
use App\Services\V1\Admin\promoCode\PromoCodeService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class PromoCodeController extends Controller
{
    public function __construct(protected PromoCodeService $service) {}

    public function index()
    {
        try {
            $promoCodes = $this->service->list();
            $promoCodes = PromoCodeResource::collection($promoCodes)->response()->getData(true);

            return ApiResponse::successResponse([
                'promo_codes' => $promoCodes['data'],
                'meta' => $promoCodes['meta'],
                'links' => $promoCodes['links'],
            ], 'Promo codes retrieved successfully.', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error retrieving promo codes: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve promo codes.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getActive()
    {
        try {
            $promoCodes = $this->service->getActivePromoCodes();
            $promoCodes = PromoCodeResource::collection($promoCodes)->response()->getData(true);

            return ApiResponse::successResponse([
                'promo_codes' => $promoCodes['data'],
                'meta' => $promoCodes['meta'],
                'links' => $promoCodes['links'],
            ], 'Active promo codes retrieved successfully.', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error retrieving active promo codes: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve active promo codes.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StorePromoCodeRequest $request)
    {
        try {
            $promoCode = $this->service->store($request->validated());

            return ApiResponse::successResponse(['promo_code' => PromoCodeResource::make($promoCode)],
                'Promo code created successfully.',
                Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating promo code: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create promo code.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdatePromoCodeRequest $request, PromoCode $promoCode)
    {
        try {
            $promoCode = $this->service->update($promoCode, $request->validated());

            return ApiResponse::successResponse(['promo_code' => PromoCodeResource::make($promoCode)],
                'Promo code updated successfully.',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating promo code: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update promo code.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(PromoCode $promoCode)
    {
        try {
            $promoCode = $this->service->show($promoCode);

            return ApiResponse::successResponse(['promo_code' => PromoCodeResource::make($promoCode)],
                'Promo code retrieved successfully.',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error retrieving promo code: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve promo code.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(PromoCode $promoCode)
    {
        try {
            $this->service->delete($promoCode);

            return ApiResponse::successResponse([], 'Promo code deleted successfully.', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Error deleting promo code: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete promo code.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
