<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
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
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'floor' => ['required', 'integer', 'min:0', 'max:50'],
            'room_number' => [
                'required',
                'string',
                'regex:/^[A-Z]{1,2}$/',
                Rule::unique('rooms', 'room_number')->where(fn ($query) => $query->where('floor', $this->input('floor'))),
            ],
            'price_per_night' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'max_occupancy' => ['required', 'integer', 'min:1', 'max:10'],
            'facility_ids' => ['sometimes', 'array'],
            'facility_ids.*' => ['integer', 'distinct', 'exists:facilities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_type_id.required' => 'Please choose a room type.',
            'room_number.unique' => 'This floor already has a room with that letter.',
            'room_number.regex' => 'The room number must be one or two letters, like A or AB.',
        ];
    }
}
