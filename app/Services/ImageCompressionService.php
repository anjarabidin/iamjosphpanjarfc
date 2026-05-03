<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageCompressionService
{
    /**
     * Compress and resize image using GD library
     */
    public function compressImage(string $tempPath, string $finalPath, array $options = []): bool
    {
        try {
            $maxWidth = $options['max_width'] ?? 1920;
            $maxHeight = $options['max_height'] ?? 1080;
            $quality = $options['quality'] ?? 80;

            $tempFullPath = Storage::disk('public')->path($tempPath);
            $finalFullPath = Storage::disk('public')->path($finalPath);

            // Create directory if not exists
            $directory = dirname($finalFullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Get image info
            $imageInfo = getimagesize($tempFullPath);
            if (!$imageInfo) {
                return false;
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];

            // Create image resource based on type
            $sourceImage = $this->createImageFromFile($tempFullPath, $mimeType);
            if (!$sourceImage) {
                return false;
            }

            // Calculate new dimensions
            $newDimensions = $this->calculateDimensions($originalWidth, $originalHeight, $maxWidth, $maxHeight);

            // Create new image
            $newImage = imagecreatetruecolor($newDimensions['width'], $newDimensions['height']);

            // Preserve transparency for PNG and GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newDimensions['width'], $newDimensions['height'], $transparent);
            }

            // Resize image
            imagecopyresampled(
                $newImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $newDimensions['width'],
                $newDimensions['height'],
                $originalWidth,
                $originalHeight
            );

            // Save compressed image
            $saved = $this->saveImage($newImage, $finalFullPath, $mimeType, $quality);

            // Clean up
            imagedestroy($sourceImage);
            imagedestroy($newImage);

            if ($saved) {
                // Delete temp file
                Storage::disk('public')->delete($tempPath);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Image compression failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create thumbnail
     */
    public function createThumbnail(string $originalPath, string $thumbnailPath, int $width = 300, int $height = 200): bool
    {
        try {
            $originalFullPath = Storage::disk('public')->path($originalPath);
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

            // Create directory if not exists
            $directory = dirname($thumbnailFullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Get image info
            $imageInfo = getimagesize($originalFullPath);
            if (!$imageInfo) {
                return false;
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];

            // Create source image
            $sourceImage = $this->createImageFromFile($originalFullPath, $mimeType);
            if (!$sourceImage) {
                return false;
            }

            // Create thumbnail
            $thumbnail = imagecreatetruecolor($width, $height);

            // Preserve transparency
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $width, $height, $transparent);
            }

            imagecopyresampled(
                $thumbnail,
                $sourceImage,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $originalWidth,
                $originalHeight
            );

            $saved = $this->saveImage($thumbnail, $thumbnailFullPath, $mimeType, 75);

            imagedestroy($sourceImage);
            imagedestroy($thumbnail);

            return $saved;
        } catch (\Exception $e) {
            Log::error('Thumbnail creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create image resource from file
     */
    private function createImageFromFile(string $filePath, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/webp':
                return imagecreatefromwebp($filePath);
            default:
                return false;
        }
    }

    /**
     * Calculate new dimensions maintaining aspect ratio
     */
    private function calculateDimensions(int $originalWidth, int $originalHeight, int $maxWidth, int $maxHeight): array
    {
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

        // If image is smaller than max dimensions, keep original size
        if ($ratio >= 1) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight
            ];
        }

        return [
            'width' => (int)($originalWidth * $ratio),
            'height' => (int)($originalHeight * $ratio)
        ];
    }

    /**
     * Save image based on type
     */
    private function saveImage($imageResource, string $filePath, string $mimeType, int $quality): bool
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagejpeg($imageResource, $filePath, $quality);
            case 'image/png':
                return imagepng($imageResource, $filePath, (int)(9 - ($quality / 10)));
            case 'image/gif':
                return imagegif($imageResource, $filePath);
            case 'image/webp':
                return imagewebp($imageResource, $filePath, $quality);
            default:
                return false;
        }
    }
}
