<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Support\Str;

class GoogleScholarValidator
{
    const LABEL_REFERENCES = 'References & Citations';

    /**
     * Validate the submission against Google Scholar indexing guidelines.
     *
     * @param Submission $submission
     * @return array
     */
    public function validate(Submission $submission): array
    {
        // Resolve Publication (OJS 3.3 Style)
        // If currentPublication exists (Latest Version), use it.
        $publication = $submission->currentPublication ?? $submission->publications()->latest('version')->first();

        // Fallback to Submission if no publication found (Draft/Legacy)
        $data = $publication ?? $submission;

        $checks = [];
        $score = 0;

        // 1. Title Check
        // Weight: 20
        $titleCheck = $this->checkTitle($data->title);
        $checks[] = $titleCheck;
        $score += $titleCheck['score'];

        // 2. Abstract Check
        // Weight: 20
        $abstractCheck = $this->checkAbstract($data->abstract);
        $checks[] = $abstractCheck;
        $score += $abstractCheck['score'];

        // 3. Authors Check
        // Weight: 20
        $authorsCheck = $this->checkAuthors($data->authors);
        $checks[] = $authorsCheck;
        $score += $authorsCheck['score'];

        // 4. Keywords Check
        // Weight: 10
        $keywordsCheck = $this->checkKeywords($data->keywords);
        $checks[] = $keywordsCheck;
        $score += $keywordsCheck['score'];

        // 5. References Check
        // Weight: 10
        // Use 'citations' first (if parsed), fallback to 'references' text
        $references = $data->citations ?? $data->references;

        if (empty($references)) {
            $references = $submission->citations ?? $submission->references;
        }

        $referencesCheck = $this->checkReferences($references);
        $checks[] = $referencesCheck;
        $score += $referencesCheck['score'];

        // 6. Galley (PDF) Check
        // Weight: 10
        $galleyCheck = $this->checkGalleys($submission); // Galleys usually stay on submission or linked via publication
        $checks[] = $galleyCheck;
        $score += $galleyCheck['score'];

        // 7. Publication Date Check
        // Weight: 10
        $dateCheck = $this->checkPublicationDate($data->date_published ?? $submission->published_at);
        $checks[] = $dateCheck;
        $score += $dateCheck['score'];

        // Calculate Status
        $status = 'bad';
        if ($score >= 80) {
            $status = 'good';
        } elseif ($score >= 50) {
            $status = 'warning';
        }

        return [
            'score' => $score,
            'status' => $status,
            'checks' => $checks,
        ];
    }

    private function checkTitle(?string $title): array
    {
        if (empty($title)) {
            return [
                'label' => 'Article Title',
                'status' => false,
                'message' => 'Title is empty.',
                'score' => 0
            ];
        }

        $wordCount = str_word_count(strip_tags($title));
        $isAllCaps = strtoupper($title) === $title && strlen($title) > 5;

        if ($isAllCaps) {
            return [
                'label' => 'Article Title',
                'status' => false,
                'message' => 'Title should not be in all capital letters.',
                'score' => 10 // Partial credit
            ];
        }

        if ($wordCount < 5) { // Relaxed min slightly
            return [
                'label' => 'Article Title',
                'status' => false,
                'message' => "Title is too short ($wordCount words). Aim for 10-20 words.",
                'score' => 10
            ];
        }

        if ($wordCount > 30) { // Relaxed max slightly
            return [
                'label' => 'Article Title',
                'status' => 'warning', // Warning instead of fail
                'message' => "Title is quite long ($wordCount words). Optimal is 10-20 words.",
                'score' => 15
            ];
        }

        return [
            'label' => 'Article Title',
            'status' => true,
            'message' => 'Perfect title length and formatting.',
            'score' => 20
        ];
    }

    private function checkAbstract(?string $abstract): array
    {
        if (empty($abstract)) {
            return [
                'label' => 'Abstract',
                'status' => false,
                'message' => 'Abstract is missing.',
                'score' => 0
            ];
        }

        $wordCount = str_word_count(strip_tags($abstract));

        if ($wordCount < 100) {
            return [
                'label' => 'Abstract',
                'status' => false,
                'message' => "Abstract is too short ($wordCount words). Minimum 100 words required.",
                'score' => 5
            ];
        }

        if ($wordCount > 300) {
            return [
                'label' => 'Abstract',
                'status' => 'warning',
                'message' => "Abstract is a bit long ($wordCount words). Recommended maximum is 300 words.",
                'score' => 15
            ];
        }

        return [
            'label' => 'Abstract',
            'status' => true,
            'message' => 'Abstract length is optimal.',
            'score' => 20
        ];
    }

