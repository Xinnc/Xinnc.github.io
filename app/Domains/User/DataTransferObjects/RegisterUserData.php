<?php

namespace App\Domains\User\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class RegisterUserData extends Data
{
    use ValidationError;

    public function __construct(
        #[Required, StringType, Max(255), Min(1)]
        public string $first_name,

        #[Required, StringType, Max(255), Min(1)]
        public string $surname,

        #[Nullable, StringType, Max(255)]
        public ?string $last_name,

        #[Required, Email, Max(255)]
        public string $email,

        #[Required, StringType, Min(6), Max(30)]
        public string $password,
    ) {}

    public static function attributes(): array
    {
        return [
            'first_name' => 'имя',
            'surname'    => 'фамилия',
            'last_name'  => 'отчество',
            'email'      => 'почта',
            'password'   => 'пароль',
        ];
    }
}
