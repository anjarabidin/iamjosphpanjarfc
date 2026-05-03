<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BookingInaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'id' opsional (dipakai saat update)
            'id'               => ['nullable', 'uuid'],

            'title'            => ['required', 'string', 'max:120'],
            'title_en'         => ['nullable', 'string', 'max:120'],

            'subtitle'         => ['nullable', 'string', 'max:1000'],
            'subtitle_en'      => ['nullable', 'string', 'max:1000'],

            'button_text'      => ['nullable', 'string', 'max:100'],
            'button_text_en'   => ['nullable', 'string', 'max:100'],

            'url'              => ['nullable', 'url', 'max:255'],

            'image'            => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalisasi teks: hilangkan NBSP, trim, dan kompres spasi berlebih
        $normalize = function ($v) {
            if ($v === null) return null;
            // Ubah NBSP (\xC2\xA0) ke spasi biasa lalu rapikan spasi
            $v = str_replace("\xC2\xA0", ' ', $v);
            $v = preg_replace('/\s+/u', ' ', trim($v));
            return $v;
        };

        $this->merge([
            'title'          => $normalize($this->input('title')),
            'title_en'       => $normalize($this->input('title_en')),
            'subtitle'       => $normalize($this->input('subtitle')),
            'subtitle_en'    => $normalize($this->input('subtitle_en')),
            'button_text'    => $normalize($this->input('button_text')),
            'button_text_en' => $normalize($this->input('button_text_en')),
            'url'            => $this->filled('url') ? trim($this->input('url')) : null,
            'order'          => $this->filled('order') ? (int) $this->input('order') : null,
        ]);
    }

    public function attributes(): array
    {
        return [
            'title'            => 'judul (ID)',
            'title_en'         => 'judul (EN)',
            'subtitle'         => 'subjudul (ID)',
            'subtitle_en'      => 'subjudul (EN)',
            'button_text'      => 'teks tombol (ID)',
            'button_text_en'   => 'teks tombol (EN)',
            'order'            => 'urutan',
            'url'              => 'tautan',
            'image'            => 'gambar',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Judul wajib diisi.',
            'order.required'   => 'Urutan wajib diisi.',
            'order.integer'    => 'Urutan harus berupa angka.',
            'order.min'        => 'Urutan minimal :min.',
            'url.url'          => 'Format tautan tidak valid.',
            'image.image'      => 'File gambar tidak valid.',
            'image.mimes'      => 'Gambar harus bertipe: jpeg, jpg, png, atau webp.',
            'image.max'        => 'Ukuran gambar maksimum 4MB.',
        ];
    }
}
