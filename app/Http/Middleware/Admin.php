<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        if(auth()->check() && !auth()->user()->isActive()) {
            if ($request->expectsJson()) {
                return response()->json(['Request is unauthorized'], 403);
            }

            return redirect('/account/confirm');
        }
        return $next($request);
    }
}
