<?php

namespace App\Enums\Payments;

enum PaymentMethodTypeEnum: string
{
    case ONLINE = 'online';
    case CASH = 'cash';
    case INSTALLMENT = 'installment';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
