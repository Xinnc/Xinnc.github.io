<?php

namespace App\Domains\TimeEntry\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{
    Nullable,
    IntegerType,
    BooleanType,
    Date,
    Min
};
use Carbon\Carbon;

class FilterTimeEntryData extends Data
{
    use ValidationError;

    public function __construct(
        #[Nullable, IntegerType, Min(1)]
        public ?int $user_id,

        #[Nullable, IntegerType, Min(1)]
        public ?int $project_id,

        #[Nullable, IntegerType, Min(1)]
        public ?int $task_id,

        #[Nullable, BooleanType]
        public ?bool $is_manual,

        #[Nullable, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'd.m.Y  H:i', 'd/m/Y H:i', 'Y-m-d', 'd.m.Y', 'd/m/Y'])]
        public ?Carbon $date_from,

        #[Nullable, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'd.m.Y  H:i', 'd/m/Y H:i', 'Y-m-d', 'd.m.Y', 'd/m/Y'])]
        public ?Carbon $date_to,
    ) {}
    public static function attributes(): array
    {
        return [
            'user_id'     => 'пользователь',
            'project_id'  => 'проект',
            'task_id'     => 'задача',
            'is_manual'   => 'тип ввода времени',
            'date_from'   => 'дата начала периода',
            'date_to'     => 'дата окончания периода',
        ];
    }
}
