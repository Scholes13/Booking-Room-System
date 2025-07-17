<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedbackSurvey;
use App\Services\FeedbackSurveySlugService;

class GenerateFeedbackSurveySlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surveys:generate-slugs {--force : Force regeneration of existing slugs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate readable URL slugs for existing feedback surveys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate URL slugs for feedback surveys...');
        
        $slugService = new FeedbackSurveySlugService();
        $force = $this->option('force');
        
        // Build query based on force option
        $query = FeedbackSurvey::with([
            'teamAssignment.team', 
            'teamAssignment.activity.salesMissionDetail',
            'blitzTeam'
        ]);
        
        if (!$force) {
            $query->whereNull('url_slug');
        }
        
        $totalSurveys = $query->count();
        
        if ($totalSurveys === 0) {
            $this->info('No surveys found that need slug generation.');
            return 0;
        }
        
        $this->info("Found {$totalSurveys} surveys to process.");
        
        $progressBar = $this->output->createProgressBar($totalSurveys);
        $progressBar->start();
        
        $processed = 0;
        $errors = 0;
        
        $query->chunk(100, function ($surveys) use ($slugService, &$processed, &$errors, $progressBar) {
            foreach ($surveys as $survey) {
                try {
                    $slug = $slugService->generateSlugForExistingSurvey($survey);
                    
                    if ($slug) {
                        $survey->update(['url_slug' => $slug]);
                        $processed++;
                    } else {
                        $this->newLine();
                        $this->warn("Could not generate slug for survey ID: {$survey->id} (missing required data)");
                        $errors++;
                    }
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Error processing survey ID {$survey->id}: " . $e->getMessage());
                    $errors++;
                }
                
                $progressBar->advance();
            }
        });
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("Slug generation completed!");
        $this->info("Successfully processed: {$processed} surveys");
        
        if ($errors > 0) {
            $this->warn("Errors encountered: {$errors} surveys");
        }
        
        // Show some examples of generated slugs
        $this->newLine();
        $this->info("Examples of generated slugs:");
        
        $examples = FeedbackSurvey::whereNotNull('url_slug')
            ->with(['teamAssignment.team', 'teamAssignment.activity.salesMissionDetail'])
            ->limit(5)
            ->get();
            
        foreach ($examples as $example) {
            $url = route('sales_mission.surveys.public.form', $example->url_slug);
            $this->line("• {$example->url_slug} → {$url}");
        }
        
        return 0;
    }
}