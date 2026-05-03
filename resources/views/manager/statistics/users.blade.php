@extends('layouts.app')

@section('title', 'User Statistics - ' . $journal->name)

@section('content')
    <div x-data="userDashboard()" x-init="init()" class="space-y-6">

        {{-- HEADER --}}
        <div
            class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200/60 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text">
                    User Statistics
                </h1>
                <p class="text-sm text-slate-500 mt-1">Overview of users, roles, and activity for {{ $journal->name }}.</p>
            </div>
            <button @click="fetchData()"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Refresh
            </button>
        </div>

        {{-- LOADING STATE --}}
        <template x-if="loading">
            <div class="flex justify-center items-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>
        </template>

        {{-- MAIN CONTENT --}}
        <template x-if="!loading">
            <div class="space-y-6">

                {{-- KPI CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">

                    {{-- Total Users --}}
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Users</span>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-slate-800" x-text="kpi.total">0</div>
                        <p class="text-xs text-slate-400 mt-1">Registered in journal</p>
                    </div>

                    {{-- New Users (30d) --}}
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">New (30d)</span>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-slate-800" x-text="kpi.new">0</div>
                        <p class="text-xs text-slate-400 mt-1">Last 30 days</p>
                    </div>

                    {{-- Active Users (90d) --}}
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active (90d)</span>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-slate-800" x-text="kpi.active">0</div>
                        <p class="text-xs text-slate-400 mt-1">Logged in recently</p>
                    </div>

                    {{-- Registered This Year --}}
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">This Year</span>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-slate-800" x-text="kpi.registered_this_year">0</div>
                        <p class="text-xs text-slate-400 mt-1">Joined in {{ date('Y') }}</p>
                    </div>

                    {{-- Active This Month --}}
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">This Month</span>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-slate-800" x-text="kpi.active_this_month">0</div>
                        <p class="text-xs text-slate-400 mt-1">Active users</p>
                    </div>

                </div>

                {{-- CHARTS & LEADERBOARD ROW --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Role Distribution (Donut Chart) --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                        <div class="mb-4">
                            <h3 class="font-bold text-slate-700">Role Distribution</h3>
                            <p class="text-xs text-slate-400">Users by role type</p>
                        </div>
                        <div id="roleChart" class="h-72"></div>
                    </div>

                    {{-- Registration Growth (Area Chart) --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                        <div class="mb-4">
                            <h3 class="font-bold text-slate-700">Registration Growth</h3>
                            <p class="text-xs text-slate-400">Monthly new registrations</p>
                        </div>
                        <div id="growthChart" class="h-72"></div>
                    </div>

                    {{-- Top Reviewers Leaderboard --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                        <div class="mb-4 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-700">Top Reviewers</h3>
                                <p class="text-xs text-slate-400">By completed reviews</p>
                            </div>
                        </div>
                        <ul class="space-y-2">
                            <template x-for="(user, index) in leaderboard.reviewers" :key="user.id">
                                <li
                                    class="flex justify-between items-center p-3 rounded-xl hover:bg-slate-50 transition-all">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold flex items-center justify-center"
                                            x-text="index + 1"></span>
                                        <span class="text-sm font-medium text-slate-700" x-text="user.name"></span>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                        <span x-text="user.count"></span> reviews
                                    </span>
                                </li>
                            </template>
                            <template x-if="!leaderboard.reviewers || leaderboard.reviewers.length === 0">
                                <li class="text-sm text-slate-400 italic text-center py-6">
                                    No completed reviews yet.
                                </li>
                            </template>
                        </ul>
                    </div>

                </div>

                {{-- TOP AUTHORS SECTION --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                    <div class="mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-700">Top Authors</h3>
                            <p class="text-xs text-slate-400">By submission count</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <template x-for="(author, index) in leaderboard.authors" :key="author.id">
                            <div
                                class="flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 hover:shadow-md transition-all">
                                <div class="w-12 h-12 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-lg mb-2"
                                    x-text="author.name ? author.name.charAt(0).toUpperCase() : '?'"></div>
                                <span class="text-sm font-medium text-slate-700 text-center line-clamp-1"
                                    x-text="author.name"></span>
                                <span class="text-xs text-indigo-600 font-semibold mt-1">
                                    <span x-text="author.count"></span> submissions
                                </span>
                            </div>
                        </template>
                        <template x-if="!leaderboard.authors || leaderboard.authors.length === 0">
                            <div class="col-span-full text-sm text-slate-400 italic text-center py-6">
                                No submissions yet.
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </template>
    </div>

    {{-- APEXCHARTS CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        function userDashboard() {
            return {
                loading: true,
                kpi: {
                    total: 0,
                    new: 0,
                    active: 0,
                    registered_this_year: 0,
                    active_this_month: 0
                },
                leaderboard: {
                    reviewers: [],
                    authors: []
                },
                roleChart: null,
                growthChart: null,

                init() {
                    this.fetchData();
                },

                async fetchData() {
                    this.loading = true;

                    try {
                        const response = await fetch(
                            `{{ route('journal.settings.statistics.users.data', ['journal' => $journal->slug]) }}`
                        );
                        const data = await response.json();

                        this.kpi = data.kpi;
                        this.leaderboard = data.leaderboard;

                        this.loading = false;

                        // Wait for DOM update then render charts
                        this.$nextTick(() => {
                            this.renderCharts(data);
                        });
                    } catch (error) {
                        console.error('Error fetching user data:', error);
                        this.loading = false;
                    }
                },

                renderCharts(data) {
                    // Destroy existing charts
                    if (this.roleChart) this.roleChart.destroy();
                    if (this.growthChart) this.growthChart.destroy();

                    // 1. Role Distribution (Donut)
                    this.roleChart = new ApexCharts(document.querySelector("#roleChart"), {
                        chart: {
                            type: 'donut',
                            height: 288,
                            fontFamily: 'inherit',
                        },
                        series: data.roles.series || [],
                        labels: data.roles.labels || [],
                        colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'],
                        legend: {
                            position: 'bottom',
                            fontSize: '11px',
                            labels: {
                                colors: '#64748b'
                            },
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'Total',
                                            color: '#475569',
                                            formatter: (w) => {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                    });
                    this.roleChart.render();

                    // 2. Registration Growth (Area)
                    this.growthChart = new ApexCharts(document.querySelector("#growthChart"), {
                        chart: {
                            type: 'area',
                            height: 288,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'inherit',
                        },
                        series: [{
                            name: 'New Users',
                            data: data.growth.data || []
                        }],
                        xaxis: {
                            categories: data.growth.categories || [],
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10px'
                                },
                                rotate: -45,
                            },
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '11px'
                                }
                            },
                        },
                        colors: ['#8b5cf6'],
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                opacityFrom: 0.5,
                                opacityTo: 0.05
                            }
                        },
                        grid: {
                            borderColor: '#e2e8f0',
                            strokeDashArray: 4,
                        },
                        dataLabels: {
                            enabled: false
                        },
                    });
                    this.growthChart.render();
                },
            };
        }
    </script>
@endsection
