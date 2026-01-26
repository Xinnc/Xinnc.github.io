<?php

namespace App\Domains\Task\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use App\Domains\Task\Enums\TaskStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{Nullable, StringType};

class FilterTaskData extends Data
{
    use ValidationError;

    public function __construct(
        #[Nullable]
        public ?TaskStatus $status,

        #[Nullable, StringType]
        public ?string $search,
    ) {}
    public static function attributes(): array
    {
        return [
            'status' => 'статус',
            'search' => 'поиск',
        ];
    }
}
