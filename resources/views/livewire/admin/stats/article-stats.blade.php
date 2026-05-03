<div class="space-y-6">
    {{-- FILTER BAR --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="font-bold text-slate-800 text-xl">Article Impact Report</h2>
                <p class="text-sm text-slate-500 mt-1">Track views, downloads, and reader engagement</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Date Range --}}
                <div class="flex items-center gap-2 bg-slate-50 rounded-lg p-2">
                    <div class="relative">
                        <input type="date" wire:model.live.debounce.300ms="dateStart"
                            class="w-36 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    </div>
                    <span class="text-slate-400 text-sm font-medium">to</span>
                    <div class="relative">
                        <input type="date" wire:model.live.debounce.300ms="dateEnd"
                            class="w-36 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    </div>
                </div>

                {{-- Granularity Buttons --}}
                <div class="flex items-center bg-slate-100 rounded-lg p-1">
                    <button wire:click="setGranularity('daily')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $granularity === 'daily' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        Daily
                    </button>
                    <button wire:click="setGranularity('weekly')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $granularity === 'weekly' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        Weekly
                    </button>
                    <button wire:click="setGranularity('monthly')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $granularity === 'monthly' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        Monthly
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI CARDS with Inline SVG Icons --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        {{-- Total Views --}}
        <div
            class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:border-blue-200 transition-all duration-200 relative overflow-hidden">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Total Views</div>
                    <div class="text-3xl font-bold text-slate-800">{{ number_format($totalViews) }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);">
                    {{-- Eye Icon SVG --}}
                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2"
                        viewBox="0 0 24 24" style="min-width: 24px; min-height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-slate-500">
                <span>Abstract & Galley views</span>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-50 rounded-full opacity-50 z-0"></div>
        </div>

        {{-- Total Downloads --}}
        <div
            class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:border-orange-200 transition-all duration-200 relative overflow-hidden">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Total Downloads</div>
                    <div class="text-3xl font-bold text-slate-800">{{ number_format($totalDownloads) }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform"
                    style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 4px 14px 0 rgba(249, 115, 22, 0.39);">
                    {{-- Download Icon SVG --}}
                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2"
                        viewBox="0 0 24 24" style="min-width: 24px; min-height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-slate-500">
                <span>PDF file downloads</span>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-orange-50 rounded-full opacity-50 z-0"></div>
        </div>

        {{-- Top Country --}}
        <div
            class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:border-emerald-200 transition-all duration-200 relative overflow-hidden">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Top Country</div>
                    <div class="text-2xl font-bold text-slate-800">
                        {{ $topCountry ? $countryNames[$topCountry->country_code] ?? $topCountry->country_code : '-' }}
                    </div>
                    @if ($topCountry)
                        <div class="text-sm text-slate-500 mt-1">{{ number_format($topCountry->total) }} visits</div>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.39);">
                    {{-- Globe Icon SVG --}}
                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2"
                        viewBox="0 0 24 24" style="min-width: 24px; min-height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-emerald-50 rounded-full opacity-50 z-0"></div>
        </div>

        {{-- Busiest Day --}}
        <div
            class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:border-purple-200 transition-all duration-200 relative overflow-hidden">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Busiest Day</div>
                    <div class="text-lg font-bold text-slate-800">
                        {{ $busiestDay ? \Carbon\Carbon::parse($busiestDay->date)->format('M d, Y') : '-' }}
                    </div>
                    @if ($busiestDay)
                        <div class="text-sm text-slate-500 mt-1">{{ number_format($busiestDay->total) }} hits</div>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform"
                    style="background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); box-shadow: 0 4px 14px 0 rgba(168, 85, 247, 0.39);">
                    {{-- Calendar Icon SVG --}}
                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2"
                        viewBox="0 0 24 24" style="min-width: 24px; min-height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-purple-50 rounded-full opacity-50 z-0"></div>
        </div>
    </div>

    {{-- MAIN CHART & MAP --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Line Chart (Span 2) --}}
        <div class="xl:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Traffic Trends</h3>
                    <p class="text-sm text-slate-500">Views vs Downloads over time</p>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-slate-600">Views</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-slate-600">Downloads</span>
                    </div>
                </div>
            </div>
            {{-- IMPORTANT: wire:ignore prevents Livewire from destroying this div on update --}}
            <div wire:ignore>
                <div id="mainChart" class="h-80"></div>
            </div>
        </div>

        {{-- Geo Map (Span 1) --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="mb-4">
                <h3 class="font-bold text-slate-800 text-lg">Reader Locations</h3>
                <p class="text-sm text-slate-500">Geographic distribution</p>
            </div>
            <div wire:ignore>
                <div id="worldMap" class="h-64 bg-slate-50 rounded-lg mb-4"></div>
            </div>

            {{-- Top Countries List --}}
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @php
                    $topGeoData = collect($geoData)->take(10);
                    $maxCount = $topGeoData->max() ?: 1;
                @endphp
                @foreach ($topGeoData as $code => $count)
                    <div class="flex items-center gap-3">
                        <div class="w-8 text-center">
                            <span class="text-sm font-mono font-bold text-slate-600">{{ $code }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                    style="width: {{ ($count / $maxCount) * 100 }}%; background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%);">
                                </div>
                            </div>
                        </div>
                        <div class="w-16 text-right">
                            <span class="text-sm font-semibold text-slate-700">{{ number_format($count) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TOP ARTICLES TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Top Performing Articles</h3>
                    <p class="text-sm text-slate-500">Ranked by total views in selected period</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Showing top</span>
                    <select wire:model.live="limit"
                        class="text-sm border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 w-12">#</th>
                        <th class="px-6 py-4">Article Title</th>
                        <th class="px-6 py-4 w-28 text-center">Section</th>
                        <th class="px-6 py-4 w-28 text-center">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                Views
                            </span>
                        </th>
                        <th class="px-6 py-4 w-28 text-center">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Downloads
                            </span>
                        </th>
                        <th class="px-6 py-4 w-40 text-center">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topArticles as $index => $article)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full"
                                    style="{{ $index < 3 ? 'background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%); color: white; font-weight: bold;' : 'background-color: #f1f5f9; color: #475569;' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-md">
                                    <a href="{{ route('journal.submissions.show', ['journal' => current_journal()->slug, 'submission' => $article->slug]) }}"
                                        class="font-semibold text-slate-800 hover:text-indigo-600 line-clamp-2">
                                        {{ $article->title }}
                                    </a>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $article->authors->pluck('last_name')->join(', ') ?: 'Unknown Author' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">
                                    {{ Str::limit($article->section->name ?? 'Uncategorized', 15) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-mono font-bold text-blue-600 text-base">
                                    {{ number_format($article->views_count) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-mono font-bold text-orange-600 text-base">
                                    {{ number_format($article->downloads_count) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{-- Sparkline using inline SVG minibar --}}
                                <div class="h-8 w-full flex items-end gap-px" wire:ignore>
                                    @php
                                        $sparkline = $article->sparkline ?? [];
                                        $maxSparkline = max($sparkline) ?: 1;
                                        // Sample every Nth element for display (max 20 bars)
                                        $step = max(1, ceil(count($sparkline) / 20));
                                        $sampledSparkline = [];
                                        for ($i = 0; $i < count($sparkline); $i += $step) {
                                            $sampledSparkline[] = $sparkline[$i];
                                        }
                                    @endphp
                                    @foreach ($sampledSparkline as $val)
                                        <div class="flex-1 bg-blue-400 rounded-t transition-all hover:bg-blue-600"
                                            style="height: {{ max(2, ($val / $maxSparkline) * 100) }}%"
                                            title="{{ $val }} views"></div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    <p class="font-medium">No article data available</p>
                                    <p class="text-sm">Adjust the date range or check back later</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- CHART SCRIPTS --}}
@assets
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">
@endassets

@script
    <script>
        // Chart instance (global to this component scope)
        let mainChart = null;
        let worldMap = null;

        // Initial chart data from PHP
        const initialData = @json($chartData);
        const initialGeoData = @json($geoData);

        function initMainChart(categories, views, downloads) {
            const options = {
                chart: {
                    type: 'area',
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: false,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 500
                    }
                },
                series: [{
                        name: 'Views',
                        data: views
                    },
                    {
                        name: 'Downloads',
                        data: downloads
                    }
                ],
                colors: ['#3b82f6', '#f97316'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '11px'
                        },
                        rotate: -45,
                        rotateAlways: categories.length > 15
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '11px'
                        },
                        formatter: function(val) {
                            return val.toFixed(0);
                        }
                    }
                },
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 4
                },
                legend: {
                    show: false
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(val) {
                            return val ? val.toLocaleString() : '0';
                        }
                    }
                }
            };

            if (mainChart) {
                mainChart.destroy();
            }

            const chartEl = document.querySelector("#mainChart");
            if (chartEl) {
                mainChart = new ApexCharts(chartEl, options);
                mainChart.render();
            }
        }

        function initWorldMap(geoData) {
            const mapElement = document.querySelector('#worldMap');
            if (!mapElement) return;

            mapElement.innerHTML = '';

            try {
                worldMap = new jsVectorMap({
                    selector: '#worldMap',
                    map: 'world',
                    zoomButtons: false,
                    zoomOnScroll: false,
                    regionStyle: {
                        initial: {
                            fill: '#e2e8f0',
                            stroke: '#cbd5e1',
                            strokeWidth: 0.5
                        },
                        hover: {
                            fill: '#6366f1'
                        }
                    },
                    visualizeData: {
                        scale: ['#c7d2fe', '#4f46e5'],
                        values: geoData
                    },
                    onRegionTooltipShow: function(event, tooltip, code) {
                        const count = geoData[code] || 0;
                        tooltip.text(
                            `<div class="p-2"><strong>${code}</strong><br><span class="text-sm">${count.toLocaleString()} views</span></div>`,
                            true
                        );
                    }
                });
            } catch (e) {
                console.warn('Map initialization error:', e);
            }
        }

        // Initialize charts on mount
        initMainChart(initialData.categories, initialData.views, initialData.downloads);
        initWorldMap(initialGeoData);

        // LISTEN FOR UPDATE EVENTS FROM LIVEWIRE
        $wire.on('update-chart', (data) => {
            const payload = data[0]; // Livewire wraps event data in array

            if (mainChart) {
                // Update chart data without destroying it
                mainChart.updateOptions({
                    xaxis: {
                        categories: payload.categories
                    }
                });
                mainChart.updateSeries(payload.series);
            } else {
                // Fallback: reinitialize if chart was lost
                const views = payload.series[0]?.data || [];
                const downloads = payload.series[1]?.data || [];
                initMainChart(payload.categories, views, downloads);
            }
        });
    </script>
@endscript

@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush
