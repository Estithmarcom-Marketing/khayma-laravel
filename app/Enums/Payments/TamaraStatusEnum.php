<?php

namespace App\Enums\Payments;

enum TamaraStatusEnum: string
{
    case AUTHORIZED = 'ORDER_AUTHORISED';
    case APPROVED = 'ORDER_APPROVED';
    case CAPTURED = 'ORDER_CAPTURED';
    case DECLINED = 'ORDER_DECLINED';
    case CANCELED = 'ORDER_CANCELED';
    case EXPIRED = 'ORDER_EXPIRED';
    case REFUNDED = 'ORDER_REFUNDED';

    public static function fromTabbyStatus(string $status): ?self
    {
        return match (strtoupper($status)) {
            'AUTHORIZED' => self::AUTHORIZED,
            'APPROVED' => self::APPROVED,
            'CAPTURED' => self::CAPTURED,
            'DECLINED' => self::DECLINED,
            'CANCELED' => self::CANCELED,
            'EXPIRED' => self::EXPIRED,
            default => null,
        };
    }
}
