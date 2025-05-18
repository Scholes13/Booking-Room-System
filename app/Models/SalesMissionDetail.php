<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesMissionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'company_name',
        'company_pic',
        'company_contact',
        'company_address'
    ];

    /**
     * Get the activity associated with the sales mission detail.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
