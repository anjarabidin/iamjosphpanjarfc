<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScholarCheckerService
{
    /**
     * Common user agents to rotate generally avoid immediate blocking.
     */
    protected array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:89.0) Gecko/20100101 Firefox/89.0',
    ];

    /**
     * Check if a submission is indexed in Google Scholar.
     *
     * @param Submission $submission
     * @return bool
     */
    public function isIndexed(Submission $submission): bool
    {
        // Placeholder for SerpApi implementation recommendation
        // For production, it is highly recommended to use SerpApi or similar services 
        // to avoid Google's captcha and IP bans.
        // Example: $url = "https://serpapi.com/search.json?engine=google_scholar&q=" ...

        try {
            $journalName = $submission->journal->name ?? '';
            $title = $submission->title;
            // Assuming there is a way to get the public URL of the submission.
            // Using a generic route helper for now, adjust based on actual route.
            $url = route('journal.public.article', ['journal' => $submission->journal->slug, 'article' => $submission->seq_id]);

            // SCHOLAR CHECK STRATEGY:
            // 1. If 'scholar_url' is monitored, use it as a PUBLIC ARTICLE URL to search.
            //    Query: q="https://journal.com/..." (Exact URL search)
            // 2. Fallback to Title + Journal Name search.

            if ($submission->indexStat && !empty($submission->indexStat->scholar_url)) {
                 $targetUrl = $submission->indexStat->scholar_url;
                 Log::info("ScholarCheckerService: Searching by Public URL: {$targetUrl}");
                 
                 // Construct Query with URL wrapped in quotes for exact match
                 // Example: "https://iamjos.test/jco/article/view/10"
                 $query = sprintf('"%s"', $targetUrl);
                 
                 $searchUrl = 'https://scholar.google.com/scholar';

                 $response = Http::withHeaders([
                    'User-Agent' => $this->getRandomUserAgent(),
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])->get($searchUrl, [
                    'q' => $query,
                    'hl' => 'en',
                ]);

                if ($response->successful()) {
                     $content = $response->body();
                     
                     // Check for blocking
                     if (str_contains($content, 'recaptcha') || str_contains($content, 'unusual traffic')) {
                        Log::warning('ScholarCheckerService: Blocking detected on URL search.');
                        // If blocked, we can't determine status, return false or throw exception?
                        // Return false conservatively.
                        return false;
                     }
                     
                     // Check "did not match any articles"
                     if (str_contains($content, 'did not match any articles')) {
                         Log::info("ScholarCheckerService: URL not found in index.");
                         return false;
                     }
                     
                     // Validation: Check if the Title appears in the snippet?
                     // If Google returns a result for the URL, it's likely the item.
                     // But to be safe, check if title exists.
                     // Normalize title for check (remove special chars if needed)
                     // Simple check:
                     if (stripos($content, $title) !== false) {
                         return true;
                     }

                     // If title is not found but we got results, it's ambiguous.
                     // It might be a citation or a different version.
                     // If the URL matched, it's arguably "indexed". 
                     // Let's assume TRUE if we got results for the specific URL.
                     return true;

                } else {
                     Log::warning("ScholarCheckerService: URL Search Request failed: " . $response->status());
                     return false;
                }
            }

            // FALLBACK TO SEARCH QUERY
            // Construct Query
            // Format: intitle:"{Article Title}" source:"{Journal Name}" OR info:{Article URL}
            // URL encoding is handled by Http client or manual implementation if needed.
            $query = sprintf('intitle:"%s" source:"%s" OR info:%s', $title, $journalName, $url);
            
            $searchUrl = 'https://scholar.google.com/scholar';

            $response = Http::withHeaders([
                'User-Agent' => $this->getRandomUserAgent(),
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ])->get($searchUrl, [
                'q' => $query,
                'hl' => 'en',
            ]);

            if ($response->failed()) {
                Log::warning('ScholarCheckerService: HTTP Request failed.', ['status' => $response->status()]);
                return false;
            }

            $content = $response->body();

            // Check for Captcha / Blocking
            if (str_contains($content, 'recaptcha') || str_contains($content, 'unusual traffic')) {
                Log::warning('ScholarCheckerService: Google Scholar blocking detected.');
                return false;
            }

            // Simple check: if we find the title or "No results", determine status.
            if (str_contains($content, 'did not match any articles')) {
                return false;
            }

            // If we see results (and not "did not match"), assume found.
            // A more robust check would scan for the specific result entry.
            // But if query is specific, any result is likely a match.
            return true;

        } catch (\Exception $e) {
            Log::error('ScholarCheckerService: Error checking index.', ['error' => $e->getMessage(), 'submission_id' => $submission->id]);
            return false; // Default to false on error, or maybe rethrow?
        }
    }

    protected function getRandomUserAgent(): string
    {
        return $this->userAgents[array_rand($this->userAgents)];
    }
}
