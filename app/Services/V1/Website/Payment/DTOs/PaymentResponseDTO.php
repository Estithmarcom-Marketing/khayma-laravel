<?php

namespace App\Services\V1\Website\Payment\DTOs;

class PaymentResponseDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $referenceId,
        public readonly string $redirectUrl,
        public readonly string $status,
        public readonly array $payload = [],
    ) {}
}
