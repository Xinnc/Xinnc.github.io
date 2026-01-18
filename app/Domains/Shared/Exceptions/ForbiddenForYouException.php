<?php

namespace App\Domains\Shared\Exceptions;

class ForbiddenForYouException extends ApiException
{
    public function __construct($code = 403, $message = 'Доступ запрещен.'){
        parent::__construct($code, $message);
    }
}
