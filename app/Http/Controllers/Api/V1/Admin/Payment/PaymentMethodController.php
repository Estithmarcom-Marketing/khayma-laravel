<?php

namespace App\Http\Controllers\Api\V1\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Resources\Payment\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Services\V1\Admin\Payment\PaymentMethodService;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

class PaymentMethodController extends Controller
{
    public function __construct(protected PaymentMethodService $service) {}

    public function index()
    {
        try {
            $paymentMethods = $this->service->list();

            return ApiResponse::successResponse(['payment_methods' => PaymentMethodResource::collection($paymentMethods)], 'Payment methods retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error retrieving payment methods: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve payment methods', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getActive()
    {
        try {
            $paymentMethods = $this->service->getActive();

            return ApiResponse::successResponse(['payment_methods' => PaymentMethodResource::collection($paymentMethods)], 'Active payment methods retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error retrieving active payment methods: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve active payment methods', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StorePaymentMethodRequest $request)
    {
        try {
            $paymentMethod = $this->service->store($request->validated());

            return ApiResponse::successResponse(['payment_method' => PaymentMethodResource::make($paymentMethod)], 'Payment method created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating payment method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create payment method', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod = $this->service->update($paymentMethod, $request->validated());

            return ApiResponse::successResponse(['payment_method' => PaymentMethodResource::make($paymentMethod)], 'Payment method updated successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating payment method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to update payment method', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod = $this->service->show($paymentMethod);

            return ApiResponse::successResponse(['payment_method' => PaymentMethodResource::make($paymentMethod)], 'Payment method retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error retrieving payment method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to retrieve payment method', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $this->service->delete($paymentMethod);

            return ApiResponse::successResponse([], 'Payment method deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Error deleting payment method: ', [$e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete payment method', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
