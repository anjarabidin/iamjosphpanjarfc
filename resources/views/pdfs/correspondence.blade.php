<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Correspondence Proof</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm;
        }
        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
        }
        .journal-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .journal-meta {
            font-size: 10pt;
        }
        h1.doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        .meta-label {
            width: 150px;
            font-weight: bold;
        }
        .authors-list sup {
            font-size: 0.7em;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        .log-table th, .log-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        .log-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            font-size: 9pt;
            text-align: center; 
            font-style: italic;
        }
        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="header">
        @if(isset($branding['logo_path']) && file_exists($branding['logo_path']))
            <img src="{{ $branding['logo_path'] }}" style="max-height: 60px; margin-bottom: 10px;">
        @endif
        <div class="journal-name">{{ $journal->name }}</div>
        <div class="journal-meta">
            e-ISSN: {{ $journal->issn ?? '-' }} | p-ISSN: {{ $journal->pissn ?? '-' }}<br>
            {{ $journal->publisher ?? '' }}
        </div>
    </div>

    <h1 class="doc-title">BUKTI KORESPONDENSI ARTIKEL JURNAL INTERNASIONAL BEREPUTASI</h1>

    <table class="meta-table">
        <tr>
            <th class="meta-label">Judul Artikel</td>
            <td>: {{ $submission->title }}</td>
        </tr>
        <tr>
            <th class="meta-label">Jurnal</td>
            <td>: {{ $journal->name }}</td>
        </tr>
        <tr>
            <th class="meta-label">Penulis</td>
            <td class="authors-list">
                @php
                    $authors = $submission->authors;
                    // Group affiliations to assign numbers
                    $affiliations = [];
                    $authorStrings = [];
                    foreach ($authors as $author) {
                        $affil = $author->affiliation ?? 'Unknown Affiliation';
                        if (!in_array($affil, $affiliations)) {
                            $affiliations[] = $affil;
                        }
                        $index = array_search($affil, $affiliations) + 1;
                        $authorStrings[] = $author->given_name . ' ' . $author->family_name . '<sup>' . $index . '</sup>';
                    }
                @endphp
                : {!! implode(', ', $authorStrings) !!}
            </td>
        </tr>
        <tr>
            <th class="meta-label">Afiliasi</td>
            <td>
                @foreach($affiliations as $key => $affil)
                    <div><sup>{{ $key + 1 }}</sup> {{ $affil }}</div>
                @endforeach
            </td>
        </tr>
        <tr>
            <th class="meta-label">DOI</td>
            <td>: {{ $publication->doi ?? '-' }}</td>
        </tr>
         @if($submission->issue)
        <tr>
            <th class="meta-label">Terbitan</td>
            <td>: Vol {{ $submission->issue->volume ?? '-' }}, No {{ $submission->issue->number ?? '-' }} ({{ $submission->issue->year ?? '-' }})</td>
        </tr>
        @endif
        <tr>
            <th class="meta-label">Tanggal Terbit</td>
            <td>: {{ optional($submission->published_at)->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
    </table>

    <div style="margin-bottom: 20px;">
        <strong>Kronologi Korespondensi (Chronology of Coresspondence):</strong>
    </div>

    <table class="log-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tanggal (Date)</th>
                <th style="width: 55%;">Aktivitas (Activity)</th>
                <th style="width: 20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log['date'] }}</td>
                <td>
                    {{ $log['description'] }}
                </td>
                <td style="text-align: center;">{{ $log['status'] }}</td>
            </tr>
            @endforeach
            @if(count($logs) === 0)
            <tr>
                <td colspan="3" style="text-align: center;">Tidak ada aktivitas tercatat.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem jurnal {{ $journal->name }}.<br>
        (This document is automatically generated by {{ $journal->name }} journal system.)
    </div>

</body>
</html>
