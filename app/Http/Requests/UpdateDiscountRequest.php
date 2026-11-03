<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
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
            'discount_code' => ['unique:discounts,discount_code', 'sometimes', 'required', 'string', 'alpha_dash', 'max:50'],
            'percentage' => ['sometimes', 'required', 'numeric', 'decimal:0,2', 'min:1', 'max:50'],
            'expiry_date' => ['sometimes', 'required', 'date', 'after_or_equal:today'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('discount_code')) {
            $this->merge(['discount_code' => strtoupper(trim((string) $this->input('discount_code')))]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'discount_code.required' => 'Please enter a discount code.',
            'discount_code.string' => 'The discount code must be text.',
            'discount_code.alpha_dash' => 'The discount code may only contain letters, numbers, dashes and underscores.',
            'discount_code.max' => 'The discount code may not be longer than 50 characters.',
            'discount_code.unique' => 'This discount code already exists. Please choose another one.',

            'percentage.required' => 'Please enter the discount percentage.',
            'percentage.numeric' => 'The discount percentage must be a number.',
            'percentage.decimal' => 'The discount percentage may have at most 2 decimal places.',
            'percentage.min' => 'The discount must be at least 1%.',
            'percentage.max' => 'The discount may not be more than 50%.',

            'expiry_date.required' => 'Please choose an expiry date.',
            'expiry_date.date' => 'Please enter a valid expiry date.',
            'expiry_date.after_or_equal' => 'The expiry date must be today or a later date.',
        ];
    }
}
