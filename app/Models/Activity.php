<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'department',
        'start_datetime',
        'end_datetime',
        'activity_type',
        'description',
    ];
}
