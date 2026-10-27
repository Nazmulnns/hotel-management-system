<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGuestRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'max:50'],
            'phone_number' => ['required', 'string', 'max:12'],
            'identity_number' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date'],
            'nationality' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:200'],
            'identity_front_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'identity_back_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'guest_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
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
            'full_name.required' => 'Please enter the guest\'s full name.',
            'full_name.max' => 'The full name may not be longer than 100 characters.',

            'email.email' => 'Please enter a valid email address.',

            'phone_number.required' => 'Please enter a phone number.',
            'phone_number.max' => 'The phone number may not be longer than 20 characters.',

            'identity_number.required' => 'Please enter the NID or passport number.',
            'identity_number.alpha_num' => 'The NID or passport number may only contain letters and numbers.',
            'identity_number.max' => 'The NID or passport number may not be longer than 20 characters.',

            'date_of_birth.required' => 'Please enter the date of birth.',
            'date_of_birth.date' => 'Please enter a valid date of birth.',
            'date_of_birth.before' => 'The date of birth must be a date in the past.',

            'nationality.required' => 'Please enter the guest\'s nationality.',

            'address.required' => 'Please enter the guest\'s address.',
            'address.max' => 'The address may not be longer than 200 characters.',

            'identity_front_image.required' => 'Please upload the front side of the NID or passport.',
            'identity_back_image.required' => 'Please upload the back side of the NID.',
            'guest_photo.required' => 'Please upload a photo of the guest.',

            'identity_front_image.image' => 'The front side must be an image.',
            'identity_back_image.image' => 'The back side must be an image.',
            'guest_photo.image' => 'The guest photo must be an image.',

            'identity_front_image.max' => 'The front side image must be 5 MB or smaller.',
            'identity_back_image.max' => 'The back side image must be 5 MB or smaller.',
            'guest_photo.max' => 'The guest photo must be 5 MB or smaller.',
        ];
    }
}
