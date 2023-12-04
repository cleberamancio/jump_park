<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderPatchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehiclePlate' => 'size:7',
            'entryDateTime' => 'nullable|date',
            'exitDateTime' => 'nullable|date',
            'priceType' => 'nullable|string|max:55',
            'price' => 'numeric|min:0'
        ];
    }
}
