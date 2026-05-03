<?php

namespace App\Http\Controllers\Admin\Stats;

use App\Http\Controllers\Controller;
use App\Models\ArticleMetric;
use App\Models\Submission;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ArticleStatsController extends Controller
{
    protected const DATE_FORMAT_DAILY = "TO_CHAR(date, 'YYYY-MM-DD')";

    /**
     * Show the statistics dashboard page
     */
    public function index()
    {
        return view('manager.statistics.articles');
    }

    /**
     * Get statistics data as JSON for AJAX requests
     */
    public function getData(Request $request)
    {
        $journal = current_journal();
        $journalId = $journal?->id;

        $start = $request->get('start', now()->subDays(30)->toDateString());
        $end = $request->get('end', now()->toDateString());
        $granularity = $request->get('granularity', 'daily');

        // 1. KPI DATA
        $kpiViews = ArticleMetric::where('type', ArticleMetric::TYPE_VIEW)
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$start, $end])
            ->count();

        $kpiDownloads = ArticleMetric::where('type', ArticleMetric::TYPE_DOWNLOAD)
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$start, $end])
            ->count();

        // Top Country
        $topCountry = ArticleMetric::selectRaw('country_code, count(*) as total')
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$start, $end])
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->first();

        // Busiest Day
        $busiestDay = ArticleMetric::selectRaw('date, count(*) as total')
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$start, $end])
            ->groupBy('date')
            ->orderByDesc('total')
            ->first();

        // 2. CHART DATA
        $chartData = $this->getChartData($journalId, $start, $end, $granularity);

        // 3. MAP DATA (Format for jsVectorMap: { "ID": count, "US": count })
        $mapData = ArticleMetric::where('type', ArticleMetric::TYPE_VIEW)
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$start, $end])
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->selectRaw('country_code, count(*) as total')
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->limit(50)
            ->pluck('total', 'country_code')
            ->toArray();

        // 4. TOP ARTICLES TABLE
        $tableData = Submission::with(['authors', 'section'])
            ->where('status', Submission::STATUS_PUBLISHED)
            ->when($journalId, fn($q) => $q->where('journal_id', $journalId))
            ->withCount([
                'articleMetrics as views_count' => function ($query) use ($start, $end) {
                    $query->where('type', ArticleMetric::TYPE_VIEW)
                        ->whereBetween('date', [$start, $end]);
                },
                'articleMetrics as downloads_count' => function ($query) use ($start, $end) {
                    $query->where('type', ArticleMetric::TYPE_DOWNLOAD)
                        ->whereBetween('date', [$start, $end]);
                }
            ])
            ->orderByDesc('views_count')
            ->take(20)
            ->get()
            ->map(function ($submission) use ($start, $end) {
                // Get sparkline data (daily views)
                $sparklineData = ArticleMetric::selectRaw(self::DATE_FORMAT_DAILY . " as day, count(*) as count")
                    ->where('submission_id', $submission->id)
                    ->where('type', ArticleMetric::TYPE_VIEW)
                    ->whereBetween('date', [$start, $end])
                    ->groupByRaw(self::DATE_FORMAT_DAILY)
                    ->orderByRaw(self::DATE_FORMAT_DAILY)
                    ->pluck('count', 'day')
                    ->toArray();

                // Fill missing days with 0 and sample for display
                $period = CarbonPeriod::create(Carbon::parse($start), '1 day', Carbon::parse($end));
                $sparkline = [];
                foreach ($period as $date) {
                    $key = $date->format('Y-m-d');
                    $sparkline[] = (int) ($sparklineData[$key] ?? 0);
                }

                // Sample sparkline (max 20 bars)
                $step = max(1, ceil(count($sparkline) / 20));
                $sampledSparkline = [];
                for ($i = 0; $i < count($sparkline); $i += $step) {
                    $sampledSparkline[] = $sparkline[$i];
                }

                return [
                    'id' => $submission->id,
                    'title' => $submission->title,
                    'slug' => $submission->slug,
                    'url' => route('journal.submissions.show', ['journal' => current_journal()->slug, 'submission' => $submission->slug]),
                    'author' => $submission->authors->pluck('last_name')->join(', ') ?: 'Unknown',
                    'section' => $submission->section->name ?? 'Uncategorized',
                    'views' => (int) $submission->views_count,
                    'downloads' => (int) $submission->downloads_count,
                    'sparkline' => $sampledSparkline,
                ];
            });

        return response()->json([
            'kpi' => [
                'views' => $kpiViews,
                'downloads' => $kpiDownloads,
                'top_country' => $topCountry ? [
                    'code' => $topCountry->country_code,
                    'name' => $this->getCountryName($topCountry->country_code),
                    'total' => $topCountry->total,
                ] : null,
                'busiest_day' => $busiestDay ? [
                    'date' => $busiestDay->date,
                    'formatted' => Carbon::parse($busiestDay->date)->format('M d, Y'),
                    'total' => $busiestDay->total,
                ] : null,
            ],
            'chart' => $chartData,
            'map' => $mapData,
            'table' => $tableData,
        ]);
    }

    /**
     * Get chart data with granularity support
     */
    protected function getChartData($journalId, $start, $end, $granularity = 'daily')
    {
        $dateFormat = match ($granularity) {
            'weekly' => "TO_CHAR(date, 'IYYY-IW')",
            'monthly' => "TO_CHAR(date, 'YYYY-MM')",
            default => self::DATE_FORMAT_DAILY,
        };

        $rawData = ArticleMetric::selectRaw("{$dateFormat} as period, type, count(*) as count")
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$start, $end])
            ->groupByRaw("{$dateFormat}, type")
            ->orderByRaw($dateFormat)
            ->get();

        // Generate all periods in range
        $periods = $this->generatePeriods($start, $end, $granularity);

        // Initialize data arrays
        $viewsData = array_fill_keys($periods, 0);
        $downloadsData = array_fill_keys($periods, 0);

        foreach ($rawData as $row) {
            $period = $row->period;
            if (!isset($viewsData[$period])) {
                continue;
            }
            
            if ($row->type === ArticleMetric::TYPE_VIEW) {
                $viewsData[$period] = (int) $row->count;
            } else {
                $downloadsData[$period] = (int) $row->count;
            }
        }

        return [
            'categories' => array_values($periods),
            'views' => array_values($viewsData),
            'downloads' => array_values($downloadsData),
        ];
    }

    /**
     * Generate period labels based on granularity
     */
    protected function generatePeriods(string $start, string $end, string $granularity): array
    {
        $periods = [];
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        if ($granularity === 'daily') {
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $date) {
                $periods[] = $date->format('Y-m-d');
            }
            return $periods;
        }

        if ($granularity === 'weekly') {
            $current = $startDate->copy()->startOfWeek();
            while ($current <= $endDate) {
                $periods[] = $current->format('o-W');
                $current->addWeek();
            }
            return $periods;
        }

        // Monthly
        $current = $startDate->copy()->startOfMonth();
        while ($current <= $endDate) {
            $periods[] = $current->format('Y-m');
            $current->addMonth();
        }

        return $periods;
    }

    /**
     * Get country name from code
     */
    protected function getCountryName(string $code): string
    {
        $countries = [
            'ID' => 'Indonesia',
            'US' => 'United States',
            'MY' => 'Malaysia',
            'SG' => 'Singapore',
            'AU' => 'Australia',
            'GB' => 'United Kingdom',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'CN' => 'China',
            'IN' => 'India',
            'DE' => 'Germany',
            'FR' => 'France',
            'NL' => 'Netherlands',
            'CA' => 'Canada',
            'BR' => 'Brazil',
            'PH' => 'Philippines',
            'TH' => 'Thailand',
            'VN' => 'Vietnam',
            'PK' => 'Pakistan',
            'BD' => 'Bangladesh',
            'NG' => 'Nigeria',
            'EG' => 'Egypt',
            'ZA' => 'South Africa',
            'SA' => 'Saudi Arabia',
            'AE' => 'UAE',
            'TR' => 'Turkey',
            'RU' => 'Russia',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'MX' => 'Mexico',
        ];

        return $countries[$code] ?? $code;
    }
}
