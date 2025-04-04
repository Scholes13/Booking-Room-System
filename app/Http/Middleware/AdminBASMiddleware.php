<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminBASMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in and has role 'admin_bas'
        if (!Auth::check() || Auth::user()->role !== 'admin_bas') {
            // If not, redirect to login page with error message
            return redirect()->route('admin.login')->with('error', 'Akses ditolak. Anda harus login sebagai Admin BAS.');
        }

        return $next($request);
    }
} 