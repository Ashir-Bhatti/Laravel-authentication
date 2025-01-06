<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TO_DO = 'to_do';
    case DOING = 'doing';
    case IN_REVIEW = 'in_review';
    case DONE = 'done';
    case CLOSED = 'closed';

    public static function keys(): array
    {
        return array_column(self::cases(), 'value');
    }
}
