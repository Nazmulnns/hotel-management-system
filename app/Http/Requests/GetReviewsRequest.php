<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetReviewsRequest extends FormRequest
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
            'room_id' => ['sometimes', 'integer', 'exists:rooms,id'],
            'guest_id' => ['sometimes', 'integer', 'exists:guests,id'],
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'sort_by' => ['sometimes', Rule::in(['created_at', 'rating'])],
            'sort_order' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
