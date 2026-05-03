<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionFile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'discussion_message_id',
        'user_id',
        'file_path',
        'original_name',
        'file_type',
        'file_size',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(DiscussionMessage::class, 'discussion_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
