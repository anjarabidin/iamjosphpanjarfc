<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MeetingRoomRequest extends FormRequest
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
            'hotel' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'max_capacity' => 'nullable|integer|min:1|max:99999',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'hotel' => 'nama hotel/venue',
            'address' => 'alamat',
            'province_id' => 'provinsi',
            'regency_id' => 'kota/kabupaten',
            'email' => 'email',
            'phone' => 'telepon',
            'max_capacity' => 'kapasitas maksimum',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hotel.required' => 'Nama hotel/venue wajib diisi.',
            'hotel.string' => 'Nama hotel/venue harus berupa teks.',
            'hotel.max' => 'Nama hotel/venue tidak boleh lebih dari 255 karakter.',

            'address.required' => 'Alamat wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat tidak boleh lebih dari 1000 karakter.',

            'province_id.required' => 'Provinsi wajib dipilih.',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid.',

            'regency_id.required' => 'Kota/kabupaten wajib dipilih.',
            'regency_id.exists' => 'Kota/kabupaten yang dipilih tidak valid.',

            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',

            'phone.string' => 'Telepon harus berupa teks.',
            'phone.max' => 'Telepon tidak boleh lebih dari 50 karakter.',

            'max_capacity.integer' => 'Kapasitas maksimum harus berupa angka.',
            'max_capacity.min' => 'Kapasitas maksimum minimal 1 orang.',
            'max_capacity.max' => 'Kapasitas maksimum tidak boleh lebih dari 99999 orang.',
        ];
    }
}
