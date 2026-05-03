<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenuItemAssignment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'menu_id',
        'menu_item_id',
        'parent_id',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the menu this assignment belongs to
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'menu_id');
    }

    /**
     * Get the menu item for this assignment
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(NavigationMenuItem::class, 'menu_item_id');
    }

    /**
     * Get the parent assignment (for nested items)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationMenuItemAssignment::class, 'parent_id');
    }

    /**
     * Get children assignments (for nested items)
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavigationMenuItemAssignment::class, 'parent_id')->orderBy('order');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to root-level assignments only
     */
    public function scopeRootLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to ordered assignments
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
