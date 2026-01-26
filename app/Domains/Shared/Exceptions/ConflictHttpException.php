<?php

namespace App\Domains\Shared\Exceptions;

class ConflictHttpException extends ApiException
{
    public function __construct($code = 409, $message = 'Конфликт.'){
        parent::__construct($code, $message);
    }
}
