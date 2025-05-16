<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use Carbon\Carbon;

class UpdateActivityStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:update-status {--force : Force update all statuses regardless of existing values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates all activities with their computed status based on dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update activity statuses...');
        
        $query = Activity::query();
        
        // Only update activities with null status unless --force is used
        if (!$this->option('force')) {
            $query->whereNull('status');
            $this->info('Only updating activities with null status. Use --force to update all.');
        } else {
            $this->info('Force option detected. Updating all activities regardless of current status.');
        }
        
        $activities = $query->get();
        $count = $activities->count();
        
        if ($count === 0) {
            $this->info('No activities found that need status updates.');
            return 0;
        }
        
        $this->info("Found {$count} activities to update.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $updated = 0;
        
        foreach ($activities as $activity) {
            $now = Carbon::now();
            $startTime = $activity->start_datetime;
            $endTime = $activity->end_datetime;
            
            // Skip activities without proper dates
            if (!$startTime || !$endTime) {
                $activity->status = 'scheduled';
                $activity->save();
                $updated++;
                $bar->advance();
                continue;
            }
            
            // Determine status based on dates
            if ($now->lt($startTime)) {
                $activity->status = 'scheduled';
            } elseif ($now->gte($startTime) && $now->lte($endTime)) {
                $activity->status = 'ongoing';
            } elseif ($now->gt($endTime)) {
                $activity->status = 'completed';
            }
            
            $activity->save();
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info("Updated {$updated} activities with their current status.");
        
        return 0;
    }
}
