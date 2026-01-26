<?php

namespace App\Domains\Shared\Enums;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
