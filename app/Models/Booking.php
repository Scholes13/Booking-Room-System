<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'booking_type',
        'external_description',
        'status',
        'user_id'
    ];

    protected $appends = ['dynamic_status'];

    public function getDynamicStatusAttribute()
    {
        $now = now();
        $startDateTime = Carbon::parse($this->date . ' ' . $this->start_time);
        $endDateTime = Carbon::parse($this->date . ' ' . $this->end_time);

        if ($now->between($startDateTime, $endDateTime)) {
            return 'Ongoing';
        }

        if ($now->isAfter($endDateTime)) {
            return 'Completed';
        }

        return 'Scheduled';
    }

    public function meetingRoom()
    {
        return $this->belongsTo(MeetingRoom::class, 'meeting_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
