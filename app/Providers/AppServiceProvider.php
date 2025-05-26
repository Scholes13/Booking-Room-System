<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Activity;
use Carbon\Carbon;
use App\Services\FontneService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register FontneService as a singleton
        $this->app->singleton(FontneService::class, function ($app) {
            return new FontneService();
        });
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
