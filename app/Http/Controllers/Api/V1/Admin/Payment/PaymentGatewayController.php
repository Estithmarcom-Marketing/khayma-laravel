<?php

namespace App\Http\Controllers\Api\V1\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGateway\StorePaymentGatewayRequest;
use App\Http\Requests\PaymentGateway\UpdatePaymentGatewayRequest;
use App\Http\Resources\Payment\PaymentGatewayResource;
use App\Models\PaymentGateway;
use App\Services\V1\Admin\Payment\PaymentGatewayService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Payment Gateways')]
class PaymentGatewayController extends Controller
{
    public function __construct(protected PaymentGatewayService $service) {}

    public function index()
    {
        try {
            $paymentGateways = $this->service->list();

            return ApiResponse::successResponse(
                ['payment_gateways' => PaymentGatewayResource::collection($paymentGateways)],
                'Payment gateways retrieved successfully.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving payment gateways ', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to retrieve payment gateways.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function getActive()
    {
        try {
            $paymentGateways = $this->service->getActive();

            return ApiResponse::successResponse(
                ['payment_gateways' => PaymentGatewayResource::collection($paymentGateways)],
                'Active payment gateways retrieved successfully.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving payment gateways ', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to retrieve payment gateways.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function show(PaymentGateway $paymentGateway)
    {
        try {
            $paymentGateway = $this->service->show($paymentGateway);

            return ApiResponse::successResponse(
                ['payment_gateway' => new PaymentGatewayResource($paymentGateway)],
                'Payment gateway retrieved successfully.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving payment gateway ', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to retrieve payment gateway.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function store(StorePaymentGatewayRequest $request)
    {
        try {
            $paymentGateway = $this->service->store($request->validated());

            return ApiResponse::successResponse(
                ['payment_gateway' => PaymentGatewayResource::make($paymentGateway)],
                'Payment gateway created successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error('Error creating payment gateway ', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to create payment gateway.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(UpdatePaymentGatewayRequest $request, PaymentGateway $paymentGateway)
    {
        try {
            $paymentGateway = $this->service->update($paymentGateway, $request->validated());

            return ApiResponse::successResponse(
                ['payment_gateway' => PaymentGatewayResource::make($paymentGateway)],
                'Payment gateway updated successfully.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Error updating payment gateway ', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to update payment gateway.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy(PaymentGateway $paymentGateway)
    {
        try {
            $this->service->delete($paymentGateway);

            return ApiResponse::successResponse(
                [],
                'Payment gateway deleted successfully.',
                Response::HTTP_NO_CONTENT
            );
        } catch (\Exception $e) {
            Log::error('Error deleting payment gateway ', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(
                'Failed to delete payment gateway.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
