<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'position',
        'department',
        'phone',
        'email',
        'is_primary',
        'notes',
        'status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the company that owns this contact
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all sales mission details for this contact
     */
    public function salesMissionDetails(): HasMany
    {
        return $this->hasMany(SalesMissionDetail::class);
    }

    /**
     * Get all activities related to this contact through sales missions
     */
    public function activities()
    {
        return $this->hasManyThrough(Activity::class, SalesMissionDetail::class);
    }

    /**
     * Get the total number of interactions with this contact
     */
    public function getTotalInteractionsAttribute(): int
    {
        return $this->salesMissionDetails()->count();
    }

    /**
     * Get the latest interaction with this contact
     */
    public function getLatestInteractionAttribute()
    {
        return $this->salesMissionDetails()->latest()->first();
    }

    /**
     * Get the first interaction with this contact
     */
    public function getFirstInteractionAttribute()
    {
        return $this->salesMissionDetails()->oldest()->first();
    }

    /**
     * Get full contact information formatted
     */
    public function getFullContactInfoAttribute(): string
    {
        $info = $this->name;
        
        if ($this->position) {
            $info .= " ({$this->position})";
        }
        
        if ($this->department) {
            $info .= " - {$this->department}";
        }
        
        return $info;
    }

    /**
     * Get contact methods (phone, email) as array
     */
    public function getContactMethodsAttribute(): array
    {
        $methods = [];
        
        if ($this->phone) {
            $methods['phone'] = $this->phone;
        }
        
        if ($this->email) {
            $methods['email'] = $this->email;
        }
        
        return $methods;
    }

    /**
     * Scope to filter active contacts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter primary contacts
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to filter by company
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to search contacts by name
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('position', 'LIKE', "%{$search}%")
                    ->orWhere('department', 'LIKE', "%{$search}%");
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When setting a contact as primary, unset other primary contacts for the same company
        static::saving(function ($contact) {
            if ($contact->is_primary && $contact->company_id) {
                static::where('company_id', $contact->company_id)
                     ->where('id', '!=', $contact->id)
                     ->update(['is_primary' => false]);
            }
        });
    }
}