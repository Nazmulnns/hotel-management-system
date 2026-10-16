<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetRoomsRequest extends FormRequest
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
            'facility_id' => [
                'sometimes',
                'integer',
                'exists:facilities,id',
            ],
            'room_type_id' => [
                'sometimes',
                'integer',
                'exists:room_types,id',
            ],
            'min_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'max_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'sort_by' => [
                'sometimes',
                Rule::in([
                    'room_number',
                    'price_per_night',
                    'max_occupancy',
                ]),
            ],
            'sort_order' => [
                'sometimes',
                Rule::in(['asc', 'desc']),
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'check_in' => [
                'sometimes',
                'required_with:check_out',
                'date',
            ],
            'check_out' => [
                'sometimes',
                'required_with:check_in',
                'date',
                'after:check_in',
            ],
        ];

    }
}
