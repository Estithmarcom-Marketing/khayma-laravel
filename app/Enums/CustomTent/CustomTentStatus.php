<?php

namespace App\Enums\CustomTent;

enum CustomTentStatus: string
{
    case PENDING = 'pending';
    case INPROGRESS = 'inprogress';
    case COMPLETED = 'completed';
 public static function all(): array
    {
        return [
            self::PENDING->value,
            self::INPROGRESS->value,
            self::COMPLETED->value
        ];
    }
}