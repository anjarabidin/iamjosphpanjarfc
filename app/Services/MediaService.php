<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    /**
     * Store uploaded media file
     */
    public function storeMedia(UploadedFile $file, string $directory = 'home-sliders'): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        $path = $directory . '/' . $filename;

        // Store original file temporarily
        $tempPath = $file->store('temp', 'public');

        return [
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $path,
            'temp_path' => $tempPath,
            'mime_type' => $mimeType,
            'size' => $size,
            'extension' => $extension,
            'type' => $this->getMediaType($mimeType)
        ];
    }

    /**
     * Delete media file
     */
    public function deleteMedia(string $path): bool
    {
        if ($path && file_exists($path)) {
            unlink($path);
        }


        return false;
    }

    /**
     * Get media type from mime type
     */
    private function getMediaType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'unknown';
    }

    /**
     * Get full URL for media
     */
    public function getMediaUrl(string $path): string
    {
        return asset('storage/' . $path);
    }

    /**
     * Check if media file exists
     */
    public function mediaExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }
}
