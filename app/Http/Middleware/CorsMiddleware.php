<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /* $request->header("Access-Control-Allow-Origin", "vue.localhost"); */
        /* $request->header("Access-Control-Allow-Methods", "GET, POST, PATCH, PUT, DELETE"); */
        /* $request->header("Access-Control-Allow-Credentials", "true"); */
        /* $request->header("Access-Control-Allow-Headers", "*"); */
        /* $request->header("Access-Control-Max-Age", "86400"); */

        return $next($request);
    }
}
