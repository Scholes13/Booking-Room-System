<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'capacity', 'facilities'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
