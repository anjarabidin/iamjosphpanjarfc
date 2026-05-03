<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * File type constants
     */
    const TYPE_MANUSCRIPT = 'manuscript';
    const TYPE_REVISION = 'revision';
    const TYPE_SUPPLEMENTARY = 'supplementary';
    const TYPE_GALLEY = 'galley';

    /**
     * File stage constants (OJS 3.3 compliant)
     */
    const STAGE_SUBMISSION = 'submission';
    const STAGE_REVIEW = 'review';
    const STAGE_COPYEDIT_DRAFT = 'copyedit_draft';  // Draft files in copyediting
    const STAGE_COPYEDITED = 'copyedited';          // Final copyedited files
    const STAGE_PRODUCTION = 'production';
    const STAGE_PRODUCTION_READY = 'production_ready'; // Final galley files

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'submission_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'file_type',
        'mime_type',
        'file_size',
        'version',
        'stage',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'file_size' => 'integer',
        'version' => 'integer',
        'metadata' => 'array',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the submission this file belongs to
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * Get the user who uploaded this file
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to filter by file type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope to get latest version only
     */
    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version', 'desc');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Get human-readable file size
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    /**
     * Get file type label
     */
    public function getFileTypeLabelAttribute(): string
    {
        return match ($this->file_type) {
            self::TYPE_MANUSCRIPT => 'Manuscript',
            self::TYPE_REVISION => 'Revision',
            self::TYPE_SUPPLEMENTARY => 'Supplementary',
            self::TYPE_GALLEY => 'Galley/Final',
            default => ucfirst($this->file_type),
        };
    }
}
