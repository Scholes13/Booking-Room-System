<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_worksheet_id',
        'user_id',
        'status',
        'notes',
    ];

    /**
     * Get the worksheet that this log belongs to.
     */
    public function leadWorksheet()
    {
        return $this->belongsTo(LeadWorksheet::class);
    }

    /**
     * Get the user who created this log entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
