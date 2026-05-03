<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id', // Added journal_id
        'event_key',
        'channel',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the journal that owns the template.
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class)->withTrashed();
    }
}
