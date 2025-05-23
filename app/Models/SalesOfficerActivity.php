<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalesOfficerActivity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'user_id',
        'department_id',
        'description',
        'activity_type',
        'meeting_type',
        'city',
        'province',
        'country',
        'country_id',
        'state_id',
        'city_id',
        'start_datetime',
        'end_datetime',
        'month_number',
        'week_number',
        'status',
        'result',
        'notes',
        'contact_id',
        'division_id',
        'pic_id',
        'account_status',
        'products_discussed',
        'next_follow_up',
        'follow_up_type',
        'jso_lead_status',
        'follow_up_frequency'
    ];
    
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime'
    ];
    
    /**
     * Get the user (sales officer) who owns this activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the department associated with this activity.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Get the contact associated with this activity.
     */
    public function contact()
    {
        return $this->belongsTo(SalesOfficerContact::class, 'contact_id');
    }
    
    /**
     * Get the division associated with this activity.
     */
    public function division()
    {
        return $this->belongsTo(CompanyDivision::class, 'division_id');
    }
    
    /**
     * Get the PIC (Person In Charge) for this activity.
     */
    public function pic()
    {
        return $this->belongsTo(ContactPerson::class, 'pic_id');
    }
    
    /**
     * Check if the activity is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    
    /**
     * Check if the activity is scheduled.
     */
    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }
    
    /**
     * Check if the activity is ongoing.
     */
    public function isOngoing()
    {
        return $this->status === 'ongoing';
    }
    
    /**
     * Update the activity status based on the current time.
     */
    public function updateStatus()
    {
        $now = Carbon::now();
        
        if ($now->isBefore($this->start_datetime)) {
            $this->status = 'scheduled';
        } elseif ($now->isAfter($this->start_datetime) && $now->isBefore($this->end_datetime)) {
            $this->status = 'ongoing';
        } elseif ($now->isAfter($this->end_datetime) && $this->status !== 'completed') {
            $this->status = 'pending_report';
        }
        
        $this->save();
        return $this;
    }
    
    /**
     * Calculate the duration in hours between start_datetime and end_datetime.
     *
     * @return float
     */
    public function getDurationHoursAttribute()
    {
        if (!$this->start_datetime || !$this->end_datetime) {
            return 1; // Default value
        }
        
        $start = $this->start_datetime;
        $end = $this->end_datetime;
        
        // Calculate the difference in hours
        $diffInSeconds = $end->diffInSeconds($start);
        return round($diffInSeconds / 3600, 1); // Convert seconds to hours with 1 decimal precision
    }
}
