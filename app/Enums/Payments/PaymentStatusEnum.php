<?php

namespace App\Enums\Payments;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public static function all(): array
    {
        return [
            self::PENDING->value,
            self::COMPLETED->value,
            self::FAILED->value,
        ];
    }
}
