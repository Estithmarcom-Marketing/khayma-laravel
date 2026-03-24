<?php

namespace App\Enums\Platform;

enum PlatformEnum: string
{
    case WEB = 'web';
    case ANDROID = 'android';
    case IOS = 'ios';

    public static function all(): array
    {
        return [
            self::WEB->value,
            self::ANDROID->value,
            self::IOS->value,
        ];
    }
}
