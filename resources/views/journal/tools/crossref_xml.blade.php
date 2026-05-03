@php
    $escape = function ($string) {
        if (empty($string)) return '';
        $decoded = htmlspecialchars_decode(trim($string), ENT_QUOTES);
        return htmlspecialchars($decoded, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    };
@endphp
<doi_batch xmlns="http://www.crossref.org/schema/4.3.6" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:jats="http://www.ncbi.nlm.nih.gov/JATS1" xmlns:ai="http://www.crossref.org/AccessIndicators.xsd" version="4.3.6"
    xsi:schemaLocation="http://www.crossref.org/schema/4.3.6 https://www.crossref.org/schemas/crossref4.3.6.xsd">

    <head>
        <doi_batch_id>{{ $batchId }}</doi_batch_id>
        <timestamp>{{ time() }}</timestamp>
        <depositor>
            <depositor_name>{!! $escape($journal->getSetting('crossref_depositor_name') ?: $journal->name) !!}</depositor_name>
            <email_address>{!! $escape($journal->getSetting('crossref_depositor_email') ?: ($journal->email ?: 'admin@example.com')) !!}</email_address>
        </depositor>
        <registrant>{!! $escape($journal->publisher ?? $journal->name) !!}</registrant>
    </head>

    <body>
        <journal>
            <journal_metadata>
                <full_title>{!! $escape($journal->name) !!}</full_title>
                <abbrev_title>{!! $escape($journal->abbreviation ?? $journal->name) !!}</abbrev_title>
                @if ($journal->issn_online)
                    <issn media_type="electronic">{{ $journal->issn_online }}</issn>
                @endif
                @if ($journal->issn_print)
                    <issn media_type="print">{{ $journal->issn_print }}</issn>
                @endif
            </journal_metadata>

            {{-- ISSUE DATA (Diambil dari artikel pertama yg dipilih) --}}
            @php $firstItem = $submissions->first(); @endphp
            @if ($firstItem && $firstItem->issue)
                <journal_issue>
                    <publication_date media_type="online">
                        <month>{{ $firstItem->issue->month ?? date('m') }}</month>
                        <day>{{ $firstItem->issue->day ?? date('d') }}</day>
                        <year>{{ $firstItem->issue->year ?? date('Y') }}</year>
                    </publication_date>
                    @if ($firstItem->issue->volume)
                    <journal_volume>
                        <volume>{{ $firstItem->issue->volume }}</volume>
                    </journal_volume>
                    @endif
                    @if ($firstItem->issue->number)
                    <issue>{{ $firstItem->issue->number }}</issue>
                    @endif
                </journal_issue>
            @endif

            {{-- ARTICLES LOOP --}}
            @foreach ($submissions as $article)
                @php
                    $pub = $article->currentPublication;
                @endphp
                @if ($pub)
                <journal_article publication_type="full_text" metadata_distribution_opts="any">

                    <titles>
                        <title>{!! $escape($pub->title) !!}</title>
                    </titles>

                    <contributors>
                        @foreach ($pub->authors as $index => $author)
                            @php
                                // Name Parsing Logic
                                $fullName = trim($author->first_name . ' ' . $author->last_name);
                                $parts = explode(' ', $fullName);
                                $surname = '';
                                $givenName = '';

                                if (count($parts) === 1) {
                                    $surname = $parts[0];
                                    $givenName = ''; // Mononym
                                } else {
                                    $surname = array_pop($parts); // Last part is surname
                                    $givenName = implode(' ', $parts); // Remaining parts are given name
                                }
                            @endphp
                            <person_name contributor_role="author"
                                sequence="{{ $index === 0 ? 'first' : 'additional' }}">
                                @if(!empty($givenName))
                                <given_name>{!! $escape($givenName) !!}</given_name>
                                @endif
                                <surname>{!! $escape($surname) !!}</surname>
                                @if ($author->orcid)
                                    @php $cleanOrcid = preg_replace('/^https?:\/\/(www\.)?orcid\.org\//', '', trim($author->orcid)); @endphp
                                    <ORCID authenticated="true">https://orcid.org/{{ $cleanOrcid }}</ORCID>
                                @elseif ($author->user && $author->user->orcid_id)
                                    @php $cleanOrcid = preg_replace('/^https?:\/\/(www\.)?orcid\.org\//', '', trim($author->user->orcid_id)); @endphp
                                    <ORCID authenticated="true">https://orcid.org/{{ $cleanOrcid }}</ORCID>
                                @endif
                            </person_name>
                        @endforeach
                    </contributors>

                    @if($pub->abstract)
                    <jats:abstract>
                        <jats:p>{!! $escape(strip_tags($pub->abstract)) !!}</jats:p>
                    </jats:abstract>
                    @endif

                    <publication_date media_type="online">
                        @if ($pub->date_published)
                            <month>{{ $pub->date_published->format('m') }}</month>
                            <day>{{ $pub->date_published->format('d') }}</day>
                            <year>{{ $pub->date_published->format('Y') }}</year>
                        @endif
                    </publication_date>

                    {{-- Pages (Optional) --}}
                    @if ($pub->pages)
                        <pages>
                            <first_page>{{ explode('-', $pub->pages)[0] ?? '' }}</first_page>
                            <last_page>{{ explode('-', $pub->pages)[1] ?? '' }}</last_page>
                        </pages>
                    @endif

                    {{-- Access Indicators Namespace --}}
                    <ai:program name="AccessIndicators">
                        <ai:license_ref>https://creativecommons.org/licenses/by/4.0</ai:license_ref>
                    </ai:program>

                    <doi_data>
                        {{-- DOI Logic: Use existing DOI or Generate generic fallback --}}
                        <doi>{{ $pub->doi ? trim($pub->doi) : ($journal->doi_prefix ? trim($journal->doi_prefix) . '/' . trim($journal->path) . '.v' . ($article->issue->volume ?? '0') . 'i' . ($article->issue->number ?? '0') . '.' . $article->id : '10.xxxx/' . trim($journal->path) . '.v' . ($article->issue->volume ?? '0') . 'i' . ($article->issue->number ?? '0') . '.' . $article->id) }}</doi>
                        <resource>{{ trim(route('journal.public.article', ['journal' => $journal->slug, 'article' => $pub->seq_id ?? $article->seq_id])) }}</resource>
                    </doi_data>

                    {{-- REFERENCES / CITATION LIST --}}
                    @php
                        $activeReferences = !empty($pub->references) ? $pub->references : $article->references;
                    @endphp
                    @if (!empty($activeReferences))
                        <citation_list>
                            @php
                                // Clean and split references
                                $refs = preg_split('/\r\n|\r|\n/', $activeReferences);
                                $refs = array_filter($refs, fn($value) => !is_null($value) && trim($value) !== '');
                                $counter = 1;
                            @endphp
                            @foreach ($refs as $ref)
                                @php
                                    $ref = trim($ref);
                                    $key = 'ref' . $counter++;
                                    $doi = null;

                                    // Regex to find a DOI (stand-alone or inside a URL like https://doi.org/...)
                                    // Pattern matches strings starting with '10.' followed by digits, slash, and valid chars
                                    if (preg_match('/(10\.\d{4,9}\/[-._;()\/:\w]+)/', $ref, $matches)) {
                                        $doi = $matches[1];
                                    }
                                @endphp
                                <citation key="{{ $key }}">
                                    @if ($doi)
                                        <doi>{{ trim($doi) }}</doi>
                                    @else
                                        <unstructured_citation>{!! $escape($ref) !!}</unstructured_citation>
                                    @endif
                                </citation>
                            @endforeach
                        </citation_list>
                    @endif

                </journal_article>
                @endif
            @endforeach

        </journal>
    </body>
</doi_batch>
