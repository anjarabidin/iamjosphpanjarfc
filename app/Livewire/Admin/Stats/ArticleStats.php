<?php

namespace App\Livewire\Admin\Stats;

use Livewire\Component;
use App\Models\ArticleMetric;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ArticleStats extends Component
{
    protected const DATE_FORMAT_DAILY = "TO_CHAR(date, 'YYYY-MM-DD')";
    protected const DATE_FORMAT_WEEKLY = "TO_CHAR(date, 'IYYY-IW')";
    protected const DATE_FORMAT_MONTHLY = "TO_CHAR(date, 'YYYY-MM')";

    public $dateStart;
    public $dateEnd;
    public $granularity = 'daily'; // daily, weekly, monthly
    public $limit = 20;

    public function mount()
    {
        // Default to last 30 days
        $this->dateStart = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateEnd = Carbon::now()->format('Y-m-d');
    }

    public function updatedDateStart()
    {
        $this->dispatchChartUpdate();
    }

    public function updatedDateEnd()
    {
        $this->dispatchChartUpdate();
    }

    public function setGranularity($granularity)
    {
        $this->granularity = $granularity;
        $this->dispatchChartUpdate();
    }

    /**
     * Dispatch chart update event to frontend
     */
    public function dispatchChartUpdate()
    {
        $journal = current_journal();
        $journalId = $journal?->id;
        
        $chartData = $this->getChartData($journalId);
        
        $this->dispatch('update-chart', [
            'categories' => $chartData['categories'],
            'series' => [
                ['name' => 'Views', 'data' => $chartData['views']],
                ['name' => 'Downloads', 'data' => $chartData['downloads']]
            ]
        ]);
    }

    public function render()
    {
        $journal = current_journal();
        $journalId = $journal?->id;

        // 1. KPI CARDS DATA
        $totalViews = ArticleMetric::where('type', ArticleMetric::TYPE_VIEW)
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$this->dateStart, $this->dateEnd])
            ->count();

        $totalDownloads = ArticleMetric::where('type', ArticleMetric::TYPE_DOWNLOAD)
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$this->dateStart, $this->dateEnd])
            ->count();

        // Top Country
        $topCountry = ArticleMetric::selectRaw('country_code, count(*) as total')
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$this->dateStart, $this->dateEnd])
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->first();

        // Busiest Day
        $busiestDay = ArticleMetric::selectRaw('date, count(*) as total')
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$this->dateStart, $this->dateEnd])
            ->groupBy('date')
            ->orderByDesc('total')
            ->first();

        // 2. MAIN CHART DATA (Group by Date/Week/Month based on granularity)
        $chartData = $this->getChartData($journalId);

        // 3. TABLE DATA (Top Performing Articles with Sparkline data)
        $topArticles = $this->getTopArticles($journalId);

        // 4. GEO MAP DATA
        $geoData = ArticleMetric::selectRaw('country_code, count(*) as total')
            ->where('type', ArticleMetric::TYPE_VIEW)
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$this->dateStart, $this->dateEnd])
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->limit(50)
            ->pluck('total', 'country_code')
            ->toArray();

        // Country names mapping
        $countryNames = $this->getCountryNames();

        return view('livewire.admin.stats.article-stats', compact(
            'totalViews',
            'totalDownloads',
            'topCountry',
            'busiestDay',
            'chartData',
            'topArticles',
            'geoData',
            'countryNames'
        ));
    }

    protected function getChartData($journalId)
    {
        $dateFormat = match ($this->granularity) {
            'weekly' => self::DATE_FORMAT_WEEKLY,
            'monthly' => self::DATE_FORMAT_MONTHLY,
            default => self::DATE_FORMAT_DAILY,
        };

        $rawData = ArticleMetric::selectRaw("{$dateFormat} as period, type, count(*) as count")
            ->when($journalId, fn($q) => $q->whereHas('submission', fn($s) => $s->where('journal_id', $journalId)))
            ->whereBetween('date', [$this->dateStart, $this->dateEnd])
            ->groupByRaw("{$dateFormat}, type")
            ->orderByRaw($dateFormat)
            ->get();

        // Generate all periods in range for consistent chart
        $periods = [];
        $start = Carbon::parse($this->dateStart);
        $end = Carbon::parse($this->dateEnd);

        if ($this->granularity === 'daily') {
            $period = CarbonPeriod::create($start, '1 day', $end);
            foreach ($period as $date) {
                $periods[] = $date->format('Y-m-d');
            }
        } elseif ($this->granularity === 'weekly') {
            $current = $start->copy()->startOfWeek();
            while ($current <= $end) {
                $periods[] = $current->format('o-W'); // ISO format
                $current->addWeek();
            }
        } else {
            $current = $start->copy()->startOfMonth();
            while ($current <= $end) {
                $periods[] = $current->format('Y-m');
                $current->addMonth();
            }
        }

        // Initialize data arrays
        $viewsData = array_fill_keys($periods, 0);
        $downloadsData = array_fill_keys($periods, 0);

        foreach ($rawData as $row) {
            $period = $row->period;
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

    protected function getTopArticles($journalId)
    {
        $articles = Submission::with(['authors', 'section'])
            ->where('status', Submission::STATUS_PUBLISHED)
            ->when($journalId, fn($q) => $q->where('journal_id', $journalId))
            ->withCount([
                'articleMetrics as views_count' => function ($query) {
                    $query->where('type', ArticleMetric::TYPE_VIEW)
                        ->whereBetween('date', [$this->dateStart, $this->dateEnd]);
                },
                'articleMetrics as downloads_count' => function ($query) {
                    $query->where('type', ArticleMetric::TYPE_DOWNLOAD)
                        ->whereBetween('date', [$this->dateStart, $this->dateEnd]);
                }
            ])
            ->orderByDesc('views_count')
            ->take($this->limit)
            ->get();

        // Get sparkline data for each article (last 30 days trend)
        foreach ($articles as $article) {
            $sparklineData = ArticleMetric::selectRaw(self::DATE_FORMAT_DAILY . " as day, count(*) as count")
                ->where('submission_id', $article->id)
                ->where('type', ArticleMetric::TYPE_VIEW)
                ->whereBetween('date', [$this->dateStart, $this->dateEnd])
                ->groupByRaw(self::DATE_FORMAT_DAILY)
                ->orderByRaw(self::DATE_FORMAT_DAILY)
                ->pluck('count', 'day')
                ->toArray();

            // Fill missing days with 0
            $period = CarbonPeriod::create(Carbon::parse($this->dateStart), '1 day', Carbon::parse($this->dateEnd));
            $sparkline = [];
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $sparkline[] = $sparklineData[$key] ?? 0;
            }

            $article->sparkline = $sparkline;
        }

        return $articles;
    }

    protected function getCountryNames(): array
    {
        return [
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
            // Add more as needed
        ];
    }
}
