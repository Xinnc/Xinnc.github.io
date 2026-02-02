<?php

namespace App\Domains\User\Actions\Auth;

use App\Domains\User\DataTransferObjects\RegisterUserData;
use App\Domains\User\Exceptions\EmailAlreadyExistException;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public static function execute(RegisterUserData $data): User
    {
        if (User::where('email', $data->email)->first()) throw new EmailAlreadyExistException();
        $user = User::create([
            'first_name' => $data->first_name,
            'surname' => $data->surname,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);
        Auth::login($user);
        return $user;
    }
}
