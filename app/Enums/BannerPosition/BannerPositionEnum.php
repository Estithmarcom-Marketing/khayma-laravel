<?php

namespace App\Enums\BannerPosition;

enum BannerPositionEnum: string
{
    case TOP_LEFT = 'top_left';
    case TOP_RIGHT = 'top_right';
    case UNDER_CATEGORY = 'under_category';
    case GIF = 'gif';
    case MIDDLE_TOP = 'middle_top';
    case MIDDLE_RIGHT = 'middle_right';
    case MIDDLE_LEFT = 'middle_left';
    case BOTTOM = 'bottom';

    public static function all(): array
    {
        return [
            self::TOP_LEFT->value,
            self::TOP_RIGHT->value,
            self::UNDER_CATEGORY->value,
            self::MIDDLE_TOP->value,
            self::MIDDLE_RIGHT->value,
            self::MIDDLE_LEFT->value,
            self::BOTTOM->value,
        ];
    }
}
