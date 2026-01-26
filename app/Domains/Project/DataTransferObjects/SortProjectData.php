<?php

namespace App\Domains\Project\DataTransferObjects;

use App\Domains\Shared\Enums\SortDirection;
use Spatie\LaravelData\Attributes\Validation\{In};
use Spatie\LaravelData\Data;

class SortProjectData extends Data
{
    public function __construct(
        #[In(['asc', 'desc'])]
        public SortDirection $deadline = SortDirection::ASC,
    ) {}

    public static function attributes(): array
    {
        return [
            'deadline' => 'сортировка по времени',
        ];
    }
}
