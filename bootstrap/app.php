<?php

use App\Domains\Shared\Exceptions\NotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\EnsureUserIsNotBanned::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            throw_if($request->expectsJson(), new NotFoundException());
        });
    })->create();
