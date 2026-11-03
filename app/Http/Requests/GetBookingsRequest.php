<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetBookingsRequest extends FormRequest
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
            'search' => ['sometimes', 'string', 'max:50'],
            'status' => ['sometimes', 'array'],
            'status.*' => ['required', Rule::enum(BookingStatus::class)],
            'guest_id' => ['sometimes', 'integer', 'exists:guests,id'],
            'room_id' => ['sometimes', 'integer', 'exists:rooms,id'],
            'check_in_from' => ['sometimes', 'date_format:Y-m-d'],
            'check_in_to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:check_in_from'],
            'staying_on' => ['sometimes', 'date_format:Y-m-d'],
            'sort_by' => ['sometimes', Rule::in(['check_in', 'check_out', 'booking_date', 'total_amount', 'created_at'])],
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
            'guest_id.exists' => 'The selected guest does not exist.',
            'room_id.exists' => 'The selected room does not exist.',
            'check_in_to.after_or_equal' => 'The end date must be the same as or after the start date.',
        ];
    }
}
