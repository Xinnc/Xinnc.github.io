<?php

use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Shared\ProgramController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\TimeEntry\TimeEntryController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ProfileController;

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
    Route::get('time_entry/get_started', [TimeEntryController::class, 'getStarted']);
    Route::post('time_entry/start', [TimeEntryController::class, 'start']);
    Route::apiResource('/time_entry', TimeEntryController::class);

    //профиль
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);

    //настройка программ
    Route::patch('program/{program}/status', [ProgramController::class, 'isActive']);
    Route::apiResource('/program', ProgramController::class)->only(['index', 'store', 'update', 'destroy']);

    //просмотр всех пользователей админом
    Route::get('/users', [AdminController::class, 'getUsers']);

    //работа с ролями
    Route::get('/role', [AdminController::class, 'getRoles']);
    Route::post('/role', [AdminController::class, 'addRole']);
    Route::delete('/role/{role}', [AdminController::class, 'deleteRole']);
    Route::patch("/user/{user}/role", [AdminController::class, 'updateRole']);

    //бан разбан пользователя
    Route::patch('/user/{user}/ban', [AdminController::class, 'banUser']);
    Route::patch('/user/{user}/unban', [AdminController::class, 'unbanUser']);

    //приндуительная остановка таймера пользователя
    Route::patch('/user/{user}/stop/time_entry', [AdminController::class, 'forceStopTimeEntry']);

    //получение данных статистики о системе и пользователе
    Route::get('/system/stat', [AdminController::class, 'getSystemStat']);
    Route::get('/user/{user}/stat', [AdminController::class, 'getUserStat']);
});
