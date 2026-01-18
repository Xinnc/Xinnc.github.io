<?php

use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\User\AuthController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::namespace('project')->group(function () {
        Route::apiResource('', ProjectController::class);
        Route::patch('/{project}/status', [ProjectController::class, 'updateStatus']);
        Route::apiResource('/{project}/task', TaskController::class);
    });
});
