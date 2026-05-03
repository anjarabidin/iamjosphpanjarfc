<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'body',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (DiscussionMessage $message) {
            // Touch discussion updated_at so unread indicator works
            $message->discussion()->touch();

            // --- Audit Trail: log to SubmissionLog ---
            try {
                $discussion = $message->discussion()->with('submission')->first();
                if ($discussion && $discussion->submission) {
                    $submission = $discussion->submission;
                    $stage = \App\Models\SubmissionLog::stageFromId($discussion->stage_id);

                    // Collect any file attachments names for metadata
                    $attachments = $message->files()->pluck('original_name')->toArray();

                    \App\Models\SubmissionLog::log(
                        submission:  $submission,
                        eventType:   \App\Models\SubmissionLog::EVENT_DISCUSSION_MESSAGE,
                        title:       'Discussion: ' . ($discussion->subject ?? 'New Message'),
                        description: \Illuminate\Support\Str::limit(strip_tags($message->body), 200),
                        metadata:    array_filter([
                            'discussion_id' => $discussion->id,
                            'message_id'    => $message->id,
                            'attachments'   => $attachments ?: null,
                        ]),
                        user:        null, // uses auth()->id()
                        fileId:      null,
                        stage:       $stage ?? $submission->stage,
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('SubmissionLog: failed to log discussion message', ['error' => $e->getMessage()]);
            }
        });
    }

    /**
     * Get the discussion this message belongs to.
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * Get the user who sent this message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(DiscussionFile::class);
    }
}
