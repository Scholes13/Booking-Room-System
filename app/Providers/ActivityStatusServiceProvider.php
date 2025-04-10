<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Activity;
use Carbon\Carbon;

class ActivityStatusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Activity::resolveRelationUsing('dynamicStatus', function ($activity) {
            $now = Carbon::now();
            $startTime = Carbon::parse($activity->start_datetime);
            $endTime = Carbon::parse($activity->end_datetime);

            if ($now->lt($startTime)) {
                return 'scheduled';
            } elseif ($now->gte($startTime) && $now->lte($endTime)) {
                return 'ongoing';
            } elseif ($now->gt($endTime)) {
                return 'completed';
            }

            return 'scheduled'; // Default status
        });
    }
}
