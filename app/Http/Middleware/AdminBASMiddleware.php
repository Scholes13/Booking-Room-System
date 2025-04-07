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
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Akses ditolak. Anda harus login sebagai Admin BAS.');
        }
        
        // Check if user has 'admin_bas' role
        if (Auth::user()->role !== 'admin_bas') {
            // If user is logged in but with different role, redirect to their appropriate route
            $user = Auth::user();
            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.activity.index');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.activity.index');
            }
            
            // If role is not recognized, redirect to login
            return redirect()->route('admin.login')
                ->with('error', 'Akses ditolak. Anda harus login sebagai Admin BAS.');
        }

        // Ensure user is accessing the correct URL prefix
        $currentPath = $request->path();
        if (!str_starts_with($currentPath, 'bas/')) {
            return redirect()->route('bas.activity.index');
        }

        // Store the role in the session to ensure context is maintained
        session(['user_role' => 'admin_bas']);
        
        // Make it available in views
        view()->share('currentRole', 'admin_bas');
        
        return $next($request);
    }
} 