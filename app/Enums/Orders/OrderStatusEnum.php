<?php

namespace App\Enums\Orders;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::PENDING->value,
            self::PROCESSING->value,
            self::SHIPPED->value,
            self::DELIVERED->value,
            self::CANCELLED->value,
        ];
    }
}
