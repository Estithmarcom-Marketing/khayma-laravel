<?php

namespace App\Services\V1\Website\Payment;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Services\V1\Website\Payment\Contracts\PaymentGatewayInterface;
use App\Services\V1\Website\Payment\Gateways\MyFatoorahGateway;
use App\Services\V1\Website\Payment\Gateways\TabbyGateway;
use App\Services\V1\Website\Payment\Gateways\TamaraGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function resolve(PaymentGatewayEnum $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            PaymentGatewayEnum::TABBY => app(TabbyGateway::class),
            PaymentGatewayEnum::TAMARA => app(TamaraGateway::class),
            PaymentGatewayEnum::MYFATOORAH => app(MyFatoorahGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway->value}")
        };
    }
}
