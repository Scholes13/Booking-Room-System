<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeedbackSurvey extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'team_assignment_id',
        'survey_token',
        'survey_type',
        'blitz_team_name',
        'blitz_team_id',
        'blitz_company_name',
        'blitz_visit_start_datetime',
        'blitz_visit_end_datetime',
        'is_completed',
        'visited_time',
        'contact_salutation',
        'contact_name',
        'contact_job_title',
        'department',
        'contact_mobile',
        'contact_email',
        'decision_maker_status',
        'sales_call_outcome',
        'next_follow_up',
        'next_follow_up_other',
        'product_interested',
        'status_lead',
        'potential_revenue',
        'key_discussion_points',
        'has_documentation',
        'has_business_card',
        'completed_at',
        'viewed_at',
        'last_viewed_at',
        'actual_end_datetime'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'visited_time' => 'datetime',
        'blitz_visit_start_datetime' => 'datetime',
        'blitz_visit_end_datetime' => 'datetime',
        'has_documentation' => 'boolean',
        'has_business_card' => 'boolean',
        'viewed_at' => 'datetime',
        'last_viewed_at' => 'datetime',
        'actual_end_datetime' => 'datetime',
    ];

    // Status constants
    const STATUS_SENDING = 'Sending';
    const STATUS_VIEWED = 'Viewed';
    const STATUS_ANSWERED = 'Answered';

    /**
     * Get the team assignment this survey belongs to
     */
    public function teamAssignment()
    {
        return $this->belongsTo(TeamAssignment::class);
    }
    
    /**
     * Check if the survey has been viewed
     */
    public function isViewed()
    {
        return !is_null($this->viewed_at);
    }
    
    /**
     * Track a view of the survey
     */
    public function trackView()
    {
        $changed = false;
        // Set the first view timestamp if not set yet
        if (is_null($this->viewed_at)) {
            $this->viewed_at = now();
            $changed = true;
        }
        
        // Always update the last viewed timestamp
        $this->last_viewed_at = now();
        $changed = true; // Selalu dianggap berubah jika kita mengupdate last_viewed_at

        if ($changed) {
            return $this->save();
        }
        return false;
    }

    /**
     * Get the current status of the survey
     *
     * @return string - One of: 'Sending', 'Viewed', 'Answered'
     */
    public function getStatus()
    {
        if ($this->is_completed) {
            return self::STATUS_ANSWERED;
        }
        
        if ($this->isViewed()) {
            return self::STATUS_VIEWED;
        }
        
        return self::STATUS_SENDING;
    }

    /**
     * Get the CSS classes for the status badge
     *
     * @return string
     */
    public function getStatusClasses()
    {
        $status = $this->getStatus();
        
        switch ($status) {
            case self::STATUS_ANSWERED:
                return 'bg-green-100 text-green-800';
            case self::STATUS_VIEWED:
                return 'bg-blue-100 text-blue-800';
            case self::STATUS_SENDING:
            default:
                return 'bg-yellow-100 text-yellow-800';
        }
    }

    /**
     * Get the team this blitz survey might belong to (if survey_type is 'sales_blitz')
     */
    public function blitzTeam()
    {
        return $this->belongsTo(Team::class, 'blitz_team_id');
    }

    /**
     * Get the lead worksheet associated with the feedback survey.
     */
    public function leadWorksheet()
    {
        return $this->hasOne(LeadWorksheet::class);
    }

    /**
     * Get all of the reviews for the feedback survey.
     */
    public function leadReviews()
    {
        return $this->hasMany(LeadReview::class);
    }
}
