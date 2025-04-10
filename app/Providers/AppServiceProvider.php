<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Activity;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add automatic status updates for activities based on current time
        Activity::retrieved(function ($activity) {
            if (!isset($activity->status)) {
                $now = Carbon::now();
                $startTime = $activity->start_datetime;
                $endTime = $activity->end_datetime;

                if ($now->lt($startTime)) {
                    $activity->setAttribute('status', 'scheduled');
                } elseif ($now->gte($startTime) && $now->lte($endTime)) {
                    $activity->setAttribute('status', 'ongoing');
                } elseif ($now->gt($endTime)) {
                    $activity->setAttribute('status', 'completed');
                }
            }
        });
    }
}
