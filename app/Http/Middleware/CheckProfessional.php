<?php

namespace App\Http\Middleware;

use Closure;

class CheckProfessional
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
        if ($request->user() && ($request->user()->role == 'PROFESSIONAL' || $request->user()->role == 'ADMIN')) {
            return $next($request);
        }

        if ($request->user() && ($request->user()->role == 'PROFESSIONAL' || $request->user()->role != 'ADMIN')) {
            abort(403);
        }

        return redirect()->route('login');
    }
}
