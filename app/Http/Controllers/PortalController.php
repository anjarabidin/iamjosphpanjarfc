<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Journal;
use App\Models\Role;
use App\Models\SiteContent;
use App\Models\SiteContentBlock;
use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PortalController extends Controller
{
    /**
     * Display the portal landing page with dynamic blocks.
     */
    public function index(): View
    {
        // Get active blocks ordered by sort_order
        $blocks = SiteContentBlock::getActiveBlocks();

        // Get site settings
        $settings = Cache::remember('site_settings', 3600, function () {
            return SiteContent::getAll();
        });

        // Load navigation menus
        $primaryMenu = $this->loadNavigationMenu('primary');

        // Lazy load data only for active blocks
        $blockData = $this->loadBlockData($blocks);

        return view('site.index', [
            'blocks' => $blocks,
            'blockData' => $blockData,
            'settings' => $settings,
            'primaryMenu' => $primaryMenu,
        ]);
    }

    /**
     * Load data for active blocks (lazy loading pattern).
     */
    protected function loadBlockData($blocks): array
    {
        $data = [];
        $blockKeys = $blocks->pluck('key')->toArray();

        // Common statistics (used by multiple blocks)
        if (array_intersect(['hero_search', 'stats_counter'], $blockKeys)) {
            $stats = Cache::remember('portal_stats', 300, function () {
                return [
                    'total_journals' => Journal::where('enabled', true)->count(),
                    'total_articles' => Submission::where('status', Submission::STATUS_PUBLISHED)->count(),
                    'total_authors' => User::whereHas('roles', function ($query) {
                        $query->where('permission_level', Role::LEVEL_AUTHOR);
                    })->count(),
                    'total_downloads' => 50000, // Placeholder
                ];
            });

            $data['hero_search'] = $stats;
            $data['stats_counter'] = $stats;
        }

        // Featured Journals
        if (in_array('featured_journals', $blockKeys)) {
            $data['featured_journals'] = [
                'journals' => Cache::remember('featured_journals', 300, function () use ($blocks) {
                    $featuredBlock = $blocks->where('key', 'featured_journals')->first();
                    $featuredIds = $featuredBlock->getConfig('featured_ids', []);

                    if (!empty($featuredIds)) {
                        // Use specifically selected journals
                        return Journal::where('enabled', true)
                            ->where('visible', true)
                            ->whereIn('id', $featuredIds)
                            ->withCount(['submissions' => fn($q) => $q->where('status', Submission::STATUS_PUBLISHED)])
                            ->withCount(['issues' => fn($q) => $q->where('is_published', true)])
                            ->orderBy('name')
                            ->get();
                    } else {
                        // Fallback to auto-selection by submission count
                        return Journal::where('enabled', true)
                            ->where('visible', true)
                            ->orderByDesc('created_at')
                            ->withCount(['submissions' => fn($q) => $q->where('status', Submission::STATUS_PUBLISHED)])
                            ->withCount(['issues' => fn($q) => $q->where('is_published', true)])
                            ->take(8)
                            ->get();
                    }
                }),
            ];
        }

        // Journal Directory
        if (in_array('journal_directory', $blockKeys)) {
            $data['journal_directory'] = [
                'journals' => Cache::remember('all_journals', 300, function () {
                    return Journal::where('enabled', true)
                        ->where('visible', true)
                        ->withCount(['submissions' => fn($q) => $q->where('status', Submission::STATUS_PUBLISHED)])
                        ->withCount(['issues' => fn($q) => $q->where('is_published', true)])
                        ->with('currentIssue')
                        ->orderBy('name')
                        ->get();
                }),
            ];
        }

        // Latest Articles
        if (in_array('latest_articles', $blockKeys)) {
            $data['latest_articles'] = [
                'latest_articles' => Cache::remember('latest_articles', 300, function () {
                    return Submission::where('status', Submission::STATUS_PUBLISHED)
                        ->with(['authors', 'journal', 'section', 'issue'])
                        ->latest('published_at')
                        ->take(6)
                        ->get();
                }),
            ];
        }

        // Subject Categories
        if (in_array('subject_categories', $blockKeys)) {
            $data['subject_categories'] = [
                'categories' => [], // Could load from DB
            ];
        }

        return $data;
    }

    /**
     * Load navigation menu items for a specific area.
     */
    protected function loadNavigationMenu(string $area): \Illuminate\Support\Collection
    {
        $menu = \App\Models\NavigationMenu::getMenu($area, null); // null = site-level

        if (!$menu) {
            return collect();
        }

        // Load assigned items with their menu item data
        return $menu->assignments()
            ->with('item')
            ->whereHas('item', fn($q) => $q->where('is_active', true))
            ->orderBy('order')
            ->get()
            ->map(function ($assignment) {
                $item = $assignment->item;
                return (object) [
                    'label' => $item->title,
                    'resolved_url' => $item->resolved_url,
                    'target' => $item->target,
                    'icon' => $item->icon,
                    'is_divider' => $item->type === 'divider',
                ];
            });
    }

    /**
     * Handle search from portal.
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $category = $request->get('category', 'all');
        $journalId = $request->get('journal_id', '');
        $sort = $request->get('sort', 'relevance');

        $journals = collect();
        $articles = collect();

        // Load Popular Keywords (Top 10 most used across active submissions) with caching
        $popularKeywords = Cache::remember('portal_popular_keywords_global', 3600, function () {
            return \App\Models\Keyword::whereHas('submissions')
                ->withCount('submissions')
                ->orderByDesc('submissions_count')
                ->take(10)
                ->get();
        });

        // Load all active journals for the filter sidebar
        $filterJournals = Journal::where('enabled', true)
            ->withCount(['submissions as published_count' => fn($q) => $q->where('status', Submission::STATUS_PUBLISHED)])
            ->get();

        if (strlen($query) >= 2 || !empty($category) || !empty($journalId)) {
            // 1. Search Journals (only if category is 'all' or 'journals')
            if ($category === 'all' || $category === 'journals') {
                $journals = Journal::where('enabled', true)
                    ->when($query, function ($q) use ($query) {
                        $q->where(function ($sub) use ($query) {
                            $sub->where('name', 'ilike', "%{$query}%")
                                ->orWhere('abbreviation', 'ilike', "%{$query}%")
                                ->orWhere('description', 'ilike', "%{$query}%");
                        });
                    })
                    ->withCount(['submissions' => fn($q) => $q->where('status', Submission::STATUS_PUBLISHED)])
                    ->take(12)
                    ->get();
            }

            // 2. Search Articles (only if category is 'all' or 'articles')
            if ($category === 'all' || $category === 'articles') {
                $articlesQuery = Submission::where('status', Submission::STATUS_PUBLISHED)
                    ->when($query, function ($q) use ($query) {
                        $q->where(function ($sub) use ($query) {
                            $sub->where('title', 'ilike', "%{$query}%")
                                ->orWhere('abstract', 'ilike', "%{$query}%")
                                ->orWhereHas('keywords', function($kw) use ($query) {
                                    $kw->where('content', 'ilike', "%{$query}%");
                                })
                                ->orWhereHas('authors', function($auth) use ($query) {
                                    $auth->where('given_name', 'ilike', "%{$query}%")
                                        ->orWhere('family_name', 'ilike', "%{$query}%");
                                });
                        });
                    })
                    ->when($journalId, function ($q) use ($journalId) {
                        $q->where('journal_id', $journalId);
                    })
                    ->with(['authors', 'journal', 'section', 'issue']);

                // Sort handling
                match ($sort) {
                    'newest' => $articlesQuery->latest('published_at'),
                    'oldest' => $articlesQuery->oldest('published_at'),
                    default => $articlesQuery->latest('published_at'),
                };

                $articles = $articlesQuery->paginate(15)->withQueryString();
            }
        }

        $settings = SiteContent::getAll();

        return view('site.search', compact(
            'query', 'journals', 'articles', 'category', 'sort', 
            'journalId', 'settings', 'popularKeywords', 'filterJournals'
        ));
    }

    /**
     * Display all journals page with filtering.
     */
    public function journals(Request $request): View
    {
        $search = $request->get('search', '');
        $alpha = $request->get('alpha', '');
        $sort = $request->get('sort', 'name');

        $journals = Journal::where('enabled', true)
            ->where('visible', true)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                        ->orWhere('abbreviation', 'ilike', "%{$search}%")
                        ->orWhere('description', 'ilike', "%{$search}%");
                });
            })
            ->when($alpha && $alpha !== 'all', function ($query) use ($alpha) {
                $query->where('name', 'ilike', "{$alpha}%");
            })
            ->withCount(['submissions' => fn($q) => $q->where('status', Submission::STATUS_PUBLISHED)])
            ->withCount(['issues' => fn($q) => $q->where('is_published', true)])
            ->when($sort === 'articles', fn($q) => $q->orderByDesc('submissions_count'))
            ->when($sort === 'name', fn($q) => $q->orderBy('name'))
            ->when($sort === 'newest', fn($q) => $q->latest('created_at'))
            ->paginate(12)
            ->withQueryString();

        $alphabet = array_merge(['all'], range('A', 'Z'));
        $totalJournals = Journal::where('enabled', true)->where('visible', true)->count();
        $settings = SiteContent::getAll();

        return view('site.journals', compact('journals', 'search', 'alpha', 'sort', 'alphabet', 'totalJournals', 'settings'));
    }

    /**
     * Display the About Us page.
     */
    public function about(): View
    {

        // Merge with site settings (for about_content from SiteSetting)
        $siteSettings = SiteSetting::first();

        return view('site.about', compact('siteSettings'));
    }

    /**
     * Display a custom site page.
     */
    public function page(string $slug): View
    {
        $page = SitePage::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $settings = SiteContent::getAll();

        return view('site.page', compact('page', 'settings'));
    }
}
