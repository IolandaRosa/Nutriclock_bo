<?php

namespace App\Http\Middleware;

use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Response;
use Closure;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->role == 'ADMIN') {
            return $next($request);
        }

        if ($request->user() && $request->user()->role != 'ADMIN') {
            abort(403);
        }

        return redirect()->route('login');

    }
}
