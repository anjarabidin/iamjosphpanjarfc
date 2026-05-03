<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL; ?>
<issues xmlns="http://pkp.sfu.ca" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://pkp.sfu.ca native.xsd">
    @php
        ini_set('memory_limit', '1024M');

        // Initialize maps to store UUID -> Integer mappings
        $mapArticleId = [];
        $mapFileId = [];
        $mapAuthorId = [];

        // Counters
        $articleCounter = 12;
        $fileCounter = 1000;
        $authorCounter = 5000;

        // Pre-calculate mappings for all submissions in all issues
        foreach ($issues as $issue) {
            foreach ($issue->submissions as $sub) {
                $mapArticleId[$sub->id] = $articleCounter++;

                foreach ($sub->files as $file) {
                    $mapFileId[$file->id] = $fileCounter++;
                }

                foreach ($sub->authors as $author) {
                    $mapAuthorId[$author->id] = $authorCounter++;
                }
            }
        }
    @endphp

    @foreach ($issues as $issue)
        <issue published="{{ $issue->is_published ? '1' : '0' }}" current="1" access_status="1"
            url_path="{{ $issue->url_path }}" xsi:schemaLocation="http://pkp.sfu.ca native.xsd">

            <id type="internal" advice="ignore">{{ $issue->id }}</id>

            @if ($issue->description)
                <description locale="en_US">{{ htmlspecialchars($issue->description) }}</description>
            @endif

            <issue_identification>
                <volume>{{ $issue->volume }}</volume>
                <number>{{ $issue->number }}</number>
                <year>{{ $issue->year }}</year>
                <title locale="en_US">{{ htmlspecialchars($issue->title ?? '') }}</title>
            </issue_identification>

            <date_published>{{ $issue->published_at ? $issue->published_at->format('Y-m-d') : '' }}</date_published>
            <last_modified>{{ $issue->updated_at ? $issue->updated_at->format('Y-m-d') : '' }}</last_modified>

            <sections>
                <section ref="ART" seq="1" editor_restricted="0" meta_indexed="1" meta_reviewed="1"
                    abstracts_not_required="0" hide_title="0" hide_author="0" abstract_word_count="0">
                    <id type="internal" advice="ignore">1</id>
                    <abbrev locale="en_US">ART</abbrev>
                    <title locale="en_US">Articles</title>

                </section>
            </sections>

            <articles>
                @foreach ($issue->submissions as $submission)
                    @php
                        $articleIntId = $mapArticleId[$submission->id];
                        $dateSubmitted = $submission->submitted_at
                            ? $submission->submitted_at->format('Y-m-d')
                            : now()->format('Y-m-d');
                    @endphp
                    @include('xml.article', [
                        'submission' => $submission,
                        'mapArticleId' => $mapArticleId,
                        'mapFileId' => $mapFileId,
                        'mapAuthorId' => $mapAuthorId,
                        'articleIntId' => $articleIntId,
                        'dateSubmitted' => $dateSubmitted,
                    ])
                @endforeach
            </articles>
        </issue>
    @endforeach
</issues>
