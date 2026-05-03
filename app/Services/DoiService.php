<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\Publication;
use App\Models\Issue;
use Illuminate\Support\Facades\Log;

/**
 * DOI Service
 * 
 * Handles DOI (Digital Object Identifier) generation and management
 * following OJS 3.3 DOI Plugin patterns.
 */
class DoiService
{
    /**
     * Generate a DOI for a publication based on journal settings.
     *
     * @param Publication $publication The publication to generate DOI for
     * @param Journal|null $journal Optional journal (defaults to submission's journal)
     * @return string|null The generated DOI or null if DOI is disabled
     */
    public static function generateForPublication(Publication $publication, ?Journal $journal = null): ?string
    {
        $submission = $publication->submission;
        $journal = $journal ?? $submission?->journal;

        if (!$journal || !$journal->doi_enabled) {
            return null;
        }

        // Check if articles should have DOIs
        $doiObjects = $journal->doi_objects ?? [];
        if (!in_array('articles', $doiObjects)) {
            return null;
        }

        $prefix = $journal->doi_prefix;
        if (!$prefix) {
            return null;
        }

        $suffix = self::generateSuffix($publication, $journal);
        
        return "{$prefix}/{$suffix}";
    }

    /**
     * Generate a DOI for a submission (via its current publication).
     *
     * @param Submission $submission The submission to generate DOI for
     * @param Journal|null $journal Optional journal (defaults to submission's journal)
     * @return string|null The generated DOI or null if DOI is disabled
     */
    public static function generateForSubmission(Submission $submission, ?Journal $journal = null): ?string
    {
        $publication = $submission->getCurrentPublication();
        if (!$publication) {
            return null;
        }
        return self::generateForPublication($publication, $journal);
    }

    /**
     * Generate a DOI for an issue based on journal settings.
     *
     * @param Issue $issue The issue to generate DOI for
     * @param Journal|null $journal Optional journal (defaults to issue's journal)
     * @return string|null The generated DOI or null if DOI is disabled
     */
    public static function generateForIssue(Issue $issue, ?Journal $journal = null): ?string
    {
        $journal = $journal ?? $issue->journal;

        if (!$journal || !$journal->doi_enabled) {
            return null;
        }

        // Check if issues should have DOIs
        $doiObjects = $journal->doi_objects ?? [];
        if (!in_array('issues', $doiObjects)) {
            return null;
        }

        $prefix = $journal->doi_prefix;
        if (!$prefix) {
            return null;
        }

        // Generate issue suffix
        $suffix = sprintf(
            '%s.v%di%d',
            $journal->path,
            $issue->volume ?? 0,
            $issue->number ?? 0
        );
        
        return "{$prefix}/{$suffix}";
    }

    /**
     * Generate suffix based on journal settings.
     *
     * @param Publication $publication
     * @param Journal $journal
     * @return string
     */
    protected static function generateSuffix(Publication $publication, Journal $journal): string
    {
        $suffixType = $journal->doi_suffix_type ?? 'default';

        switch ($suffixType) {
            case 'manual':
                // For manual, return existing suffix or a placeholder
                // The actual suffix should be set on the publication itself
                return $publication->doi_suffix ?? self::generateDefaultSuffix($publication, $journal);

            case 'custom_pattern':
                $pattern = $journal->doi_custom_pattern;
                if ($pattern) {
                    return self::parsePattern($pattern, $publication, $journal);
                }
                // Fall through to default if no pattern set

            case 'default':
            default:
                return self::generateDefaultSuffix($publication, $journal);
        }
    }

