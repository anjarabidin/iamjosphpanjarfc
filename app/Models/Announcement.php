<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'journal_id',
        'user_id',
        'title',
        'excerpt',
        'content',
        'is_active',
        'is_urgent',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_urgent' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the journal that owns the announcement.
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the user who created the announcement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to only include active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope to only include published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        });
    }

    /**
     * Scope to only include urgent announcements.
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Check if the announcement is currently active (not expired).
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the status label.
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        if ($this->isExpired()) {
            return 'expired';
        }
        return 'active';
    }
}
