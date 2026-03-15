<?php

namespace App\Enums\Payments;

enum MyFatoorahStatusEnum: string
{
    case SUCCESS = 'SUCCESS';
    case FAILED = 'FAILED';
    case AUTHORIZE = 'AUTHORIZE';
    case CANCELED = 'CANCELED';

    public static function fromMyFatoorahStatus(string $status): ?self
    {
        return match (strtoupper($status)) {
            'SUCCESS' => self::SUCCESS,
            'FAILED' => self::FAILED,
            'AUTHORIZE' => self::AUTHORIZE,
            'CANCELED' => self::CANCELED,
            default => null,
        };
    }
}
