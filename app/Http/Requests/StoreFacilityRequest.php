<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facility_name' => 'required|string|max:255|unique:facilities,facility_name',
        ];
    }

    public function messages(): array
    {
        return [
            'facility_name.required' => 'Facility name is required.',
            'facility_name.string' => 'Facility name must be text.',
            'facility_name.max' => 'Facility name cannot exceed 255 characters.',
            'facility_name.unique' => 'This facility name already exists.',
        ];
    }
}
