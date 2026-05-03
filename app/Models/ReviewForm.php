<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewForm extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'journal_id',
        'title',
        'description',
        'elements',
        'is_active',
        'response_count',
    ];

    protected function casts(): array
    {
        return [
            'elements' => 'array',
            'is_active' => 'boolean',
            'response_count' => 'integer',
        ];
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // =====================================================
    // HELPERS
    // =====================================================

    public function incrementResponseCount(): void
    {
        $this->increment('response_count');
    }
}
