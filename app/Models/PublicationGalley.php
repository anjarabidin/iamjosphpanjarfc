<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationGalley extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'submission_id',
        'file_id',
        'label',
        'locale',
        'url_path',
        'url_remote',
        'is_remote',
        'seq',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    // =====================================================
    // ROUTE MODEL BINDING
    // =====================================================

    /**
     * Get the route key name for Laravel's route model binding.
     * Allows finding galley by url_path or id
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // First try to find by url_path, then by id
        return $this->where('url_path', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the submission this galley belongs to
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * Get the file associated with this galley
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(SubmissionFile::class, 'file_id');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Get the download URL for this galley
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->is_remote && $this->url_remote) {
            return $this->url_remote;
        }

        if ($this->file) {
            return route('files.download', $this->file);
        }

        return null;
    }

    /**
     * Get the public URL for this galley (for SEO)
     */
    public function getPublicUrlAttribute(): string
    {
        $identifier = $this->url_path ?? $this->id;
        return route('journal.article.galley', [
            'journal' => $this->submission->journal->slug,
            'submission' => $this->submission->slug,
            'galley' => $identifier,
        ]);
    }

    /**
     * Get formatted label with icon
     */
    public function getLabelIconAttribute(): string
    {
        return match (strtolower($this->label)) {
            'pdf' => 'fa-file-pdf',
            'html' => 'fa-file-code',
            'epub' => 'fa-book',
            'xml' => 'fa-file-code',
            default => 'fa-file',
        };
    }

    /**
     * Get label color class
     */
    public function getLabelColorAttribute(): string
    {
        return match (strtolower($this->label)) {
            'pdf' => 'bg-red-100 text-red-700',
            'html' => 'bg-orange-100 text-orange-700',
            'epub' => 'bg-purple-100 text-purple-700',
            'xml' => 'bg-blue-100 text-blue-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get locale display name
     */
    public function getLocaleNameAttribute(): string
    {
        $locales = [
            'en' => 'English',
            'id' => 'Indonesian',
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
        ];

        return $locales[$this->locale] ?? ucfirst($this->locale ?? 'en');
    }

    /**
     * Get type display name (Local or Remote)
     */
    public function getTypeNameAttribute(): string
    {
        return $this->is_remote ? 'Remote' : 'Local';
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Order by sequence
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('seq')->orderBy('created_at');
    }

    /**
     * Filter by locale
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}
