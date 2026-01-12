<?php

namespace App\Domains\Shared\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiException extends HttpResponseException
{
    public function __construct($code = 400, $message = 'Что-то пошло не так.', $errors = [])
    {
        $data = [
            'message' => $message,
        ];
        if (count($errors) > 0) {
            $data['errors'] = $errors;
        }
        parent::__construct(response()->json($data)->setStatusCode($code));
    }
}
