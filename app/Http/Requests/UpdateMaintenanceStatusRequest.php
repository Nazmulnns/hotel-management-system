<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaintenanceStatusRequest extends FormRequest
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
            'issue_description' => ['sometimes', 'required', 'string', 'min:10', 'max:500'],
            'maintenance_status' => ['sometimes', 'required', 'string', Rule::in(MaintenanceStatus::cases())],
        ];
    }
}
