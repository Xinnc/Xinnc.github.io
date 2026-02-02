<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app');
}); // Главная, например, дашборд

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

// Список проектов
Route::get('/projects', function () {
    return view('projects.index');
})->name('projects.index');

// Создание проекта
Route::get('/projects/create', function () {
    return view('projects.create');
})->name('projects.create');

// Просмотр проекта
Route::get('/projects/{id}', function ($id) {
    return view('projects.show', ['id' => $id]);
})->name('projects.show');

// Редактирование проекта
Route::get('/projects/{id}/edit', function ($id) {
    return view('projects.edit', ['id' => $id]);
})->name('projects.edit');

Route::get('/tasks', function () {
    return view('tasks.index');
})->name('tasks.index');

// Создание задачи в проекте
Route::get('/projects/{project}/task/create', function ($project) {
    return view('tasks.create', ['projectId' => $project]);
})->name('tasks.create');

// Просмотр задачи
Route::get('/projects/{project}/task/{task}', function ($project, $task) {
    return view('tasks.show', ['projectId' => $project, 'taskId' => $task]);
})->name('tasks.show');

// Редактирование задачи
Route::get('/projects/{project}/task/{task}/edit', function ($project, $task) {
    return view('tasks.edit', ['projectId' => $project, 'taskId' => $task]);
})->name('tasks.edit');

// Список всех записей времени
Route::get('/time-entries', function () {
    return view('time-entries.index');
})->name('time-entries.index');

// Создание записи
Route::get('/time-entries/create', function () {
    return view('time-entries.create');
})->name('time-entries.create');

// Просмотр одной записи
Route::get('/time-entries/{id}', function ($id) {
    return view('time-entries.show', ['id' => $id]);
})->name('time-entries.show');

// Редактирование записи
Route::get('/time-entries/{id}/edit', function ($id) {
    return view('time-entries.edit', ['id' => $id]);
})->name('time-entries.edit');

Route::get('/programs', function () {
    return view('programs.index');
})->name('programs.index');

Route::get('/programs/create', function () {
    return view('programs.create');
})->name('programs.create');

// Редактирование
Route::get('/programs/{id}/edit', function ($id) {
    return view('programs.edit', ['id' => $id]);
})->name('programs.edit');

// Просмотр профиля
Route::get('/profile', function () {
    return view('profile.show');
})->name('profile.show');

// Редактирование профиля
Route::get('/profile/edit', function () {
    return view('profile.edit');
})->name('profile.edit');

// Смена пароля (можно сделать отдельную страницу или вкладку)
Route::get('/profile/password', function () {
    return view('profile.password');
})->name('profile.password');

// Админ-панель (только для admin)
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Управление пользователями
    Route::get('/admin/users', function () {
        return view('admin.users');
    })->name('admin.users');

    // Изменение роли (PATCH)
    Route::patch('/admin/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
    // Блокировка/разблокировка (PATCH)
    Route::patch('/admin/users/{id}/block', [UserController::class, 'toggleBlock'])->name('admin.users.block');
