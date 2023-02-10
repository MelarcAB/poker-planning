<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class VerifyCsrfTokenCustom
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
        /*Session::start();

        //validar que el token sea correcto
        if ($request->method() == 'POST') {
            $token = $request->header('X-CSRF-TOKEN');
            if ($token != Session::token()) {
                return response()->json(['message' => 'Token incorrecto'], 401);
            }
        } else {
            return response()->json(['message' => 'Método no permitido'], 405);
        }
*/

        return $next($request);
    }
}
