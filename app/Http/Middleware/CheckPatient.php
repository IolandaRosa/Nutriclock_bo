<?php

namespace App\Http\Middleware;

use Closure;

class CheckPatient
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
         if ($request->user() && $request->user()->role == 'PATIENT') {
            return $next($request);
         }

         if ($request->user() && $request->user()->role != 'PATIENT') {
            abort(403);
         }

         return redirect()->route('login');
    }
}
