<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\Issue;
use Illuminate\Support\Str;

/**
 * MetadataManager Service
 * 
 * Generates SEO metadata for Google Scholar (Highwire Press tags)
 * and Google Search (Schema.org JSON-LD).
 * 
 * Reference: https://scholar.google.com/intl/en/scholar/inclusion.html
 */
class MetadataManager
{
    /**
     * Generate metadata based on page type.
     *
     * @param string $pageType 'article', 'journal', 'issue', 'author'
     * @param array|null $data Context data
     * @return array
     */
    public function generate(string $pageType, ?array $data = null): array
    {
        return match ($pageType) {
            'article' => $this->generateArticleMetadata($data),
            'journal' => $this->generateJournalMetadata($data),
            'issue' => $this->generateIssueMetadata($data),
            default => $this->generateDefaultMetadata($data),
        };
    }

    /**
     * Generate Highwire Press tags for article pages.
     * These are CRITICAL for Google Scholar indexing.
     *
     * @param array $data ['article' => Submission, 'journal' => Journal, 'issue' => Issue]
     * @return array
     */
    protected function generateArticleMetadata(array $data): array
    {
        $article = $data['article'] ?? null;
        $journal = $data['journal'] ?? null;
        $issue = $data['issue'] ?? null;

        if (!$article || !$journal) {
            return [];
        }

        $metadata = [
            'highwire' => [],
            'opengraph' => [],
            'twitter' => [],
            'schema' => null,
        ];

        // ============================================
        // HIGHWIRE PRESS TAGS (Google Scholar)
        // ============================================
        $metadata['highwire'] = [
            'citation_title' => $article->title,
            'citation_journal_title' => $journal->name,
            'citation_journal_abbrev' => $journal->abbreviation,
            'citation_publisher' => $journal->publisher,
            'citation_language' => $article->locale ?? 'en',
        ];

        // Publication Date (YYYY/MM/DD format - CRITICAL for Scholar)
        if ($issue && $issue->published_at) {
            $metadata['highwire']['citation_publication_date'] = $issue->published_at->format('Y/m/d');
            $metadata['highwire']['citation_date'] = $issue->published_at->format('Y/m/d');
        } elseif ($article->published_at) {
            $metadata['highwire']['citation_publication_date'] = $article->published_at->format('Y/m/d');
            $metadata['highwire']['citation_date'] = $article->published_at->format('Y/m/d');
        }

        // Volume & Issue
        if ($issue) {
            if ($issue->volume) {
                $metadata['highwire']['citation_volume'] = $issue->volume;
            }
            if ($issue->number) {
                $metadata['highwire']['citation_issue'] = $issue->number;
            }
        }

        // Page Numbers
        if ($article->pages) {
            $metadata['highwire']['citation_firstpage'] = $article->first_page ?? $article->pages;
            if ($article->last_page) {
                $metadata['highwire']['citation_lastpage'] = $article->last_page;
            }
        }

        // DOI (Critical for citation tracking)
        if ($article->doi) {
            $metadata['highwire']['citation_doi'] = $article->doi;
        }

        // ISSN
        if ($journal->issn_online) {
            $metadata['highwire']['citation_issn'] = $journal->issn_online;
        } elseif ($journal->issn_print) {
            $metadata['highwire']['citation_issn'] = $journal->issn_print;
        }

        // Authors (One tag per author - CRITICAL)
        $authorNames = [];
        if ($article->authors && $article->authors->isNotEmpty()) {
            foreach ($article->authors as $author) {
                $fullName = $author->full_name ?? ($author->given_name . ' ' . $author->family_name);
                $metadata['highwire']['citation_author'][] = $fullName;
                $authorNames[] = $fullName;
                
                if ($author->affiliation) {
                    $metadata['highwire']['citation_author_institution'][] = $author->affiliation;
                }
                if ($author->orcid) {
                    $metadata['highwire']['citation_author_orcid'][] = $author->orcid;
                }
            }
        }

        // Keywords
        if ($article->keywords && is_array($article->keywords)) {
            $metadata['highwire']['citation_keywords'] = implode('; ', $article->keywords);
        }

        // Abstract URL (landing page)
        $metadata['highwire']['citation_abstract_html_url'] = route('journal.article.view', [
            'journal' => $journal->slug,
            'article' => $article->seq_id,
        ]);

        // PDF URL (CRITICAL - must point to actual download, not view page)
        $pdfGalley = $article->galleys?->where('label', 'PDF')->first() 
            ?? $article->galleys?->first();
        
        if ($pdfGalley) {
            $metadata['highwire']['citation_pdf_url'] = route('journal.article.download', [
                'journal' => $journal->slug,
                'article' => $article->seq_id,
                'galley' => $pdfGalley->id,
            ]);
        }

        // ============================================
        // OPEN GRAPH TAGS
        // ============================================
        $metadata['opengraph'] = [
            'og:type' => 'article',
            'og:title' => $article->title,
            'og:description' => Str::limit(strip_tags($article->abstract ?? ''), 200),
            'og:url' => $metadata['highwire']['citation_abstract_html_url'],
            'og:site_name' => $journal->name,
            'article:published_time' => $issue?->published_at?->toIso8601String() ?? $article->published_at?->toIso8601String(),
            'article:section' => $article->section?->name,
        ];

        if ($article->cover_image_path) {
            $metadata['opengraph']['og:image'] = \Storage::url($article->cover_image_path);
        } elseif ($journal->logo_path) {
            $metadata['opengraph']['og:image'] = \Storage::url($journal->logo_path);
        }

        foreach ($authorNames as $author) {
            $metadata['opengraph']['article:author'][] = $author;
        }

        // ============================================
        // TWITTER CARD TAGS
        // ============================================
        $metadata['twitter'] = [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $article->title,
            'twitter:description' => Str::limit(strip_tags($article->abstract ?? ''), 200),
        ];

        // ============================================
        // SCHEMA.ORG JSON-LD (ScholarlyArticle)
        // ============================================
        $schemaAuthors = [];
        foreach ($authorNames as $author) {
            $schemaAuthors[] = [
                '@type' => 'Person',
                'name' => $author,
            ];
        }

        $metadata['schema'] = [
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            'headline' => $article->title,
            'name' => $article->title,
            'description' => Str::limit(strip_tags($article->abstract ?? ''), 300),
            'author' => $schemaAuthors,
            'datePublished' => $issue?->published_at?->toIso8601String() ?? $article->published_at?->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => $journal->publisher ?? $journal->name,
            ],
            'isPartOf' => [
                '@type' => 'PublicationIssue',
                'issueNumber' => $issue?->number,
                'isPartOf' => [
                    '@type' => 'PublicationVolume',
                    'volumeNumber' => $issue?->volume,
                    'isPartOf' => [
                        '@type' => 'Periodical',
                        'name' => $journal->name,
                        'issn' => $journal->issn_online ?? $journal->issn_print,
                    ],
                ],
            ],
            'mainEntityOfPage' => $metadata['highwire']['citation_abstract_html_url'],
        ];

