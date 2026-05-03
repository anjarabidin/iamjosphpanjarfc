<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\NavigationMenu;
use App\Models\NavigationMenuItem;
use App\Models\NavigationMenuItemAssignment;
use Illuminate\Database\Seeder;

class DefaultNavigationSeeder extends Seeder
{
    /**
     * Default navigation configuration like OJS 3.3
     */
    public function run(): void
    {
        $journals = Journal::all();

        foreach ($journals as $journal) {
            $this->seedNavigationForJournal($journal);
        }

        $this->command->info('Default navigation created for ' . $journals->count() . ' journal(s).');
    }

    /**
     * Seed navigation for a specific journal
     */
    public function seedNavigationForJournal(Journal $journal): void
    {
        // Skip if journal already has menus
        if (NavigationMenu::where('journal_id', $journal->id)->exists()) {
            $this->command?->info("Skipping journal '{$journal->name}' - already has navigation menus.");
            return;
        }

        // Create default menu items
        $menuItems = $this->createDefaultMenuItems($journal);

        // Create Primary Navigation Menu
        $primaryMenu = NavigationMenu::create([
            'journal_id' => $journal->id,
            'title' => 'Primary Navigation',
            'area_name' => 'primary',
            'is_active' => true,
        ]);

        // Assign items to primary menu (OJS 3.3 default)
        $primaryItems = ['home', 'about', 'current', 'archives', 'announcements'];
        $order = 1;
        foreach ($primaryItems as $key) {
            if (isset($menuItems[$key])) {
                NavigationMenuItemAssignment::create([
                    'menu_id' => $primaryMenu->id,
                    'menu_item_id' => $menuItems[$key]->id,
                    'parent_id' => null,
                    'order' => $order++,
                ]);
            }
        }

        // Create User Navigation Menu
        $userMenu = NavigationMenu::create([
            'journal_id' => $journal->id,
            'title' => 'User Navigation',
            'area_name' => 'user',
            'is_active' => true,
        ]);

        // Assign items to user menu (OJS 3.3 default)
        $userItems = ['submit', 'search'];
        $order = 1;
        foreach ($userItems as $key) {
            if (isset($menuItems[$key])) {
                NavigationMenuItemAssignment::create([
                    'menu_id' => $userMenu->id,
                    'menu_item_id' => $menuItems[$key]->id,
                    'parent_id' => null,
                    'order' => $order++,
                ]);
            }
        }
    }

    /**
     * Create default menu items for a journal
     */
    private function createDefaultMenuItems(Journal $journal): array
    {
        $items = [];

        // Default system pages like OJS 3.3
        $defaultItems = [
            'home' => [
                'title' => 'Home',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.home',
                'icon' => 'fa-solid fa-house',
            ],
            'about' => [
                'title' => 'About',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.about',
                'icon' => 'fa-solid fa-info-circle',
            ],
            'editorial_team' => [
                'title' => 'Editorial Team',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.editorial-team',
                'icon' => 'fa-solid fa-users',
            ],
            'current' => [
                'title' => 'Current Issue',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.current',
                'icon' => 'fa-solid fa-newspaper',
            ],
            'archives' => [
                'title' => 'Archives',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.archives',
                'icon' => 'fa-solid fa-archive',
            ],
            'author_guidelines' => [
                'title' => 'Author Guidelines',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.author-guidelines',
                'icon' => 'fa-solid fa-book',
            ],
            'announcements' => [
                'title' => 'Announcements',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.announcements',
                'icon' => 'fa-solid fa-bullhorn',
            ],
            'submit' => [
                'title' => 'Submit Article',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.submissions.create',
                'icon' => 'fa-solid fa-paper-plane',
            ],
            'search' => [
                'title' => 'Search',
                'type' => NavigationMenuItem::TYPE_ROUTE,
                'route_name' => 'journal.public.search',
                'icon' => 'fa-solid fa-search',
            ],
        ];

        foreach ($defaultItems as $key => $data) {
            $items[$key] = NavigationMenuItem::create([
                'journal_id' => $journal->id,
                'title' => $data['title'],
                'type' => $data['type'],
                'route_name' => $data['route_name'],
                'icon' => $data['icon'],
                'target' => '_self',
                'is_active' => true,
            ]);
        }

        return $items;
    }
}
