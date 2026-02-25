<?php

namespace App\Http\Controllers\Api\V1\Website\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payment\PaymentMethodResource;
use App\Services\V1\Website\PaymentMethod\PaymentMethodService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaymentMethodController extends Controller
{
    public function __construct(protected PaymentMethodService $service) {}

    public function index()
    {
        try {
            $paymentMethods = $this->service->list();

            return ApiResponse::successResponse(['payment_methods' => PaymentMethodResource::collection($paymentMethods)],
                'Payment methods retrieved successfully',
                Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment methods: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch payment methods.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
