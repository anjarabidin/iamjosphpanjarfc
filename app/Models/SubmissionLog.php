<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'user_id',
        'event_type',
        'title',
        'description',
        'metadata',
        'stage',
        'email_subject',
        'email_body',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // =====================================================
    // EVENT TYPE CONSTANTS
    // =====================================================

    public const EVENT_SUBMITTED             = 'submission_created';
    public const EVENT_EDITOR_ASSIGNED       = 'editor_assigned';
    public const EVENT_EDITOR_UNASSIGNED     = 'editor_unassigned';
    public const EVENT_REVIEWER_ASSIGNED     = 'reviewer_assigned';
    public const EVENT_REVIEW_SUBMITTED      = 'review_submitted';
    public const EVENT_DECISION_MADE         = 'decision_made';
    public const EVENT_STAGE_CHANGED         = 'stage_changed';
    public const EVENT_DISCUSSION_CREATED    = 'discussion_created';
    public const EVENT_DISCUSSION_MESSAGE    = 'discussion_message_sent';
    public const EVENT_FILE_UPLOADED         = 'file_uploaded';
    public const EVENT_METADATA_UPDATED      = 'metadata_updated';
    public const EVENT_PUBLISHED             = 'published';

    // =====================================================
    // STAGE NAME MAP (integer stage_id → string)
    // =====================================================

    public const STAGE_MAP = [
        1 => 'submission',
        2 => 'review',
        3 => 'copyediting',
        4 => 'production',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The submission files attached to this log entry.
     */
    public function files()
    {
        return $this->belongsToMany(SubmissionFile::class, 'submission_log_files')
                    ->withTimestamps();
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getIconAttribute(): string
    {
        return match ($this->event_type) {
            self::EVENT_SUBMITTED           => 'fa-file-circle-plus',
            self::EVENT_EDITOR_ASSIGNED     => 'fa-user-tie',
            self::EVENT_EDITOR_UNASSIGNED   => 'fa-user-minus',
            self::EVENT_REVIEWER_ASSIGNED   => 'fa-clipboard-check',
            self::EVENT_REVIEW_SUBMITTED    => 'fa-check-circle',
            self::EVENT_DECISION_MADE       => 'fa-gavel',
            self::EVENT_STAGE_CHANGED       => 'fa-arrow-right-arrow-left',
            self::EVENT_DISCUSSION_CREATED  => 'fa-comments',
            self::EVENT_DISCUSSION_MESSAGE  => 'fa-message',
            self::EVENT_FILE_UPLOADED       => 'fa-file-arrow-up',
            self::EVENT_METADATA_UPDATED    => 'fa-pen-to-square',
            self::EVENT_PUBLISHED           => 'fa-globe',
            default                         => 'fa-circle',
        };
    }

    public function getColorAttribute(): string
    {
        return match ($this->event_type) {
            self::EVENT_SUBMITTED           => 'indigo',
            self::EVENT_EDITOR_ASSIGNED     => 'purple',
            self::EVENT_REVIEWER_ASSIGNED   => 'blue',
            self::EVENT_REVIEW_SUBMITTED    => 'emerald',
            self::EVENT_DECISION_MADE       => 'amber',
            self::EVENT_DISCUSSION_CREATED,
            self::EVENT_DISCUSSION_MESSAGE  => 'sky',
            self::EVENT_FILE_UPLOADED       => 'teal',
            self::EVENT_METADATA_UPDATED    => 'orange',
            self::EVENT_PUBLISHED           => 'green',
            default                         => 'gray',
        };
    }

    /**
     * Human-readable stage label.
     */
    public function getStageLabelAttribute(): string
    {
        return match ($this->stage) {
            'submission'   => 'Submission',
            'review'       => 'Review',
            'copyediting'  => 'Copyediting',
            'production'   => 'Production',
            default        => ucfirst($this->stage ?? ''),
        };
    }

    // =====================================================
    // STATIC HELPERS
    // =====================================================

    /**
     * Create a log entry.
     *
     * @param Submission  $submission
     * @param string      $eventType  One of the EVENT_* constants
     * @param string      $title
     * @param string|null $description
     * @param array|null  $metadata
     * @param User|null   $user       Defaults to the authenticated user
     * @param array       $fileIds    Array of submission_files.id to attach
     * @param string|null $stage      Workflow stage string (submission|review|copyediting|production)
     * @param string|null $emailSubject
     * @param string|null $emailBody
     */
    public static function log(
        Submission $submission,
        string $eventType,
        string $title,
        ?string $description = null,
        ?array $metadata = null,
        ?User $user = null,
        array $fileIds = [],
        ?string $stage = null,
        ?string $emailSubject = null,
        ?string $emailBody = null
    ): self {
        $log = self::create([
            'submission_id' => $submission->id,
            'user_id'       => $user?->id ?? auth()->id(),
            'event_type'    => $eventType,
            'title'         => $title,
            'description'   => $description,
            'metadata'      => $metadata,
            'stage'         => $stage ?? $submission->stage,
            'email_subject' => $emailSubject,
            'email_body'    => $emailBody,
        ]);

        if (!empty($fileIds)) {
            $log->files()->attach($fileIds);
        }

        return $log;
    }

    /**
     * Log a metadata diff (before vs after) when submission fields are updated.
     *
     * @param Submission  $submission
     * @param array       $before     Key-value of original values
     * @param array       $after      Key-value of new values
     * @param User|null   $user
     */
    public static function logMetadataDiff(
        Submission $submission,
        array $before,
        array $after,
        ?User $user = null
    ): self {
        // Only keep keys that actually changed
        $changed = array_keys(array_diff_assoc($after, $before));
        $filteredBefore = array_intersect_key($before, array_flip($changed));
        $filteredAfter  = array_intersect_key($after,  array_flip($changed));

        $fields = implode(', ', $changed);

        return self::log(
            submission:  $submission,
            eventType:   self::EVENT_METADATA_UPDATED,
            title:       'Metadata updated: ' . $fields,
            description: 'The following fields were changed: ' . $fields,
            metadata:    ['before' => $filteredBefore, 'after' => $filteredAfter],
            user:        $user,
            stage:       $submission->stage,
        );
    }

    /**
     * Resolve the stage string from an integer stage_id (Discussion.stage_id).
     */
    public static function stageFromId(?int $stageId): ?string
    {
        return self::STAGE_MAP[$stageId] ?? null;
    }
}
