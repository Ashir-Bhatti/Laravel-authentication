<?php

namespace App\Enums;

enum EventPrivacy: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';

    public static function keys(): array
    {
        return array_column(self::cases(), 'value');
    }
}
