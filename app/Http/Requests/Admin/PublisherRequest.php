<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PublisherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routePublisher = $this->route('publisher');
        $publisherId = is_object($routePublisher) ? $routePublisher->getKey() : $routePublisher;
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            // Admin data
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => [
                'required',
                'email',
                $isUpdate ? 'unique:admins,email,' . $publisherId . ',id' : 'unique:admins,email'
            ],
            'password'              => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'avatar'                => ['nullable', 'image', 'max:2048'],

            // Publisher data
            'code'                  => [
                'required',
                'string',
                'max:50',
                $isUpdate ? 'unique:publishers,code,' . $publisherId . ',admin_id' : 'unique:publishers,code'
            ],
            'alias'                 => ['required', 'string', 'max:255'],
            'type'                  => ['required', 'in:Institusi,Asosiasi,Yayasan,CV,PT'],
            'sk_kemenkumham_link'   => ['nullable', 'url', 'max:500'],
            'akta_notaris_link'     => ['nullable', 'url', 'max:500'],
            'address'               => ['required', 'string', 'max:500'],
            'city'                  => ['required', 'string', 'max:100'],
            'website'               => ['nullable', 'url', 'max:255'],
            'contact_name'          => ['required', 'string', 'max:255'],
            'phone'                 => ['required', 'string', 'max:20', 'regex:/^[\d+\-\s\(\)]+$/'],
            'prefix_doi'            => ['required', 'string', 'max:100'],
            'additional_prefixes'   => ['nullable', 'array'],
            'additional_prefixes.*' => ['string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                => 'Nama harus diisi',
            'email.required'               => 'Email harus diisi',
            'email.email'                  => 'Format email tidak valid',
            'email.unique'                 => 'Email sudah terdaftar',
            'password.required'            => 'Password harus diisi',
            'password.min'                 => 'Password minimal 8 karakter',
            'password.confirmed'           => 'Konfirmasi password tidak sesuai',
            'code.required'                => 'Kode publisher harus diisi',
            'code.unique'                  => 'Kode publisher sudah digunakan',
            'alias.required'               => 'Alias harus diisi',
            'type.required'                => 'Tipe publisher harus dipilih',
            'type.in'                      => 'Tipe publisher tidak valid',
            'address.required'             => 'Alamat harus diisi',
            'city.required'                => 'Kota harus diisi',
            'contact_name.required'        => 'Nama kontak harus diisi',
            'phone.required'               => 'Telepon/WhatsApp harus diisi',
            'phone.regex'                  => 'Format telepon/WhatsApp tidak valid',
            'prefix_doi.required'          => 'Prefix DOI harus diisi',
            'sk_kemenkumham_link.url'      => 'Link SK Kemenkumham harus berupa URL yang valid',
            'akta_notaris_link.url'        => 'Link AKTA Notaris harus berupa URL yang valid',
            'website.url'                  => 'Website harus berupa URL yang valid',
        ];
    }
}
