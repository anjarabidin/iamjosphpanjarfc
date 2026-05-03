<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HotelBookingRequest extends FormRequest
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
        $rules = [
            'name'      => 'required|string|max:255',
            'price'     => 'required|numeric',
            'rating'    => 'required|string',
            'url'       => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ];

        if ($this->isMethod('post')) {
            // untuk create
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240';
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // untuk update
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240';
        }

        return $rules;
    }
}
