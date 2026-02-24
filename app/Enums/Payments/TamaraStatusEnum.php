<?php

namespace App\Enums\Payments;

enum TamaraStatusEnum: string
{
    case AUTHORIZED = 'AUTHORIZED';
    case APPROVED = 'APPROVED';
    case CAPTURED = 'CAPTURED';
    case DECLINED = 'DECLINED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';

    public static function fromTabbyStatus(string $status): ?self
    {
        return match (strtoupper($status)) {
            'AUTHORIZED' => self::AUTHORIZED,
            'APPROVED' => self::APPROVED,
            'CAPTURED' => self::CAPTURED,
            'DECLINED' => self::DECLINED,
            'CANCELLED' => self::CANCELLED,
            'EXPIRED' => self::EXPIRED,
            default => null,
        };
    }
}
