<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Di-comment sementara agar preview klien bisa melihat /install walau sudah terinstall
        // if (file_exists(storage_path('installed'))) {
        //     return redirect('/login');
        // }

        return $next($request);
    }
}
