{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- 1. Main Landing Page --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- 2. Journal Homepages --}}
    @foreach ($journals as $journal)
        @if ($journal->enabled)
            <url>
                <loc>{{ route('journal.public.home', $journal->slug) }}</loc>
                <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.8</priority>
            </url>
        @endif
    @endforeach

    {{-- 3. Articles --}}
    @foreach ($articles as $article)
        @if ($article->journal)
            <url>
                <loc>
                    {{ route('journal.public.article', ['journal' => $article->journal->slug, 'article' => $article->seq_id ?? $article->slug ?? $article->id]) }}
                </loc>
                <lastmod>{{ \Carbon\Carbon::parse($article->last_mod_date)->format('Y-m-d') }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.6</priority>
            </url>
        @endif
    @endforeach
</urlset>
