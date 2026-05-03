{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
{!! '<' . '?xml-stylesheet type="text/xsl" href="' . asset('oai.xsl') . '"?' . '>' !!}
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z') }}</responseDate>
    <request{!! \App\Http\Controllers\Public\OaiController::getRequestAttributes() !!}>{{ url()->current() }}</request>
    <GetRecord>
        <record>
            <header>
                <identifier>oai:{{ parse_url(config('app.url'), PHP_URL_HOST) }}:article/{{ $record->seq_id }}</identifier>
                <datestamp>{{ $record->updated_at->utc()->format('Y-m-d\TH:i:s\Z') }}</datestamp>
                <setSpec>{{ strtoupper($journal->abbreviation ?? 'JRN') }}:ART</setSpec>
            </header>
            <metadata>
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                    xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
                    <dc:title>{!! htmlspecialchars($record->title, ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:title>
                    @if(isset($record->authors) && is_iterable($record->authors))
                        @foreach ($record->authors as $author)
                            @php
                                $creatorName = trim(($author->first_name ?? '') . ' ' . ($author->last_name ?? ''));
                            @endphp
                            @if($creatorName)
                                <dc:creator>{!! htmlspecialchars($creatorName, ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:creator>
                            @endif
                        @endforeach
                    @else
                        <dc:creator>Unknown</dc:creator>
                    @endif
                    {{-- Subject --}}
                    @if(isset($record->keywords) && is_iterable($record->keywords))
                        @foreach ($record->keywords as $keywordModel)
                            @php
                                $keyword = trim($keywordModel->content ?? $keywordModel->keyword ?? $keywordModel->name ?? '');
                            @endphp
                            @if ($keyword)
                                <dc:subject>{{ htmlspecialchars($keyword, ENT_XML1 | ENT_QUOTES, 'UTF-8') }}</dc:subject>
                            @endif
                        @endforeach
                    @endif
                    <dc:description>{!! htmlspecialchars(strip_tags(html_entity_decode($record->abstract)), ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:description>
                    <dc:publisher>{!! htmlspecialchars($journal->name, ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:publisher>
                    <dc:date>
                        {{ ($record->currentPublication && $record->currentPublication->date_published) ? \Carbon\Carbon::parse($record->currentPublication->date_published)->format('Y-m-d') : '' }}
                    </dc:date>
                    <dc:type>info:eu-repo/semantics/article</dc:type>
                    <dc:type>info:eu-repo/semantics/publishedVersion</dc:type>
                    <dc:format>application/pdf</dc:format>
                    @if ($record->currentPublication && $record->currentPublication->pages)
                        <dc:format>{!! htmlspecialchars($record->currentPublication->pages, ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:format>
                    @endif

                    {{-- DOI --}}
                    @if ($record->currentPublication && $record->currentPublication->doi)
                        <dc:identifier>{!! htmlspecialchars('https://doi.org/' . $record->currentPublication->doi, ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:identifier>
                        <dc:identifier>{!! htmlspecialchars('doi:' . $record->currentPublication->doi, ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:identifier>
                    @endif

                    {{-- Source / Issue Info --}}
                    @if ($record->issue)
                        @php
                            $sourceInfo = $journal->name . '; Vol. ' . ($record->issue->volume ?? '') . ' No. ' . ($record->issue->number ?? '') . ' (' . ($record->issue->year ?? '') . ')';
                        @endphp
                        <dc:source>{!! htmlspecialchars($sourceInfo, ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:source>
                    @endif

                    {{-- Identifier (Slug URL) --}}
                    <dc:identifier>{!! htmlspecialchars(route('journal.public.article', ['journal' => $journal->slug, 'article' => $record->seq_id]), ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:identifier>

                    {{-- Rights (License) --}}
                    @if ($journal->license_url)
                        <dc:rights>{{ htmlspecialchars($journal->license_url, ENT_XML1 | ENT_QUOTES, 'UTF-8') }}</dc:rights>
                    @elseif ($journal->license_terms)
                        <dc:rights>{{ htmlspecialchars($journal->license_terms, ENT_XML1 | ENT_QUOTES, 'UTF-8') }}</dc:rights>
                    @endif

                    {{-- Relation (PDF URL) --}}
                    @php
                        $pdf = $record->galleys->first();
                    @endphp
                    @if ($pdf)
                        <dc:relation>{!! htmlspecialchars(route('journal.article.galley', ['journal' => $journal->slug, 'article' => $record->seq_id, 'galley' => $pdf->id]), ENT_XML1 | ENT_QUOTES, 'UTF-8') !!}</dc:relation>
                    @endif

                    <dc:language>{{ $record->locale ?? 'en' }}</dc:language>
                </oai_dc:dc>
            </metadata>
        </record>
    </GetRecord>
</OAI-PMH>
