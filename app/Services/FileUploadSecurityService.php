<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileUploadSecurityService
{
    /**
     * Dangerous extensions that indicate executable/server-side code.
     */
    protected const DANGEROUS_EXTENSIONS = [
        'php', 'php2', 'php3', 'php4', 'php5', 'php6', 'php7', 'phtml', 'phar', 'pht', 'phpt', 'pgif',
        'shtml', 'htaccess', 'htpasswd', 'asp', 'aspx', 'cer', 'asa', 'jsp', 'jspx',
        'exe', 'dll', 'bat', 'cmd', 'sh', 'bash', 'ps1', 'psm1', 'pl', 'cgi', 'py', 'rb',
    ];

    /**
     * Allowed extensions per upload context.
     */
    protected const ALLOWED_EXTENSIONS = [
        'manuscript'    => ['pdf', 'doc', 'docx', 'odt', 'rtf'],
        'revision'      => ['pdf', 'doc', 'docx', 'odt', 'rtf'],
        'supplementary' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png', 'mp4', 'mp3'],
        'galley'        => ['pdf', 'html', 'htm', 'epub', 'xml'],
        'review'        => ['pdf', 'doc', 'docx', 'odt'],
        'decision'      => ['pdf', 'doc', 'docx', 'odt', 'rtf'],
        'image'         => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'cover'         => ['jpg', 'jpeg', 'png', 'webp'],
        'library'       => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'discussion'    => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'zip'],
        'avatar'        => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'ckeditor'      => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ];

    /**
     * Legitimate MIME types per extension (magic bytes, not browser-supplied).
     */
    protected const MIME_MAP = [
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'odt'  => ['application/vnd.oasis.opendocument.text', 'application/zip'],
        'xls'  => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        'ppt'  => ['application/vnd.ms-powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
        'rtf'  => ['application/rtf', 'text/rtf'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'zip'  => ['application/zip', 'application/x-zip-compressed'],
        'epub' => ['application/epub+zip', 'application/zip'],
        'mp4'  => ['video/mp4'],
        'mp3'  => ['audio/mpeg'],
        'html' => ['text/html', 'text/plain'],
        'htm'  => ['text/html', 'text/plain'],
        'xml'  => ['application/xml', 'text/xml', 'text/plain'],
    ];

    /**
     * Max file size in bytes per context.
     */
    protected const MAX_SIZE = [
        'manuscript'    => 52428800,   // 50MB
        'revision'      => 52428800,
        'supplementary' => 104857600,  // 100MB
        'galley'        => 52428800,
        'review'        => 20971520,   // 20MB
        'decision'      => 20971520,
        'image'         => 5242880,    // 5MB
        'cover'         => 5242880,
        'library'       => 20971520,
        'discussion'    => 20971520,
        'avatar'        => 2097152,    // 2MB
        'ckeditor'      => 5242880,
    ];

    /**
     * PHP code signatures to scan for in text-based files.
     */
    protected const PHP_SIGNATURES = [
        '<?php', '<?=', '<?', '<script language="php">', '<%',
    ];

    /**
     * Extensions whose first 8KB should be scanned for PHP code.
     */
    protected const SCANNABLE_EXTENSIONS = ['doc', 'docx', 'rtf', 'html', 'htm', 'xml', 'odt'];

    /**
     * Validate an uploaded file against all security checks.
     *
     * @throws ValidationException
     */
    public function validate(UploadedFile $file, string $context, ?Request $request = null): void
    {
        $filename = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());

        // CHECK 1 — Double Extension Attack
        $this->checkDoubleExtension($file, $filename, $request);

        // CHECK 2 — Null Byte & Path Traversal
        $this->checkNullByteAndTraversal($file, $filename, $request);

        // CHECK 3 — Whitelist Extension per Context
        $this->checkExtensionWhitelist($file, $extension, $context, $request);

        // CHECK 4 — Real MIME Type via Magic Bytes
        $this->checkMimeType($file, $extension, $request);

        // CHECK 5 — PHP Code Detection in Text Files
        $this->checkPhpCodeInjection($file, $extension, $request);

        // CHECK 6 — File Size per Context
        $this->checkFileSize($file, $context, $request);
    }

    /**
     * CHECK 1: Detect double extension attacks (e.g., shell.php.pdf).
     */
    protected function checkDoubleExtension(UploadedFile $file, string $filename, ?Request $request): void
    {
        $segments = explode('.', strtolower($filename));
        // Remove the last segment (legitimate extension) and check the rest
        foreach ($segments as $segment) {
            if (in_array($segment, self::DANGEROUS_EXTENSIONS)) {
                $this->logRejection($file, "Double extension attack detected: dangerous segment '{$segment}' in filename '{$filename}'", $request);
                throw ValidationException::withMessages([
                    'file' => 'The uploaded file contains a dangerous extension and has been rejected.',
                ]);
            }
        }
    }

    /**
     * CHECK 2: Detect null byte injection and path traversal.
     */
    protected function checkNullByteAndTraversal(UploadedFile $file, string $filename, ?Request $request): void
    {
        $dangerousPatterns = ["\0", '%00', '..', '/', '\\'];

        foreach ($dangerousPatterns as $pattern) {
            if (str_contains($filename, $pattern)) {
                $this->logRejection($file, "Null byte or path traversal detected: '{$pattern}' in filename '{$filename}'", $request);
                throw ValidationException::withMessages([
                    'file' => 'The uploaded filename contains invalid characters.',
                ]);
            }
        }
    }

    /**
     * CHECK 3: Verify extension is whitelisted for the given context.
     */
    protected function checkExtensionWhitelist(UploadedFile $file, string $extension, string $context, ?Request $request): void
    {
        $allowed = self::ALLOWED_EXTENSIONS[$context] ?? [];

        if (empty($allowed)) {
            $this->logRejection($file, "Unknown upload context: '{$context}'", $request);
            throw ValidationException::withMessages([
                'file' => 'Invalid upload context.',
            ]);
        }

        if (!in_array($extension, $allowed)) {
            $this->logRejection($file, "Extension '{$extension}' not allowed for context '{$context}'. Allowed: " . implode(', ', $allowed), $request);
            throw ValidationException::withMessages([
                'file' => "File type '.{$extension}' is not allowed. Accepted types: " . implode(', ', $allowed),
            ]);
        }
    }

    /**
     * CHECK 4: Verify real MIME type via magic bytes matches declared extension.
     */
    protected function checkMimeType(UploadedFile $file, string $extension, ?Request $request): void
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        $allowedMimes = self::MIME_MAP[$extension] ?? [];

        if (!empty($allowedMimes) && !in_array($realMime, $allowedMimes)) {
            $this->logRejection($file, "MIME spoofing detected: real MIME '{$realMime}' does not match extension '{$extension}'. Expected: " . implode(', ', $allowedMimes), $request);
            throw ValidationException::withMessages([
                'file' => 'The file content does not match its extension. Possible MIME type spoofing detected.',
            ]);
        }
    }

    /**
     * CHECK 5: Scan text-based files for injected PHP code.
     */
    protected function checkPhpCodeInjection(UploadedFile $file, string $extension, ?Request $request): void
    {
        if (!in_array($extension, self::SCANNABLE_EXTENSIONS)) {
            return;
        }

        $content = file_get_contents($file->getRealPath(), false, null, 0, 8192); // First 8KB
        $contentLower = strtolower($content);

        foreach (self::PHP_SIGNATURES as $signature) {
            if (str_contains($contentLower, strtolower($signature))) {
                $this->logRejection($file, "PHP code signature detected in file: '{$signature}'", $request);
                throw ValidationException::withMessages([
                    'file' => 'The file contains potentially dangerous code and has been rejected.',
                ]);
            }
        }
    }

    /**
     * CHECK 6: Enforce file size limit per context.
     */
    protected function checkFileSize(UploadedFile $file, string $context, ?Request $request): void
    {
        $maxSize = self::MAX_SIZE[$context] ?? 20971520; // Default 20MB

        if ($file->getSize() > $maxSize) {
            $maxMB = round($maxSize / 1048576);
            $this->logRejection($file, "File size {$file->getSize()} exceeds max {$maxSize} bytes for context '{$context}'", $request);
            throw ValidationException::withMessages([
                'file' => "The file exceeds the maximum allowed size of {$maxMB}MB.",
            ]);
        }
    }

    /**
     * Generate a sanitized, safe filename.
     */
    public function sanitizeFilename(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        // Strip dangerous characters, keep only alphanumeric, dash, underscore
        $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $basename);
        $cleanName = strtolower(trim($cleanName, '_'));

        // Truncate to reasonable length
        $cleanName = Str::limit($cleanName, 100, '');

        // Prefix with UUID for uniqueness
        $uuid = Str::random(8);

        return "{$uuid}_{$cleanName}.{$extension}";
    }

    /**
     * Log a file rejection to both Laravel log and security_logs table.
     */
    public function logRejection(UploadedFile $file, string $reason, ?Request $request = null): void
    {
        $details = [
            'filename' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'reason' => $reason,
        ];

        Log::channel('single')->warning('[SECURITY] File upload rejected', $details);

        try {
            DB::table('security_logs')->insert([
                'user_id' => auth()->id(),
                'ip_address' => $request?->ip() ?? request()->ip(),
                'action' => 'file_upload_rejected',
                'details' => json_encode($details),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Don't fail the request if logging fails (table might not exist yet)
            Log::error('[SECURITY] Failed to log rejection to database: ' . $e->getMessage());
        }
    }
}
