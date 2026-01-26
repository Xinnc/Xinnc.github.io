<?php

namespace App\Domains\Task\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class StoreTaskData extends Data
{
    use ValidationError;

    public function __construct(
        #[Required, StringType, Min(1), Max(255)]
        public string $name,

        #[Required, StringType, Min(1), Max(1000)]
        public string $description,
    ) {}

    public static function attributes(): array
    {
        return [
            'name'       => 'название',
            'description' => 'описание',
        ];
    }
}