    /**
     * Generate default suffix pattern.
     * Format: {journal_path}.v{volume}i{issue}.{article_id}
     * Example: jti.v1i2.100
     *
     * @param Publication $publication
     * @param Journal $journal
     * @return string
     */
    protected static function generateDefaultSuffix(Publication $publication, Journal $journal): string
    {
        $volume = 0;
        $issueNumber = 0;
        $year = date('Y');

        // Try to get issue information
        $issue = $publication->issue;
        if ($issue) {
            $volume = $issue->volume ?? 0;
            $issueNumber = $issue->number ?? 0;
            $year = $issue->year ?? date('Y');
        }

        // Use submission ID for uniqueness
        $submissionId = $publication->submission_id ?? random_int(1000, 9999);

        return sprintf(
            '%s.v%di%d.%s',
            $journal->path,
            $volume,
            $issueNumber,
            substr($submissionId, -8) // Last 8 chars of UUID for readability
        );
    }

    /**
     * Parse a custom pattern and replace placeholders.
     * 
     * Available placeholders:
     * - %j: Journal path/slug
     * - %v: Volume number
     * - %i: Issue number
     * - %Y: Publication year
     * - %a: Article/Submission ID
     *
     * @param string $pattern
     * @param Publication $publication
     * @param Journal $journal
     * @return string
     */
    protected static function parsePattern(string $pattern, Publication $publication, Journal $journal): string
    {
        $volume = 0;
        $issueNumber = 0;
        $year = date('Y');

        $issue = $publication->issue;
        if ($issue) {
            $volume = $issue->volume ?? 0;
            $issueNumber = $issue->number ?? 0;
            $year = $issue->year ?? date('Y');
        }

        // Use last 8 chars of submission ID for readability
        $articleId = substr($publication->submission_id ?? '00000000', -8);

        $replacements = [
            '%j' => $journal->path,
            '%v' => $volume,
            '%i' => $issueNumber,
            '%Y' => $year,
            '%a' => $articleId,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $pattern
        );
    }

    /**
     * Regenerate all DOIs for a journal.
     * This clears and regenerates DOIs for all published publications.
     *
     * @param Journal $journal
     * @return array{success: int, failed: int, skipped: int}
     */
    public static function regenerateAll(Journal $journal): array
    {
        $stats = ['success' => 0, 'failed' => 0, 'skipped' => 0];

        if (!$journal->doi_enabled) {
            Log::warning("DOI regeneration skipped: DOI is not enabled for journal {$journal->path}");
            return $stats;
        }

        // Check if articles should have DOIs
        $doiObjects = $journal->doi_objects ?? [];
        if (!in_array('articles', $doiObjects)) {
            Log::warning("DOI regeneration skipped: Articles are not enabled for DOI in journal {$journal->path}");
            return $stats;
        }

        // Get all published submissions for this journal
        $submissions = Submission::where('journal_id', $journal->id)
            ->where('status', 'published')
            ->with(['publications' => function ($query) {
                $query->where('status', Publication::STATUS_PUBLISHED)
                      ->orderBy('version', 'desc');
            }])
            ->get();

        foreach ($submissions as $submission) {
            try {
                // Get the current (latest) publication
                $publication = $submission->publications->first();
                
                if (!$publication) {
                    $stats['skipped']++;
                    continue;
                }

                // Generate new DOI
                $newDoi = self::generateForPublication($publication, $journal);
                
                if ($newDoi) {
                    $publication->doi = $newDoi;
                    $publication->save();
                    $stats['success']++;
                } else {
                    $stats['skipped']++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to regenerate DOI for submission {$submission->id}: " . $e->getMessage());
                $stats['failed']++;
            }
        }

        Log::info("DOI regeneration completed for journal {$journal->path}: " . json_encode($stats));

        return $stats;
    }

    /**
     * Validate a DOI prefix format.
     * DOI prefixes must start with "10."
     *
     * @param string $prefix
     * @return bool
     */
    public static function validatePrefix(string $prefix): bool
    {
        return str_starts_with($prefix, '10.');
    }

    /**
     * Clear all DOIs for a journal.
     *
     * @param Journal $journal
     * @return int Number of DOIs cleared
     */
    public static function clearAll(Journal $journal): int
    {
        $submissionIds = Submission::where('journal_id', $journal->id)->pluck('id');
        
        return Publication::whereIn('submission_id', $submissionIds)
            ->whereNotNull('doi')
            ->update(['doi' => null]);
    }
}
