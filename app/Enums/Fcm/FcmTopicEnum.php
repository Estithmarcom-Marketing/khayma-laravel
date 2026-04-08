<?php

namespace App\Enums\Fcm;

enum FcmTopicEnum:string
{
    case OFFERS = 'offers';
    case GENERAL = 'general';

    public static function values(): array
    {
        return [
            self::OFFERS->value,
            self::GENERAL->value,
        ];
    }

}
