<?php

namespace App\Services;

use App\Models\FeedbackSurvey;
use App\Models\TeamAssignment;
use App\Models\Team;
use Illuminate\Support\Str;

class FeedbackSurveySlugService
{
    /**
     * Generate a readable URL slug for a team assignment feedback survey
     *
     * @param TeamAssignment $teamAssignment
     * @return string
     */
    public function generateSlug(TeamAssignment $teamAssignment): string
    {
        // Load necessary relationships if not already loaded
        $teamAssignment->load(['team', 'activity.salesMissionDetail']);
        
        // Extract team name and company name
        $teamName = $this->sanitizeForSlug($teamAssignment->team->name ?? 'team');
        $companyName = $this->sanitizeForSlug(
            $teamAssignment->activity->salesMissionDetail->company_name ?? 'company'
        );
        
        // Create base slug: team-alpha-pt-abc-company
        $baseSlug = "{$teamName}-{$companyName}";
        
        // Ensure uniqueness with incremental suffix
        return $this->ensureUniqueSlug($baseSlug);
    }
    
    /**
     * Generate a readable URL slug for a sales blitz survey
     *
     * @param array $blitzData
     * @return string
     */
    public function generateBlitzSlug(array $blitzData): string
    {
        $team = Team::find($blitzData['blitz_team_id']);
        $teamName = $this->sanitizeForSlug($team->name ?? 'team');
        $companyName = $this->sanitizeForSlug($blitzData['blitz_company_name'] ?? 'company');
        
        $baseSlug = "blitz-{$teamName}-{$companyName}";
        return $this->ensureUniqueSlug($baseSlug);
    }
    
    /**
     * Sanitize text for use in URL slug
     *
     * @param string $text
     * @return string
     */
    private function sanitizeForSlug(string $text): string
    {
        // Remove common company prefixes/suffixes for cleaner slugs
        $text = preg_replace('/^(PT\.?\s*|CV\.?\s*|UD\.?\s*|PD\.?\s*)/i', '', $text);
        $text = preg_replace('/(\s*,?\s*(PT|CV|UD|PD)\.?)$/i', '', $text);
        
        // Convert to lowercase, remove special chars, replace spaces with hyphens
        $slug = Str::slug($text, '-');
        
        // Ensure we have something if the slug becomes empty
        return $slug ?: 'company';
    }
    
    /**
     * Ensure the slug is unique by adding incremental suffix if needed
     *
     * @param string $baseSlug
     * @return string
     */
    private function ensureUniqueSlug(string $baseSlug): string
    {
        $counter = 1;
        $slug = $baseSlug;
        
        // Check if base slug already exists
        while (FeedbackSurvey::where('url_slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Generate slug for existing survey (for migration purposes)
     *
     * @param FeedbackSurvey $survey
     * @return string|null
     */
    public function generateSlugForExistingSurvey(FeedbackSurvey $survey): ?string
    {
        if ($survey->survey_type === 'sales_blitz') {
            // Handle sales blitz surveys
            if ($survey->blitzTeam && $survey->blitz_company_name) {
                return $this->generateBlitzSlug([
                    'blitz_team_id' => $survey->blitz_team_id,
                    'blitz_company_name' => $survey->blitz_company_name
                ]);
            }
        } else {
            // Handle regular team assignment surveys
            if ($survey->teamAssignment) {
                return $this->generateSlug($survey->teamAssignment);
            }
        }
        
        return null;
    }
}