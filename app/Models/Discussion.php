<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discussion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'submission_id',
        'user_id',
        'subject',
        'stage_id',
        'is_open',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'stage_id' => 'integer',
        'closed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the submission this discussion belongs to.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Get the user who created this discussion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all messages in this discussion.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(DiscussionMessage::class)->orderBy('created_at');
    }

    /**
     * Get the participants of this discussion (many-to-many through pivot).
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'discussion_participants')
            ->withTimestamps();
    }

    /**
     * Get participant records.
     */
    public function participantRecords(): HasMany
    {
        return $this->hasMany(DiscussionParticipant::class);
    }

    /**
     * Get the user who closed this discussion.
     */
    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Close the discussion.
     */
    public function close(?string $userId = null): bool
    {
        $this->is_open = false;
        $this->closed_at = now();
        $this->closed_by = $userId ?? auth()->id();
        return $this->save();
    }

    /**
     * Reopen the discussion.
     */
    public function reopen(): bool
    {
        $this->is_open = true;
        $this->closed_at = null;
        $this->closed_by = null;
        return $this->save();
    }

    /**
     * Check if user is a participant.
     */
    public function hasParticipant(string $userId): bool
    {
        return $this->participants()->where('users.id', $userId)->exists();
    }

    /**
     * Add participants to this discussion.
     */
    public function addParticipants(array $userIds): void
    {
        foreach ($userIds as $userId) {
            DiscussionParticipant::firstOrCreate([
                'discussion_id' => $this->id,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Sync participants (replace all existing with new list).
     */
    public function syncParticipants(array $userIds): void
    {
        $this->participants()->sync($userIds);
    }

    /**
     * Check if the discussion is unread for the given user.
     */
    /**
     * Get the number of unread messages for the given user.
     */
    public function unreadMessagesCountForUser(string $userId): int
    {
        $participant = $this->participantRecords->where('user_id', $userId)->first();

        if (!$participant) {
            return 0;
        }

        if (is_null($participant->last_read_at)) {
            return $this->messages->count();
        }

        return $this->messages->where('created_at', '>', $participant->last_read_at)->count();
    }
}
