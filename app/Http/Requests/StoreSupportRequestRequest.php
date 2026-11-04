<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportRequestRequest extends FormRequest
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
            'issue' => ['required', 'string', 'min:10', 'max:255'],
            'room_id' => ['nullable', 'integer', Rule::exists('rooms', 'id')->whereNull('deleted_at')],
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
            'issue.required' => 'Please describe the issue.',
            'issue.min' => 'Please describe the issue in at least 10 characters.',
            'issue.max' => 'The issue may not be longer than 255 characters.',
        ];
    }
}
