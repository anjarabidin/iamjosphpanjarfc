@extends('layouts.master', ['title' => 'Detail Statistik Iklan', 'main' => 'Dashboard'])

@section('content')
    @php
        use Illuminate\Support\Str;
        use Carbon\Carbon;

        $isActive = (bool) ($statistics['is_currently_active'] ?? false);
        $statusColor = $isActive ? 'success' : 'danger';
        $statusText = $isActive ? 'Aktif' : 'Tidak Aktif';

        $startStr = $homeAds->start_date
            ? Carbon::parse($homeAds->start_date)->locale('id')->isoFormat('dddd, D MMMM YYYY')
            : 'Tidak ditentukan';

        $endStr = $homeAds->end_date
            ? Carbon::parse($homeAds->end_date)->locale('id')->isoFormat('dddd, D MMMM YYYY')
            : 'Tidak ditentukan';

        $totalViews = (int) ($statistics['total_views'] ?? 0);
        $totalClicks = (int) ($statistics['total_clicks'] ?? 0);
        $ctrPct = number_format((float) ($statistics['ctr_percentage'] ?? 0), 2);
        $progressPct = max(0, min(100, (float) ($statistics['campaign_progress'] ?? 0)));

        $totalDays = (int) ($statistics['total_campaign_days'] ?? 0);
        $daysElapsed = (int) ($statistics['days_elapsed'] ?? 0);
        $daysRemaining = $statistics['days_remaining'] ?? 0; // bisa negatif saat lewat masa kampanye

        $avgViewsDay = number_format((float) ($statistics['avg_views_per_day'] ?? 0), 1);
        $avgClicksDay = number_format((float) ($statistics['avg_clicks_per_day'] ?? 0), 1);

        $projViews = number_format((float) ($statistics['projected_total_views'] ?? 0));
        $projClicks = number_format((float) ($statistics['projected_total_clicks'] ?? 0));

        $pendingViews = number_format((int) ($statistics['pending_views'] ?? 0));
        $pendingClicks = number_format((int) ($statistics['pending_clicks'] ?? 0));
        $hasPending = (int) ($statistics['pending_views'] ?? 0) > 0 || (int) ($statistics['pending_clicks'] ?? 0) > 0;
    @endphp

    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <!--begin::Navbar / Header Card-->
                    <div class="card mb-5 mb-xxl-8">
                        <div class="card-body pt-9 pb-0">
                            <!--begin::Details-->
                            <div class="d-flex flex-wrap flex-sm-nowrap align-items-start">
                                <!--begin::Media-->
                                <div class="me-7 mb-4">
                                    <div
                                        class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative overflow-hidden rounded-2">
                                        @if (strtolower($homeAds->media_type) === 'video')
                                            <video class="w-100 h-100" style="object-fit: cover;" controls playsinline>
                                                <source src="{{ asset($homeAds->media_url) }}" type="video/mp4">
                                                Browser Anda tidak mendukung video.
                                            </video>
                                        @else
                                            <img src="{{ asset($homeAds->media_url) }}" alt="Media Iklan"
                                                class="w-100 h-100" loading="lazy" style="object-fit: cover;">
                                        @endif

                                        <!-- Status dot -->
                                        <span
                                            class="position-absolute bottom-0 end-0 translate-middle p-2 rounded-circle border border-4 border-body bg-{{ $statusColor }}"
                                            title="{{ $statusText }}" aria-label="Status: {{ $statusText }}"></span>
                                    </div>
                                </div>
                                <!--end::Media-->

                                <!--begin::Info-->
                                <div class="flex-grow-1 w-100">
                                    <!--begin::Title-->
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2 w-100">
                                        <div class="d-flex flex-column pe-3">
                                            <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
                                                <span class="text-gray-900 text-hover-primary fs-3 fw-bold">Detail Statistik
                                                    Iklan</span>
                                                <span
                                                    class="badge badge-light-{{ $statusColor }} fs-8 fw-semibold">{{ $statusText }}</span>
                                            </div>

                                            <ul class="list-inline text-gray-600 fw-semibold fs-7 mb-0">
                                                <li class="list-inline-item me-4">
                                                    <i class="ki-duotone ki-profile-circle fs-5 me-1"><span
                                                            class="path1"></span><span class="path2"></span><span
                                                            class="path3"></span></i>
                                                    Tipe Media: <span
                                                        class="text-gray-800">{{ ucfirst($homeAds->media_type) }}</span>
                                                </li>
                                                <li class="list-inline-item me-4">
                                                    <i class="ki-duotone ki-geolocation fs-5 me-1"><span
                                                            class="path1"></span><span class="path2"></span></i>
                                                    Urutan: <span class="text-gray-800">{{ $homeAds->order }}</span>
                                                </li>
                                                @if ($homeAds->link)
                                                    <li class="list-inline-item d-inline-flex align-items-center">
                                                        <i class="ki-duotone ki-sms fs-5 me-1"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                        <a href="{{ $homeAds->link }}" target="_blank" rel="noopener"
                                                            class="text-primary text-decoration-underline">{{ Str::limit($homeAds->link, 50) }}</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="d-flex my-3">
                                            <a href="{{ route('home-ads.edit', $homeAds->id) }}"
                                                class="btn btn-sm btn-primary me-2">Edit Iklan</a>
                                            <a href="{{ route('home-ads.index') }}"
                                                class="btn btn-sm btn-light">Kembali</a>
                                        </div>
                                    </div>
                                    <!--end::Title-->

                                    <!--begin::Compact Overview (4 stats, ringkas & responsif)-->
                                    <div class="border rounded-2 p-3 p-md-4 mt-3 bg-light">
                                        <div class="row row-cols-2 row-cols-md-4 g-3 g-md-4 align-items-stretch">
                                            <div class="col">
                                                <div
                                                    class="h-100 bg-white rounded-2 p-3 shadow-sm d-flex flex-column justify-content-between">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="text-muted fs-7">Total Views</span>
                                                        <i class="ki-duotone ki-eye fs-4 text-warning"><span
                                                                class="path1"></span><span class="path2"></span><span
                                                                class="path3"></span></i>
                                                    </div>
                                                    <div class="fs-5 fw-bold">{{ number_format($totalViews) }}</div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div
                                                    class="h-100 bg-white rounded-2 p-3 shadow-sm d-flex flex-column justify-content-between">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="text-muted fs-7">Total Clicks</span>
                                                        <i class="ki-duotone ki-click fs-4 text-primary"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                    <div class="fs-5 fw-bold">{{ number_format($totalClicks) }}</div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div
                                                    class="h-100 bg-white rounded-2 p-3 shadow-sm d-flex flex-column justify-content-between">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="text-muted fs-7">CTR</span>
                                                        <i class="ki-duotone ki-chart-simple fs-4 text-info"><span
                                                                class="path1"></span><span class="path2"></span><span
                                                                class="path3"></span><span class="path4"></span></i>
                                                    </div>
                                                    <div class="fs-5 fw-bold">{{ $ctrPct }}%</div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="h-100 bg-white rounded-2 p-3 shadow-sm">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="text-muted fs-7">Progress</span>
                                                        <i class="ki-duotone ki-calendar fs-4 text-success"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                    <div class="progress h-5px bg-light-success mb-1" role="progressbar"
                                                        aria-valuenow="{{ $progressPct }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        <div class="progress-bar bg-success"
                                                            style="width: {{ $progressPct }}%;"></div>
                                                    </div>
                                                    <div class="fs-7 text-gray-700 fw-semibold">{{ $progressPct }}%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Compact Overview-->
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Details-->

                            <!--begin::Navs-->
                            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-bold mt-6">
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary active" href="#statistik">Statistik &
                                        Analytics</a>
                                </li>
                            </ul>
                            <!--end::Navs-->
                        </div>
                    </div>
                    <!--end::Navbar / Header Card-->

                    <!--begin::Row : Timeline -->
                    <div id="statistik" class="row g-5 g-xxl-8 mb-2">
                        <!--begin::Col Timeline-->
                        <div class="col-12 col-xxl-6">
                            <div class="card card-flush h-100">
                                <div class="card-header pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-4 mb-1">Periode Kampanye</span>
                                        <span class="text-muted fw-semibold fs-7">Timeline dan durasi iklan</span>
                                    </h3>
                                </div>

                                <div class="card-body pt-5">
                                    <div class="row g-5 align-items-stretch">
                                        <!-- Left: Timeline -->
                                        <div class="col-12 col-lg-7">
                                            <div class="timeline-label pe-lg-4">
                                                <!-- Mulai -->
                                                <div class="timeline-item">
                                                    <div class="timeline-label fw-bold text-gray-800 fs-7">Mulai</div>
                                                    <div class="timeline-badge">
                                                        <i class="ki-duotone ki-abstract-8 text-success fs-2"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                    <div class="fw-muted text-muted ps-3">{{ $startStr }}</div>
                                                </div>
                                                <!-- Berakhir -->
                                                <div class="timeline-item">
                                                    <div class="timeline-label fw-bold text-gray-800 fs-7">Berakhir</div>
                                                    <div class="timeline-badge">
                                                        <i class="ki-duotone ki-abstract-8 text-danger fs-2"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                    <div class="fw-muted text-muted ps-3">{{ $endStr }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right: Ringkasan Periode -->
                                        <div class="col-12 col-lg-5">
                                            <div class="h-100 bg-light rounded-2 p-4 shadow-sm">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="ki-duotone ki-calendar fs-2 me-2 text-success"><span
                                                            class="path1"></span><span class="path2"></span></i>
                                                    <span class="fw-bold">Ringkasan Periode</span>
                                                </div>

                                                <div class="d-flex justify-content-between py-2">
                                                    <span class="text-gray-600">Total Hari Kampanye</span>
                                                    <span class="fw-bold text-gray-800">{{ $totalDays }} hari</span>
                                                </div>
                                                <div class="separator separator-dashed my-2"></div>

                                                <div class="d-flex justify-content-between py-2">
                                                    <span class="text-gray-600">Hari Berlalu</span>
                                                    <span class="fw-bold text-gray-800">{{ $daysElapsed }} hari</span>
                                                </div>
                                                <div class="separator separator-dashed my-2"></div>

                                                <div class="d-flex justify-content-between py-2">
                                                    <span class="text-gray-600">Sisa Hari</span>
                                                    <span
                                                        class="fw-bold text-{{ $daysRemaining < 0 ? 'danger' : 'primary' }}">{{ $daysRemaining ?? 0 }}
                                                        hari</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Col Timeline-->

                        <!--begin::Col Spacer (keeps layout balanced on xl) -->
                        <div class="col-12 col-xxl-6 d-none d-xxl-block"></div>
                        <!--end::Col Spacer -->
                    </div>
                    <!--end::Row : Timeline -->

                    <!--begin::Row : Detail Cards-->
                    <div class="row g-5 g-xxl-8">
                        <!-- Performa Harian -->
                        <div class="col-12 col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-header pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-4 mb-1">Performa Harian</span>
                                        <span class="text-muted fw-semibold fs-7">Rata-rata aktivitas per hari</span>
                                    </h3>
                                </div>
                                <div class="card-body py-5">
                                    <div class="d-flex flex-stack py-1">
                                        <div class="text-gray-700 fw-semibold fs-7 me-2">Views/Hari</div>
                                        <div class="d-flex align-items-center"><span
                                                class="text-gray-900 fw-bolder fs-6">{{ $avgViewsDay }}</span></div>
                                    </div>
                                    <div class="separator separator-dashed my-3"></div>
                                    <div class="d-flex flex-stack py-1">
                                        <div class="text-gray-700 fw-semibold fs-7 me-2">Clicks/Hari</div>
                                        <div class="d-flex align-items-center"><span
                                                class="text-gray-900 fw-bolder fs-6">{{ $avgClicksDay }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Proyeksi -->
                        <div class="col-12 col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-header pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-4 mb-1">Proyeksi Total</span>
                                        <span class="text-muted fw-semibold fs-7">Estimasi akhir kampanye</span>
                                    </h3>
                                </div>
                                <div class="card-body py-5">
                                    <div class="d-flex flex-stack py-1">
                                        <div class="text-gray-700 fw-semibold fs-7 me-2">Proyeksi Views</div>
                                        <div class="d-flex align-items-center"><span
                                                class="text-gray-900 fw-bolder fs-6">{{ $projViews }}</span></div>
                                    </div>
                                    <div class="separator separator-dashed my-3"></div>
                                    <div class="d-flex flex-stack py-1">
                                        <div class="text-gray-700 fw-semibold fs-7 me-2">Proyeksi Clicks</div>
                                        <div class="d-flex align-items-center"><span
                                                class="text-gray-900 fw-bolder fs-6">{{ $projClicks }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Real-time -->
                        <div class="col-12 col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-header pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-4 mb-1">Data Real-time</span>
                                        <span class="text-muted fw-semibold fs-7">Pending di cache Redis</span>
                                    </h3>
                                </div>
                                <div class="card-body py-5" aria-live="polite">
                                    <div class="d-flex flex-stack py-1">
                                        <div class="text-gray-700 fw-semibold fs-7 me-2">Pending Views</div>
                                        <div class="d-flex align-items-center"><span
                                                class="text-warning fw-bolder fs-6">{{ $pendingViews }}</span></div>
                                    </div>
                                    <div class="separator separator-dashed my-3"></div>
                                    <div class="d-flex flex-stack py-1">
                                        <div class="text-gray-700 fw-semibold fs-7 me-2">Pending Clicks</div>
                                        <div class="d-flex align-items-center"><span
                                                class="text-warning fw-bolder fs-6">{{ $pendingClicks }}</span></div>
                                    </div>

                                    @if ($hasPending)
                                        <div class="separator separator-dashed my-3"></div>
                                        <div
                                            class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-3">
                                            <i class="ki-duotone ki-information fs-2tx text-warning me-4"><span
                                                    class="path1"></span><span class="path2"></span><span
                                                    class="path3"></span></i>
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-7 text-gray-700">Data belum tersinkronisasi</div>
                                                    <div class="fs-8 text-gray-500">Akan otomatis tersinkron dalam beberapa
                                                        menit</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Row : Detail Cards-->

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
@endsection
