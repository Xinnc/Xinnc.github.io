<?php

namespace App\Domains\User\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{IntegerType, Nullable, BooleanType, Min, StringType};

class FilterUserData extends Data
{
    use ValidationError;

    public function __construct(
        #[Nullable, StringType, Min(1)]
        public ?string $search,

        #[Nullable, IntegerType]
        public ?int   $role_id,

        #[Nullable, BooleanType]
        public ?bool   $is_banned,
    )
    {
    }

    public static function attributes(): array
    {
        return [
            'role_id' => 'роль',
            'is_banned' => 'статус блокировки',
            'search' => 'поиск',
        ];
    }
}
