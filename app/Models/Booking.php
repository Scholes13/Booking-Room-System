<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'department',
        'date',
        'start_time',
        'end_time',
        'description',
        'meeting_room_id',
    ];

    public function meetingRoom()
    {
        return $this->belongsTo(MeetingRoom::class);
    }
}
