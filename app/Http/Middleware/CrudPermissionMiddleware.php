<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CrudPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('*.edit') && !Admin::find($request->user()->id)?->roles()?->first()?->is_allowed_edit) {
            return back()->with('warning', 'Anda tidak memiliki ijin untuk mengakses halaman ini');
        }

        if ($request->routeIs('*.create') && !Admin::find($request->user()->id)?->roles()?->first()?->is_allowed_create) {
            return back()->with('warning', 'Anda tidak memiliki ijin untuk mengakses halaman ini');
        }

        if ($request->routeIs('*.destroy') && !Admin::find($request->user()->id)?->roles()?->first()?->is_allowed_delete) {
            return back()->with('warning', 'Anda tidak memiliki ijin untuk mengakses halaman ini');
        }
        return $next($request);
    }
}
