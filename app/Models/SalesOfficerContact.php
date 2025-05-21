<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Log;

class SalesOfficerContact extends Model
{
    use HasFactory;
    
    protected $attributes = [
        'line_of_business' => 'Other',
        'status' => 'active',
        'visit_count' => 0
    ];
    
    protected $fillable = [
        'user_id',
        'company_name',
        'line_of_business',
        'company_address',
        'contact_name',
        'position',
        'phone_number',
        'email',
        'sales_mission_detail_id',
        'notes',
        'status',
        'source',
        'visit_count',
        'country',
        'province',
        'city',
        'general_information',
        'current_event',
        'target_business',
        'project_type',
        'project_estimation',
        'potential_revenue',
        'potential_project_count'
    ];
    
    /**
     * Define the casts for attributes.
     */
    protected $casts = [
        'potential_revenue' => 'decimal:2',
    ];
    
    /**
     * Get the formatted potential revenue attribute (in Rupiah format).
     */
    protected function formattedPotentialRevenue(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->potential_revenue) {
                    return null;
                }
                return 'Rp ' . number_format($this->potential_revenue, 0, ',', '.');
            }
        );
    }
    
    /**
     * Handle potential revenue formatting when setting the value.
     */
    protected function potentialRevenue(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                // Log raw value for debugging
                Log::info('SalesOfficerContact - Setting potential_revenue raw value: ' . (is_string($value) ? $value : gettype($value)));
                
                if (is_null($value) || $value === '') {
                    Log::info('SalesOfficerContact - Empty potential_revenue, setting to null');
                    return null;
                }
                
                if (is_string($value)) {
                    // Clean the string value by removing 'Rp', dots (thousand separators), and spaces
                    $value = str_replace(['Rp', '.', ' '], '', $value);
                    // Replace comma with period for decimal point if present
                    $value = str_replace(',', '.', $value);
                    
                    // Ensure it's a valid numeric value
                    if (is_numeric($value)) {
                        $numericValue = (float)$value;
                        Log::info('SalesOfficerContact - Processed potential_revenue: ' . $numericValue);
                        return $numericValue;
                    } else {
                        Log::warning('SalesOfficerContact - Invalid numeric value after processing: ' . $value);
                        return null; // Return null for invalid values instead of 0
                    }
                } elseif (is_numeric($value)) {
                    $numericValue = (float)$value;
                    Log::info('SalesOfficerContact - Numeric potential_revenue: ' . $numericValue);
                    return $numericValue;
                }
                
                // For any other type, log and return null
                Log::warning('SalesOfficerContact - Unsupported value type: ' . gettype($value));
                return null;
            }
        );
    }
    
    /**
     * Get the user (sales officer) who owns this contact.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the activities associated with this contact.
     */
    public function activities()
    {
        return $this->hasMany(SalesOfficerActivity::class, 'contact_id');
    }
    
    /**
     * Get the original sales mission detail if this contact was imported.
     */
    public function salesMissionDetail()
    {
        return $this->belongsTo(SalesMissionDetail::class);
    }
    
    /**
     * Get the divisions associated with this company contact.
     */
    public function divisions()
    {
        return $this->hasMany(CompanyDivision::class, 'contact_id');
    }
    
    /**
     * Get the PICs (contact persons) associated with this company contact.
     */
    public function contactPeople()
    {
        return $this->hasMany(ContactPerson::class, 'contact_id');
    }
    
    /**
     * Get the primary PIC for this company.
     */
    public function primaryContactPerson()
    {
        return $this->contactPeople()->where('is_primary', true)->first();
    }
    
    /**
     * Increment the visit count for this company.
     */
    public function incrementVisitCount()
    {
        $this->increment('visit_count');
        return $this;
    }
    
    /**
     * Check if this contact was imported from Sales Mission.
     */
    public function isFromSalesMission()
    {
        return $this->sales_mission_detail_id !== null;
    }
    
    /**
     * Check if this contact is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
