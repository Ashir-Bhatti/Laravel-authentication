<?php

namespace App\Enums;

enum EventType: string
{
    case PHYSICAL = 'physical';
    case VIRTUAL = 'virtual';

    public static function keys(): array
    {
        return array_column(self::cases(), 'value');
    }
}
