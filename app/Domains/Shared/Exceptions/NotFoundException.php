<?php

namespace App\Domains\Shared\Exceptions;

class NotFoundException extends ApiException
{
    public function __construct($code = 404, $message = 'Ресурс не найден.'
        , $errors = [])
    {
        parent::__construct($code, $message, $errors);
    }
}
