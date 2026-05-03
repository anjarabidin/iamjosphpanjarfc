<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Simpan satu gambar WebP (opsional resize).
     *
     * @param UploadedFile $file
     * @param int|null $maxWidth null = tanpa resize; angka = resize max width
     * @param int $quality Kualitas WebP 0-100
     * @param string $disk Storage disk
     * @param string $dir Direktori relatif di dalam disk
     * @param string|null $basename Nama dasar file (null = UUID)
     * @return array ['path' => 'uploads/xxx.webp', 'url' => 'http://.../storage/uploads/xxx.webp']
     */
    public function storeSingleWebp(
        UploadedFile $file,
        ?int $maxWidth = null,
        int $quality = 80,
        string $disk = 'public',
        string $dir = 'uploads',
        ?string $basename = null
    ): array {
        $basename = $basename ?? Str::uuid()->toString();

        // Baca gambar
        $img = Image::read($file);

        // Resize jika diminta
        if (!empty($maxWidth)) {
            $img->resize($maxWidth, null, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });
        }

        // Encode ke WebP
        $bytes = $img->encodeByExtension('webp', quality: $quality);

        // Simpan
        $relativePath = rtrim($dir, '/') . "/{$basename}.webp";
        Storage::disk($disk)->put($relativePath, $bytes);

        return [
            'path' => $relativePath,
            'url'  => Storage::disk($disk)->url($relativePath),
        ];
    }

    /**
     * Simpan banyak ukuran sekaligus (label => maxWidth) sebagai WebP.
     * Contoh sizes: ['thumb' => 320, 'large' => 1600, 'original' => null]
     *
     * @return array ['label' => ['path' => ..., 'url' => ...], ...]
     */
    public function storeWebp(
        UploadedFile $file,
        array $sizes = ['original' => null],
        int $quality = 80,
        string $disk = 'public',
        string $dir = 'uploads',
        ?string $basename = null
    ): array {
        $basename = $basename ?? Str::uuid()->toString();
        $result = [];

        foreach ($sizes as $label => $maxWidth) {
            $img = Image::read($file);

            if (!empty($maxWidth)) {
                $img->resize($maxWidth, null, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
            }

            $bytes = $img->encodeByExtension('webp', quality: $quality);

            $relativePath = rtrim($dir, '/') . "/{$basename}_{$label}.webp";
            Storage::disk($disk)->put($relativePath, $bytes);

            $result[$label] = [
                'path' => $relativePath,
                'url'  => Storage::disk($disk)->url($relativePath),
            ];
        }

        return $result;
    }

    /**
     * Simple method to store image with compression
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @return string File path
     */
    public function storeImage(UploadedFile $file, string $directory = 'uploads', int $quality = 80): string
    {
        $filename = Str::uuid() . '.webp';
        $path = $directory . '/' . $filename;

        // Resize dan compress gambar
        $image = Image::read($file);

        // Resize jika lebih dari 1200px width
        if ($image->width() > 1200) {
            $image->scaleDown(width: 1200);
        }

        // Simpan sebagai WebP dengan kompresi
        $bytes = $image->toWebp($quality);
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }
}
