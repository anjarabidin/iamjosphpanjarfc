{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
{!! '<' . '?xml-stylesheet type="text/xsl" href="' . asset('oai2.xsl') . '" ?' . '>' !!}
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ now()->toIso8601String() }}</responseDate>
    <request verb="{{ $verb }}" metadataPrefix="marcxml">{{ route('journal.oai', $journal->path) }}</request>

    <{{ $verb }}>
        @foreach ($records as $record)
            <record>
                <header>
                    <identifier>oai:{{ parse_url(config('app.url'), PHP_URL_HOST) }}:article/{{ $record->seq_id }}
                    </identifier>
                    <datestamp>{{ \Carbon\Carbon::parse($record->publication->date_published)->format('Y-m-d') }}
                    </datestamp>
                    <setSpec>{{ $journal->path }}</setSpec>
                </header>
                <metadata>
                    <record xmlns="http://www.loc.gov/MARC21/slim" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">

                        {{-- Leader (Fixed Length Control Field) --}}
                        <leader>00000nam a22000007a 4500</leader>

                        {{-- 001: Control Number --}}
                        <controlfield tag="001">{{ $record->id }}</controlfield>

                        {{-- 008: Fixed Length Data Elements --}}
                        <controlfield tag="008">
                            {{ \Carbon\Carbon::parse($record->publication->date_published)->format('ymd') }} 2026 eng d
                        </controlfield>

                        {{-- 022: ISSN --}}
                        @if ($journal->issn)
                            <datafield tag="022" ind1="#" ind2="#">
                                <subfield code="a">{{ $journal->issn }}</subfield>
                            </datafield>
                        @endif

                        {{-- 100: Main Entry (First Author) --}}
                        @if ($record->authors->first())
                            <datafield tag="100" ind1="1" ind2="#">
                                <subfield code="a">{{ $record->authors->first()->last_name }},
                                    {{ $record->authors->first()->first_name }}</subfield>
                                <subfield code="e">author</subfield>
                            </datafield>
                        @endif

                        {{-- 245: Title --}}
                        <datafield tag="245" ind1="1" ind2="0">
                            <subfield code="a">{{ $record->publication->title }}</subfield>
                        </datafield>

                        {{-- 260: Publication Info --}}
                        <datafield tag="260" ind1="#" ind2="#">
                            <subfield code="b">{{ $journal->name }}</subfield>
                            <subfield code="c">
                                {{ \Carbon\Carbon::parse($record->publication->date_published)->format('Y') }}
                            </subfield>
                        </datafield>

                        {{-- 520: Abstract --}}
                        <datafield tag="520" ind1="3" ind2="#">
                            <subfield code="a">{{ strip_tags($record->publication->abstract ?? '') }}</subfield>
                        </datafield>

                        {{-- 700: Added Entries (Other Authors) --}}
                        @foreach ($record->authors->skip(1) as $author)
                            <datafield tag="700" ind1="1" ind2="#">
                                <subfield code="a">{{ $author->last_name }}, {{ $author->first_name }}
                                </subfield>
                                <subfield code="e">author</subfield>
                            </datafield>
                        @endforeach

                        {{-- 856: Electronic Location (URL) --}}
                        <datafield tag="856" ind1="4" ind2="0">
                            <subfield code="u">
                                {{ route('journal.article', ['journal' => $journal->path, 'id' => $record->id]) }}
                            </subfield>
                        </datafield>

                    </record>
                </metadata>
            </record>
        @endforeach
        </{{ $verb }}>
</OAI-PMH>
