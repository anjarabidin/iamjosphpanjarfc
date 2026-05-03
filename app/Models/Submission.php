<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\ArticleMetric;
use Illuminate\Http\Exceptions\HttpResponseException;

class Submission extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Submission status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_REVISION_REQUIRED = 'revision_required';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_QUEUED_FOR_COPYEDITING = 'queued_for_copyediting';
    const STATUS_IN_PRODUCTION = 'in_production';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PUBLISHED = 'published';

    /**
     * Submission stage constants
     */
    const STAGE_SUBMISSION = 'submission';
    const STAGE_REVIEW = 'review';
    const STAGE_REVISION = 'revision';
    const STAGE_COPYEDITING = 'copyediting';
    const STAGE_PRODUCTION = 'production';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'journal_id',
        'user_id',
        'section_id',
        'issue_id',
        'title',
        'slug',
        'submission_code',
        'subtitle',
        'abstract',
        'submission_file_path',
        'status',
        'stage',
        'stage_id',
        'submitted_at',
        'accepted_at',
        'published_at',
        'metadata',
        'references',
        'seq_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'published_at' => 'datetime',
        'metadata' => 'array', // JSONB to array
    ];

    // =====================================================
    // ROUTE MODEL BINDING
    // =====================================================

    /**
     * Get the route key name for Laravel's route model binding.
     * This makes URLs use /submissions/{seq_id} instead of /submissions/{slug}
     */
    public function getRouteKeyName(): string
    {
        return 'seq_id';
    }

    /**
     * Retrieve the model for a bound value.
     * Handles backward compatibility for slugs and UUIDs via 301 Redirect.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (!is_numeric($value)) {
            $submission = $this->where('id', $value)->orWhere('slug', $value)->first();
            
            if ($submission && $submission->seq_id) {
                $currentUrl = request()->url();
                
                // Replace the slug/uuid with the new seq_id
                $newUrl = preg_replace('/\/'.preg_quote($value, '/').'(?=\/|$)/', '/' . $submission->seq_id, $currentUrl, 1);
                
                if ($newUrl === $currentUrl) {
                     $newUrl = str_replace($value, $submission->seq_id, $currentUrl);
                }
                
                if (request()->getQueryString()) {
                    $newUrl .= '?' . request()->getQueryString();
                }

                throw new HttpResponseException(redirect($newUrl, 301));
            }
            
            if ($submission) {
               return $submission;
            }
        }

        return $this->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
    }

    // =====================================================
    // MODEL EVENTS (Auto-generate slug & code)
    // =====================================================

    /**
     * Bootstrap the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Submission $submission) {
            // Auto-generate slug from title
            if (empty($submission->slug) && !empty($submission->title)) {
                $submission->slug = static::generateUniqueSlug($submission->title);
            }

            // Auto-generate submission code
            if (empty($submission->submission_code) && !empty($submission->journal_id)) {
                $submission->submission_code = static::generateSubmissionCode($submission->journal_id);
            }
        });

        // Update slug if title changes (optional, can be removed if slugs should be permanent)
        static::updating(function (Submission $submission) {
            if ($submission->isDirty('title') && !empty($submission->title)) {
                // Only update slug if it hasn't been manually changed
                $oldSlug = Str::slug($submission->getOriginal('title'));
                if ($submission->slug === $oldSlug || Str::startsWith($submission->slug, $oldSlug . '-')) {
                    $submission->slug = static::generateUniqueSlug($submission->title, $submission->id);
                }
            }
        });
    }

    /**
     * Generate a unique slug from a title.
     */
    public static function generateUniqueSlug(string $title, ?string $excludeId = null): string
    {
        $baseSlug = Str::slug($title);

        // Ensure base slug is not empty
        if (empty($baseSlug)) {
            $baseSlug = 'submission';
        }

        // Limit slug length to prevent database issues
        $baseSlug = Str::limit($baseSlug, 200, '');

        $slug = $baseSlug;
        $counter = 1;

        // Keep incrementing until we find a unique slug
        while (static::slugExists($slug, $excludeId)) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists.
     */
    private static function slugExists(string $slug, ?string $excludeId = null): bool
    {
        $query = static::withTrashed()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Generate a unique submission code.
     * Format: [JOURNAL_ABBR]-[YEAR]-[SEQUENCE]
     * Example: JCO-2026-001
     */
    public static function generateSubmissionCode(string $journalId): string
    {
        $journal = Journal::find($journalId);

        // Get journal abbreviation (uppercase, max 5 chars)
        $abbreviation = strtoupper(Str::limit(
            $journal->abbreviation ?? $journal->slug ?? 'SUB',
            5,
            ''
        ));

        // Current year
        $year = now()->year;

        // Count existing submissions for this journal this year
        $count = static::withTrashed()
            ->where('journal_id', $journalId)
            ->whereYear('created_at', $year)
            ->count();

        // Next sequence number (padded to 3 digits)
        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $code = "{$abbreviation}-{$year}-{$sequence}";

        // Ensure uniqueness (in rare race conditions)
        while (static::withTrashed()->where('submission_code', $code)->exists()) {
            $count++;
            $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $code = "{$abbreviation}-{$year}-{$sequence}";
        }

        return $code;
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the journal this submission belongs to
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    /**
     * Get the author (user) who submitted this
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the section this submission belongs to
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    /**
     * Get the issue this submission is assigned to
     */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }

    /**
     * Get all files for this submission
     */
    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class, 'submission_id');
    }

    /**
     * Get all notes for this submission
     */
    public function notes(): HasMany
    {
        return $this->hasMany(SubmissionNote::class, 'submission_id')->latest();
    }

    /**
     * Get all authors for this submission
     */
    public function authors(): HasMany
    {
        return $this->hasMany(SubmissionAuthor::class, 'submission_id')->orderBy('sort_order');
    }

    /**
     * Get all keywords for this submission (many-to-many)
     */
    public function keywords(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'submission_keyword')
            ->withTimestamps();
    }

    /**
     * Get all publications (versions) for this submission
     */
    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class, 'submission_id')->orderBy('version', 'desc');
    }

    /**
     * Get the publication (latest version).
     */
    public function publication(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
         return $this->hasOne(Publication::class, 'submission_id')->orderByDesc('version');
    }

    /**
     * Get the current (latest) publication version as a relationship.
     * Uses orderBy instead of latestOfMany for PostgreSQL UUID compatibility.
     */
    public function currentPublication(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Publication::class, 'submission_id')->orderByDesc('version');
    }

    /**
     * Get the current publication instance (non-relationship accessor).
     * Use this method when you need the model instance directly.
     */
    public function getCurrentPublication(): ?Publication
    {
        return $this->currentPublication;
    }

    /**
     * Get or create the current publication
     */
    public function getOrCreatePublication(): Publication
    {
        $publication = $this->currentPublication;

        if (!$publication) {
            $publication = Publication::create([
                'submission_id' => $this->id,
                'section_id' => $this->section_id,
                'version' => 1,
                'status' => Publication::STATUS_QUEUED,
                'title' => $this->title,
                'subtitle' => $this->subtitle,
                'abstract' => $this->abstract,
                'keywords' => $this->keywords_string,
            ]);

            // Copy authors from submission to publication
            foreach ($this->authors as $author) {
                $newAuthor = $author->replicate();
                $newAuthor->submission_id = null;
                $newAuthor->publication_id = $publication->id;
                $newAuthor->save();
            }
        }

        return $publication;
    }

    /**
     * Get review assignments for this submission
     */
    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class, 'submission_id');
    }

    /**
     * Get review rounds for this submission
     */
    public function reviewRounds(): HasMany
    {
        return $this->hasMany(ReviewRound::class, 'submission_id')->orderBy('round');
    }

    /**
     * Get current (latest) review round
     */
    public function currentReviewRound()
    {
        return $this->reviewRounds()->latest('round')->first();
    }

    /**
     * Get editorial assignments for this submission
     */
    public function editorialAssignments(): HasMany
    {
        return $this->hasMany(EditorialAssignment::class, 'submission_id');
    }

    /**
     * Get active editorial assignments
     */
    public function activeEditors(): HasMany
    {
        return $this->editorialAssignments()->where('is_active', true);
    }

    /**
     * Get discussions for this submission
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class, 'submission_id');
    }

    /**
     * Get activity logs for this submission (timeline)
     */
    public function logs(): HasMany
    {
        return $this->hasMany(SubmissionLog::class, 'submission_id')->orderBy('created_at', 'desc');
    }

    /**
     * Alias for logs() - activity logs
     */
    public function activityLogs(): HasMany
    {
        return $this->logs();
    }

    /**
     * Get publication galleys for this submission
     */
    public function galleys(): HasMany
    {
        return $this->hasMany(PublicationGalley::class, 'submission_id')->ordered();
    }

    /**
     * Get article metrics for this submission
     */
    public function articleMetrics(): HasMany
    {
        return $this->hasMany(ArticleMetric::class, 'submission_id');
    }

    /**
     * Check if submission has at least one galley
     */
    public function hasGalleys(): bool
    {
        return $this->galleys()->exists();
    }

    /**
     * Check if submission is ready for publication
     * (Has galleys AND assigned to an issue)
     */
    public function isReadyForPublication(): bool
    {
        return $this->hasGalleys() && $this->issue_id !== null;
    }

    /**
     * Check if submission has an assigned editor
     */
    public function hasEditor(): bool
    {
        return $this->editorialAssignments()->where('is_active', true)->exists();
    }

    /**
     * Get the index stat for this submission.
     */
    public function indexStat(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SubmissionIndexStat::class, 'submission_id');
    }

    // =====================================================
    // STAGE CONSTANTS (OJS 3.3 Style)
    // =====================================================

    const STAGE_ID_SUBMISSION = 1;
    const STAGE_ID_REVIEW = 2;
    const STAGE_ID_COPYEDITING = 3;
    const STAGE_ID_PRODUCTION = 4;
    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by stage
     */
    public function scopeStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Scope for submissions awaiting editorial decision (all active)
     */
    public function scopeInQueue($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_IN_REVIEW,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_REVISION_REQUIRED,
        ]);
    }

    /**
     * Scope for UNASSIGNED submissions (OJS 3.3 style)
     * Submissions that are submitted but not yet picked up by an editor
     */
    public function scopeUnassigned($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED)
            ->where('stage_id', 1); // Stage 1 = Submission
    }

    /**
     * Scope for ASSIGNED/ACTIVE submissions (editor has picked it up)
     */
    public function scopeAssigned($query)
    {
        return $query->whereIn('status', [
            self::STATUS_IN_REVIEW,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_REVISION_REQUIRED,
        ])->orWhere(function ($q) {
            // Also include submitted but moved past stage 1
            $q->where('status', self::STATUS_SUBMITTED)
                ->where('stage_id', '>', 1);
        });
    }

    /**
     * Scope for archived submissions
     */
    public function scopeArchived($query)
    {
        return $query->whereIn('status', [
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_PUBLISHED,
        ]);
    }

    /**
     * Scope for published submissions
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    // =====================================================
    // ACCESSORS & HELPERS
    // =====================================================

    /**
     * Get status label with proper formatting
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_IN_REVIEW => 'In Review',
            self::STATUS_REVISION_REQUIRED => 'Revision Required',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_QUEUED_FOR_COPYEDITING => 'Queued for Copyediting',
            self::STATUS_IN_PRODUCTION => 'In Production',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PUBLISHED => 'Published',
            default => ucwords(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SUBMITTED => 'blue',
            self::STATUS_UNDER_REVIEW => 'amber',
            self::STATUS_IN_REVIEW => 'amber',
            self::STATUS_REVISION_REQUIRED => 'orange',
            self::STATUS_ACCEPTED => 'green',
            self::STATUS_QUEUED_FOR_COPYEDITING => 'cyan',
            self::STATUS_IN_PRODUCTION => 'purple',
            self::STATUS_SCHEDULED => 'indigo',
            self::STATUS_REJECTED => 'red',
            self::STATUS_PUBLISHED => 'emerald',
            default => 'gray',
        };
    }

    /**
     * Get keywords as array (for backward compatibility)
     */
    public function getKeywordsArrayAttribute(): array
    {
        return $this->keywords()->pluck('content')->toArray();
    }

    /**
     * Get keywords as comma-separated string (for display)
     */
    public function getKeywordsStringAttribute(): string
    {
        return $this->keywords()->pluck('content')->implode(', ');
    }

    /**
     * Get total views count from article metrics
     */
    public function getViewsCountAttribute(): int
    {
        return $this->articleMetrics()->where('type', ArticleMetric::TYPE_VIEW)->count();
    }

    /**
     * Get total downloads count from article metrics
     */
    public function getDownloadsCountAttribute(): int
    {
        return $this->articleMetrics()->where('type', ArticleMetric::TYPE_DOWNLOAD)->count();
    }

    /**
     * Check if submission can be edited
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_REVISION_REQUIRED,
        ]);
    }

    /**
     * Get corresponding author
     */
    public function getCorrespondingAuthor()
    {
        return $this->authors()->where('is_corresponding', true)->first();
    }
}
