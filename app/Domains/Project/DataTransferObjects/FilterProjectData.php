<?php

namespace App\Domains\Project\DataTransferObjects;

use App\Domains\Project\Enums\ProjectStatus;
use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{Exists, Nullable, Date, StringType};
use Carbon\Carbon;

class FilterProjectData extends Data
{
    use ValidationError;

    public function __construct(
        #[Nullable]
        public ?ProjectStatus $status,

        #[Nullable, Exists('users', 'id')]
        public ?int $manager_id,

        #[Nullable, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'd.m.Y', 'd/m/Y'])]

        public ?Carbon $deadline_from,

        #[Nullable, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'd.m.Y', 'd/m/Y'])]
        public ?Carbon $deadline_to,

        #[Nullable, StringType]
        public ?string $search,
    ) {}
    public static function attributes(): array
    {
        return [
            'status' => 'статус',
            'manager_id' => 'менеджер',
            'deadline_from' => 'дедлайн с',
            'deadline_to' => 'дедлайн до',
            'search' => 'поиск',
        ];
    }
}
