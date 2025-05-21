<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'name',
        'visit_count'
    ];

    /**
     * Get the company contact that owns this division.
     */
    public function contact()
    {
        return $this->belongsTo(SalesOfficerContact::class, 'contact_id');
    }

    /**
     * Get the PICs (contact persons) associated with this division.
     */
    public function contactPeople()
    {
        return $this->hasMany(ContactPerson::class, 'division_id');
    }

    /**
     * Get the activities associated with this division (through the PIC).
     */
    public function activities()
    {
        return $this->hasManyThrough(
            SalesOfficerActivity::class,
            ContactPerson::class,
            'division_id', // Foreign key on ContactPerson table
            'pic_id',      // Foreign key on SalesOfficerActivity table
            'id',          // Local key on CompanyDivision table
            'id'           // Local key on ContactPerson table
        );
    }

    /**
     * Increment the visit count for this division.
     */
    public function incrementVisitCount()
    {
        $this->increment('visit_count');
        return $this;
    }
}
