<?php

namespace App\Enums\Payments;

enum TabbyStatusEnum: string
{
    case AUTHORIZED = 'AUTHORIZED';
    case CLOSED = 'CLOSED';
    case REJECTED = 'REJECTED';
    case EXPIRED = 'EXPIRED';
    case CANCELLED = 'CANCELLED';

    public static function fromTabbyStatus(string $status): ?self
    {
        return match (strtoupper($status)) {
            'AUTHORIZED' => self::AUTHORIZED,
            'CLOSED' => self::CLOSED,
            'REJECTED' => self::REJECTED,
            'EXPIRED' => self::EXPIRED,
            'CANCELLED' => self::CANCELLED,
            default => null,
        };
    }
}
