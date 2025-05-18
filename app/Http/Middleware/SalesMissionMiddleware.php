<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SalesMissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }
        
        $user = Auth::user();

        // Periksa apakah user memiliki role sales_mission atau superadmin
        if ($user->role !== 'sales_mission' && $user->role !== 'superadmin') {
            // Don't logout, just redirect with error message
            return redirect()->route('admin.login')
                ->with('error', 'Anda tidak memiliki akses ke area Sales Mission.');
        }

        // Simpan role di session untuk memastikan konsistensi
        session(['user_role' => $user->role]);
        
        return $next($request);
    }
}
