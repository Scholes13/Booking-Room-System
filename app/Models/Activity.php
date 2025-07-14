<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Services\FontneService;
use Illuminate\Support\Facades\Log;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'activity_type',
        'other_activity_type',
        'description',
        'city',
        'province',
        'start_datetime',
        'end_datetime',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime'
    ];
    
    /**
     * Get the activity's status based on current time.
     * This also persists the computed status to the database.
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // If there's a status already set in the database, use it
                if ($value) {
                    return $value;
                }
                
                $now = Carbon::now();
                $startTime = $this->start_datetime;
                $endTime = $this->end_datetime;
                
                // If start or end time is not set, return default status
                if (!$startTime || !$endTime) {
                    return 'scheduled';
                }

                $computedStatus = 'scheduled';
                
                if ($now->lt($startTime)) {
                    $computedStatus = 'scheduled';
                } elseif ($now->gte($startTime) && $now->lte($endTime)) {
                    $computedStatus = 'ongoing';
                } elseif ($now->gt($endTime)) {
                    $computedStatus = 'completed';
                }
                
                // Save the computed status to the database
                if ($this->exists && !$value) {
                    $this->status = $computedStatus;
                    $this->saveQuietly();
                }

                return $computedStatus;
            },
            set: function ($value) {
                return $value;
            }
        );
    }

    /**
     * Boot method to ensure status is always updated on save
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($activity) {
            // Update status based on dates if not explicitly set
            if (!$activity->isDirty('status') && $activity->start_datetime && $activity->end_datetime) {
                $now = Carbon::now();
                $startTime = $activity->start_datetime;
                $endTime = $activity->end_datetime;
                
                if ($now->lt($startTime)) {
                    $activity->status = 'scheduled';
                } elseif ($now->gte($startTime) && $now->lte($endTime)) {
                    $activity->status = 'ongoing';
                } elseif ($now->gt($endTime)) {
                    $activity->status = 'completed';
                }
            }
            
            // Set created_by if not explicitly set
            if (!$activity->created_by && auth()->check()) {
                $activity->created_by = auth()->id();
            }
        });

        static::updated(function ($activity) {
            // Check if start_datetime or end_datetime was changed
            if ($activity->isDirty('start_datetime') || $activity->isDirty('end_datetime')) {
                // Load team assignments with their related data
                $activity->loadMissing('teamAssignments.team', 'teamAssignments.feedbackSurvey');

                if ($activity->teamAssignments->isNotEmpty()) {
                    $fontneService = new FontneService();
                    foreach ($activity->teamAssignments as $assignment) {
                        try {
                            // Make sure the assignment itself isn't null and has a team
                            if ($assignment && $assignment->team) {
                                Log::info('Sending schedule update notification for assignment ID: ' . $assignment->id);
                                $fontneService->sendScheduleUpdateNotification($assignment);
                            } else {
                                Log::warning('Skipping notification for assignment due to missing team or assignment data.', ['activity_id' => $activity->id, 'assignment_id' => $assignment->id ?? null]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error dispatching schedule update notification for assignment ID: ' . $assignment->id . ' - ' . $e->getMessage());
                        }
                    }
                }
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function room()
    {
        return $this->belongsTo(MeetingRoom::class, 'meeting_room_id');
    }
    
    /**
     * Get the sales mission details associated with this activity.
     */
    public function salesMissionDetail()
    {
        return $this->hasOne(SalesMissionDetail::class);
    }
    
    /**
     * Get the user who created this activity.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the teams assigned to this activity.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_assignments', 'activity_id', 'team_id')
            ->withPivot('assignment_date', 'notes', 'assigned_by')
            ->withTimestamps();
    }
    
    /**
     * Get the team assignments for this activity.
     */
    public function teamAssignments()
    {
        return $this->hasMany(TeamAssignment::class);
    }
}
