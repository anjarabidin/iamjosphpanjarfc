<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Journal;
use Illuminate\View\View;
use App\Models\Submission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class PublicController extends Controller
{
    /**
     * Resolve journal from route parameter or return null for portal pages.
     */
    protected function resolveJournal(string $journalSlug): ?Journal
    {
        $journal = Journal::where('slug', $journalSlug)
            ->where('enabled', true)
            ->first();

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        return $journal;
    }

    // =====================================================
    // PORTAL PAGES (Multi-Journal Landing)
    // =====================================================

    /**
     * Display the portal homepage (list of journals).
     */
    public function portalHome(): View
    {
        $journals = Journal::where('enabled', true)
            ->where('visible', true)
            ->withCount(['submissions' => fn($q) => $q->published()])
            ->withCount(['issues' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        // Featured journals (first 4)
        $featuredJournals = $journals->take(4);

        // Latest articles across all journals
        $latestArticles = Submission::published()
            ->with(['authors', 'journal', 'section'])
            ->latest('published_at')
            ->take(6)
            ->get();

        // Stats
        $totalJournals = $journals->count();
        $totalArticles = Submission::published()->count();
        $totalIssues = Issue::published()->count();

        return view('public.portal-home', compact(
            'journals',
            'featuredJournals',
            'latestArticles',
            'totalJournals',
            'totalArticles',
            'totalIssues'
        ));
    }

    /**
     * Display full list of journals.
     */
    public function journalList(Request $request): View
    {
        $search = $request->get('search');

        $query = Journal::where('enabled', true)
            ->where('visible', true)
            ->withCount(['submissions' => fn($q) => $q->published()])
            ->withCount(['issues' => fn($q) => $q->published()]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('abbreviation', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $journals = $query->orderBy('name')->paginate(12);

        return view('public.journals', compact('journals', 'search'));
    }

    // =====================================================
    // JOURNAL-SPECIFIC PAGES
    // =====================================================

    /**
     * Display the journal homepage.
     */
    public function journalHome(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Latest published issue
        $latestIssue = Issue::where('journal_id', $journal->id)
            ->published()
            ->latest()
            ->first();

        // Latest published articles
        $latestArticles = Submission::where('journal_id', $journal->id)
            ->published()
            ->with(['authors', 'section', 'issue'])
            ->latest('published_at')
            ->take(6)
            ->get();

        // Popular articles (placeholder - based on most recent for now)
        $popularArticles = Submission::where('journal_id', $journal->id)
            ->published()
            ->with(['authors', 'section'])
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('public.home', compact('journal', 'latestIssue', 'latestArticles', 'popularArticles'));
    }

    /**
     * Display current issue for a journal.
     */
    public function currentIssue(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        $issue = Issue::where('journal_id', $journal->id)
            ->published()
            ->latest()
            ->first();

        if (!$issue) {
            $title = 'No Current Issue';
            return view('public.no-current-issue', compact('journal', 'settings', 'title'));
        }

        $articles = Submission::where('issue_id', $issue->id)
            ->published()
            ->with(['authors', 'section', 'galleys' => function ($q) {
                $q->ordered();
            }])
            ->orderBy('created_at')
            ->get();

        // Group by section
        $articlesBySection = $articles->groupBy(fn($article) => $article->section?->name ?? 'Uncategorized');

        return view('public.issue', compact('journal', 'settings', 'issue', 'articles', 'articlesBySection'));
    }

    /**
     * Display archives (list of all issues) for a journal.
     */
    public function archives(Request $request, string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);
        $year = $request->get('year');

        // Get settings with defaults (similar to JournalHomepageController)
        $settings = $this->getSettingsWithDefaults($journal);

        $query = Issue::where('journal_id', $journal->id)
            ->published()
            ->withCount(['submissions' => function ($q) {
                $q->published();
            }]);

        if ($year) {
            $query->where('year', $year);
        }

        $issues = $query->latest()->paginate(12);

        // Get available years for filter
        $years = Issue::where('journal_id', $journal->id)
            ->published()
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('public.archives', compact('journal', 'settings', 'issues', 'years', 'year'));
    }

    /**
     * Display a specific issue.
     */
    public function issue(string $journalSlug, $issueSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        // =============================================
        // LOGIKA BARU: HANDLER SEQ_ID, UUID, AND URL_PATH
        // =============================================
        $issue = null;

        if (Str::isUuid($issueSlug)) {
            $issue = Issue::where('id', $issueSlug)
                ->where('journal_id', $journal->id)
                ->first();
        } elseif (is_numeric($issueSlug)) {
            $issue = Issue::where('seq_id', $issueSlug)
                ->where('journal_id', $journal->id)
                ->first();
                
            if (!$issue) {
                // Fallback for numeric slugs
                $issue = Issue::where('url_path', $issueSlug)
                    ->where('journal_id', $journal->id)
                    ->first();
            }
        } else {
            $issue = Issue::where('url_path', $issueSlug)
                ->where('journal_id', $journal->id)
                ->first();
        }

        if (!$issue) {
            abort(404);
        }

        if (!$issue->is_published) {
            abort(404);
        }

        $articles = Submission::where('issue_id', $issue->id)
            ->published()
            ->with(['authors', 'section', 'galleys'])
            ->orderBy('created_at')
            ->get();

        // Group by section
        $articlesBySection = $articles->groupBy(fn($article) => $article->section?->name ?? 'Uncategorized');

        return view('public.issue', compact('journal', 'settings', 'issue', 'articles', 'articlesBySection'));
    }

    public function article(string $journalSlug, $slug): \Illuminate\View\View|RedirectResponse
    {
        // 1. Resolve Journal Terlebih Dahulu (Penting untuk validasi)
        $journal = $this->resolveJournal($journalSlug);

        // =============================================
        // LOGIKA BARU: HANDLER SEQ_ID, UUID, AND SLUG
        // =============================================
        $article = null;

        if (Str::isUuid($slug)) {
            // Cari artikel berdasarkan ID (UUID)
            $article = Submission::published()
                ->where('id', $slug)
                ->where('journal_id', $journal->id)
                ->first();
        } elseif (is_numeric($slug)) {
            // Cari artikel berdasarkan seq_id
            $article = Submission::published()
                ->where('seq_id', $slug)
                ->where('journal_id', $journal->id)
                ->first();
                
            // Fallback jika ternyata slug dibuat dari angka (jarang terjadi tapi mungkin)
            if (!$article) {
                $article = Submission::published()
                    ->where('slug', $slug)
                    ->where('journal_id', $journal->id)
                    ->first();
            }
        } else {
            // Cari artikel berdasarkan Slug
            $article = Submission::published()
                ->where('slug', $slug)
                ->where('journal_id', $journal->id)
                ->first();
        }

        if (!$article) {
            abort(404);
        }

        // Eager load all required relationships
        $article->load([
            'authors' => function ($q) {
                $q->orderBy('sort_order');
            },
            'section',
            'issue',
            'journal',
            'galleys' => function ($q) {
                $q->ordered();
            },
            'currentPublication',
        ]);

        // Get the issue for additional metadata
        $issue = $article->issue;
        $ip = request()->ip();
        
        // Simple bot detection (check user agent)
        $userAgent = strtolower(request()->userAgent() ?? '');
        $isBot = str_contains($userAgent, 'bot') || 
                str_contains($userAgent, 'crawler') || 
                str_contains($userAgent, 'spider');

        if (!$isBot) {
            // Mock location data (replace with actual GeoIP package like stevebauman/location)
            $countryCode = 'ID'; // Default
            $city = null;
            
            // Log the view
            DB::table('article_metrics')->insert([
                'submission_id' => $article->id,
                'type' => 'view',
                'ip_address' => $ip,
                'country_code' => $countryCode,
                'city' => $city,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =============================================
        // ANALYTICS: Prepare Chart Data (Last 12 Months)
        // =============================================
        $stats = DB::table('article_metrics')
            ->selectRaw("TO_CHAR(date, 'YYYY-MM') as month, type, count(*) as total")
            ->where('submission_id', $article->id)
            ->where('date', '>=', now()->subYear())
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get();
        
        // Build complete month range for last 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }
        
        // Map data to months (fill missing months with 0)
        $chartLabels = $months->values();
        $viewsData = $months->mapWithKeys(function($month) use ($stats) {
            $stat = $stats->where('month', $month)->where('type', 'view')->first();
            return [$month => $stat ? $stat->total : 0];
        })->values();
        
        $downloadsData = $months->mapWithKeys(function($month) use ($stats) {
            $stat = $stats->where('month', $month)->where('type', 'download')->first();
            return [$month => $stat ? $stat->total : 0];
        })->values();

        // =============================================
        // ANALYTICS: Country Stats (Admin Only)
        // =============================================
        $countryStats = collect();
        if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'journal manager', 'editor'])) {
            $countryStats = DB::table('article_metrics')
                ->select('country_code', DB::raw('count(*) as total'))
                ->where('submission_id', $article->id)
                ->where('type', 'view')
                ->whereNotNull('country_code')
                ->groupBy('country_code')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        }

        // Related articles from same section in same journal
        $relatedArticles = Submission::where('journal_id', $journal->id)
            ->where('section_id', $article->section_id)
            ->where('id', '!=', $article->id)
            ->published()
            ->with(['authors', 'section'])
            ->latest('published_at')
            ->take(5)
            ->get();

        // Get journal website settings for theming
        $settings = $journal->getWebsiteSettings();

        return view('journal.public.article', compact(
            'journal',
            'article',
            'issue',
            'relatedArticles',
            'settings',
            'chartLabels',
            'viewsData',
            'downloadsData',
            'countryStats'
        ));
    }

    /**
     * Display a single article (OJS 3.3 Style).
     * 
     * This page displays the full article metadata with proper Google Scholar
     * indexing support (Highwire Press meta tags) and a 2-column layout.
     */
    // public function article(string $journalSlug, $slug): View
    // {
    //     $article = Submission::published()->where('slug', $slug)->first();
        
    //     if (!$article) {
    //         abort(404);
    //     }

    //     $journal = $this->resolveJournal($journalSlug);

    //     // Ensure submission belongs to this journal
    //     if ($article->journal_id !== $journal->id) {
    //         abort(404);
    //     }

    //     // Only show published articles on public pages
    //     if ($article->status !== Submission::STATUS_PUBLISHED) {
    //         abort(404);
    //     }

    //     // Eager load all required relationships
    //     $article->load([
    //         'authors' => function ($q) {
    //             $q->orderBy('sort_order');
    //         },
    //         'section',
    //         'issue',
    //         'journal',
    //         'galleys' => function ($q) {
    //             $q->ordered();
    //         },
    //         'currentPublication',
    //     ]);

    //     // Get the issue for additional metadata
    //     $issue = $article->issue;
    //     $ip = request()->ip();
        
    //     // Simple bot detection (check user agent)
    //     $userAgent = strtolower(request()->userAgent() ?? '');
    //     $isBot = str_contains($userAgent, 'bot') || 
    //              str_contains($userAgent, 'crawler') || 
    //              str_contains($userAgent, 'spider');

    //     if (!$isBot) {
    //         // Mock location data (replace with actual GeoIP package like stevebauman/location)
    //         $countryCode = 'ID'; // Default
    //         $city = null;
            
    //         // Log the view
    //         DB::table('article_metrics')->insert([
    //             'submission_id' => $article->id,
    //             'type' => 'view',
    //             'ip_address' => $ip,
    //             'country_code' => $countryCode,
    //             'city' => $city,
    //             'date' => now()->toDateString(),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     // =============================================
    //     // ANALYTICS: Prepare Chart Data (Last 12 Months)
    //     // =============================================
    //     $stats = DB::table('article_metrics')
    //         ->selectRaw("TO_CHAR(date, 'YYYY-MM') as month, type, count(*) as total")
    //         ->where('submission_id', $article->id)
    //         ->where('date', '>=', now()->subYear())
    //         ->groupBy('month', 'type')
    //         ->orderBy('month')
    //         ->get();
        
    //     // Build complete month range for last 12 months
    //     $months = collect();
    //     for ($i = 11; $i >= 0; $i--) {
    //         $months->push(now()->subMonths($i)->format('Y-m'));
    //     }
        
    //     // Map data to months (fill missing months with 0)
    //     $chartLabels = $months->values();
    //     $viewsData = $months->mapWithKeys(function($month) use ($stats) {
    //         $stat = $stats->where('month', $month)->where('type', 'view')->first();
    //         return [$month => $stat ? $stat->total : 0];
    //     })->values();
        
    //     $downloadsData = $months->mapWithKeys(function($month) use ($stats) {
    //         $stat = $stats->where('month', $month)->where('type', 'download')->first();
    //         return [$month => $stat ? $stat->total : 0];
    //     })->values();

    //     // =============================================
    //     // ANALYTICS: Country Stats (Admin Only)
    //     // =============================================
    //     $countryStats = collect();
    //     if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'journal manager', 'editor'])) {
    //         $countryStats = DB::table('article_metrics')
    //             ->select('country_code', DB::raw('count(*) as total'))
    //             ->where('submission_id', $article->id)
    //             ->where('type', 'view')
    //             ->whereNotNull('country_code')
    //             ->groupBy('country_code')
    //             ->orderByDesc('total')
    //             ->limit(10)
    //             ->get();
    //     }

    //     // Related articles from same section in same journal
    //     $relatedArticles = Submission::where('journal_id', $journal->id)
    //         ->where('section_id', $article->section_id)
    //         ->where('id', '!=', $article->id)
    //         ->published()
    //         ->with(['authors', 'section'])
    //         ->latest('published_at')
    //         ->take(5)
    //         ->get();

    //     // Get journal website settings for theming
    //     $settings = $journal->getWebsiteSettings();

    //     return view('journal.public.article', compact(
    //         'journal',
    //         'article',
    //         'issue',
    //         'relatedArticles',
    //         'settings',
    //         'chartLabels',
    //         'viewsData',
    //         'downloadsData',
    //         'countryStats'
    //     ));
    // }

    /**
     * About the journal.
     */
    public function about(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);
        $about = $journal->settings['masthead']['about'] ?? '';

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        return view('public.about', compact('journal', 'settings', 'about'));
    }

    /**
     * Author guidelines.
     */
    public function authorGuidelines(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        return view('public.author-guidelines', compact('journal', 'settings'));
    }

    /**
     * Editorial team.
     */
    public function editorialTeam(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        return view('public.editorial-team', compact('journal', 'settings'));
    }

    /**
     * Information for Readers.
     */
    public function infoReaders(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        $title = 'For Readers';
        $content = $journal->info_readers;

        return view('public.information', compact('journal', 'settings', 'title', 'content'));
    }

    /**
     * Information for Authors.
     */
    public function infoAuthors(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        $title = 'For Authors';
        $content = $journal->info_authors;

        return view('public.information', compact('journal', 'settings', 'title', 'content'));
    }

    /**
     * Information for Librarians.
     */
    public function infoLibrarians(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        $title = 'For Librarians';
        $content = $journal->info_librarians;

        return view('public.information', compact('journal', 'settings', 'title', 'content'));
    }

    /**
     * Display list of announcements.
     */
    public function announcements(string $journalSlug): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        if (!$journal->enable_announcements) {
             abort(404);
        }

        $announcements = \App\Models\Announcement::where('journal_id', $journal->id)
            ->where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $title = 'Announcements';

        return view('public.announcement.index', compact('journal', 'settings', 'announcements', 'title'));
    }

    /**
     * Display a specific announcement.
     */
    public function announcement(string $journalSlug, $id): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Get settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        if (!$journal->enable_announcements) {
             abort(404);
        }

        $announcement = \App\Models\Announcement::where('journal_id', $journal->id)
            ->where('is_active', true)
            ->where('published_at', '<=', now())
            ->findOrFail($id);

        $title = $announcement->title;

        return view('public.announcement.show', compact('journal', 'settings', 'announcement', 'title'));
    }

    /**
     * Display article in full-screen PDF reader.
     */
    public function articleReader(string $journalSlug, Submission $submission): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        if ($submission->status !== Submission::STATUS_PUBLISHED) {
            abort(404);
        }

        $submission->load([
            'authors',
            'section',
            'issue',
            'journal',
            'files' => function ($q) {
                $q->where('file_type', 'galley')
                    ->orderBy('version', 'desc');
            }
        ]);

        // Get the galley file (PDF)
        $galleyFile = $submission->files->first();

        return view('public.article-reader', compact('journal', 'submission', 'galleyFile'));
    }

    /**
     * Download a publication galley file.
     * CRITICAL for Google Scholar indexing - must stream the actual file.
     */
    public function downloadGalley(string $journalSlug, $article, $galley)
    {
        $journal = $this->resolveJournal($journalSlug);

        // Resolve article by ID or slug
        $submissionQuery = Submission::where('journal_id', $journal->id)
            ->published();

        if (\Str::isUuid($article)) {
            $submissionQuery->where('id', $article);
        } elseif (is_numeric($article)) {
            $submissionQuery->where('seq_id', $article);
        } else {
            $submissionQuery->where('slug', $article);
        }

        $submission = $submissionQuery->firstOrFail();

        // Load galleys
        $submission->load('galleys');

        // Find the galley
        $publicationGalley = $submission->galleys->where('id', $galley)->first();

        if (!$publicationGalley) {
            abort(404, 'Galley not found');
        }

        // If it's a remote galley, redirect to URL
        if ($publicationGalley->is_remote && $publicationGalley->url_remote) {
            return redirect($publicationGalley->url_remote);
        }

        // Get the associated file
        if (!$publicationGalley->file_id) {
            abort(404, 'File not found');
        }

        $file = \App\Models\SubmissionFile::find($publicationGalley->file_id);

        if (!$file) {
            abort(404, 'File not found');
        }

        // Stream the file for download
        $disk = Storage::disk('local');

        if (!$disk->exists($file->file_path)) {
            abort(404, 'File not found on disk');
        }

        // =============================================
        // ANALYTICS: Log Download
        // =============================================
        $ip = request()->ip();
        $userAgent = strtolower(request()->userAgent() ?? '');
        $isBot = str_contains($userAgent, 'bot') || 
                 str_contains($userAgent, 'crawler') || 
                 str_contains($userAgent, 'spider');

        if (!$isBot) {
            $countryCode = 'ID'; // Mock (replace with GeoIP)
            $city = null;
            
            DB::table('article_metrics')->insert([
                'submission_id' => $submission->id,
                'type' => 'download',
                'ip_address' => $ip,
                'country_code' => $countryCode,
                'city' => $city,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Increment download counter
        // $publicationGalley->increment('views'); // Column 'views' does not exist

        // Return file stream with proper headers for Google Scholar
        $filename = $file->original_name;
        // SEO: Gunakan slug judul artikel sebagai nama file jika file adalah PDF
        if (str_contains(strtolower($file->mime_type ?? 'application/pdf'), 'pdf') || $publicationGalley->label === 'PDF') {
            $filename = \Illuminate\Support\Str::slug($submission->title) . '.pdf';
        }

        return response()->stream(
            function () use ($disk, $file) {
                $stream = $disk->readStream($file->file_path);
                fpassthru($stream);
                fclose($stream);
            },
            200,
            [
                'Content-Type' => $file->mime_type ?? 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Content-Length' => $disk->size($file->file_path),
                'Cache-Control' => 'public, max-age=86400',
            ]
        );
    }

    /**
     * Download an article PDF directly via SEO-friendly route.
     * Pattern: /{journal}/article/download/{seq_id}/{filename}.pdf
     * CRITICAL for Google Scholar indexing.
     */
    public function downloadPdf(string $journalSlug, string $seqId, string $filename)
    {
        $journal = $this->resolveJournal($journalSlug);

        // Resolve article by seq_id
        $submission = Submission::where('journal_id', $journal->id)
            ->where('seq_id', $seqId)
            ->published()
            ->firstOrFail();

        // Load galleys
        $submission->load('galleys');

        // Find the PDF galley
        $pdfGalley = $submission->galleys->firstWhere('label', 'PDF') 
            ?? $submission->galleys->where('file_type', 'galley')->first();

        if (!$pdfGalley) {
            abort(404, 'PDF Galley not found');
        }

        if ($pdfGalley->is_remote && $pdfGalley->url_remote) {
            return redirect($pdfGalley->url_remote);
        }

        if (!$pdfGalley->file_id) {
            abort(404, 'File not found');
        }

        $file = \App\Models\SubmissionFile::find($pdfGalley->file_id);

        if (!$file) {
            abort(404, 'File not found');
        }

        $disk = Storage::disk('local');

        if (!$disk->exists($file->file_path)) {
            abort(404, 'File not found on disk');
        }

        // Analytics
        if (!preg_match('/bot|crawler|spider/i', request()->userAgent() ?? '')) {
            DB::table('article_metrics')->insert([
                'submission_id' => $submission->id,
                'type' => 'download',
                'ip_address' => request()->ip(),
                'country_code' => 'ID',
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Return file stream with inline disposition for browser preview but with correct filename
        return response()->stream(
            function () use ($disk, $file) {
                $stream = $disk->readStream($file->file_path);
                fpassthru($stream);
                fclose($stream);
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"',
                'Content-Length' => $disk->size($file->file_path),
                'Cache-Control' => 'public, max-age=86400',
            ]
        );
    }

    /**
     * View a galley inline (for HTML galleys or embedded PDF viewer).
     */
    public function viewGalley(string $journalSlug, $article, $galley)
    {
        $journal = $this->resolveJournal($journalSlug);

        // Resolve article by ID or slug
        $submissionQuery = Submission::where('journal_id', $journal->id)
            ->published();

        if (Str::isUuid($article)) {
            $submissionQuery->where('id', $article);
        } elseif (is_numeric($article)) {
            $submissionQuery->where('seq_id', $article);
        } else {
            $submissionQuery->where('slug', $article);
        }

        $submission = $submissionQuery->firstOrFail();

        // Load galleys
        $submission->load(['galleys', 'authors', 'section', 'issue']);

        // Find the galley
        $publicationGalley = $submission->galleys->where('id', $galley)->first();

        if (!$publicationGalley) {
            abort(404, 'Galley not found');
        }

        // If it's a remote galley, redirect
        if ($publicationGalley->is_remote && $publicationGalley->url_remote) {
            return redirect($publicationGalley->url_remote);
        }

        // Get website settings
        $settings = $journal->getWebsiteSettings();

        // Increment view counter
        // $publicationGalley->increment('views'); // Column 'views' does not exist

        return view('journal.public.galley-viewer', [
            'journal' => $journal,
            'article' => $submission,
            'galley' => $publicationGalley,
            'issue' => $submission->issue,
            'settings' => $settings,
        ]);
    }

    /**
     * Export citation in RIS format (for EndNote, Zotero, Mendeley)
     */
    public function exportCitationRIS(string $journalSlug, $article)
    {
        $journal = $this->resolveJournal($journalSlug);

        // Resolve article by ID or slug
        $submission = Submission::where('journal_id', $journal->id)
            ->where(function ($q) use ($article) {
                $q->Where('slug', $article);
            })
            ->with(['authors', 'issue', 'section'])
            ->published()
            ->firstOrFail();

        $issue = $submission->issue;
        $publicationDate = $issue?->published_at ?? $submission->published_at;

        // Build RIS format
        $ris = "TY  - JOUR\n";
        $ris .= "TI  - {$submission->title}\n";
        
        // Authors
        foreach ($submission->authors as $author) {
            $lastName = $author->family_name ?? $author->last_name ?? '';
            $firstName = $author->given_name ?? $author->first_name ?? '';
            $ris .= "AU  - {$lastName}, {$firstName}\n";
        }

        // Publication details
        $ris .= "T2  - {$journal->name}\n";
        
        if ($publicationDate) {
            $ris .= "PY  - {$publicationDate->year}\n";
            $ris .= "DA  - {$publicationDate->format('Y/m/d')}\n";
        }
        
        if ($issue && $issue->volume) {
            $ris .= "VL  - {$issue->volume}\n";
        }
        
        if ($issue && $issue->number) {
            $ris .= "IS  - {$issue->number}\n";
        }
        
        if ($submission->pages) {
            $pages = explode('-', $submission->pages);
            $ris .= "SP  - " . trim($pages[0]) . "\n";
            if (isset($pages[1])) {
                $ris .= "EP  - " . trim($pages[1]) . "\n";
            }
        }
        
        if ($submission->doi) {
            $ris .= "DO  - {$submission->doi}\n";
            $ris .= "UR  - https://doi.org/{$submission->doi}\n";
        }
        
        if ($submission->abstract) {
            $abstractClean = strip_tags($submission->abstract);
            $ris .= "AB  - {$abstractClean}\n";
        }
        
        if ($submission->keywords && is_array($submission->keywords)) {
            foreach ($submission->keywords as $keyword) {
                $ris .= "KW  - {$keyword}\n";
            }
        }
        
        if ($journal->publisher) {
            $ris .= "PB  - {$journal->publisher}\n";
        }
        
        $ris .= "ER  - \n";

        return response($ris)
            ->header('Content-Type', 'application/x-research-info-systems')
            ->header('Content-Disposition', 'attachment; filename="' . \Str::slug($submission->title) . '.ris"');
    }

    /**
     * Export citation in BibTeX format
     */
    public function exportCitationBibTeX(string $journalSlug, $article)
    {
        $journal = $this->resolveJournal($journalSlug);

        // Resolve article by ID or slug
        $submission = Submission::where('journal_id', $journal->id)
            ->where(function ($q) use ($article) {
                $q->Where('slug', $article);
            })
            ->with(['authors', 'issue', 'section'])
            ->published()
            ->firstOrFail();

        $issue = $submission->issue;
        $publicationDate = $issue?->published_at ?? $submission->published_at;

        // Generate citation key (FirstAuthorLastName + Year)
        $firstAuthor = $submission->authors->first();
        $lastName = $firstAuthor ? ($firstAuthor->family_name ?? $firstAuthor->last_name ?? 'Author') : 'Author';
        $year = $publicationDate?->year ?? now()->year;
        $citationKey = Str::slug($lastName) . $year;

        // Build BibTeX format
        $bibtex = "@article{{$citationKey},\n";
        $bibtex .= "  title = {{{$submission->title}}},\n";
        
        // Authors
        $authors = $submission->authors->map(function($author) {
            $lastName = $author->family_name ?? $author->last_name ?? '';
            $firstName = $author->given_name ?? $author->first_name ?? '';
            return "{$firstName} {$lastName}";
        })->implode(' and ');
        $bibtex .= "  author = {{{$authors}}},\n";
        
        $bibtex .= "  journal = {{{$journal->name}}},\n";
        
        if ($publicationDate) {
            $bibtex .= "  year = {{{$publicationDate->year}}},\n";
        }
        
        if ($issue && $issue->volume) {
            $bibtex .= "  volume = {{{$issue->volume}}},\n";
        }
        
        if ($issue && $issue->number) {
            $bibtex .= "  number = {{{$issue->number}}},\n";
        }
        
        if ($submission->pages) {
            $bibtex .= "  pages = {{{$submission->pages}}},\n";
        }
        
        if ($submission->doi) {
            $bibtex .= "  doi = {{{$submission->doi}}},\n";
            $bibtex .= "  url = {{https://doi.org/{$submission->doi}}},\n";
        }
        
        if ($journal->publisher) {
            $bibtex .= "  publisher = {{{$journal->publisher}}},\n";
        }
        
        $bibtex .= "}\n";

        return response($bibtex)
            ->header('Content-Type', 'application/x-bibtex')
            ->header('Content-Disposition', 'attachment; filename="' . \Str::slug($submission->title) . '.bib"');
    }

    /**
     * Get settings with defaults merged.
     */
    private function getSettingsWithDefaults(Journal $journal): array
    {
        $defaults = [
            // Content
            'about' => '',
            'masthead' => ['about' => '', 'editorial_team' => ''],

            // Appearance
            'hero_image' => null,
            'primary_color' => '#4F46E5',
            'secondary_color' => '#7C3AED',

            // Hero Content
            'hero_title' => $journal->name,
            'hero_description' => $journal->description ?? 'A peer-reviewed scholarly journal dedicated to advancing knowledge and research.',
            'hero_tagline' => 'Peer-Reviewed • Open Access • Indexed',

            // Stats
            'stat_acceptance_rate' => '25%',
            'stat_review_time' => '4 Weeks',
            'stat_impact_factor' => 'N/A',
            'stat_citations' => '1000+',

            // Section Visibility
            'show_announcements' => true,
            'show_editorial_team' => true,
            'show_indexed_in' => true,
            'show_stats' => true,

            // Indexed In
            'indexed_in_images' => [],

            // Footer
            'footer_description' => $journal->description ?? 'A leading academic journal.',
            'social_facebook' => '',
            'social_twitter' => '',
            'social_linkedin' => '',
            'social_instagram' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'contact_address' => '',
        ];

        $actual = $journal->getWebsiteSettings();

        return array_merge($defaults, $actual);
    }

    // =====================================================
    // CUSTOM PAGES (Navigation Menu Items)
    // =====================================================

    /**
     * Display a custom page created via Navigation Menu Items.
     */
    /**
     * Display a custom page created via Navigation Menu Items or Sidebar Blocks (Custom Pages).
     */
    public function customPage(string $journalSlug, string $path): View
    {
        $journal = $this->resolveJournal($journalSlug);

        // 1. Try Navigation Menu Item
        $page = \App\Models\NavigationMenuItem::where('journal_id', $journal->id)
            ->where('type', 'page')
            ->where('path', $path)
            ->where('is_active', true)
            ->first();

        if ($page) {
            return view('journal.public.custom-page', [
                'journal' => $journal,
                'page' => $page,
                'content' => $page->content, // Assuming content field exists
                'title' => $page->title,
            ]);
        }

        // 2. Try Sidebar Block (Custom Page)
        $blockPage = \App\Models\SidebarBlock::where('journal_id', $journal->id)
            ->where('type', 'page')
            ->where('slug', $path)
            ->where('is_active', true)
            ->firstOrFail();

        return view('journal.public.custom-page', [
            'journal' => $journal,
            'page' => $blockPage, // Duck typing or separate variable
            'content' => $blockPage->content,
            'title' => $blockPage->show_title ? $blockPage->title : null,
        ]);
    }
}


