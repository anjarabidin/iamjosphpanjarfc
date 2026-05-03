<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keyword extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'content',
    ];

    /**
     * Get all submissions that have this keyword.
     */
    public function submissions(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'submission_keyword')
            ->withTimestamps();
    }

    /**
     * Scope to search keywords by content.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('content', 'ILIKE', "%{$search}%");
    }
}
