<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publication extends Model
{
    use HasFactory, HasUuids, SoftDeletes;


    /**
     * Publication status constants
     */
    const STATUS_QUEUED = 1;
    const STATUS_SCHEDULED = 2;
    const STATUS_PUBLISHED = 3;
    const STATUS_UNPUBLISHED = 4;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'submission_id',
        'section_id',
        'issue_id',
        'version',
        'status',
        'title',
        'subtitle',
        'abstract',
        'keywords',
        'references',
        'pages',
        'url_path',
        'doi',
        'doi_suffix',
        'copyright_holder',
        'copyright_year',
        'license_url',
        'date_published',
        'metadata',
        'cover_image_path',
        'url_path',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'version' => 'integer',
        'status' => 'integer',
        'copyright_year' => 'integer',
        'date_published' => 'date',
        'metadata' => 'array',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the submission this publication belongs to
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * Get the section for this publication
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    /**
     * Get the issue this publication is assigned to
     */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }

    /**
     * Get authors for this publication
     */
    public function authors(): HasMany
    {
        return $this->hasMany(SubmissionAuthor::class, 'publication_id')->orderBy('sort_order');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope for published publications
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope for current version
     */
    public function scopeCurrentVersion($query)
    {
        return $query->orderBy('version', 'desc')->limit(1);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_QUEUED => 'Unscheduled',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_UNPUBLISHED => 'Unpublished',
            default => 'Unknown',
        };
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_QUEUED => 'gray',
            self::STATUS_SCHEDULED => 'blue',
            self::STATUS_PUBLISHED => 'green',
            self::STATUS_UNPUBLISHED => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get keywords as array
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (empty($this->keywords)) {
            return [];
        }

        return array_map('trim', explode(',', $this->keywords));
    }

    /**
     * Check if publication is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if publication is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Get full title with subtitle
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->subtitle) {
            return "{$this->title}: {$this->subtitle}";
        }
        return $this->title;
    }

    // =====================================================
    // HELPERS
    // =====================================================

    /**
     * Create a new version of this publication
     */
    public function createNewVersion(): self
    {
        $newVersion = $this->replicate();
        $newVersion->version = $this->version + 1;
        $newVersion->status = self::STATUS_QUEUED;
        $newVersion->date_published = null;
        $newVersion->save();

        // Copy authors
        foreach ($this->authors as $author) {
            $newAuthor = $author->replicate();
            $newAuthor->publication_id = $newVersion->id;
            $newAuthor->save();
        }

        return $newVersion;
    }
}
