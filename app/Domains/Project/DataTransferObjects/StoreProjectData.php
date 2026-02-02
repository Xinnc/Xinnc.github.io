<?php

namespace App\Domains\Project\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\{AfterOrEqual, Date, Max, Min, Required, StringType};
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class StoreProjectData extends Data
{
    use ValidationError;

    public function __construct(
        #[Required, StringType, Min(1), Max(255)]
        public string $name,

        #[Required, StringType, Min(1), Max(1000)]
        public string $description,

        #[Required, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'd.m.Y', 'd/m/Y'])]
        #[AfterOrEqual('today')]
        public Carbon $deadline,
    )
    {
    }

    public static function attributes(): array
    {
        return [
            'name' => 'название',
            'description' => 'описание',
            'deadline' => 'дедлайн',
        ];
    }

    public static function messages(): array{
        return [
            'deadline.after_or_equal' => 'Дедлайн не может быть раньше сегодняшней даты'
        ];
    }
}
