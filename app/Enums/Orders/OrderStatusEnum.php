<?php

namespace App\Enums\Orders;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELED = 'canceled';

    public static function all(): array
    {
        return [
            self::PENDING->value,
            self::PROCESSING->value,
            self::SHIPPED->value,
            self::DELIVERED->value,
            self::CANCELED->value,
        ];
    }

    public function label(): array
    {
        return match ($this) {
            self::PENDING => ['ar' => 'قيد المراجعة', 'en' => 'under review'],
            self::PROCESSING => ['ar' => 'قيد التجهيز', 'en' => 'being prepared'],
            self::SHIPPED => ['ar' => 'تم الشحن', 'en' => 'shipped'],
            self::DELIVERED => ['ar' => 'تم التسليم', 'en' => 'delivered'],
            self::CANCELED => ['ar' => 'تم الإلغاء', 'en' => 'canceled'],
        };
    }
}
