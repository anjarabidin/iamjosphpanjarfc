<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionAuthor extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'submission_id',
        'publication_id',
        'user_id',
        'name',
        'given_name',
        'family_name',
        'preferred_public_name',
        'first_name',
        'last_name',
        'email',
        'affiliation',
        'country',
        'orcid',
        'url',
        'biography',
        'is_corresponding',
        'is_primary_contact',
        'include_in_browse',
        'user_group_id',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_corresponding' => 'boolean',
        'is_primary_contact' => 'boolean',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the submission this author belongs to
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * Get the user account (if author is registered)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the publication this author belongs to
     */
    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to get corresponding author
     */
    public function scopeCorresponding($query)
    {
        return $query->where('is_corresponding', true);
    }

    /**
     * Scope to order by position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Get formatted ORCID URL
     */
    public function getOrcidUrlAttribute(): ?string
    {
        if (empty($this->orcid)) {
            return null;
        }

        // Remove any existing URL prefix to normalize
        $orcid = preg_replace('/^https?:\/\/orcid\.org\//', '', $this->orcid);

        return "https://orcid.org/{$orcid}";
    }

    /**
     * Get display name with affiliation
     */
    public function getDisplayNameAttribute(): string
    {
        $display = $this->name;

        if ($this->affiliation) {
            $display .= " ({$this->affiliation})";
        }

        return $display;
    }
}