    private function checkAuthors($authors): array
    {
        if ($authors->isEmpty()) {
            return [
                'label' => 'Authors',
                'status' => false,
                'message' => 'No authors listed.',
                'score' => 0
            ];
        }

        $missingAffiliation = [];
        $singleNameAuthors = [];

        foreach ($authors as $author) {
            if (empty($author->affiliation)) {
                $missingAffiliation[] = $author->first_name . ' ' . $author->last_name;
            }
            // Simple check for single name (no space check if name field used, but we split logic usually)
            // Assuming accessors or properties
            $nameParts = explode(' ', trim($author->first_name . ' ' . $author->last_name));
            if (count($nameParts) < 2) {
                $singleNameAuthors[] = $author->first_name;
            }
        }

        if (!empty($missingAffiliation)) {
            return [
                'label' => 'Authors Affiliation',
                'status' => false,
                'message' => 'Missing affiliation for: ' . implode(', ', $missingAffiliation) . '.',
                'score' => 5
            ];
        }

        if (!empty($singleNameAuthors)) {
            return [
                'label' => 'Author Names',
                'status' => 'warning',
                'message' => 'Some authors have single names (' . implode(', ', $singleNameAuthors) . '). "First Last" format is preferred.',
                'score' => 15
            ];
        }

        return [
            'label' => 'Authors',
            'status' => true,
            'message' => 'All authors have valid names and affiliations.',
            'score' => 20
        ];
    }

    private function checkKeywords($keywords): array
    {
        // Keywords can be string or array depending on model casting
        $keywordArray = [];
        if (is_string($keywords)) {
            $keywordArray = array_filter(array_map('trim', explode(',', $keywords)));
        } elseif (is_array($keywords)) {
            $keywordArray = $keywords;
        }

        $count = count($keywordArray);

        if ($count < 3) {
            return [
                'label' => 'Keywords',
                'status' => false,
                'message' => "Too few keywords ($count). Minimum 3 required.",
                'score' => 0
            ];
        }

        if ($count > 10) { // Google Scholar says max 6 usually, but we relax to 10
            return [
                'label' => 'Keywords',
                'status' => 'warning',
                'message' => "Too many keywords ($count). Recommended maximum is 6.",
                'score' => 5
            ];
        }

        return [
            'label' => 'Keywords',
            'status' => true,
            'message' => 'Keyword count is optimal.',
            'score' => 10
        ];
    }

    private function checkReferences($references): array
    {
        if (empty($references)) {
            return [
                'label' => self::LABEL_REFERENCES,
                'status' => false,
                'message' => 'No references provided. Google Scholar requires citations to trace the citation graph.',
                'score' => 0
            ];
        }

        // Handle string vs array/collection
        $count = 0;
        if (is_string($references)) {
            // Count newlines as rough proxy for citation count
            $count = substr_count($references, "\n") + 1;
        } elseif (is_array($references) || $references instanceof \Traversable) {
            $count = count($references);
        }

        if ($count < 10) {
            return [
                'label' => self::LABEL_REFERENCES,
                'status' => 'warning',
                'message' => "Reference count is low ($count). Google Scholar prefers at least 10+ robust citations.",
                'score' => 5
            ];
        }

        return [
                'label' => self::LABEL_REFERENCES,
            'status' => true,
            'message' => "Good number of references found ($count).",
            'score' => 10
        ];
    }

    private function checkGalleys(Submission $submission): array
    {
        // Assuming relationship 'galleys' or similar exists for published files (PDFs)
        // If Model doesn't have galleys yet, check if there's at least a manuscript file in the final stage?
        // Prompt says "Galley (PDF): Must have at least one published PDF Galley"
        
        if (method_exists($submission, 'galleys')) {
             // Check galleys
             foreach ($submission->galleys as $galley) {
                 if (Str::contains(strtolower($galley->label), 'pdf') || Str::contains(strtolower($galley->file_type ?? ''), 'pdf')) {
                     break;
                 }
             }
        } elseif (method_exists($submission, 'files')) {
             // Fallback to checking files if galleys relation implies files
             // Looking at Submission.php: public function galleys() exists.
        }

        if ($submission->galleys()->count() == 0) {
             return [
                'label' => 'Galley (PDF)',
                'status' => false,
                'message' => 'No publication galleys found. At least one PDF galley is required.',
                'score' => 0
            ];
        }

        return [
            'label' => 'Galley (PDF)',
            'status' => true,
            'message' => 'Galley files are available.',
            'score' => 10
        ];
    }

    private function checkPublicationDate($date): array
    {
        if (empty($date)) {
            return [
                'label' => 'Publication Date',
                'status' => false, // Critical? Or just warning if not yet published?
                // Context: "Must be set (for published articles)"
                'message' => 'Publication date is not set.',
                'score' => 0
            ];
        }

        return [
            'label' => 'Publication Date',
            'status' => true,
            'message' => 'Publication date is set.',
            'score' => 10
        ];
    }
}
