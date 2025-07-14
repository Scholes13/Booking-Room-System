<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class LeadWorksheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_survey_id',
        'pic_employee_id',
        'line_of_business',
        'current_status',
        'project_name',
        'month_project',
        'month_receive_lead',
        'requirements',
        'service_type',
        'follow_up_status',
        'estimated_revenue',
        'materialized_revenue',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'service_type' => 'array',
        'month_project' => 'date',
        'month_receive_lead' => 'date',
    ];

    /**
     * Get the survey that this worksheet belongs to.
     */
    public function feedbackSurvey()
    {
        return $this->belongsTo(FeedbackSurvey::class);
    }

    /**
     * Get the employee (PIC) assigned to this worksheet.
     */
    public function pic()
    {
        return $this->belongsTo(Employee::class, 'pic_employee_id');
    }

    /**
     * Get all of the status logs for this worksheet.
     */
    public function statusLogs()
    {
        return $this->hasMany(LeadStatusLog::class)->orderBy('created_at', 'desc');
    }
}
