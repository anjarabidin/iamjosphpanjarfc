<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionParticipant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'last_read_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    /**
     * Get the discussion this participant belongs to.
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * Get the user who is a participant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
