<?php

namespace App\Http\Controllers\User;

use App\Domains\User\Actions\LoginUserAction;
use App\Domains\User\Actions\LogoutUserAction;
use App\Domains\User\Actions\RegisterUserAction;
use App\Domains\User\DataTransferObjects\LoginUserData;
use App\Domains\User\DataTransferObjects\RegisterUserData;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterUserData $data){
        $user = RegisterUserAction::execute($data);
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'message' => 'Вы успешно зарегестрировались.',
            'token' => $token,
        ], 201);
    }

    public function login(LoginUserData $data){
        $token = LoginUserAction::execute($data);
        return response()->json([
            'message' => 'Вы успешно авторизовались.',
            'token' => $token,
        ]);
    }

    public function logout(){
        LogoutUserAction::execute();
        return response()->noContent( 204);
    }
}
