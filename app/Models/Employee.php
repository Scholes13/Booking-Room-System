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
        'position'
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
}