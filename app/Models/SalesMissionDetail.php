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
        'company_position',
        'company_contact',
        'company_email',
        'company_address'
    ];

    /**
     * Get the activity associated with the sales mission detail.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    
    /**
     * Get the sales officer contact that was created from this sales mission detail.
     */
    public function salesOfficerContact()
    {
        return $this->hasOne(SalesOfficerContact::class);
    }
}
