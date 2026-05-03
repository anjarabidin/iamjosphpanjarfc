<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialAssignment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'submission_id',
        'user_id',
        'assigned_by',
        'role',
        'is_active',
        'can_edit',
        'can_access_editorial_history',
        'date_assigned',
        'date_notified',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'can_edit' => 'boolean',
        'can_access_editorial_history' => 'boolean',
        'date_assigned' => 'datetime',
        'date_notified' => 'datetime',
    ];

    // Role constants
    const ROLE_EDITOR = 'editor';
    const ROLE_SECTION_EDITOR = 'section_editor';
    const ROLE_MANAGER = 'manager';

    /**
     * Get the submission this assignment belongs to.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Get the assigned user (editor).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who made the assignment.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope for active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
