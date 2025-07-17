<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'province',
        'industry',
        'company_size',
        'status',
        'website',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get all contacts for this company
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CompanyContact::class);
    }

    /**
     * Get the primary contact for this company
     */
    public function primaryContact()
    {
        return $this->hasOne(CompanyContact::class)->where('is_primary', true);
    }

    /**
     * Get all active contacts for this company
     */
    public function activeContacts(): HasMany
    {
        return $this->hasMany(CompanyContact::class)->where('status', 'active');
    }

    /**
     * Get all sales mission details for this company
     */
    public function salesMissionDetails(): HasMany
    {
        return $this->hasMany(SalesMissionDetail::class);
    }

    /**
     * Get all activities related to this company through sales missions
     */
    public function activities()
    {
        return $this->hasManyThrough(Activity::class, SalesMissionDetail::class);
    }

    /**
     * Get the total number of visits to this company
     */
    public function getTotalVisitsAttribute(): int
    {
        return $this->salesMissionDetails()->count();
    }

    /**
     * Get the latest visit to this company
     */
    public function getLatestVisitAttribute()
    {
        return $this->salesMissionDetails()->latest()->first();
    }

    /**
     * Get the first visit to this company
     */
    public function getFirstVisitAttribute()
    {
        return $this->salesMissionDetails()->oldest()->first();
    }

    /**
     * Check if this is a new prospect (only initial visits)
     */
    public function getIsNewProspectAttribute(): bool
    {
        return $this->salesMissionDetails()->where('visit_type', 'follow_up')->count() === 0;
    }

    /**
     * Get visit history grouped by type
     */
    public function getVisitHistoryAttribute(): array
    {
        $visits = $this->salesMissionDetails()
            ->with(['activity', 'companyContact'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'total' => $visits->count(),
            'initial' => $visits->where('visit_type', 'initial')->count(),
            'follow_up' => $visits->where('visit_type', 'follow_up')->count(),
            'visits' => $visits->toArray(),
        ];
    }

    /**
     * Scope to filter companies by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter companies by city
     */
    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope to search companies by name
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    /**
     * Scope to get companies with multiple visits
     */
    public function scopeWithMultipleVisits($query)
    {
        return $query->has('salesMissionDetails', '>', 1);
    }

    /**
     * Scope to get new prospects (companies with only initial visits)
     */
    public function scopeNewProspects($query)
    {
        return $query->whereDoesntHave('salesMissionDetails', function ($q) {
            $q->where('visit_type', 'follow_up');
        });
    }
}