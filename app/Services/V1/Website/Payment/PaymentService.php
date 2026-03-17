<?php

namespace App\Services\V1\Website\Payment;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Enums\Payments\PaymentMethodTypeEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Services\V1\Website\Payment\Contracts\PaymentGatewayInterface;
use App\Services\V1\Website\Payment\DTOs\PaymentResponseDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayManager $gatewayManager
    ) {}

    public function initiateIfNeeded(Order $order, ?string $gateway): ?PaymentResponseDTO
    {
        if ($order->paymentMethod->type->value == PaymentMethodTypeEnum::CASH->value) {
            return null;
        }

        if (! $gateway) {
            throw new \LogicException('Gateway required for online payment');
        }

        $gatewayEnum = PaymentGatewayEnum::from($gateway);

        if (! $order->paymentMethod->supportsGateway($gatewayEnum)) {
            throw new \LogicException("Gateway {$gatewayEnum->value} not supported for this payment method");
        }

        return $this->initiate($order, $gatewayEnum);
    }

    public function initiate(Order $order, PaymentGatewayEnum $gatewayEnum): PaymentResponseDTO
    {
        try {
            $dto = null;
            $payment = $this->getOrderPayment($order);

            $gateway = $this->resolveGateway($gatewayEnum);

            $dto = $this->createGatewayPayment($gateway, $order, $gatewayEnum);
            DB::transaction(function () use ($payment, $dto, $gatewayEnum) {
                $this->updatePayment($payment, $dto, $gatewayEnum);
            });

            return $dto;

        } catch (\Exception $e) {
            Log::error('Failed to create payment',
                ['error' => $e->getMessage(),
                    'payment' => $payment,
                    'gateway' => $gateway,
                    'dto' => $dto,
                    'method' => __METHOD__]);
            throw $e;
        }

    }

    private function getOrderPayment(Order $order): Payment
    {
        $payment = $order->payments()->latest()->first();

        if (! $payment) {
            $payment = $order->payments()->create([
                'amount' => $order->total_price,
                'payment_method_id' => $order->payment_method_id,
                'status' => PaymentStatusEnum::PENDING,
            ]);
        }

        return $payment;
    }

    public function resetPayment(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {

            $order->payments()
                ->where('status', PaymentStatusEnum::PENDING)
                ->update([
                    'status' => PaymentStatusEnum::FAILED,
                ]);

            return $order->payments()->create([
                'amount' => $order->total_price,
                'payment_method_id' => $order->payment_method_id,
                'payment_gateway_id' => null,
                'gateway' => null,
                'status' => PaymentStatusEnum::PENDING,
                'transaction_id' => null,
                'meta_data' => null,
            ]);
        });
    }

    private function resolveGateway(PaymentGatewayEnum $gatewayEnum): PaymentGatewayInterface
    {
        return $this->gatewayManager->resolve($gatewayEnum);
    }

    private function createGatewayPayment(
        PaymentGatewayInterface $gateway,
        Order $order,
        PaymentGatewayEnum $gatewayEnum
    ): PaymentResponseDTO {
        $dto = $gateway->createPayment($order);

        Log::info('Payment initiation response', [
            'order_id' => $order->id,
            'gateway' => $gatewayEnum->value,
            'response' => $dto,
        ]);

        return $dto;
    }

    private function updatePayment(
        Payment $payment,
        PaymentResponseDTO $dto,
        PaymentGatewayEnum $gatewayEnum
    ): void {
        $paymentGatewayId = $payment->paymentMethod
            ->paymentGateways()
            ->where('gateway', $gatewayEnum)
            ->value('id');

        $payment->update([
            'transaction_id' => $dto->referenceId,
            'meta_data' => $dto->payload,
            'gateway' => $gatewayEnum,
            'payment_gateway_id' => $paymentGatewayId,
        ]);
    }

    /**
     * Handle incoming webhook
     */
    public function handleWebhook(PaymentGatewayEnum $gatewayEnum, array $payload): void
    {
        $gateway = $this->gatewayManager->resolve($gatewayEnum);
        $gateway->handleWebhook($payload);
    }
}
