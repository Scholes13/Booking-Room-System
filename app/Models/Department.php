<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the bookings for the department.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'department', 'name');
    }
}