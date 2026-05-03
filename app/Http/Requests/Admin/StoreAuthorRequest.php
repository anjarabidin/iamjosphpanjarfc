<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|max:2048',
            'institution' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'avatar.image' => 'Avatar harus berupa file gambar',
            'avatar.max' => 'Ukuran maksimal avatar 2MB',
        ];
    }
}
