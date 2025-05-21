<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan perintah reminder setiap menit untuk cek booking/aktivitas yang akan datang dalam 1 jam
        $schedule->command('reminders:booking')->everyMinute();
        $schedule->command('reminders:activity')->everyMinute();
        
        // Update activity statuses every hour
        $schedule->command('activity:update-status')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        // ... existing commands ...
        \App\Console\Commands\UpdateSalesMissionLocations::class,
        \App\Console\Commands\UpdateJakartaLocations::class,
        \App\Console\Commands\UpdateAllSalesMissionLocations::class,
        \App\Console\Commands\ImportPicsFromSalesMission::class,
    ];
} 