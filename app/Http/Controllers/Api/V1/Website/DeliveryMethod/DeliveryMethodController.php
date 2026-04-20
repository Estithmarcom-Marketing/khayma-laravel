<?php

namespace App\Http\Controllers\Api\V1\Website\DeliveryMethod;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryMethod\DeliveryMethodResource;
use App\Services\V1\Website\DeliveryMethod\DeliveryMethodService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Dedoc\Scramble\Attributes\Group;
#[Group('Website Delivery Methods')]
class DeliveryMethodController extends Controller
{
    public function __construct(protected DeliveryMethodService $service) {}

    public function index()
    {
        try {
            $deliveryMethods = $this->service->list();

            return ApiResponse::successResponse(['delivery_methods' => DeliveryMethodResource::collection($deliveryMethods)],
                'Delivery methods retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch delivery methods: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch delivery methods.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
