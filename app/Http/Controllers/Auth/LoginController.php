<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login admin.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login admin/superadmin menggunakan Auth.
     * Input login bisa berupa username atau email.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $login    = $request->input('login');
        $password = $request->input('password');

        // Tentukan apakah input login merupakan email atau username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Gunakan Auth::attempt() untuk verifikasi user + password
        if (Auth::attempt([$field => $login, 'password' => $password])) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Set role in session without flushing first
            session(['user_role' => $user->role]);

            // Pisahkan admin & superadmin & admin_bas berdasarkan role
            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard')
                               ->with('success', 'Selamat datang, Super Admin!');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                               ->with('success', 'Selamat datang, Admin!');
            } elseif ($user->role === 'admin_bas') {
                return redirect()->route('bas.dashboard')
                               ->with('success', 'Selamat datang, Admin BAS!');
            } elseif ($user->role === 'sales_mission') {
                return redirect()->route('sales_mission.dashboard')
                               ->with('success', 'Selamat datang, Sales Mission!');
            } elseif ($user->role === 'sales_officer') {
                return redirect()->route('sales_officer.dashboard')
                               ->with('success', 'Selamat datang, Sales Officer!');
            } elseif ($user->role === 'lead') {
                return redirect()->route('lead.dashboard')
                               ->with('success', 'Selamat datang, Lead!');
            } else {
                // Jika role tidak valid, logout dan kembalikan error
                Auth::logout();
                session()->forget('user_role');
                return redirect()->route('admin.login')
                    ->with('error', 'Anda tidak memiliki akses ke area admin. Role tidak valid.');
            }
        }

        return redirect()->back()
                       ->with('error', 'Login gagal. Periksa kembali login dan password anda.');
    }

    /**
     * Logout admin/superadmin.
     */
    public function logout()
    {
        Auth::logout();
        session()->forget('user_role');
        return redirect()->route('admin.login')
                       ->with('success', 'Anda telah berhasil logout.');
    }
} 