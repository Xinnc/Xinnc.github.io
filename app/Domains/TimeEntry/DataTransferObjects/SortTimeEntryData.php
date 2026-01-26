<?php

namespace App\Domains\TimeEntry\DataTransferObjects;

use App\Domains\Shared\Enums\SortDirection;
use Spatie\LaravelData\Attributes\Validation\{In};
use Spatie\LaravelData\Data;

class SortTimeEntryData extends Data
{
    public function __construct(
        #[In(['asc', 'desc'])]
        public SortDirection $sort_by_time = SortDirection::DESC,
    ) {}

    public static function attributes(): array
    {
        return [
            'sort_by_time' => 'сортировка по времени',
        ];
    }
}
