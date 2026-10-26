<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    private ?Room $roomToUpdate = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function room(): Room
    {
        return $this->roomToUpdate ??= Room::findOrFail($this->route('id'));
    }
        /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $room = $this->route('room');

        return [
            'room_type_id' => ['sometimes', 'required', 'integer', 'exists:room_types,id'],
            'floor' => ['sometimes', 'required', 'integer', 'min:0', 'max:50'],
            'room_number' => [
                'sometimes',
                'required',
                'string',
                'regex:/^[A-Z]{1,2}$/',
                Rule::unique('rooms', 'room_number')
                    ->where(fn ($query) => $query->where('floor', $this->input('floor')))
                    ->ignore($this->room()),
            ],
            'price_per_night' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'max_occupancy' => ['sometimes', 'required', 'integer', 'min:1', 'max:10'],
            'facility_ids' => ['sometimes', 'array'],
            'facility_ids.*' => ['integer', 'distinct', 'exists:facilities,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('room_number')) {
            $this->merge(['room_number' => strtoupper(trim((string) $this->input('room_number')))]);
        }

        if ($this->hasAny(['floor', 'room_number'])) {
            $this->merge([
                'floor' => $this->input('floor', $this->room()->floor),
                'room_number' => $this->input('room_number', $this->room()->room_number),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'room_type_id.required' => 'Please choose a room type.',
            'room_number.unique' => 'This floor already has a room with that letter.',
            'room_number.regex' => 'The room number must be one or two letters, like A or AB.',
        ];
    }
}
