<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\AuthenticateApi;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
        ]);
    })    
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if (! $request->is('api/*')) {
                return;
            }
            if (is_null($request->route())) {
                return response()->json([
                    'message' => 'Endpoint não encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Transação não encontrada'
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Não autenticado'
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

    })
    ->create();
