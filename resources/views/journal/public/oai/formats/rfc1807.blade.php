{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
{!! '<' . '?xml-stylesheet type="text/xsl" href="' . asset('oai2.xsl') . '" ?' . '>' !!}
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ now()->toIso8601String() }}</responseDate>
    <request verb="{{ $verb }}" metadataPrefix="rfc1807">{{ route('journal.oai', $journal->path) }}</request>

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
                    <rfc1807 xmlns="http://info.internet.isi.edu:80/in-notes/rfc/files/rfc1807.txt"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xsi:schemaLocation="http://info.internet.isi.edu:80/in-notes/rfc/files/rfc1807.txt http://www.openarchives.org/OAI/1.1/rfc1807.xsd">

                        <bib-version>v2.0</bib-version>
                        <id>{{ $record->id }}</id>
                        <entry>{{ \Carbon\Carbon::parse($record->publication->date_published)->format('F d, Y') }}
                        </entry>
                        <organization>{{ $journal->name }}</organization>
                        <title>{{ $record->publication->title }}</title>
                        <type>Research Article</type>

                        @foreach ($record->authors as $author)
                            <author>{{ $author->last_name }}, {{ $author->first_name }}</author>
                        @endforeach

                        <date>{{ \Carbon\Carbon::parse($record->publication->date_published)->format('Y-m-d') }}</date>
                        <abstract>{{ strip_tags($record->publication->abstract ?? '') }}</abstract>
                        <keyword>{{ $record->publication->keywords }}</keyword>
                        <period>Annual</period>
                        <monitoring>{{ $journal->publisher ?? 'Publisher' }}</monitoring>
                        <language>{{ $record->publication->locale ?? 'en' }}</language>

                    </rfc1807>
                </metadata>
            </record>
        @endforeach
        </{{ $verb }}>
</OAI-PMH>
