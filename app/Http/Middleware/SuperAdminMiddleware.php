<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Anda harus login sebagai super admin.');
        }
        
        $user = Auth::user();

        // Periksa apakah user memiliki role superadmin
        if ($user->role !== 'superadmin') {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki akses ke area super admin.');
        }

        // Simpan role di session untuk memastikan konsistensi
        session(['user_role' => 'superadmin']);
        
        // Buat parameter untuk view layout agar semua view tahu ini adalah superadmin
        view()->share('isSuperAdmin', true);
        
        return $next($request);
    }
} 