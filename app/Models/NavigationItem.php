<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'navigation_menu_items';

    /**
     * Item type constants
     */
    public const TYPE_CUSTOM = 'custom';
    public const TYPE_PAGE = 'page';
    public const TYPE_ROUTE = 'route';
    public const TYPE_DIVIDER = 'divider';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'url',
        'type',
        'route_name',
        'route_params',
        'icon',
        'target',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'route_params' => 'array',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the menu this item belongs to
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'menu_id');
    }

    /**
     * Get the parent item (for nested dropdowns)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    /**
     * Get all child items
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get only active child items
     */
    public function activeChildren(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Get the resolved URL for this item
     */
    public function getResolvedUrlAttribute(): ?string
    {
        if ($this->type === self::TYPE_DIVIDER) {
            return null;
        }

        if ($this->type === self::TYPE_ROUTE && $this->route_name) {
            try {
                $params = $this->route_params ?? [];

                // Replace journal placeholder with current journal if needed
                if (isset($params['journal']) && $params['journal'] === '{journal}') {
                    $params['journal'] = current_journal()?->slug ?? '';
                }

                return route($this->route_name, $params);
            } catch (\Exception $e) {
                return '#';
            }
        }

        return $this->url ?? '#';
    }

    /**
     * Check if this item has children
     */
    public function getHasChildrenAttribute(): bool
    {
        return $this->activeChildren()->count() > 0;
    }

    /**
     * Check if this is a divider
     */
    public function getIsDividerAttribute(): bool
    {
        return $this->type === self::TYPE_DIVIDER;
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to active items only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to root items only (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // =====================================================
    // STATIC HELPERS
    // =====================================================

    /**
     * Get available item types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_CUSTOM => 'Custom Link',
            self::TYPE_PAGE => 'Page',
            self::TYPE_ROUTE => 'Route',
            self::TYPE_DIVIDER => 'Divider',
        ];
    }
}
