<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL; ?>
<articles xmlns="http://pkp.sfu.ca" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://pkp.sfu.ca native.xsd">
    @php
        // -------------------------------------------------------------
        // OJS 3.3 COMPATIBILITY: UUID TO INTEGER MAPPING
        // -------------------------------------------------------------
        // Increase memory limit for Base64 processing (Gold Standard Requirement)
        ini_set('memory_limit', '1024M');

        // Initialize maps to store UUID -> Integer mappings
        $mapArticleId = [];
        $mapFileId = [];
        $mapAuthorId = [];

        // Counters
        $articleCounter = 12; // Start from 12 as per example, or 1
        $fileCounter = 1000;
        $authorCounter = 5000;

        foreach ($submissions as $sub) {
            $mapArticleId[$sub->id] = $articleCounter++;

            // Map Files
            foreach ($sub->files as $file) {
                $mapFileId[$file->id] = $fileCounter++;
            }

            // Map Authors
            foreach ($sub->authors as $author) {
                $mapAuthorId[$author->id] = $authorCounter++;
            }
        }
    @endphp

    @foreach ($submissions as $sub)
        @php
            $articleIntId = $mapArticleId[$sub->id];
            // Format: 2026-02-01
            $dateSubmitted = $sub->submitted_at ? $sub->submitted_at->format('Y-m-d') : now()->format('Y-m-d');
        @endphp

        <article xmlns="http://pkp.sfu.ca" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" locale="en_US"
            date_submitted="{{ $dateSubmitted }}" status="1" submission_progress="0"
            current_publication_id="{{ $articleIntId }}" stage="submission"
            xsi:schemaLocation="http://pkp.sfu.ca native.xsd">

            <id type="internal" advice="ignore">{{ $articleIntId }}</id>

            {{-- 1. SUBMISSION FILES (Base64 Embedding & Strict Attributes) --}}
            @foreach ($sub->files as $file)
                @php
                    $fileIntId = $mapFileId[$file->id];
                    $dateCreated = $file->created_at->format('Y-m-d');
                    $dateUpdated = $file->updated_at->format('Y-m-d');

                    // ---------------------------------------------------------
                    // ROBUST FILE HANDLING & PATH VERIFICATION
                    // ---------------------------------------------------------
                    $content = '';
                    $filesize = 0;
                    $fileExists = false;

                    // Standardize Path: Replace backslashes (Windows) with forward slashes (Linux/Unix)
                    $relativePath = str_replace('\\', '/', $file->file_path);

                    // Construct Full System Path
                    $fullPath = storage_path('app/public/' . $relativePath);

                    // Fallback: If not found in storage_path, check public_path (sometimes symlinked differently)
                    if (!file_exists($fullPath)) {
                        $fullPath = public_path('storage/' . $relativePath);
                    }

                    // Log for Debugging
                    \Illuminate\Support\Facades\Log::info(
                        "Exporting File ID {$file->id}: Checking path -> " . $fullPath,
                    );

                    if (file_exists($fullPath)) {
                        $fileData = file_get_contents($fullPath);
                        if ($fileData !== false) {
                            $content = base64_encode($fileData);
                            $filesize = strlen($fileData); // Bytes
                            $fileExists = true;
                        } else {
                            \Illuminate\Support\Facades\Log::warning(
                                "File ID {$file->id} exists but could not be read.",
                            );
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::warning(
                            "File ID {$file->id} NOT FOUND at {$fullPath}. Skipping.",
                        );
                    }
                @endphp

                @if ($fileExists && !empty($content))
                    <submission_file xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="{{ $fileIntId + 5000 }}"
                        created_at="{{ $dateCreated }}" date_created="" file_id="{{ $fileIntId }}"
                        stage="submission" updated_at="{{ $dateUpdated }}" viewable="false" genre="Article Text"
                        uploader="admin" xsi:schemaLocation="http://pkp.sfu.ca native.xsd">
                        <name locale="en_US">{{ $file->file_name ?? $file->name }}</name>
                        <file id="{{ $fileIntId }}" filesize="{{ $filesize }}" extension="pdf">
                            <embed encoding="base64">{{ $content }}</embed>
                        </file>
                    </submission_file>
                @endif
            @endforeach

            {{-- 2. PUBLICATION METADATA (Strict OJS 3.3 Structure) --}}
            @php
                // PRIMARY CONTACT LOGIC
                // 1. Try to find author with is_primary flag
                $primaryAuthor = $sub->authors->where('is_primary', true)->first();

                // 2. Fallback: If no primary flagged, take the FIRST author
                if (!$primaryAuthor && $sub->authors->count() > 0) {
                    $primaryAuthor = $sub->authors->first();
                }

                // 3. Map to Integer ID (Safe default to 0 if no authors exist, though unlikely)
                $primaryContactId = $primaryAuthor ? $mapAuthorId[$primaryAuthor->id] : 0;

                // SECTION REF: Hardcoded to 'ART' as per OJS 3.3 Default Standard
                $sectionRef = 'ART';
            @endphp

            <publication xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" locale="en_US" version="1"
                status="1" primary_contact_id="{{ $primaryContactId }}" url_path="" seq="0"
                section_ref="{{ $sectionRef }}" access_status="0" xsi:schemaLocation="http://pkp.sfu.ca native.xsd">

                <id type="internal" advice="ignore">{{ $articleIntId }}</id>
                <title locale="en_US">{{ $sub->title }}</title>
                @if ($sub->subtitle)
                    <subtitle locale="en_US">{{ $sub->subtitle }}</subtitle>
                @endif

                {{-- CDATA Wrapped Abstract --}}
                <abstract locale="en_US"><![CDATA[{!! $sub->abstract ?? '' !!}]]></abstract>

                {{-- Keywords Loop --}}
                @if ($sub->keywords)
                    <keywords locale="en_US">
                        @foreach ($sub->keywords as $keyword)
                            <keyword>{{ $keyword->content }}</keyword>
                        @endforeach
                    </keywords>
                @endif

                {{-- Authors Loop --}}
                <authors xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://pkp.sfu.ca native.xsd">
                    @php $seq = 0; @endphp
                    @foreach ($sub->authors as $author)
                        @php $authorIntId = $mapAuthorId[$author->id]; @endphp
                        {{-- USER GROUP REF: Hardcoded to 'Author' (Case Sensitive) --}}
                        <author include_in_browse="true" user_group_ref="Author" seq="{{ $seq++ }}"
                            id="{{ $authorIntId }}">
                            <givenname locale="en_US">{{ $author->first_name ?? $author->given_name }}</givenname>
                            <familyname locale="en_US">{{ $author->last_name ?? $author->family_name }}</familyname>

                            {{-- Affiliation before Email --}}
                            @if ($author->affiliation)
                                <affiliation locale="en_US">{{ $author->affiliation }}</affiliation>
                            @endif

                            <country>{{ $author->country ?? 'ID' }}</country>
                            <email>{{ $author->email }}</email>
                        </author>
                    @endforeach
                </authors>

                {{-- 3. ARTICLE GALLEYS (Crucial for PDF Button in OJS) --}}
                @foreach ($sub->files as $file)
                    @php
                        $fileIntId = $mapFileId[$file->id];
                    @endphp
                    <article_galley xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" locale="en_US" xsi:schemaLocation="http://pkp.sfu.ca native.xsd">
                        <id type="internal" advice="ignore">{{ $fileIntId }}</id>
                        <name locale="en_US">PDF</name>
                        <seq>0</seq>
                        <submission_file_ref id="{{ $fileIntId + 5000 }}" />
                    </article_galley>
                @endforeach

                {{-- Citations / References (Moved AFTER Authors as per Gold Standard) --}}
                @php
                    // Priority: Publication > Submission
                    $sourceRefs = $sub->currentPublication->references ?? $sub->references;
                @endphp

                @if ($sourceRefs)
                    <citations xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xsi:schemaLocation="http://pkp.sfu.ca native.xsd">
                        @php
                            $refs = $sourceRefs;
                            if (is_string($refs)) {
                                $refs = explode("\n", $refs);
                            }
                        @endphp
                        @foreach ($refs as $citation)
                            @if (trim($citation))
                                <citation><![CDATA[{{ trim($citation) }}]]></citation>
                            @endif
                        @endforeach
                    </citations>
                @endif

            </publication>
        </article>
    @endforeach
</articles>
