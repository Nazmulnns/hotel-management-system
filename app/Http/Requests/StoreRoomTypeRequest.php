<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomTypeRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:room_types,name',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Room type name is required.',
            'name.string' => 'Room type name must be a string.',
            'name.max' => 'Room type name may not be greater than 255 characters.',
            'name.unique' => 'This room type name already exists.',

            'description.string' => 'Description must be a string.',
            'description.max' => 'Description may not be greater than 1000 characters.',
        ];
    }
}
