<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\NavigationMenu;
use App\Models\NavigationMenuItem;
use App\Models\NavigationMenuItemAssignment;
use App\Models\SiteContentBlock;
use Illuminate\Database\Seeder;

class PortalSeeder extends Seeder
{
    /**
     * Seed the portal with default content and navigation.
     */
    public function run(): void
    {
        $this->command->info('🌐 Seeding Portal Content...');

        // Create default site-level navigation menus
        $this->createSiteNavigationMenus();

        // Ensure default content blocks exist
        $this->ensureDefaultContentBlocks();

        $this->command->info('✅ Portal content seeded successfully!');
    }

    /**
     * Create default site-level navigation menus.
     */
    protected function createSiteNavigationMenus(): void
    {
        $this->command->info('Creating site navigation menus...');

        // Primary Navigation Menu
        $primaryMenu = NavigationMenu::firstOrCreate(
            [
                'journal_id' => null,
                'area_name' => NavigationMenu::AREA_PRIMARY,
            ],
            [
                'title' => 'Site Primary Navigation',
                'is_active' => true,
            ]
        );

        // Create primary menu items
        $this->createPrimaryMenuItems($primaryMenu);

        // Footer Navigation Menu
        $footerMenu = NavigationMenu::firstOrCreate(
            [
                'journal_id' => null,
                'area_name' => 'footer',
            ],
            [
                'title' => 'Site Footer Navigation',
                'is_active' => true,
            ]
        );

        // Create footer menu items
        $this->createFooterMenuItems($footerMenu);
    }

    /**
     * Create primary navigation menu items.
     */
    protected function createPrimaryMenuItems(NavigationMenu $menu): void
    {
        $items = [
            [
                'title' => 'Home',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'portal.home',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'title' => 'Journals',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'portal.journals',
                'icon' => 'fa-solid fa-book',
            ],
            [
                'title' => 'About',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'portal.about',
                'icon' => 'fa-solid fa-info-circle',
            ],
            [
                'title' => 'Search',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'portal.search',
                'icon' => 'fa-solid fa-search',
            ],
        ];

        $order = 1;
        foreach ($items as $itemData) {
            $item = NavigationMenuItem::firstOrCreate(
                [
                    'journal_id' => null,
                    'title' => $itemData['title'],
                ],
                $itemData
            );

            // Assign to menu
            NavigationMenuItemAssignment::firstOrCreate(
                [
                    'menu_id' => $menu->id,
                    'menu_item_id' => $item->id,
                ],
                [
                    'parent_id' => null,
                    'order' => $order++,
                ]
            );
        }
    }

    /**
     * Create footer navigation menu items.
     */
    protected function createFooterMenuItems(NavigationMenu $menu): void
    {
        $items = [
            [
                'title' => 'All Journals',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'portal.journals',
                'icon' => 'fa-solid fa-list',
            ],
            [
                'title' => 'Search Articles',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'portal.search',
                'icon' => 'fa-solid fa-search',
            ],
            [
                'title' => 'About IAMJOS',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'portal.about',
                'icon' => 'fa-solid fa-info-circle',
            ],
            [
                'title' => 'Contact',
                'type' => NavigationMenuItem::TYPE_CUSTOM,
                'url' => '#contact',
                'icon' => 'fa-solid fa-envelope',
            ],
        ];

        $order = 1;
        foreach ($items as $itemData) {
            $item = NavigationMenuItem::firstOrCreate(
                [
                    'journal_id' => null,
                    'title' => $itemData['title'],
                ],
                $itemData
            );

            // Assign to menu
            NavigationMenuItemAssignment::firstOrCreate(
                [
                    'menu_id' => $menu->id,
                    'menu_item_id' => $item->id,
                ],
                [
                    'parent_id' => null,
                    'order' => $order++,
                ]
            );
        }
    }

    /**
     * Ensure default content blocks exist.
     */
    protected function ensureDefaultContentBlocks(): void
    {
        $this->command->info('Ensuring default content blocks exist...');

        // Call the SiteContentBlockSeeder if it hasn't been run
        $this->call(SiteContentBlockSeeder::class);
    }
}