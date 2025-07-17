<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesMissionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'company_id',
        'company_contact_id',
        'visit_type',
        'visit_sequence',
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
     * Get the company associated with this sales mission detail.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Get the company contact associated with this sales mission detail.
     */
    public function companyContact()
    {
        return $this->belongsTo(CompanyContact::class);
    }
    
    /**
     * Get the sales officer contact that was created from this sales mission detail.
     */
    public function salesOfficerContact()
    {
        return $this->hasOne(SalesOfficerContact::class);
    }
    
    /**
     * Check if this is an initial visit
     */
    public function isInitialVisit(): bool
    {
        return $this->visit_type === 'initial';
    }
    
    /**
     * Check if this is a follow-up visit
     */
    public function isFollowUpVisit(): bool
    {
        return $this->visit_type === 'follow_up';
    }
    
    /**
     * Get the previous visit to the same company
     */
    public function getPreviousVisit()
    {
        if (!$this->company_id) {
            return null;
        }
        
        return static::where('company_id', $this->company_id)
                    ->where('visit_sequence', '<', $this->visit_sequence)
                    ->orderBy('visit_sequence', 'desc')
                    ->first();
    }
    
    /**
     * Get the next visit to the same company
     */
    public function getNextVisit()
    {
        if (!$this->company_id) {
            return null;
        }
        
        return static::where('company_id', $this->company_id)
                    ->where('visit_sequence', '>', $this->visit_sequence)
                    ->orderBy('visit_sequence', 'asc')
                    ->first();
    }
    
    /**
     * Scope to filter by visit type
     */
    public function scopeByVisitType($query, string $type)
    {
        return $query->where('visit_type', $type);
    }
    
    /**
     * Scope to filter initial visits
     */
    public function scopeInitialVisits($query)
    {
        return $query->where('visit_type', 'initial');
    }
    
    /**
     * Scope to filter follow-up visits
     */
    public function scopeFollowUpVisits($query)
    {
        return $query->where('visit_type', 'follow_up');
    }
    
    /**
     * Scope to filter by company
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
