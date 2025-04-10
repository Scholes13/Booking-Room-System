<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'end_datetime'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime'
    ];
    
    /**
     * Get the activity's status based on current time.
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

                if ($now->lt($startTime)) {
                    return 'scheduled';
                } elseif ($now->gte($startTime) && $now->lte($endTime)) {
                    return 'ongoing';
                } elseif ($now->gt($endTime)) {
                    return 'completed';
                }

                return 'scheduled'; // Default status
            }
        );
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function room()
    {
        return $this->belongsTo(MeetingRoom::class, 'meeting_room_id');
    }
}
