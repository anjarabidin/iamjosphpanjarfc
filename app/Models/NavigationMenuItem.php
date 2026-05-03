<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenuItem extends Model
{
    use HasFactory, HasUuids;

    /**
     * Item type constants
     */
    public const TYPE_CUSTOM = 'custom';
    public const TYPE_ROUTE = 'route';
    public const TYPE_PAGE = 'page';

    protected $fillable = [
        'journal_id',
        'title',
        'type',
        'url',
        'route_name',
        'path',
        'content',
        'related_id',
        'icon',
        'target',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'metadata' => 'array',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the journal this item belongs to
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get all assignments for this item
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(NavigationMenuItemAssignment::class, 'menu_item_id');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Get the resolved URL for this item
     */
    public function getResolvedUrlAttribute(): ?string
    {
        if ($this->type === self::TYPE_CUSTOM) {
            return $this->url;
        }

        if ($this->type === self::TYPE_ROUTE && $this->route_name) {
            try {
                // Get the journal from context
                $journal = current_journal();
                if ($journal && str_starts_with($this->route_name, 'journal.')) {
                    return route($this->route_name, ['journal' => $journal->slug]);
                }
                return route($this->route_name);
            } catch (\Exception $e) {
                return '#';
            }
        }

        if ($this->type === self::TYPE_PAGE && $this->path) {
            $journal = current_journal();
            if ($journal) {
                return route('journal.custom-page', ['journal' => $journal->slug, 'path' => $this->path]);
            }
        }

        return '#';
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
     * Scope to items for a specific journal
     */
    public function scopeForJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    // =====================================================
    // STATIC METHODS
    // =====================================================

    /**
     * Get available route options for menu items
     */
    public static function getAvailableRoutes(): array
    {
        return [
            ['name' => 'journal.public.home', 'label' => 'Home'],
            ['name' => 'journal.public.about', 'label' => 'About'],
            ['name' => 'journal.public.about-journal', 'label' => 'About the Journal'],
            ['name' => 'journal.public.editorial-team', 'label' => 'Editorial Team'],
            ['name' => 'journal.public.current', 'label' => 'Current'],
            ['name' => 'journal.public.archives', 'label' => 'Archives'],
            ['name' => 'journal.public.announcements', 'label' => 'Announcements'],
            ['name' => 'journal.public.author-guidelines', 'label' => 'Author Guidelines'],
            ['name' => 'journal.submissions.create', 'label' => 'Submissions'],
            ['name' => 'journal.public.privacy', 'label' => 'Privacy Statement'],
            ['name' => 'journal.public.contact', 'label' => 'Contact'],
            ['name' => 'journal.public.search', 'label' => 'Search'],
            ['name' => 'login', 'label' => 'Login'],
            ['name' => 'register', 'label' => 'Register'],
            ['name' => 'admin.dashboard', 'label' => 'Admin'],
            ['name' => 'admin.settings.index', 'label' => 'Administration'],
            ['name' => 'journal.admin.dashboard', 'label' => 'Dashboard'],
            ['name' => 'profile.edit', 'label' => 'View Profile'],
            ['name' => 'logout', 'label' => 'Logout'],
        ];
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
