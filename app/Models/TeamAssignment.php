<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamAssignment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'team_id',
        'activity_id',
        'assignment_date',
        'notes',
        'assigned_by'
    ];
    
    protected $casts = [
        'assignment_date' => 'date'
    ];
    
    /**
     * Get the team for this assignment.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    
    /**
     * Get the activity for this assignment.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    
    /**
     * Get the user who assigned this team.
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    /**
     * Get the feedback survey for this assignment.
     */
    public function feedbackSurvey()
    {
        return $this->hasOne(FeedbackSurvey::class);
    }
}
