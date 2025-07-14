<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk superadmin.
     * Pastikan file resources/views/superadmin/dashboard.blade.php sudah ada.
     */
    public function superAdminDashboard()
    {
        return view('superadmin.dashboard.index');
    }
} 