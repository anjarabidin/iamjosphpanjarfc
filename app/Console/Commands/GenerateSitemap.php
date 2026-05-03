<?php

namespace App\Console\Commands;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\Issue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Sitemap Generator Command
 * 
 * Generates an XML sitemap for all published content including:
 * - Journal homepages
 * - Archive pages
 * - Issue pages
 * - Published article pages
 * 
 * Usage: php artisan sitemap:generate
 */
class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sitemap:generate {--journal= : Generate sitemap for specific journal slug only}';

    /**
     * The console command description.
     */
    protected $description = 'Generate XML sitemap for all published journals and articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🗺️  Starting sitemap generation...');

        $journalSlug = $this->option('journal');
        
        $journalsQuery = Journal::where('enabled', true)->where('visible', true);
        
        if ($journalSlug) {
            $journalsQuery->where('slug', $journalSlug);
        }
        
        $journals = $journalsQuery->get();

        if ($journals->isEmpty()) {
            $this->warn('No active journals found.');
            return Command::SUCCESS;
        }

        $urls = [];
        $now = now()->toW3cString();

        // Add main portal homepage
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => $now,
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        foreach ($journals as $journal) {
            $this->info("📚 Processing journal: {$journal->name}");

            // Journal Homepage
            $urls[] = [
                'loc' => route('journal.public.home', $journal->slug),
                'lastmod' => $journal->updated_at?->toW3cString() ?? $now,
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];

            // About Page
            $urls[] = [
                'loc' => route('journal.public.about', $journal->slug),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];

            // Editorial Team
            $urls[] = [
                'loc' => route('journal.public.editorial-team', $journal->slug),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];

            // Author Guidelines
            $urls[] = [
                'loc' => route('journal.public.author-guidelines', $journal->slug),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];

            // Archives
            $urls[] = [
                'loc' => route('journal.public.archives', $journal->slug),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];

            // Current Issue
            $urls[] = [
                'loc' => route('journal.public.current', $journal->slug),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];

            // Published Issues
            $issues = Issue::where('journal_id', $journal->id)
                ->where('is_published', true)
                ->orderBy('published_at', 'desc')
                ->get();

            $this->line("  Found {$issues->count()} published issues");

            foreach ($issues as $issue) {
                $urls[] = [
                    'loc' => route('journal.public.issue', [$journal->slug, $issue->seq_id]),
                    'lastmod' => $issue->updated_at?->toW3cString() ?? $issue->published_at?->toW3cString() ?? $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            }

            // Published Articles
            $articles = Submission::where('journal_id', $journal->id)
                ->where('status', 'published')
                ->whereNotNull('issue_id')
                ->orderBy('published_at', 'desc')
                ->get();

            $this->line("  Found {$articles->count()} published articles");

            foreach ($articles as $article) {
                $urls[] = [
                    'loc' => route('journal.article.view', [
                        'journal' => $journal->slug, 
                        'article' => $article->seq_id
                    ]),
                    'lastmod' => $article->updated_at?->toW3cString() ?? $article->published_at?->toW3cString() ?? $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }
        }

        // Generate XML
        $xml = $this->generateXml($urls);

        // Save to public directory
        $path = public_path('sitemap.xml');
        File::put($path, $xml);

        $this->newLine();
        $this->info("✅ Sitemap generated successfully!");
        $this->info("📍 Location: {$path}");
        $this->info("🔗 URL: " . url('/sitemap.xml'));
        $this->info("📊 Total URLs: " . count($urls));

        return Command::SUCCESS;
    }

    /**
     * Generate the XML sitemap content.
     */
    protected function generateXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            
            if (!empty($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            }
            
            if (!empty($url['changefreq'])) {
                $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            }
            
            if (!empty($url['priority'])) {
                $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            }
            
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>' . PHP_EOL;

        return $xml;
    }
}
