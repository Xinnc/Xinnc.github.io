<?php

namespace App\Domains\User\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class LoginUserData extends Data
{
    use ValidationError;

    public function __construct(
        #[Required, Email, Max(255)]
        public string $email,

        #[Required, StringType, Min(6), Max(30)]
        public string $password,
    ) {}

    public static function attributes(): array
    {
        return [
            'email'      => 'почта',
            'password'   => 'пароль',
        ];
    }
}
