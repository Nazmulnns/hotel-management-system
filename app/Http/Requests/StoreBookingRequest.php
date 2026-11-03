<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
            'guest_id' => ['required', 'integer', 'exists:guests,id'],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'rooms' => ['required', 'array', 'min:1', 'max:10'],
            'rooms.*.room_id' => ['required', 'integer', 'distinct', Rule::exists('rooms', 'id')->whereNull('deleted_at')],
            'rooms.*.guests' => ['required', 'integer', 'min:1', 'max:100'],
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
            'guest_id.required' => 'Please choose a guest.',
            'guest_id.exists' => 'The selected guest does not exist.',
            'check_in.required' => 'Please choose a check-in date.',
            'check_in.date_format' => 'The check-in date must be in YYYY-MM-DD format.',
            'check_in.after_or_equal' => 'The check-in date cannot be in the past.',
            'check_out.required' => 'Please choose a check-out date.',
            'check_out.date_format' => 'The check-out date must be in YYYY-MM-DD format.',
            'check_out.after' => 'The check-out date must be after the check-in date.',
            'rooms.required' => 'Please choose at least one room.',
            'rooms.min' => 'Please choose at least one room.',
            'rooms.max' => 'A booking may have at most 10 rooms.',
            'rooms.*.room_id.required' => 'Each room needs a room id.',
            'rooms.*.room_id.distinct' => 'The same room was selected more than once.',
            'rooms.*.room_id.exists' => 'One of the selected rooms does not exist.',
            'rooms.*.guests.required' => 'Please enter the number of guests for each room.',
            'rooms.*.guests.min' => 'Each room needs at least 1 guest.',
            'rooms.*.guests.max' => 'A room may have at most 10 guests.',
        ];
    }
}
