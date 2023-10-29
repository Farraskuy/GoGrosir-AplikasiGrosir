<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AppConfigController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class onlySuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!isSuperAdmin($request->user()->role_id)) {
            abort(403, "Anda tidak memiliki izin untuk mengakses halaman ini");
        }

        return $next($request);
    }
}
