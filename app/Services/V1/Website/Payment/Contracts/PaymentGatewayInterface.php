<?php

namespace App\Services\V1\Website\Payment\Contracts;

use App\Models\Order;
use App\Services\V1\Website\Payment\DTOs\PaymentResponseDTO;

interface PaymentGatewayInterface
{
    /**
     * Create payment session with gateway
     */
    public function createPayment(Order $order): PaymentResponseDTO;

    /**
     * Handle webhook payload from gateway
     */
    public function handleWebhook(array $payload): void;

    // /**
    //  * Refund payment
    //  */
    // public function refund(Payment $payment, float $amount = null): bool;
}
