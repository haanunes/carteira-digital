<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('sanctum');

        if (! $user) {
            return response()->json([
                'message' => 'Não autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
