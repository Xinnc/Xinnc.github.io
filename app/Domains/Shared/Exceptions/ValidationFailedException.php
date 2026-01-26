<?php

namespace App\Domains\Shared\Exceptions;

class ValidationFailedException extends ApiException
{
    public function __construct($code = 422, $message = 'Некорректный ввод данных. Пожалуйста, проверьте введенные данные.'
        , $errors = [])
    {
        parent::__construct($code, $message, $errors);
    }
}
