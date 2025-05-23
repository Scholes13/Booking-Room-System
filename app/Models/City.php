<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = ['name', 'province_id'];

    /**
     * Get the province that owns the city.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
