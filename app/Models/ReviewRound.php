<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewRound extends Model
{
    use HasFactory, HasUuids;

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_REVISIONS_REQUESTED = 'revisions_requested';
    const STATUS_RESUBMIT_FOR_REVIEW = 'resubmit_for_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';

    protected $fillable = [
        'submission_id',
        'round',
        'status',
    ];

    protected $casts = [
        'round' => 'integer',
        'stage_id' => 'integer',
        'status' => 'integer',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_REVISIONS_REQUESTED => 'Revisions Requested',
            self::STATUS_RESUBMIT_FOR_REVIEW => 'Resubmit for Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DECLINED => 'Declined',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_REVISIONS_REQUESTED => 'orange',
            self::STATUS_RESUBMIT_FOR_REVIEW => 'blue',
            self::STATUS_APPROVED => 'green',
            self::STATUS_DECLINED => 'red',
            default => 'gray',
        };
    }
}
