<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isGerente
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
        //validar si el usuario es gestor
        if (auth()->user()->user_type->name == 'gestor' || auth()->user()->user_type->name == 'admin') {
            return $next($request);
        }
        return redirect('home')->with('error', 'No tienes permisos para acceder a esta página');
    }
}
