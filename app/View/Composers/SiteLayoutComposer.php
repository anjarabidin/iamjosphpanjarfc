<?php

namespace App\View\Composers;

use App\Models\NavigationMenu;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * SiteLayoutComposer
 * 
 * Provides navigation menus and settings to portal (site-level) views.
 * Site menus have journal_id = NULL.
 */
class SiteLayoutComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Cache site menus for 5 minutes
        $menus = Cache::remember('site_navigation_menus', 300, function () {
            return $this->loadSiteMenus();
        });

        // Share data with the view
        $view->with([
            'primaryMenu' => $menus['primary']['items'] ?? collect(),
            'userMenu' => $menus['user']['items'] ?? collect(),
            'footerMenu' => $menus['footer']['items'] ?? collect(),
        ]);
    }

    /**
     * Load all site-level navigation menus.
     */
    protected function loadSiteMenus(): array
    {
        $menus = [];

        foreach (NavigationMenu::getAreas() as $area => $name) {
            $menu = NavigationMenu::where('journal_id', null)
                ->where('area_name', $area)
                ->where('is_active', true)
                ->with(['assignments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->orderBy('order')
                        ->with(['item', 'children.item']);
                }])
                ->first();

            $items = collect();
            if ($menu) {
                $items = $this->buildNavItems($menu);
            }

            $menus[$area] = [
                'menu' => $menu,
                'items' => $items,
            ];
        }

        return $menus;
    }

    /**
     * Clear the cached site menus.
     */
    public static function clearCache(): void
    {
        Cache::forget('site_navigation_menus');
    }

    /**
     * Build navigation items from menu assignments
     */
    private function buildNavItems(NavigationMenu $menu): \Illuminate\Support\Collection
    {
        $assignments = $menu->rootAssignments()
            ->with(['item', 'children.item'])
            ->get();

        return $assignments->filter(fn($a) => $a->item && $a->item->is_active)
            ->map(function ($assignment) {
                $item = $assignment->item;

                return (object) [
                    'id' => $item->id,
                    'label' => $item->title,
                    'icon' => $item->icon,
                    'target' => $item->target ?? '_self',
                    'resolved_url' => $this->resolveItemUrl($item),
                    'is_divider' => false,
                    'children' => $assignment->children->filter(fn($c) => $c->item && $c->item->is_active)
                        ->map(function ($child) {
                            $childItem = $child->item;
                            return (object) [
                                'id' => $childItem->id,
                                'label' => $childItem->title,
                                'icon' => $childItem->icon,
                                'target' => $childItem->target ?? '_self',
                                'resolved_url' => $this->resolveItemUrl($childItem),
                                'is_divider' => false,
                            ];
                        }),
                ];
            });
    }

    /**
     * Resolve URL for a navigation item
     */
    private function resolveItemUrl($item): string
    {
        if ($item->type === 'custom' && $item->url) {
            return $item->url;
        }

        if ($item->type === 'route' && $item->route_name) {
            try {
                return route($item->route_name);
            } catch (\Exception $e) {
                return '#';
            }
        }

        // TODO: Handle page type
        return '#';
    }
}
