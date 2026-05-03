<?php

namespace Database\Seeders;

use App\Models\SiteContentBlock;
use Illuminate\Database\Seeder;

class SiteContentBlockSeeder extends Seeder
{
    /**
     * Seed the default site content blocks.
     */
    public function run(): void
    {
        $blocks = [
            [
                'key' => 'hero_search',
                'title' => 'Hero Search',
                'description' => 'Main hero banner with search bar and statistics',
                'icon' => 'fa-solid fa-search',
                'category' => 'hero',
                'sort_order' => 1,
                'is_active' => true,
                'config' => [
                    'headline' => 'Discover Academic Excellence',
                    'subheadline' => 'Search across peer-reviewed journals and scholarly articles',
                    'background_type' => 'gradient', // gradient, image, solid
                    'background_gradient' => 'from-slate-900 via-blue-900 to-indigo-900',
                    'background_image' => null,
                    'show_stats' => true,
                    'show_popular_topics' => true,
                    'popular_topics' => ['Artificial Intelligence', 'Education', 'Economics', 'Health Sciences'],
                ],
            ],
            [
                'key' => 'stats_counter',
                'title' => 'Statistics Counter',
                'description' => 'Animated counters showing platform statistics',
                'icon' => 'fa-solid fa-chart-line',
                'category' => 'content',
                'sort_order' => 2,
                'is_active' => true,
                'config' => [
                    'layout' => 'horizontal', // horizontal, grid
                    'show_animation' => true,
                    'stats' => [
                        ['label' => 'Journals', 'icon' => 'fa-book', 'count_type' => 'journals'],
                        ['label' => 'Articles', 'icon' => 'fa-file-text', 'count_type' => 'articles'],
                        ['label' => 'Authors', 'icon' => 'fa-users', 'count_type' => 'authors'],
                        ['label' => 'Countries', 'icon' => 'fa-globe', 'count_type' => 'static', 'value' => '50+'],
                    ],
                ],
            ],
            [
                'key' => 'featured_journals',
                'title' => 'Featured Journals',
                'description' => 'Showcase highlighted journals',
                'icon' => 'fa-solid fa-star',
                'category' => 'content',
                'sort_order' => 3,
                'is_active' => true,
                'config' => [
                    'title' => 'Featured Journals',
                    'subtitle' => 'Explore our top-rated peer-reviewed publications',
                    'layout' => 'grid', // grid, carousel, list
                    'columns' => 4,
                    'limit' => 8,
                    'show_badges' => true,
                    'show_stats' => true,
                    'featured_ids' => [], // Empty = auto-select
                ],
            ],
            [
                'key' => 'subject_categories',
                'title' => 'Subject Categories',
                'description' => 'Browse journals by research area',
                'icon' => 'fa-solid fa-th-large',
                'category' => 'navigation',
                'sort_order' => 4,
                'is_active' => true,
                'config' => [
                    'title' => 'Browse by Subject',
                    'subtitle' => 'Find journals in your research area',
                    'layout' => 'icon-grid', // icon-grid, list, dropdown
                    'columns' => 6,
                    'show_count' => true,
                    'categories' => [
                        ['name' => 'Science & Technology', 'icon' => 'fa-flask', 'color' => 'blue'],
                        ['name' => 'Medicine & Health', 'icon' => 'fa-heartbeat', 'color' => 'red'],
                        ['name' => 'Social Sciences', 'icon' => 'fa-users', 'color' => 'green'],
                        ['name' => 'Arts & Humanities', 'icon' => 'fa-palette', 'color' => 'purple'],
                        ['name' => 'Business & Economics', 'icon' => 'fa-chart-bar', 'color' => 'amber'],
                        ['name' => 'Education', 'icon' => 'fa-graduation-cap', 'color' => 'indigo'],
                    ],
                ],
            ],
            [
                'key' => 'latest_articles',
                'title' => 'Latest Articles',
                'description' => 'Recent publications across all journals',
                'icon' => 'fa-solid fa-newspaper',
                'category' => 'content',
                'sort_order' => 5,
                'is_active' => true,
                'config' => [
                    'title' => 'Latest Publications',
                    'subtitle' => 'Recently published research articles',
                    'layout' => 'cards', // cards, list, masonry
                    'columns' => 3,
                    'limit' => 6,
                    'show_abstract' => true,
                    'show_authors' => true,
                    'show_journal' => true,
                    'abstract_length' => 150,
                ],
            ],
            [
                'key' => 'journal_directory',
                'title' => 'Journal Directory',
                'description' => 'Complete listing of all journals',
                'icon' => 'fa-solid fa-list',
                'category' => 'content',
                'sort_order' => 6,
                'is_active' => true,
                'config' => [
                    'title' => 'All Journals',
                    'subtitle' => 'Browse our complete collection',
                    'layout' => 'grid', // grid, list, compact
                    'columns' => 4,
                    'paginate' => true,
                    'per_page' => 12,
                    'show_search' => true,
                    'show_filter' => true,
                ],
            ],
            [
                'key' => 'indexing_partners',
                'title' => 'Indexing Partners',
                'description' => 'Logos of indexing databases',
                'icon' => 'fa-solid fa-certificate',
                'category' => 'trust',
                'sort_order' => 7,
                'is_active' => false, // Disabled by default
                'config' => [
                    'title' => 'Indexed by Major Databases',
                    'layout' => 'marquee', // marquee, static-grid
                    'logos' => [],
                ],
            ],
            [
                'key' => 'newsletter_cta',
                'title' => 'Newsletter CTA',
                'description' => 'Call-to-action for newsletter subscription',
                'icon' => 'fa-solid fa-envelope',
                'category' => 'cta',
                'sort_order' => 8,
                'is_active' => false, // Disabled by default
                'config' => [
                    'headline' => 'Stay Updated',
                    'subheadline' => 'Get the latest publications delivered to your inbox',
                    'button_text' => 'Subscribe',
                    'background' => 'gradient',
                ],
            ],
            [
                'key' => 'custom_html',
                'title' => 'Custom HTML Block',
                'description' => 'Add custom HTML content',
                'icon' => 'fa-solid fa-code',
                'category' => 'advanced',
                'sort_order' => 99,
                'is_active' => false,
                'config' => [
                    'html_content' => '',
                    'css_classes' => '',
                ],
            ],
        ];

        foreach ($blocks as $block) {
            SiteContentBlock::updateOrCreate(
                ['key' => $block['key']],
                $block
            );
        }
    }
}
