<?php

use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\TimeEntry\TimeEntryController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\user\ProfileController;

use Illuminate\Support\Facades\Route;

//авторизация
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);

    //проекты
    Route::apiResource('/project', ProjectController::class);


    Route::prefix('project/{project}')->group(function () {
        //статус проекта
        Route::patch('/status', [ProjectController::class, 'updateStatus']);

        //задачи
        Route::apiResource('/task', TaskController::class);
        Route::patch('/task/{task}/status', [TaskController::class, 'updateStatus']);
    });

    //все задачи, независимо от проекта
    Route::get('/tasks', [TaskController::class, 'allTasks']);

    //запись времени
    Route::patch('time_entry/stop', [TimeEntryController::class, 'stop']);
    Route::post('time_entry/start', [TimeEntryController::class, 'start']);
    Route::apiResource('/time_entry', TimeEntryController::class);

    //профиль
    Route::apiResource('/profile/{user}', ProfileController::class);
});
