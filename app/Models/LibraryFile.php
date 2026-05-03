<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LibraryFile extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'journal_id',
        'name',
        'original_name',
        'file_path',
        'file_type',
        'category',
        'file_size',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Get human-readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Get file extension badge color
     */
    public function getTypeColorAttribute(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf' => 'red',
            'doc', 'docx' => 'blue',
            'xls', 'xlsx' => 'green',
            'ppt', 'pptx' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    // =====================================================
    // HELPERS
    // =====================================================

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->delete($this->file_path);
        }
        return false;
    }

    /**
     * Boot method to delete file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            $file->deleteFile();
        });
    }
}
