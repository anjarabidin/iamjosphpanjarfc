<ici-import>
    <journal issn="{{ $journal->issn_online }}" />
    @foreach($submissions as $article)
        <issue number="{{ $article->issue?->number }}" volume="{{ $article->issue?->volume }}" year="{{ $article->issue?->year }}" />
        <article>
            <type>Original article</type>
            <languageVersion language="{{ $article->language ?? 'en' }}">
                <title><![CDATA[{{ $article->currentPublication?->title ?? $article->title }}]]></title>
                <abstract><![CDATA[{{ trim(strip_tags(html_entity_decode(str_replace('&nbsp;', ' ', $article->currentPublication?->abstract ?? $article->abstract), ENT_QUOTES, 'UTF-8'))) }}]]></abstract>
                <pdfFileUrl><![CDATA[{{ route('journal.article.download.pdf', ['journal' => $journal->slug, 'seq_id' => $article->seq_id, 'filename' => \Str::slug($article->currentPublication?->title ?? $article->title)]) }}]]></pdfFileUrl>
                <publicationDate>{{ $article->currentPublication?->date_published ? $article->currentPublication->date_published->format('Y-m-d') : ($article->published_at ? $article->published_at->format('Y-m-d') : ($article->issue?->published_at ? $article->issue->published_at->format('Y-m-d') : '')) }}</publicationDate>
            </languageVersion>
            <authors>
                @foreach($article->authors as $index => $author)
                    <author>
                        @php
                            $fullName = trim(($author->given_name ?? '') . ' ' . ($author->family_name ?? ''));
                            if (empty($fullName)) $fullName = $author->name ?? '';
                            $nameParts = explode(' ', trim($fullName));
                            $surname = count($nameParts) > 1 ? array_pop($nameParts) : '';
                            $name = count($nameParts) > 0 ? implode(' ', $nameParts) : $fullName;
                        @endphp
                        <name><![CDATA[{{ $name }}]]></name>
                        <surname><![CDATA[{{ $surname }}]]></surname>
                        <email><![CDATA[{{ $author->email }}]]></email>
                        <order>{{ $index + 1 }}</order>
                        <instituteAffiliation><![CDATA[{{ $author->affiliation }}]]></instituteAffiliation>
                        <role>AUTHOR</role>
                    </author>
                @endforeach
            </authors>
            <keywords language="{{ $article->language ?? 'en' }}">
                @php
                    if ($article->currentPublication && !empty($article->currentPublication->keywords)) {
                        $keywordsList = $article->currentPublication->keywords_array;
                    } else {
                        $keywordsList = $article->keywords->pluck('content')->toArray();
                    }
                @endphp
                @foreach($keywordsList as $keyword)
                    <keyword><![CDATA[{{ trim($keyword) }}]]></keyword>
                @endforeach
            </keywords>
            <references>
                @php
                    $referencesStr = $article->currentPublication->references ?? $article->references;
                    $referencesArray = $referencesStr ? array_filter(array_map('trim', explode("\n", $referencesStr))) : [];
                @endphp
                @foreach($referencesArray as $reference)
                    <reference><![CDATA[{{ trim(strip_tags(html_entity_decode($reference))) }}]]></reference>
                @endforeach
            </references>
            <pages><![CDATA[{{ $article->currentPublication?->pages ?? $article->pages ?? '' }}]]></pages>
            <doi><![CDATA[{{ $article->currentPublication?->doi ?? $article->doi ?? '' }}]]></doi>
        </article>
    @endforeach
</ici-import>
