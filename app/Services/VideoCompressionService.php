<?php

namespace App\Services;

use FFMpeg\Coordinate\Dimension;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoCompressionService
{
    /**
     * Compress video using FFmpeg
     */
    public function compressVideo(string $tempPath, string $finalPath, array $options = []): bool
    {
        try {
            $maxWidth = $options['max_width'] ?? 1280;
            $maxHeight = $options['max_height'] ?? 720;
            $crf = $options['crf'] ?? 28; // Constant Rate Factor (lower = better quality)
            $preset = $options['preset'] ?? 'medium'; // ultrafast, superfast, veryfast, faster, fast, medium, slow, slower, veryslow

            $tempFullPath = Storage::disk('public')->path($tempPath);
            $finalFullPath = Storage::disk('public')->path($finalPath);

            // Create directory if not exists
            $directory = dirname($finalFullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // FFmpeg command
            $command = [
                'ffmpeg',
                '-i',
                $tempFullPath,
                '-vf',
                "scale='min({$maxWidth},iw)':'min({$maxHeight},ih)':force_original_aspect_ratio=decrease",
                '-c:v',
                'libx264',
                '-crf',
                (string)$crf,
                '-preset',
                $preset,
                '-c:a',
                'aac',
                '-b:a',
                '128k',
                '-movflags',
                '+faststart',
                '-y', // Overwrite output file
                $finalFullPath
            ];

            // Execute FFmpeg process
            $process = new Process($command);
            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            if ($process->isSuccessful()) {
                // Delete temp file
                Storage::disk('public')->delete($tempPath);
                return true;
            } else {
                Log::error('Video compression failed: ' . $process->getErrorOutput());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Video compression failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create video thumbnail
     */
    public function createVideoThumbnail(string $videoPath, string $thumbnailPath, int $timeInSeconds = 1): bool
    {
        try {
            // Dapatkan path absolut dari thumbnail untuk disimpan
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

            // Buat direktori jika belum ada
            $directory = dirname($thumbnailFullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Buka video dari disk 'public' dan buat thumbnail
            FFMpeg::fromDisk('public')
                ->open($videoPath)
                ->getFrameFromSeconds($timeInSeconds)
                ->addFilter(function ($filters) {
                    // Sesuaikan ukuran thumbnail sesuai kebutuhan
                    $filters->resize(new Dimension(320, 240));
                })
                ->save($thumbnailFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Gagal membuat thumbnail video: ' . $e->getMessage());
            return false;
        }
    }
    // public function createVideoThumbnail(string $videoPath, string $thumbnailPath, int $timeInSeconds = 1): bool
    // {
    //     try {
    //         $videoFullPath = Storage::disk('public')->path($videoPath);
    //         $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

    //         // Create directory if not exists
    //         $directory = dirname($thumbnailFullPath);
    //         if (!file_exists($directory)) {
    //             mkdir($directory, 0755, true);
    //         }

    //         $command = [
    //             'ffmpeg',
    //             '-i',
    //             $videoFullPath,
    //             '-ss',
    //             (string)$timeInSeconds,
    //             '-vframes',
    //             '1',
    //             '-vf',
    //             'scale=300:200',
    //             '-y',
    //             $thumbnailFullPath
    //         ];

    //         $process = new Process($command);
    //         $process->setTimeout(60); // 1 minute timeout
    //         $process->run();

    //         return $process->isSuccessful();
    //     } catch (\Exception $e) {
    //         Log::error('Video thumbnail creation failed: ' . $e->getMessage());
    //         return false;
    //     }
    // }

    /**
     * Get video info using FFprobe
     */
    public function getVideoInfo(string $videoPath): ?array
    {
        try {
            $videoFullPath = Storage::disk('public')->path($videoPath);

            $command = [
                'ffprobe',
                '-v',
                'quiet',
                '-print_format',
                'json',
                '-show_format',
                '-show_streams',
                $videoFullPath
            ];

            $process = new Process($command);
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                return json_decode($output, true);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Getting video info failed: ' . $e->getMessage());
            return null;
        }
    }
}
