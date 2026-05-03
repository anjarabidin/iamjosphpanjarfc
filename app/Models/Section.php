<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'journal_id',
        'name',
        'abbreviation',
        'policy',
        'meta_indexed',
        'meta_reviewed',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'meta_indexed' => 'boolean',
        'meta_reviewed' => 'boolean',
        'sort_order' => 'integer',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the journal that owns this section
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    /**
     * Get submissions in this section
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'section_id');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to only include active sections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
