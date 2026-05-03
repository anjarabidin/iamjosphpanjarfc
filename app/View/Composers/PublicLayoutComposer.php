<?php

namespace App\View\Composers;

use App\Models\NavigationMenu;
use App\Models\NavigationItem;
use App\Models\SidebarBlock;
use Illuminate\View\View;

class PublicLayoutComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $journal = current_journal();

        if (!$journal) {
            return;
        }

        // Fetch Primary Menu Items (location = 'primary')
        $primaryMenu = $this->getMenuItems($journal->id, NavigationMenu::LOCATION_PRIMARY);

        // FALLBACK: If no custom primary menu exists, use OJS 3.3 defaults
        if ($primaryMenu->isEmpty()) {
            $primaryMenu = $this->getDefaultOJSMenu($journal);
        }

        // Fetch User Menu Items (location = 'user_top')
        $userMenu = $this->getMenuItems($journal->id, NavigationMenu::LOCATION_USER_TOP);

        // Fetch Footer Menu Items (location = 'footer')
        $footerMenu = $this->getMenuItems($journal->id, NavigationMenu::LOCATION_FOOTER);

        // Fetch Active Sidebar Blocks
        $sidebarBlocks = SidebarBlock::where('journal_id', $journal->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Share data with the view
        $view->with([
            'primaryMenu' => $primaryMenu,
            'userMenu' => $userMenu,
            'footerMenu' => $footerMenu,
            'sidebarBlocks' => $sidebarBlocks ?? collect(),
            'journal' => $journal,
        ]);
    }

    /**
     * Get menu items for a specific location as a tree structure.
     */
    protected function getMenuItems(string $journalId, string $location): \Illuminate\Support\Collection
    {
        // First, get or create the menu for this location
        $menu = NavigationMenu::where('journal_id', $journalId)
            ->where('location', $location)
            ->where('is_active', true)
            ->first();

        if (!$menu) {
            return collect([]);
        }

        // Get all items for this menu
        $items = NavigationItem::where('menu_id', $menu->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Build tree structure
        return $this->buildTree($items);
    }

    /**
     * Build a tree structure from flat items.
     */
    protected function buildTree(\Illuminate\Support\Collection $items, ?string $parentId = null): \Illuminate\Support\Collection
    {
        $branch = collect([]);

        foreach ($items as $item) {
            if ($item->parent_id === $parentId) {
                $children = $this->buildTree($items, $item->id);

                if ($children->isNotEmpty()) {
                    $item->children = $children;
                } else {
                    $item->children = collect([]);
                }

                // Resolve dynamic URL for route-type items
                $item->resolved_url = $this->resolveItemUrl($item);

                $branch->push($item);
            }
        }

        return $branch;
    }

    /**
     * Resolve the URL for a menu item.
     */
    protected function resolveItemUrl(NavigationItem $item): string
    {
        if ($item->type === 'divider') {
            return '#';
        }

        if ($item->type === 'route' && $item->route_name) {
            try {
                $params = $item->route_params ?? [];
                // Add journal slug if route requires it
                if (str_contains($item->route_name, 'journal.')) {
                    $journal = current_journal();
                    if ($journal) {
                        $params = array_merge(['journal' => $journal->slug], $params);
                    }
                }
                return route($item->route_name, $params);
            } catch (\Exception $e) {
                return '#';
            }
        }

        return $item->url ?? '#';
    }

    /**
     * Generate default OJS 3.3 menu structure.
     * Returns a collection of objects mimicking NavigationItem structure.
     * Order: Current | Archives | Announcements | About (Dropdown)
     */
    protected function getDefaultOJSMenu($journal): \Illuminate\Support\Collection
    {
        $menu = collect([]);

        // 1. CURRENT (First item in OJS 3.3)
        $menu->push((object)[
            'label' => 'Current',
            'resolved_url' => route('journal.public.current', ['journal' => $journal->slug]),
            'children' => collect([]),
            'target' => '_self',
            'type' => 'route',
            'is_active' => true,
        ]);

        // 2. ARCHIVES
        $menu->push((object)[
            'label' => 'Archives',
            'resolved_url' => route('journal.public.archives', ['journal' => $journal->slug]),
            'children' => collect([]),
            'target' => '_self',
            'type' => 'route',
            'is_active' => true,
        ]);

        // 3. ANNOUNCEMENTS (Conditional - only if enabled)
        if ($journal->enable_announcements) {
            $menu->push((object)[
                'label' => 'Announcements',
                'resolved_url' => route('journal.announcement.index', ['journal' => $journal->slug]),
                'children' => collect([]),
                'target' => '_self',
                'type' => 'route',
                'is_active' => true,
            ]);
        }

        // 4. ABOUT (Dropdown)
        $aboutChildren = collect([
            (object)[
                'label' => 'About the Journal',
                'resolved_url' => route('journal.public.about', ['journal' => $journal->slug]),
                'target' => '_self',
                'type' => 'route',
                'is_active' => true,
            ],
            (object)[
                'label' => 'Submissions',
                'resolved_url' => route('journal.public.author-guidelines', ['journal' => $journal->slug]),
                'target' => '_self',
                'type' => 'route',
                'is_active' => true,
            ],
            (object)[
                'label' => 'Editorial Team',
                'resolved_url' => route('journal.public.editorial-team', ['journal' => $journal->slug]),
                'target' => '_self',
                'type' => 'route',
                'is_active' => true,
            ],
        ]);

        $menu->push((object)[
            'label' => 'About',
            'resolved_url' => '#',
            'children' => $aboutChildren,
            'target' => '_self',
            'type' => 'dropdown',
            'is_active' => true,
        ]);

        return $menu;
    }
}
