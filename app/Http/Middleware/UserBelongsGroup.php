<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserBelongsGroup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        echo 'entro';
        exit;

        // Check if the user belongs to the group
        if (!$request->user()->belongsToGroup($request->slug)) {
            return redirect()->route('home')->with('error', 'No puedes acceder a esta página');
        }
        return $next($request);
    }
}
