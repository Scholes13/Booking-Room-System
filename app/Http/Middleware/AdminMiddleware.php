<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Anda harus login sebagai admin.');
        }
        
        $user = Auth::user();

        // Periksa apakah user memiliki role admin atau superadmin
        if ($user->role !== 'admin' && $user->role !== 'superadmin') {
            abort(403, 'Anda tidak memiliki akses ke area admin.');
        }

        return $next($request);
    }
}
