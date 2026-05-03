{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
{!! '<' . '?xml-stylesheet type="text/xsl" href="' . asset('oai.xsl') . '" ?' . '>' !!}
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z') }}</responseDate>
    <request{!! \App\Http\Controllers\Public\OaiController::getRequestAttributes() !!}>{{ url()->current() }}</request>
    <ListIdentifiers>
        @foreach ($records as $record)
            <header>
                <identifier>oai:{{ parse_url(config('app.url'), PHP_URL_HOST) }}:article/{{ $record->seq_id }}</identifier>
                <datestamp>{{ $record->updated_at->utc()->format('Y-m-d\TH:i:s\Z') }}</datestamp>
                <setSpec>{{ strtoupper($record->journal->abbreviation ?? 'JRN') }}:ART</setSpec>
            </header>
        @endforeach
    </ListIdentifiers>
</OAI-PMH>
