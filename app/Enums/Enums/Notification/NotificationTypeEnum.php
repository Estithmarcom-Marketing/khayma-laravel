<?php

namespace App\Enums\Enums\Notification;

enum NotificationTypeEnum: string
{
    case ORDER_STATUS_UPDATED = 'order_status_updated';
    case ORDER_PLACED = 'order_placed';
    case PRODUCT_BACK_IN_STOCK = 'product_back_in_stock';

    public static function all(): array
    {
        return [
            self::ORDER_STATUS_UPDATED->value,
            self::ORDER_PLACED->value,
            self::PRODUCT_BACK_IN_STOCK->value,
        ];
    }
}
