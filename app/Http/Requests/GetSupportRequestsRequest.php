<?php

namespace App\Http\Requests;

use App\Enums\SupportRequestStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetSupportRequestsRequest extends FormRequest
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
            'search' => ['sometimes', 'string', 'max:100'],
            'status' => ['sometimes', 'array'],
            'status.*' => ['required', Rule::enum(SupportRequestStatus::class)],
            'guest_id' => ['sometimes', 'integer', 'exists:guests,id'],
            'room_id' => ['sometimes', 'integer', 'exists:rooms,id'],
            'from' => ['sometimes', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:from'],
            'sort_by' => ['sometimes', Rule::in(['created_at', 'updated_at', 'status'])],
            'sort_order' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.*.enum' => 'Each status must be pending, in_progress or resolved.',
            'guest_id.exists' => 'The selected guest does not exist.',
            'to.after_or_equal' => 'The end date must be the same as or after the start date.',
        ];
    }
}
