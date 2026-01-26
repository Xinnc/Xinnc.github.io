<?php

namespace App\Domains\Shared\Concerns;


use App\Domains\Shared\Exceptions\ValidationFailedException;
use Illuminate\Contracts\Validation\Validator;

trait ValidationError
{
    public static function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            throw new ValidationFailedException(422, 'Некорректный ввод данных. Пожалуйста, проверьте введенные данные. ', $validator->errors());
        }
    }
}
