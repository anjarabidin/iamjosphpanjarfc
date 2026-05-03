<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUninstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Di-comment sementara karena selalu mengarah ke /install terus
        // if (!file_exists(storage_path('installed')) && !$request->is('install*')) {
        //     return redirect('/install');
        // }

        return $next($request);
    }
}
