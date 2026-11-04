<?php

namespace App\Http\Requests;

use App\Enums\SupportRequestStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupportRequestRequest extends FormRequest
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
            'room_id' => ['sometimes', 'nullable', 'integer', Rule::exists('rooms', 'id')->whereNull('deleted_at')],
            'issue' => ['sometimes', 'required', 'string', 'min:10', 'max:255'],
            'status' => ['sometimes', 'required', Rule::enum(SupportRequestStatus::class)],
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
            'issue.min' => 'Please describe the issue in at least 10 characters.',
            'issue.max' => 'The issue may not be longer than 255 characters.',
            'status.enum' => 'The status must be pending, in_progress or resolved.',
        ];
    }
}
