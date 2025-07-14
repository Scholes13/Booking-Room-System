<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'department_id',
        'position',
        'phone',
        'email',
        'employee_id'
    ];

    /**
     * Get the gender label
     */
    public function getGenderLabelAttribute()
    {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Get the bookings for the employee.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'nama', 'name');
    }

    /**
     * Get the department that owns the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the teams that this employee is a member of
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_employees', 'employee_id', 'team_id');
    }

    // Look for the Employee model file and add an array/orderBy function for the numbered positions
}