        if ($article->doi) {
            $metadata['schema']['identifier'] = [
                '@type' => 'PropertyValue',
                'propertyID' => 'DOI',
                'value' => $article->doi,
            ];
            $metadata['schema']['sameAs'] = 'https://doi.org/' . $article->doi;
        }

        if ($article->cover_image_path) {
            $metadata['schema']['image'] = \Storage::url($article->cover_image_path);
        }

        return $metadata;
    }

    /**
     * Generate metadata for journal homepage.
     */
    protected function generateJournalMetadata(array $data): array
    {
        $journal = $data['journal'] ?? null;

        if (!$journal) {
            return [];
        }

        $metadata = [
            'opengraph' => [
                'og:type' => 'website',
                'og:title' => $journal->name,
                'og:description' => Str::limit($journal->description ?? '', 200),
                'og:url' => route('journal.public.home', $journal->slug),
                'og:site_name' => $journal->name,
            ],
            'twitter' => [
                'twitter:card' => 'summary',
                'twitter:title' => $journal->name,
                'twitter:description' => Str::limit($journal->description ?? '', 200),
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'Periodical',
                'name' => $journal->name,
                'description' => $journal->description,
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $journal->publisher ?? $journal->name,
                ],
                'issn' => $journal->issn_online ?? $journal->issn_print,
                'url' => route('journal.public.home', $journal->slug),
            ],
        ];

        if ($journal->logo_path) {
            $metadata['opengraph']['og:image'] = \Storage::url($journal->logo_path);
            $metadata['schema']['image'] = \Storage::url($journal->logo_path);
        }

        return $metadata;
    }

    /**
     * Generate metadata for issue pages.
     */
    protected function generateIssueMetadata(array $data): array
    {
        $journal = $data['journal'] ?? null;
        $issue = $data['issue'] ?? null;

        if (!$journal || !$issue) {
            return [];
        }

        $title = $issue->title ?? "Vol. {$issue->volume} No. {$issue->number} ({$issue->year})";

        return [
            'opengraph' => [
                'og:type' => 'website',
                'og:title' => $title . ' | ' . $journal->name,
                'og:description' => $issue->description ?? "Issue {$issue->number} of {$journal->name}",
                'og:url' => route('journal.public.issue', [$journal->slug, $issue->seq_id]),
                'og:site_name' => $journal->name,
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'PublicationIssue',
                'issueNumber' => $issue->number,
                'datePublished' => $issue->published_at?->toIso8601String(),
                'isPartOf' => [
                    '@type' => 'Periodical',
                    'name' => $journal->name,
                ],
            ],
        ];
    }

    /**
     * Default metadata.
     */
    protected function generateDefaultMetadata(?array $data): array
    {
        return [
            'opengraph' => [
                'og:type' => 'website',
                'og:site_name' => config('app.name', 'IAMJOS'),
            ],
        ];
    }

    /**
     * Render Highwire Press meta tags as HTML.
     */
    public function renderHighwireTags(array $highwire): string
    {
        $html = '';
        foreach ($highwire as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $html .= '<meta name="' . e($name) . '" content="' . e($item) . '">' . "\n    ";
                }
            } else {
                $html .= '<meta name="' . e($name) . '" content="' . e($value) . '">' . "\n    ";
            }
        }
        return $html;
    }

    /**
     * Render Open Graph meta tags as HTML.
     */
    public function renderOpenGraphTags(array $og): string
    {
        $html = '';
        foreach ($og as $property => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $html .= '<meta property="' . e($property) . '" content="' . e($item) . '">' . "\n    ";
                }
            } elseif ($value) {
                $html .= '<meta property="' . e($property) . '" content="' . e($value) . '">' . "\n    ";
            }
        }
        return $html;
    }

    /**
     * Render Twitter Card meta tags as HTML.
     */
    public function renderTwitterTags(array $twitter): string
    {
        $html = '';
        foreach ($twitter as $name => $value) {
            if ($value) {
                $html .= '<meta name="' . e($name) . '" content="' . e($value) . '">' . "\n    ";
            }
        }
        return $html;
    }

    /**
     * Render Schema.org JSON-LD as HTML.
     */
    public function renderSchemaJsonLd(?array $schema): string
    {
        if (!$schema) {
            return '';
        }

        return '<script type="application/ld+json">' . "\n" . 
               json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
               "\n</script>";
    }
}
