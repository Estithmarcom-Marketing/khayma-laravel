<?php

namespace App\Enums\Enums\Notification;

enum NotificationTypeEnum: string
{
    case ORDER_STATUS_UPDATED = 'order_status_updated';
    case ORDER_PLACED = 'order_placed';
    case PRODUCT_BACK_IN_STOCK = 'product_back_in_stock';
    case ORDER_PAID = 'order_paid';
    case CONTACT_US_MESSAGE_PLACED = 'contact_us_message_placed';
    case PRODUCT_REMINDER_PLACED = 'product_reminder_placed';

    public static function all(): array
    {
        return [
            self::ORDER_STATUS_UPDATED->value,
            self::ORDER_PLACED->value,
            self::ORDER_PAID->value,
            self::PRODUCT_BACK_IN_STOCK->value,
            self::CONTACT_US_MESSAGE_PLACED->value,
            self::PRODUCT_REMINDER_PLACED->value,
        ];
    }
}
