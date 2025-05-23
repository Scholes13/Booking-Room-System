<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = ['name', 'country_id'];

    /**
     * Get the country that owns the province.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the cities for the province.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
