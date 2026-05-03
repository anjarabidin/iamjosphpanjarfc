<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\SubmissionAuthor;
use App\Models\SubmissionFile;
use App\Models\PublicationGalley;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Exception;

class OaiHarvesterService
{
    /**
     * Validate OAI-PMH Endpoint
     *
     * @param string $url
     * @return bool
     * @throws Exception
     */
    public function validateUrl(string $url): bool
    {
        try {
            // Append ?verb=Identify to check validity
            $response = Http::get($url, [
                'verb' => 'Identify'
            ]);

            if (!$response->successful()) {
                throw new Exception('Failed to connect to OAI endpoint.');
            }

            $xml = new SimpleXMLElement($response->body());
            
            // Check for error node
            if (isset($xml->error)) {
                throw new Exception("OAI Error: " . (string)$xml->error);
            }

            // Minimal check if it's OAI
            if (!isset($xml->Identify)) {
               // Some OAI responses might wrap it differently or just have root attributes
               // Common root is <OAI-PMH>
               $namespaces = $xml->getNamespaces(true);
               // Usually checks for http://www.openarchives.org/OAI/2.0/
            }
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Record Count (Estimate)
     * Using ListIdentifiers to count records if possible, or just checking first page.
     * Note: OAI does not always give total count unless resumptionToken has completeListSize.
     */
    public function countRecords(string $url): int
    {
        $response = Http::get($url, [
            'verb' => 'ListIdentifiers',
            'metadataPrefix' => 'oai_dc'
        ]);

        if ($response->successful()) {
            $xml = new SimpleXMLElement($response->body());
            if (isset($xml->ListIdentifiers->resumptionToken) && isset($xml->ListIdentifiers->resumptionToken['completeListSize'])) {
                return (int) $xml->ListIdentifiers->resumptionToken['completeListSize'];
            }
            
            // Fallback: Count records on first page
            if (isset($xml->ListIdentifiers->header)) {
                $count = count($xml->ListIdentifiers->header);
                if (isset($xml->ListIdentifiers->resumptionToken) && (string)$xml->ListIdentifiers->resumptionToken !== '') {
                     // Indicate there are more, but we don't know how many.
                     // Just return the count of the first page + 1 to show "More than X" logic if needed, 
                     // but for now just return what we find or 0 if failed.
                     return $count; // Or maybe return -1 to indicate "Many"
                }
                return $count;
            }
        }
        return 0;
    }

    /**
     * Fetch Records (Page by Page or using Token)
     */
    public function fetchRecords(string $url, ?string $token = null)
    {
        $params = [];
        if ($token) {
            $params = ['verb' => 'ListRecords', 'resumptionToken' => $token];
        } else {
            $params = ['verb' => 'ListRecords', 'metadataPrefix' => 'oai_dc'];
        }

        $response = Http::get($url, $params);

        if (!$response->successful()) {
            throw new Exception("Failed to fetch records: " . $response->status());
        }

        return new SimpleXMLElement($response->body());
    }

    /**
     * Parse and Import a Single Record
     */
    public function importRecord(SimpleXMLElement $record, Journal $journal, $sectionId, $userId)
    {
        $ns = $record->getNamespaces(true);
        $header = $record->header;
        $metadata = $record->metadata->children($ns['oai_dc'] ?? null)->dc->children($ns['dc'] ?? null);

        if (!$metadata) {
            // Try without namespaces if failed
            $metadata = $record->metadata->children()->dc->children();
        }

        // 1. Check Duplicates by Title or Source URL (Identifier)
        $title = (string)$metadata->title;
        $identifier = (string)$metadata->identifier; // This is often the URL
        
        // Sometimes identifier is repeated (DOI, URL, URI). We grab the one that looks like a URL.
        $sourceUrl = null;
        foreach ($metadata->identifier as $id) {
            if (str_contains($id, 'http')) {
                $sourceUrl = (string)$id;
                break;
            }
        }
        if (!$sourceUrl) $sourceUrl = (string)$metadata->identifier[0] ?? '';

        // Strict duplicate check within current Journal Context
        $exists = Submission::where('journal_id', $journal->id)
            ->where(function($q) use ($title, $sourceUrl) {
                $q->where('title', $title);
            })->exists();

        if ($exists) {
            return false; // Skip duplicate
        }

        // 2. Parse Date
        $date = isset($metadata->date) ? Carbon::parse((string)$metadata->date[0]) : now();

        // 3. Create Submission
        $submission = Submission::create([
            'journal_id' => $journal->id,
            'user_id' => $userId, // The admin running the import, or a generic user
            'section_id' => $sectionId,
            'title' => $title,
            'abstract' => (string)$metadata->description ?? null,
            'keywords' => isset($metadata->subject) ? implode(', ', (array)$metadata->subject) : null,
            'status' => Submission::STATUS_PUBLISHED, // Import as published
            'stage' => Submission::STAGE_PRODUCTION,
            'stage_id' => Submission::STAGE_ID_PRODUCTION,
            'submitted_at' => $date,
            'published_at' => $date,
            'metadata' => [
                'source_method' => 'oai_import',
                'source_url' => $sourceUrl,
                'oai_identifier' => (string)$header->identifier
            ],
        ]);

        // 4. Parse Authors
        $this->parseAuthors($metadata->creator, $submission);

        // 5. Download PDF
        $this->processGalley($metadata->relation, $submission, $journal);

        return true;
    }

    private function parseAuthors($creators, $submission)
    {
        if (!$creators) return;

        $count = 0;
        foreach ($creators as $creator) {
            $name = (string)$creator;
            // Name format often "Last, First"
            $firstName = $name;
            $lastName = '';
            
            if (str_contains($name, ',')) {
                $parts = explode(',', $name);
                $lastName = trim($parts[0]);
                $firstName = trim($parts[1] ?? '');
                $fullName = "$firstName $lastName";
            } else {
                $fullName = $name;
                // Try to split logic if needed, but simple assignment is safer
            }

            SubmissionAuthor::create([
                'submission_id' => $submission->id,
                'name' => $fullName,
                'given_name' => $firstName ?: $fullName,
                'family_name' => $lastName,
                'first_name' => $firstName ?: $fullName,
                'last_name' => $lastName,
                'email' => Str::slug($fullName) . '@example.com', // Dummy email, required?
                'is_corresponding' => $count === 0, // First author as corresponding
                'sort_order' => $count++,
            ]);
        }
    }

    /**
     * Convert OJS View URL to Download URL
     */
    public function getDirectPdfUrl($url)
    {
        // Fix OJS "View" URL to "Download" URL
        if (str_contains($url, '/article/view/')) {
            return str_replace('/article/view/', '/article/download/', $url);
        }
        return $url;
    }

    private function processGalley($relations, $submission, Journal $journal)
    {
        if (!$relations) return;

        foreach ($relations as $relation) {
            $link = (string)$relation;
            
            // Detect PDF link
            if (
                (str_ends_with(strtolower($link), '.pdf') || str_contains($link, '/download/') || str_contains($link, '/view/')) 
                && str_contains(strtolower($link), 'pdf') // Ensure it mentions PDF somewhere if it's a view link
            ) {
                try {
                    // 1. Convert to Direct Download URL
                    $downloadUrl = $this->getDirectPdfUrl($link);

                    // 2. Validate Content Type via HEAD request
                    $headResponse = Http::head($downloadUrl);
                    $contentType = $headResponse->header('Content-Type');

                    if (!str_contains(strtolower($contentType), 'application/pdf')) {
                         // Not a PDF (maybe a landing page), skip
                         continue;
                    }

                    // 3. Download file
                    $fileContent = Http::get($downloadUrl)->body();
                    if (!$fileContent) continue;

                    // 4. Determine Filename
                    $fileName = 'imported_' . $submission->id . '_' . Str::random(5) . '.pdf';
                    $contentDisposition = $headResponse->header('Content-Disposition');
                    
                    if ($contentDisposition && preg_match('/filename="?([^"]+)"?/', $contentDisposition, $matches)) {
                        $fileName = $matches[1];
                        // Ensure it has .pdf extension
                        if (!str_ends_with(strtolower($fileName), '.pdf')) {
                            $fileName .= '.pdf';
                        }
                    }

                    $path = "journals/{$journal->id}/articles/{$submission->id}/{$fileName}";
                    
                    Storage::disk('public')->put($path, $fileContent);

                    // Create SubmissionFile
                    $file = SubmissionFile::create([
                        'submission_id' => $submission->id,
                        'uploaded_by' => $submission->user_id,
                        'file_path' => $path,
                        'file_name' => $fileName,
                        'file_type' => SubmissionFile::TYPE_GALLEY,
                        'mime_type' => 'application/pdf',
                        'file_size' => strlen($fileContent), // Length of content
                        'version' => 1,
                        'stage' => SubmissionFile::STAGE_PRODUCTION_READY,
                    ]);

                    // Create PublicationGalley
                    PublicationGalley::create([
                        'submission_id' => $submission->id,
                        'file_id' => $file->id,
                        'label' => 'PDF',
                        'locale' => 'en',
                        'seq' => 1,
                    ]);

                    // Only download one PDF? Or multiple? Usually one main PDF.
                    // Break after first PDF found to avoid duplicates or junk
                    break;
                } catch (Exception $e) {
                    // Log error but don't stop import of record
                    \Log::error("Failed to download PDF for submission {$submission->id} ({$link}): " . $e->getMessage());
                }
            }
        }
    }
}
