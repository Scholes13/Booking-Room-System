<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SalesOfficerActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authentication/authorization is handled by middleware
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Handle potential_revenue formatting
        if ($this->has('potential_revenue')) {
            $potentialRevenue = $this->potential_revenue;
            // Log the raw input value for debugging
            Log::info('Raw potential_revenue input: ' . $potentialRevenue);
            
            if (is_string($potentialRevenue)) {
                // Remove currency symbol, dots as thousand separators, and spaces
                $potentialRevenue = str_replace(['Rp', '.', ' '], '', $potentialRevenue);
                // Replace comma with period if used as decimal separator
                $potentialRevenue = str_replace(',', '.', $potentialRevenue);
                
                // Ensure it's properly converted to numeric format
                $potentialRevenue = (float)$potentialRevenue;
                
                // Log the processed value for debugging
                Log::info('Processed potential_revenue: ' . $potentialRevenue);
                
                $this->merge([
                    'potential_revenue' => $potentialRevenue
                ]);
            }
        }
        
        // Ensure start_datetime and end_datetime are the same date
        // This avoids the "after" validation issue when using only dates
        if ($this->has('start_datetime') && $this->has('end_datetime')) {
            $startDate = $this->start_datetime;
            $endDate = $this->end_datetime;
            
            // Log dates for debugging
            Log::info('Date values in form', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            // If end date is empty or the same as start date, this is fine
            // We're only using the date part anyway
            if (empty($endDate) || $endDate === '') {
                $this->merge([
                    'end_datetime' => $startDate
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_selector' => 'sometimes|required',
            'division_selector' => 'nullable',
            'pic_selector' => 'sometimes|required',
            'company_name' => 'required_if:company_selector,new',
            'line_of_business' => 'sometimes|required|string|max:255',
            'division_name' => 'required_if:division_selector,new',
            'pic_name' => 'required_if:pic_selector,new',
            'pic_phone' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'position' => 'required|string|max:255',
            'company_address' => 'required|string',
            'country' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'activity_type' => 'required|string',
            'meeting_type' => 'required|string',
            'general_information' => 'nullable|string',
            'current_event' => 'nullable|string',
            'target_business' => 'nullable|string',
            'project_type' => 'nullable|string',
            'project_estimation' => 'nullable|string',
            'potential_revenue' => 'nullable|numeric',
            'potential_project_count' => 'nullable|integer',
            'next_follow_up' => 'nullable|string',
            'status' => 'required|string',
            'follow_up_type' => 'required|string',
            'follow_up_frequency' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date', // Removed after:start_datetime validation
            'month_number' => 'required|integer|min:1|max:12',
            'week_number' => 'required|integer|min:1|max:5',
            'account_status' => 'required|in:New,Contracted,Existing',
            'products_discussed' => 'nullable|integer|min:1|max:50',
            'jso_lead_status' => 'required|string',
        ];
    }
} 