<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\SalesOfficerActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesOfficerDashboardService
{
    public function getDashboardData()
    {
        $currentMonthActivities = $this->getCurrentMonthActivities();
        $totalSalesMissions = $this->getTotalSalesMissions();
        $recentActivities = $this->getRecentActivities();
        $chartData = $this->getChartData();

        return compact(
            'currentMonthActivities',
            'totalSalesMissions',
            'recentActivities',
            'chartData'
        );
    }

    private function getCurrentMonthActivities()
    {
        return SalesOfficerActivity::whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->where('user_id', auth()->id())
            ->count();
    }

    private function getTotalSalesMissions()
    {
        return Activity::where('activity_type', 'Sales Mission')
            ->whereHas('salesMissionDetail')
            ->count();
    }

    private function getRecentActivities()
    {
        return SalesOfficerActivity::where('user_id', auth()->id())
            ->orderBy('start_datetime', 'desc')
            ->limit(5)
            ->get();
    }

    private function getChartData()
    {
        $activitiesByMonth = SalesOfficerActivity::select(DB::raw('MONTH(start_datetime) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('start_datetime', now()->year)
            ->where('user_id', auth()->id())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $chartData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            $count = $activitiesByMonth->firstWhere('month', $monthNumber)->count ?? 0;
            
            $chartData[] = [
                'month' => $month,
                'count' => $count
            ];
        }

        return $chartData;
    }
} 