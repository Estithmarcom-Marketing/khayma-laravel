<?php

namespace App\Enums\Payments;

enum PaymentGatewayEnum: string
{
    case TABBY = 'tabby';
    case TAMARA = 'tamara';
    case MYFATOORAH = 'myfatoorah';

  public static function values(): array
{
    return array_column(self::cases(), 'value');
}
}
