<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class NavigationMenu extends Model
{
    use HasFactory, HasUuids;

    /**
     * Menu area constants - OJS 3.3 Compatible
     */
    public const AREA_PRIMARY = 'primary';
    public const AREA_USER = 'user';

    protected $fillable = [
        'journal_id',
        'title',
        'area_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the journal this menu belongs to
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get all item assignments for this menu
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(NavigationMenuItemAssignment::class, 'menu_id')->orderBy('order');
    }

    /**
     * Get all navigation items for this menu (site-level)
     */
    public function items(): HasMany
    {
        return $this->hasMany(NavigationMenuItemAssignment::class, 'menu_id')->with('item')->orderBy('order');
    }

    /**
     * Get root-level assignments (no parent)
     */
    public function rootAssignments(): HasMany
    {
        return $this->hasMany(NavigationMenuItemAssignment::class, 'menu_id')
            ->whereNull('parent_id')
            ->orderBy('order');
    }

    /**
     * Get menu tree structure (nested items via assignments)
     */
    public function getTreeAttribute(): \Illuminate\Support\Collection
    {
        return $this->rootAssignments()
            ->with(['item', 'children.item'])
            ->get()
            ->filter(fn($a) => $a->item && $a->item->is_active);
    }

    /**
     * Get preview of assigned items (comma separated titles)
     */
    public function getItemsPreviewAttribute(): string
    {
        $titles = $this->assignments()
            ->with('item')
            ->get()
            ->pluck('item.title')
            ->filter()
            ->take(5)
            ->toArray();

        $preview = implode(', ', $titles);
        $total = $this->assignments()->count();

        if ($total > 5) {
            $preview .= ' (+' . ($total - 5) . ' more)';
        }

        return $preview ?: 'No items assigned';
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to active menus only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by area
     */
    public function scopeArea($query, string $area)
    {
        return $query->where('area_name', $area);
    }

    /**
     * Scope by journal
     */
    public function scopeForJournal($query, ?string $journalId)
    {
        if ($journalId) {
            return $query->where('journal_id', $journalId);
        }
        return $query->whereNull('journal_id');
    }

    // =====================================================
    // STATIC HELPERS
    // =====================================================

    /**
     * Get menu for a specific area and journal
     */
    public static function getMenu(string $area, ?string $journalId = null): ?self
    {
        return static::active()
            ->area($area)
            ->forJournal($journalId)
            ->first();
    }

    /**
     * Get all available areas (OJS 3.3 Compatible)
     */
    public static function getAreas(): array
    {
        return [
            self::AREA_PRIMARY => 'Primary Navigation Menu',
            self::AREA_USER => 'User Navigation Menu',
        ];
    }

    /**
     * @deprecated Use getAreas() instead
     */
    public static function getLocations(): array
    {
        return self::getAreas();
    }
}
