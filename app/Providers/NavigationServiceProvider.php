<?php

namespace App\Providers;

use App\Models\NavigationMenu;
use App\Models\SidebarBlock;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compose navigation data for public layouts
        // Target the actual layout component paths used in views
        View::composer(['components.layouts.public', 'layouts.public', 'components.public.navbar'], function ($view) {
            // Get journal from view data (passed as prop) or fall back to current_journal()
            $viewData = $view->getData();
            $journal = $viewData['journal'] ?? current_journal();
            $journalId = $journal?->id;

            // Get Primary Navigation Menu (OJS 3.3 uses area_name)
            $primaryMenu = NavigationMenu::getMenu(NavigationMenu::AREA_PRIMARY, $journalId);
            $primaryNavItems = $primaryMenu ? $this->buildNavItems($primaryMenu, $journal) : collect();

            // Get User Navigation Menu
            $userMenu = NavigationMenu::getMenu(NavigationMenu::AREA_USER, $journalId);
            $userNavItems = $userMenu ? $this->buildNavItems($userMenu, $journal) : collect();

            // Footer is no longer used in OJS 3.3 style
            $footerNavItems = collect();

            // Get Sidebar Blocks
            $sidebarBlocks = $journalId
                ? SidebarBlock::getActiveBlocks($journalId, 'right')
                : collect();

            $leftSidebarBlocks = $journalId
                ? SidebarBlock::getActiveBlocks($journalId, 'left')
                : collect();

            // Share with view
            $view->with([
                'primaryNavItems' => $primaryNavItems,
                'userNavItems' => $userNavItems,
                'footerNavItems' => $footerNavItems,
                'sidebarBlocks' => $sidebarBlocks,
                'leftSidebarBlocks' => $leftSidebarBlocks,
                'primaryMenu' => $primaryNavItems,
                'userMenu' => $userNavItems,
            ]);
        });

        // Compose navigation data for admin settings page
        View::composer(['admin.journals.settings-navigation', 'journal.admin.settings.navigation'], function ($view) {
            $journal = current_journal();

            if (!$journal) {
                return;
            }

            // Get all menus for this journal (OJS 3.3 uses assignments)
            $menus = NavigationMenu::where('journal_id', $journal->id)
                ->with(['assignments' => function ($q) {
                    $q->orderBy('order')->with('item');
                }])
                ->get();

            // Get all sidebar blocks
            $sidebarBlocks = SidebarBlock::forJournal($journal->id)
                ->ordered()
                ->get();

            // Available system blocks
            $availableSystemBlocks = SidebarBlock::getSystemBlocks();

            $view->with([
                'navigationMenus' => $menus,
                'sidebarBlocks' => $sidebarBlocks,
                'availableSystemBlocks' => $availableSystemBlocks,
            ]);
        });
    }

    /**
     * Build navigation items from menu assignments
     */
    private function buildNavItems(NavigationMenu $menu, $journal): \Illuminate\Support\Collection
    {
        $assignments = $menu->rootAssignments()
            ->with(['item', 'children.item'])
            ->get();

        return $assignments->filter(fn($a) => $a->item && $a->item->is_active)
            ->map(function ($assignment) use ($journal) {
                $item = $assignment->item;
                
                return (object) [
                    'id' => $item->id,
                    'label' => $item->title,
                    'icon' => $item->icon,
                    'target' => $item->target ?? '_self',
                    'resolved_url' => $this->resolveItemUrl($item, $journal),
                    'is_divider' => false,
                    'children' => $assignment->children->filter(fn($c) => $c->item && $c->item->is_active)
                        ->map(function ($child) use ($journal) {
                            $childItem = $child->item;
                            return (object) [
                                'id' => $childItem->id,
                                'label' => $childItem->title,
                                'icon' => $childItem->icon,
                                'target' => $childItem->target ?? '_self',
                                'resolved_url' => $this->resolveItemUrl($childItem, $journal),
                                'is_divider' => false,
                            ];
                        }),
                ];
            });
    }

    /**
     * Resolve URL for a navigation item
     */
    private function resolveItemUrl($item, $journal): string
    {
        if ($item->type === 'custom' && $item->url) {
            return $item->url;
        }

        if ($item->type === 'route' && $item->route_name) {
            try {
                if ($journal && str_starts_with($item->route_name, 'journal.')) {
                    return route($item->route_name, ['journal' => $journal->slug]);
                }
                return route($item->route_name);
            } catch (\Exception $e) {
                return '#';
            }
        }

        if ($item->type === 'page' && $item->path) {
            if ($journal) {
                return route('journal.custom-page', ['journal' => $journal->slug, 'path' => $item->path]);
            }
        }

        return '#';
    }
}
