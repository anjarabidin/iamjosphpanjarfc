<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class YouTubeSearchService
{
    private const BASE = 'https://www.googleapis.com/youtube/v3';
    private string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.youtube.key');
    }

    /**
     * Cari video lalu filter ketat:
     * - judul/deskripsi mengandung salah satu terms
     * - channel country = ID (hard filter)
     *
     * @param array<string> $terms
     * @param string|null $publishedAfter ISO8601, opsional
     * @param int $maxPages batas halaman per keyword (hemat kuota)
     * @return array<int, array<string,mixed>>
     */
    public function searchByTitleOrDescription(array $terms, ?string $publishedAfter = null, int $maxPages = 2): array
    {
        $terms = array_values(array_unique(array_filter($terms)));
        $found = [];         // [videoId => videoData]
        $channelIds = [];    // kumpulkan untuk cek negara channel

        foreach ($terms as $term) {
            $pageToken = null;

            for ($page = 1; $page <= max(1, $maxPages); $page++) {
                $query = [
                    'key'               => $this->apiKey,
                    'part'              => 'snippet',
                    'q'                 => $term,
                    'type'              => 'video',
                    'maxResults'        => 50,
                    'order'             => 'date', // atau 'relevance'
                    'relevanceLanguage' => 'id',   // bias Indonesia
                    'regionCode'        => 'ID',   // bias Indonesia
                ];
                if ($publishedAfter) $query['publishedAfter'] = Carbon::parse($publishedAfter)->toIso8601String();
                if ($pageToken)      $query['pageToken']      = $pageToken;

                $resp = Http::retry(3, 600)->get(self::BASE . '/search', $query)->throw()->json();
                $pageToken = $resp['nextPageToken'] ?? null;

                foreach ($resp['items'] ?? [] as $it) {
                    $id   = $it['id']['videoId'] ?? null;
                    $snip = $it['snippet'] ?? [];
                    if (!$id || !$snip) continue;

                    // Filter teks (tepatkan makna 'phri' Indonesia vs India)
                    $text = Str::of(($snip['title'] ?? '') . ' ' . ($snip['description'] ?? ''))->lower();
                    $matchTerm = collect($terms)->contains(fn($t) => $text->contains(Str::lower($t)));

                    if (!$matchTerm) continue;

                    $found[$id] = [
                        'videoId'     => $id,
                        'title'       => $snip['title'] ?? '',
                        'description' => $snip['description'] ?? '',
                        'channel'     => $snip['channelTitle'] ?? '',
                        'channelId'   => $snip['channelId'] ?? null,
                        'publishedAt' => $snip['publishedAt'] ?? '',
                        'url'         => 'https://www.youtube.com/watch?v=' . $id,
                        'thumbnails'  => $snip['thumbnails'] ?? [],
                    ];

                    if (!empty($snip['channelId'])) {
                        $channelIds[$snip['channelId']] = true;
                    }
                }

                if (!$pageToken) break;
                usleep(150_000);
            }
        }

        if (empty($found)) return [];

        // === [INTI] Filter berdasarkan negara channel ===
        $countries = $this->fetchChannelCountries(array_keys($channelIds)); // [channelId => 'ID'|...|null]

        // Simpan hanya yang channel country = 'ID'
        $filtered = [];
        foreach ($found as $vid => $v) {
            $cid = $v['channelId'] ?? null;
            $country = $countries[$cid] ?? null;

            // Hard filter: channel dari Indonesia
            if ($country === 'ID') {
                $filtered[$vid] = $v;
                continue;
            }

            // (Fallback opsional) kalau channel country tidak tersedia,
            // tetap lolos jika teks mengandung "indonesia"
            $text = Str::of(($v['title'] ?? '') . ' ' . ($v['description'] ?? ''))->lower();
            if (!$country && $text->contains('indonesia')) {
                $filtered[$vid] = $v;
            }
        }

        return array_values($filtered);
    }

    /**
     * Ambil negara channel (snippet.country) untuk daftar channel IDs
     * @param array<string> $channelIds
     * @return array<string, string|null> mapping [channelId => 'ID'|'US'|...|null]
     */
    private function fetchChannelCountries(array $channelIds): array
    {
        $channelIds = array_values(array_unique(array_filter($channelIds)));
        if (empty($channelIds)) return [];

        $map = [];
        foreach (array_chunk($channelIds, 50) as $chunk) {
            $resp = Http::retry(3, 600)->get(self::BASE . '/channels', [
                'key'  => $this->apiKey,
                'part' => 'snippet',
                'id'   => implode(',', $chunk),
                // note: quota cost channels.list = 1/call
            ])->throw()->json();

            foreach ($resp['items'] ?? [] as $ch) {
                $id   = $ch['id'] ?? null;
                $snip = $ch['snippet'] ?? [];
                if ($id) {
                    $map[$id] = $snip['country'] ?? null; // bisa null kalau channel tak set negara
                }
            }
        }
        return $map;
    }
}
