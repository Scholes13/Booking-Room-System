<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];

    /**
     * Get the user who created the team.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assignments for the team.
     */
    public function assignments()
    {
        return $this->hasMany(TeamAssignment::class);
    }

    /**
     * Get the activities assigned to this team.
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'team_assignments', 'team_id', 'activity_id')
            ->withPivot('assignment_date', 'notes', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Get the employees (members) assigned to this team
     */
    public function members()
    {
        return $this->belongsToMany(Employee::class, 'team_employees', 'team_id', 'employee_id');
    }
}
