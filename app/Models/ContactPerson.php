<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'division_id',
        'title',
        'name',
        'position',
        'phone_number',
        'email',
        'notes',
        'is_primary',
        'source'
    ];

    /**
     * Get the company contact that owns this PIC.
     */
    public function contact()
    {
        return $this->belongsTo(SalesOfficerContact::class, 'contact_id');
    }

    /**
     * Get the division that this PIC is associated with.
     */
    public function division()
    {
        return $this->belongsTo(CompanyDivision::class, 'division_id');
    }

    /**
     * Get the activities associated with this PIC.
     */
    public function activities()
    {
        return $this->hasMany(SalesOfficerActivity::class, 'pic_id');
    }

    /**
     * Get the full name with title.
     */
    public function getFullNameAttribute()
    {
        return $this->title . ' ' . $this->name;
    }

    /**
     * Mark this PIC as primary for the company.
     */
    public function markAsPrimary()
    {
        // First, unmark all other PICs for this company
        ContactPerson::where('contact_id', $this->contact_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
            
        // Then mark this one as primary
        $this->update(['is_primary' => true]);
        
        return $this;
    }
}
