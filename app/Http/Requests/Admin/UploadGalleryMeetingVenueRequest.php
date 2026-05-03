<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadGalleryMeetingVenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi
     */
    public function rules(): array
    {
        return [
            // validasi jumlah file
            'gallery_images'   => 'required|array|min:1|max:10',

            // validasi per-file
            // catatan: max adalah dalam kilobytes (5120 KB = 5 MB)
            // tambahkan webp jika perlu
            'gallery_images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ];
    }

    /**
     * Pesan error kustom
     */
    public function messages(): array
    {
        return [
            'gallery_images.required'   => 'Pilih minimal 1 gambar untuk diupload.',
            'gallery_images.array'      => 'Format unggahan tidak valid.',
            'gallery_images.min'        => 'Pilih minimal 1 gambar untuk diupload.',
            'gallery_images.max'        => 'Maksimal 10 gambar sekaligus.',

            'gallery_images.*.required' => 'File gambar wajib diisi.',
            'gallery_images.*.image'    => 'File harus berupa gambar.',
            'gallery_images.*.mimes'    => 'Format gambar harus JPEG, JPG, PNG, GIF, atau WEBP.',
            'gallery_images.*.max'      => 'Ukuran gambar maksimal 5 MB per file.',
        ];
    }

    /**
     * (Opsional) Ganti label atribut agar pesan lebih manusiawi
     */
    public function attributes(): array
    {
        return [
            'gallery_images'   => 'daftar gambar',
            'gallery_images.*' => 'gambar',
        ];
    }
}
