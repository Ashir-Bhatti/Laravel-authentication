<?php

namespace App\Enums;

enum EventGenre: string
{
    case EVENT = 'event';
    case TASK = 'task';

    public static function keys(): array
    {
        return array_column(self::cases(), 'value');
    }
}
