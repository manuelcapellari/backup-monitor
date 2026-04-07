<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedUser = env('INTERNAL_AUTH_USER');
        $expectedPass = env('INTERNAL_AUTH_PASSWORD');

        if (! $expectedUser || ! $expectedPass) {
            return $next($request);
        }

        $providedUser = $request->getUser();
        $providedPass = $request->getPassword();

        if ($providedUser === $expectedUser && $providedPass === $expectedPass) {
            return $next($request);
        }

        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="Backup Monitor Internal"',
        ]);
    }
}
