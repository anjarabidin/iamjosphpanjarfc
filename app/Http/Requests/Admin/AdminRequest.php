<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil ID admin saka route (bisa object model utawa angka)
        $routeAdmin = $this->route('admin');
        $adminId = is_object($routeAdmin) ? $routeAdmin->getKey() : $routeAdmin;

        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'email',
                // create: unique biasa; update: kecualikan ID saiki
                $isUpdate ? 'unique:admins,email,' . $adminId : 'unique:admins,email'
            ],
            // create: wajib; update: opsional
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'avatar'   => ['nullable', 'image', 'max:2048'],
        ];

        // role_id bisa single utawa array
        if (is_array($this->input('role_id'))) {
            $rules['role_id']   = ['required', 'array', 'min:1'];
            $rules['role_id.*'] = ['integer', 'exists:roles,id'];
        } else {
            $rules['role_id']   = ['required', 'exists:roles,id'];
        }

        return $rules;
    }
}
