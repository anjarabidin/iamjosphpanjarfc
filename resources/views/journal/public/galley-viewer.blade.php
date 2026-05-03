<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $article->title }} | {{ $journal->name }}</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .header-bar {
            background-color: #1a202c;
            /* gray-900 */
            color: white;
            padding: 0 1rem;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-title {
            font-weight: 600;
            font-size: 0.875rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 60%;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
        }

        .header-actions a {
            color: #edf2f7;
            text-decoration: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }

        .header-actions a:hover {
            background-color: #2d3748;
        }

        .pdf-frame {
            width: 100%;
            height: calc(100% - 50px);
            border: none;
            display: block;
            background: #e2e8f0;
        }
    </style>
</head>

<body>

    <div class="header-bar">
        <div class="header-title">
            <span style="opacity: 0.7; margin-right: 0.5rem;">{{ $journal->abbreviation ?? $journal->name }}</span>
            {{ $article->title }}
        </div>
        <div class="header-actions">
            @php
                $downloadUrl = route('journal.article.download', [
                    'journal' => $journal->path,
                    'article' => $article->slug,
                    'galley' => $galley->id,
                ]);
            @endphp
            <a href="{{ $downloadUrl }}" download>
                <svg style="display:inline-block; vertical-align:text-bottom; width:16px; height:16px" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('journal.public.article', ['journal' => $journal->path, 'article' => $article->seq_id]) }}">
                &larr; Back to Article
            </a>
        </div>
    </div>

    <iframe src="{{ $downloadUrl }}" class="pdf-frame" title="PDF Viewer">
        This browser does not support PDFs. Please download the PDF to view it: <a href="{{ $downloadUrl }}">Download
            PDF</a>
    </iframe>

</body>

</html>
