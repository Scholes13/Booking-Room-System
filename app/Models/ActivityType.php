<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Get the activities that belong to this activity type.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'activity_type', 'name');
    }
}
