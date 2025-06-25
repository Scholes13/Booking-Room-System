<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name'           => 'required|exists:employees,name',
            'department_id'  => 'required|exists:departments,id',
            'activity_type'  => 'required|string',
            'description'    => 'required|string',
            'province'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime'   => 'required|date_format:Y-m-d H:i|after:start_datetime',
        ];
        
        if ($this->input('activity_type') === 'Lainnya') {
            $rules['activity_type_other'] = 'required|string|max:100';
        }
        
        if ($this->input('activity_type') === 'Sales Mission') {
            $rules['company_name'] = 'required|string|max:255';
            $rules['company_pic'] = 'required|string|max:255';
            $rules['company_position'] = 'required|string|max:255';
            $rules['company_contact'] = 'required|string|max:255';
            $rules['company_email'] = 'required|email|max:255';
            $rules['company_address'] = 'required|string';
        }

        return $rules;
    }
}
