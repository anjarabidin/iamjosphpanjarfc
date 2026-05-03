@extends('layouts.master', ['title' => 'Dashboard Overview', 'main' => 'Dashboard'])
@push('css')
    <style>
        .title {
            font-size: 2.125rem;
            font-weight: 700;
        }

        .subs {
            font-weight: 500;
            color: #B5B5C3;
        }

        .bg-nonaktif {
            background-color: #E4E6EF;
        }

        .bg-freeze {
            background-color: #4B5675;
        }

        .label-chart {
            font-weight: 500;
            color: #B5B5C3;
            font-size: 0.875rem;
        }

        .border-custome {
            border-right: 1px solid var(--bs-card-border-color);
        }

        .max-content {
            width: max-content;
        }

        .card-menu {
            position: relative;
            display: inline-block;
        }

        .card-menu-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #B5B5C3;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .card-menu-toggle:hover {
            background-color: #f8f9fa;
            color: #5E6278;
        }

        .card-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e4e6ef;
            border-radius: 6px;
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
            min-width: 150px;
            z-index: 1000;
            display: none;
        }

        .card-menu-dropdown.show {
            display: block;
        }

        .card-menu-item {
            display: block;
            padding: 8px 16px;
            color: #5E6278;
            text-decoration: none;
            font-size: 0.875rem;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .card-menu-item:hover {
            background-color: #f8f9fa;
            color: #181C32;
        }

        .card-menu-item i {
            margin-right: 8px;
            width: 16px;
        }
    </style>
    <style>
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .1) !important;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        .bg-blue {
            background-color: #007bff;
        }

        .bg-pink {
            background-color: #e91e63;
        }

        .info-icon {
            position: relative;
            display: inline-flex;
            font-size: 1rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #f1f1f1;
            border: 1px solid #dadce0;
            align-items: center;
            justify-content: center;
            color: #5E6278;
            cursor: help;
            opacity: 0.5;
            z-index: 5;
            padding: 0;
            line-height: 1;
            margin-left: 6px;
            vertical-align: middle;
        }

        .info-icon i {
            font-size: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-icon:hover {
            opacity: 1;
            background-color: #e8e8e8;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
        }

        /* Bootstrap tooltip customization */
        .tooltip-inner {
            max-width: 280px;
            padding: 8px 12px;
            text-align: left;
            background-color: #ffffff;
            color: #000000;
            font-weight: 500;
            border-radius: 4px;
            font-size: 0.8rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
            border: 1px solid #e4e6ef;
        }

        /* Dark mode tooltip alternative */
        .tooltip-inner.tooltip-dark {
            background-color: #1e1e2d;
            color: #ffffff;
            border-color: #1e1e2d;
            font-weight: 400;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
        }

        .tooltip.bs-tooltip-auto[x-placement^=top] .arrow::before,
        .tooltip.bs-tooltip-top .arrow::before {
            border-top-color: #ffffff;
        }

        .tooltip.bs-tooltip-auto[x-placement^=bottom] .arrow::before,
        .tooltip.bs-tooltip-bottom .arrow::before {
            border-bottom-color: #ffffff;
        }

        /* Dark mode tooltip arrow colors */
        .tooltip.tooltip-dark.bs-tooltip-auto[x-placement^=top] .arrow::before,
        .tooltip.tooltip-dark.bs-tooltip-top .arrow::before {
            border-top-color: #1e1e2d;
        }

        .tooltip.tooltip-dark.bs-tooltip-auto[x-placement^=bottom] .arrow::before,
        .tooltip.tooltip-dark.bs-tooltip-bottom .arrow::before {
            border-bottom-color: #1e1e2d;
        }

        @media (max-width: 768px) {
            .fs-4 {
                font-size: 1.1rem !important;
            }

            .fs-3 {
                font-size: 1.3rem !important;
            }

            .fs-2 {
                font-size: 1.5rem !important;
            }
        }
    </style>
@endpush
@section('content')
    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    @if (!$isPasswordSafe)
                        <div class="container-fluid">
                            <div class="alert alert-danger fade show mt-4 mb-2" role="alert">
                                <strong>Perhatian!</strong> Password Anda tidak memenuhi kriteria keamanan. Segera ganti
                                password Anda
                                <a href="{{ route('profile-admin.edit') }}" class="text-primary">Ganti Password
                                    Sekarang</a>.
                            </div>
                        </div>
                    @endif
                    <div class="d-flex align-items-center justify-content-end gap-3 my-4">
                        <!-- Tempat Gym -->
                        <div>
                            <label for="gym_place_id" class="form-label fw-semibold mb-1">Tempat Gym</label>
                            <select class="form-select form-select-sm" id="gym_place_id" name="gym_place_id">
                                <option value="">Semua Tempat Gym</option>
                                @foreach ($gymPlaces as $place)
                                    <option value="{{ $place->id }}">{{ $place->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Filter -->
                        <div>
                            <label for="dateRange" class="form-label fw-semibold mb-1">Pilih Rentang Tanggal</label>
                            <div class="d-flex align-items-center border rounded px-3 py-1 bg-light">
                                <input placeholder="Pick date range"
                                    class="form-control form-control-sm bg-transparent border-0 text-dark fw-semibold me-2"
                                    id="dateRange" />
                                <i class="ki-duotone ki-calendar fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                            </div>
                            <input type="text" id="start_date" hidden>
                            <input type="text" id="end_date" hidden>
                        </div>
                    </div>

                    {{-- 

                    <div style="width: max-content" class="ms-auto my-4">
                        <label for="gymPlaceSelect" class="btn btn-sm btn-light text-dark fw-600 d-flex align-items-center px-4">
                            Tempat Gym
                        </label>
                        <select class="form-select form-select-sm" id="gym_place_id" name="gym_place_id">
                            @foreach ($gymPlaces as $place)
                                <option value="{{ $place->id }}">{{ $place->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="width: max-content" class="ms-auto my-4">
                        <label for="dateRange" class="btn btn-sm btn-light text-dark fw-600 d-flex align-items-center px-4">
                            <input placeholder="Pick date range" class="bg-transparent text-dark fw-600 cursor-pointer"
                                id="dateRange" />
                            <i class="ki-duotone ki-calendar fs-1 ms-0 me-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                        </label>
                        <input type="text" id="start_date" hidden>
                        <input type="text" id="end_date" hidden>
                    </div> --}}

                    {{-- Top Level Metrics --}}
                    <div id="top-level-metrics" class="row mb-4">
                        {{-- Total App Downloads --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-muted mb-1 fw-normal">
                                                Total App Downloads
                                                <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-theme="light"
                                                    title="Data jumlah total download aplikasi mobile dari semua platform.">
                                                    <i class="fas fa-info"></i>
                                                </div>
                                            </h6>
                                            <h2 class="mb-0 fw-bold text-info"><span id="total-download-2">0</span></h2>
                                            <small class="text-info">
                                                <i class="fas fa-download me-1"></i>
                                                Mobile app installs
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                                <i class="fas fa-mobile-alt text-info fs-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total Registered Members --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="card-menu position-absolute top-0 end-0 mt-2 me-2">
                                        <button class="card-menu-toggle" type="button" onclick="toggleCardMenu(this)">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="card-menu-dropdown">
                                            <button class="card-menu-item" onclick="exportMembershipStats('total_members')">
                                                <i class="fas fa-file-excel"></i>
                                                Download Excel
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-muted mb-1 fw-normal">
                                                Total Members
                                                <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Data jumlah total anggota yang berlangganan.">
                                                    <i class="fas fa-info"></i>
                                                </div>
                                            </h6>
                                            <h2 class="mb-0 fw-bold text-primary"><span
                                                    id="total-member-registered">0</span>
                                            </h2>
                                            <small class="text-primary">
                                                <i class="fas fa-user-plus me-1"></i>
                                                All members registered
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                <i class="fas fa-users text-primary fs-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Conversion Rate --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-muted mb-1 fw-normal">
                                                Conversion Rate
                                                <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Persentase pengguna yang mengunduh aplikasi dan berlangganan membership">
                                                    <i class="fas fa-info"></i>
                                                </div>
                                            </h6>
                                            <h2 class="mb-0 fw-bold text-success">
                                                <span id="conversion-rate">0%</span>
                                            </h2>
                                            <br>
                                            <small class="text-success">
                                                <i class="fas fa-chart-line me-1"></i>
                                                Download to membership
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                                <i class="fas fa-percentage text-success fs-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Current Membership Status --}}
                    <div class="row g-4 mb-6">
                        <div class="col-12">
                            <h5 class="mb-3 text-dark fw-semibold">Current Membership Status</h5>
                        </div>

                        {{-- Baris pertama: 3 kartu --}}
                        <div class="col-12 col-md-4">
                            <x-card.membership-card icon="fas fa-check-circle" color="success" title="Active Memberships"
                                subtitle="Currently Active"
                                tooltip="Data anggota dengan paket keanggotaan yang masih aktif."
                                value_id="active-membership" percent_id="percent-active-membership"
                                progress_id="progress-active-membership" show_menu="true" export_types="active" />
                        </div>

                        <div class="col-12 col-md-4">
                            <x-card.membership-card icon="fas fa-exclamation-triangle" color="danger"
                                title="Expired Memberships" subtitle="Expired"
                                tooltip="Data anggota dengan paket keanggotaan yang telah berakhir dan perlu perpanjangan."
                                value_id="expired-membership" percent_id="percent-expired-membership"
                                progress_id="progress-expired-membership" show_menu="true" export_types="expired" />
                        </div>

                        <div class="col-12 col-md-4">
                            <x-card.membership-card icon="fas fa-user-times" color="warning" title="Membership Leave"
                                subtitle="" tooltip="Data total member yang mempunyai membership dan sedang cuti."
                                value_id="timeoff-membership" percent_id="percent-timeoff-membership"
                                progress_id="progress-timeoff-membership" show_menu="true" export_types="timeoff" />
                        </div>

                        {{-- Baris kedua: 2 kartu full width --}}
                        <div class="col-12 col-md-6">
                            <x-card.membership-card icon="fas fa-thumbs-up" color="success" title="Total Compliment"
                                subtitle="" tooltip="Data total member aktif dengan status compliment aktif"
                                value_id="total-compliment" percent_id="percent-compliment"
                                progress_id="progress-compliment" show_menu="true" export_types="complimentary" />
                        </div>

                        <div class="col-12 col-md-6">
                            <x-card.membership-card icon="fas fa-calendar-day" color="primary" title="Total Daily"
                                subtitle="" tooltip="Data dari kunjungan harian atau pengunjung tanpa langganan tetap."
                                value_id="total-daily" show_menu="true" export_types="daily" />
                        </div>
                    </div>

                    {{-- Member Status Breakdown --}}
                    <div class="row g-4" id="member-status-breakdown">
                        <div class="col-12">
                            <h5 class="mb-3 text-dark fw-semibold">Member Status Breakdown</h5>
                            {{-- <p class="text-muted mb-4">
                                From <span id="total-download-3">0</span> total registered users:
                            </p> --}}
                        </div>

                        {{-- Purchased Membership --}}
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">

                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 mx-auto mb-3"
                                        style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-id-card text-primary fs-3"></i>
                                    </div>
                                    <h3 class="fw-bold text-primary mb-1"><span id="purchase_membership">0</span></h3>
                                    <h6 class="text-muted mb-2">Purchased to Membership
                                        <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Data anggota yang sudah membeli paket keanggotaan gym.">
                                            <i class="fas fa-info"></i>
                                        </div>
                                    </h6>
                                    <small class="text-primary"><span id="purchase_percent">0</span>% of total
                                        users</small>
                                    <div class="progress mt-2" style="height: 20px;">
                                        <div class="progress-bar bg-primary" id="purchase_bar" style="width: 0%">0%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Never Purchased --}}
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-3 mx-auto mb-3"
                                        style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user-slash text-secondary fs-3"></i>
                                    </div>
                                    <h3 class="fw-bold text-secondary mb-1"><span id="never_purchase_membership">0</span>
                                    </h3>
                                    <h6 class="text-muted mb-2">Never Join Membership
                                        <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Data anggota yang belum pernah membeli paket keanggotaan gym.">
                                            <i class="fas fa-info"></i>
                                        </div>
                                    </h6>
                                    <small class="text-secondary"><span id="never_purchase_percent">0</span>% of total
                                        users</small>
                                    <div class="progress mt-2" style="height: 20px;">
                                        <div class="progress-bar bg-primary" id="never_purchase_bar" style="width: 0%">0%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Demographics & Services Row --}}
                    <div class="row">
                        {{-- Gender Demographics --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div
                                    class="card-header bg-transparent border-0 pb-0 pt-5 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-semibold text-dark">Member Demographics
                                        <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Data membership berdasarkan jenis kelamin.">
                                            <i class="fas fa-info"></i>
                                        </div>
                                    </h5>
                                    {{-- <div class="card-menu">
                                        <button class="card-menu-toggle" type="button" onclick="toggleCardMenu(this)">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="card-menu-dropdown">
                                            <button class="card-menu-item" onclick="exportGenderData()">
                                                <i class="fas fa-file-excel"></i>
                                                Download Excel
                                            </button>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center p-3">
                                                <div class="bg-blue bg-opacity-10 rounded-circle p-3 mx-auto mb-2"
                                                    style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-mars text-primary fs-2"></i>
                                                </div>
                                                <h4 class="fw-bold text-primary mb-1"><span id="male-members">0</span>
                                                </h4>
                                                <p class="text-muted mb-0">Male Members</p>
                                                <small class="text-primary"><span
                                                        id="percent-male-members">0</span>%</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3">
                                                <div class="bg-pink bg-opacity-10 rounded-circle p-3 mx-auto mb-2"
                                                    style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-venus" style="color: #e91e63; font-size: 2rem;"></i>
                                                </div>
                                                <h4 class="fw-bold mb-1" style="color: #e91e63;"><span
                                                        id="female-members">0</span></h4>
                                                <p class="text-muted mb-0">Female Members</p>
                                                <small style="color: #e91e63;"><span
                                                        id="percent-female-members">0</span>%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress" style="height: 8px;">
                                            <div id="bar-male-members" class="progress-bar bg-primary" style="width: 0%">
                                            </div>
                                            <div id="bar-female-members" class="progress-bar"
                                                style="background-color: #e91e63; width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Coach Services --}}
                        {{-- Coach Services --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pb-0 pt-5">
                                    <h5 class="mb-0 fw-semibold text-dark">Personal Training Services
                                        <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Data membership berdasarkan paket coach">
                                            <i class="fas fa-info"></i>
                                        </div>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center p-3">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-3 mx-auto mb-2"
                                                    style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-dumbbell text-success fs-2"></i>
                                                </div>
                                                <h4 class="fw-bold text-success mb-1"><span id="with-coach">0</span></h4>
                                                <p class="text-muted mb-0">With Coach</p>
                                                <small class="text-success"><span
                                                        id="percent-with-coach">0</span>%</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3">
                                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3 mx-auto mb-2"
                                                    style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user text-secondary fs-2"></i>
                                                </div>
                                                <h4 class="fw-bold text-secondary mb-1"><span id="self-training">0</span>
                                                </h4>
                                                <p class="text-muted mb-0">Self Training</p>
                                                <small class="text-secondary"><span
                                                        id="percent-self-training">0</span>%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress" style="height: 8px;">
                                            <div id="bar-with-coach" class="progress-bar bg-success" style="width: 0%">
                                            </div>
                                            <div id="bar-self-training" class="progress-bar bg-secondary"
                                                style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5 g-5 g-xl-8">
                        <!-- Total Member -->

                        <div class="col-12">
                            {{-- Check in & check out --}}
                            <div class="card">
                                <div class="card-header card-header-stretch">
                                    <h3 class="card-title">Check In & Check Out</h3>
                                    <div class="card-toolbar">
                                        <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                                            <li class="nav-item">
                                                <a class="nav-link custom-tab active" data-bs-toggle="tab"
                                                    href="#kt_tab_today">Hari Ini</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link custom-tab" data-bs-toggle="tab" href="#kt_tab_week">7
                                                    hari terakhir</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link custom-tab" data-bs-toggle="tab"
                                                    href="#kt_tab_month">30
                                                    hari terakhir</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link custom-tab" data-bs-toggle="tab"
                                                    href="#kt_tab_custom">Custom</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 border-custome">
                                        <div class="p-5">
                                            <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-5 pb-3 my-4"
                                                style="height: 8rem">
                                                <span class="fs-4 fw-semibold text-primary d-block">Check In</span>
                                                <span id="checkInCount" class="fs-2hx fw-bold text-gray-900"
                                                    data-kt-countup="true" data-kt-countup-value="0"></span>
                                            </div>
                                            <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-5 pb-3 my-4"
                                                style="height: 8rem">
                                                <span class="fs-4 fw-semibold text-danger d-block">Check Out</span>
                                                <span id="checkOutCount" class="fs-2hx fw-bold text-gray-900"
                                                    data-kt-countup="true" data-kt-countup-value="0"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <ul class="nav nav-pills max-content nav-line-pills border rounded p-1">
                                                <li class="nav-item me-2">
                                                    <a class="nav-link btn btn-active-light btn-active-color-gray-700 btn-color-gray-400 py-2 px-5 fs-6 fw-semibold active"
                                                        data-bs-toggle="tab" id="kt_checkin" href="#kt_tab_checkin">Check
                                                        In</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link btn btn-active-light btn-active-color-gray-700 btn-color-gray-400 py-2 px-5 fs-6 fw-semibold"
                                                        data-bs-toggle="tab" id="kt_checkout"
                                                        href="#kt_tab_checkout">Check
                                                        Out</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="myTabContent">
                                                <div class="tab-content px-3">
                                                    <!--begin::Tab pane-->
                                                    <div class="tab-pane fade active show" id="kt_tab_checkin"
                                                        role="tabpanel">
                                                        <!--begin::Chart-->
                                                        <div id="kt_chart_checkin" class="w-100" style="height: 21rem">
                                                        </div>
                                                        <!--end::Chart-->
                                                    </div>
                                                    <!--end::Tab pane-->
                                                    <!--begin::Tab pane-->
                                                    <div class="tab-pane fade" id="kt_tab_checkout" role="tabpanel">
                                                        <!--begin::Chart-->
                                                        <div id="kt_chart_checkout" class="w-100" style="height: 21rem">
                                                        </div>
                                                        <!--end::Chart-->
                                                    </div>
                                                    <!--end::Tab pane-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Data member circulation -->
                        <div class="col-xl-12">
                            <div class="card h-100 p-4 shadow-sm border-0">
                                <div class="col">
                                    <div
                                        class="card-header row d-flex flex-row justify-content-between align-items-center">
                                        <div class="col d-flex gap-2 justify-content-start align-items-center">
                                            <h3 class="card-title m-0">Data Member Circulation</h3>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#infoModal">
                                                <i class="fas fa-info-circle text-primary"></i>
                                            </a>
                                        </div>
                                        <div class="col d-flex justify-content-end gap-2">
                                            <div class="btn btn-primary d-flex flex-row gap-2" id="exportMembershipData">
                                                <img src="{{ asset('assets/media/icons/printer-fill.svg') }}"
                                                    alt="Export">Export
                                            </div>
                                            <select id="yearMember" class="form-select w-50" aria-label="Pilih tahun">
                                                @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                                    <option value="{{ $year }}"
                                                        {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center align-items-center">
                                    <!-- Tabel -->
                                    <div class="col-lg-12 col-md-12 mb-4">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped m-0">
                                                <thead>
                                                    <tr>
                                                        <th id="dynamic-colspan-header" colspan="9"
                                                            class="text-center bg-primary">
                                                            <h4 class="card-title m-0">JOIN VS EXPIRED MEMBER by MONTH</h4>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            MONTH
                                                        </th>
                                                        <th rowspan="2" class="text-center align-middle bg-light">TOTAL
                                                            MEMBER</th>
                                                        <th rowspan="2" class="text-center align-middle bg-light">NEW
                                                            MEMBER
                                                        </th>
                                                        <th rowspan="2" class="text-center align-middle bg-light">
                                                            EXPIRED
                                                        </th>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            EXPIRED ALL</th>
                                                        <th colspan="2" class="text-center align-middle bg-light">DATA
                                                            FOLLOW UP</th>
                                                        <th colspan="1" id="membership-category-header"
                                                            class="text-center align-middle bg-secondary">
                                                            Membership Expired Category
                                                        </th>
                                                    </tr>
                                                    <tr id="membership-names-row">
                                                        <!-- Baris kategori membership akan ditambahkan secara dinamis di sini -->
                                                        <th class="text-center align-middle bg-secondary">RENEWAL</th>
                                                        <th class="text-center align-middle bg-secondary">RENEW</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="membership-body">
                                                    <!-- Data tabel akan diisi secara dinamis oleh JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Data coach circulation -->
                        <div class="col-xl-12">
                            <div class="card h-100 p-4 shadow-sm border-0">
                                <div class="card-header row d-flex flex-row justify-content-between align-items-center">
                                    <div class="col">
                                        <h3 class="card-title m-0 me-auto">Data Coach Circulation</h3>
                                    </div>
                                    <div class="col d-flex flex-row justify-content-end gap-2">
                                        <div class="btn btn-primary d-flex flex-row gap-2" id="exportCoachData"><img
                                                src="{{ asset('assets/media/icons/printer-fill.svg') }}"
                                                alt="Export">Export
                                        </div>
                                        <select id="year-coach" class="form-select w-50" aria-label="Pilih tahun">
                                            @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                                <option value="{{ $year }}"
                                                    {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-center align-items-center">
                                    <!-- Tabel -->
                                    <div class="col-lg-12 col-md-12 mb-4">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped m-0">
                                                <thead>
                                                    <tr>
                                                        <th colspan="9" class="text-center bg-primary">
                                                            <h4 class="card-title m-0">NO EXTENSION COACH SESSION</h4>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            MONTH
                                                        </th>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            TOTAL
                                                            MEMBER</th>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            NEW
                                                            MEMBER</th>
                                                        <th colspan="3" class="text-center align-middle bg-light">DATA
                                                            FOLLOW UP</th>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            ACTIVE
                                                            MEMBER USED</th>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            NO
                                                            ACTIVE</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center bg-secondary">FINISH/EXPIRED</th>
                                                        <th class="text-center bg-secondary">RENEWAL</th>
                                                        <th class="text-center bg-secondary">NON RENEW</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="coach-data">
                                                    <!-- Data akan diisi oleh JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Statistik -->
                                    {{-- <div class="col-lg-4 col-md-12">
                                        <div class="d-flex flex-column gap-2">
                                            <div
                                                class="d-flex flex-row justify-content-center align-items-center border h-100 border-dashed border-gray-300 text-center rounded p-3 mb-3">
                                                <div>
                                                    <h5>Male</h5>
                                                    <p class="display-5" id="male-count">0</p>
                                                </div>
                                                <img src="{{ asset('assets/media/icons/person-standing.svg') }}"
                                                    alt="Male">
                                            </div>
                                            <div
                                                class="d-flex flex-row justify-content-center align-items-center border h-100 border-dashed border-gray-300 text-center rounded p-3 mb-3">
                                                <div>
                                                    <h5>Female</h5>
                                                    <p class="display-5" id="female-count">0</p>
                                                </div>
                                                <img src="{{ asset('assets/media/icons/person-standing-dress.svg') }}"
                                                    alt="Female">
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>


                        <!-- Data average checkin -->
                        <div class="col-xl-12">
                            <div class="card h-100 p-4 shadow-sm border-0">
                                <div class="card-header row d-flex flex-row justify-content-between align-items-center">
                                    <div class="col">
                                        <h3 class="card-title m-0 me-auto">Data Average Checkin</h3>
                                    </div>
                                    <div class="col d-flex flex-row justify-content-end gap-2">
                                        <div class="btn btn-primary d-flex flex-row gap-2" id="exportCheckinData"><img
                                                src="{{ asset('assets/media/icons/printer-fill.svg') }}"
                                                alt="Export">Export
                                        </div>
                                        <select id="year-checkin" class="form-select w-50" aria-label="Pilih tahun">
                                            <option value="alltime">Semua Tahun</option>
                                            @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                                <option value="{{ $year }}"
                                                    {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-center align-items-center">
                                    <!-- Tabel -->
                                    <div class="col-lg-12 col-md-12 mb-4">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-striped m-0">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            MONTH
                                                        </th>
                                                        <th rowspan="2" class="text-center align-middle bg-secondary">
                                                            TOTAL
                                                            CHECKIN</th>
                                                        <th colspan="7" class="text-center align-middle bg-light">
                                                            AVERAGE
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center bg-secondary">MONDAY</th>
                                                        <th class="text-center bg-secondary">TUESDAY</th>
                                                        <th class="text-center bg-secondary">WEDNESDAY</th>
                                                        <th class="text-center bg-secondary">THURSDAY</th>
                                                        <th class="text-center bg-secondary">FRIDAY</th>
                                                        <th class="text-center bg-secondary">SATURDAY</th>
                                                        <th class="text-center bg-secondary">SUNDAY</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="checkin-data">
                                                    <!-- Data akan diisi oleh JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            {{-- Schedule --}}
                            <div class="row g-5 lg:g-8">
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card h-100">
                                        <div class="card-header align-items-center d-flex justify-content-between">
                                            <div>
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-dark">Jadwal Kelas</span>
                                                    <span id="total-classes-today"
                                                        class="text-muted mt-1 fw-semibold fs-7">0
                                                        jadwal hari ini</span>
                                                </h3>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="card-toolbar d-none">
                                                    <x-form.month-datepicker inputId="filterClass" />
                                                </div>
                                                <!-- Tombol Panah -->
                                                <div class="d-flex justify-content-between gap-2">
                                                    <button id="prev-date" class="btn btn-light">⟨</button>
                                                    <button id="next-date" class="btn btn-light">⟩</button>
                                                </div>
                                                {{-- <div class="card-menu">
                                                    <button class="card-menu-toggle" type="button"
                                                        onclick="toggleCardMenu(this)">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="card-menu-dropdown">
                                                        <button class="card-menu-item"
                                                            onclick="exportClassScheduleData()">
                                                            <i class="fas fa-file-excel"></i>
                                                            Download Excel
                                                        </button>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                        <div class="card-body pt-7 px-0">
                                            <!--begin::Nav-->
                                            <ul
                                                class="nav nav-stretch nav-pills nav-pills-custom nav-pills-active-custom d-flex justify-content-between mb-8 px-5 nav-date">
                                                @foreach ($dates as $key => $date)
                                                    <li class="nav-item p-0 ms-0">
                                                        <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-primary {{ $key == 0 ? 'active' : '' }}"
                                                            data-bs-toggle="tab"
                                                            href="#kt_timeline_widget_3_tab_content_{{ $key + 1 }}">
                                                            <span class="fs-7 fw-semibold">{{ $date['day'] }}</span>
                                                            <span class="fs-6 fw-bold">{{ $date['date'] }}</span>
                                                            <span class="fs-7 fw-semibold">{{ $date['month'] }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content mb-2 px-9 content-class"
                                                style="max-height: 400px; overflow-y: auto;">
                                                @foreach ($dates as $key => $date)
                                                    <div class="tab-pane tab-class fade {{ $key == 0 ? 'show active' : '' }}"
                                                        id="kt_timeline_widget_3_tab_content_{{ $key + 1 }}">
                                                        @if (isset($gymClassHistories[$date['full_date']]))
                                                            @foreach ($gymClassHistories[$date['full_date']] as $class)
                                                                <div class="d-flex align-items-center mb-6">
                                                                    <span
                                                                        class="bullet bullet-vertical d-flex align-items-center min-h-80px mh-100 me-4 bg-primary"></span>
                                                                    <div class="flex-grow-1 me-5">
                                                                        <div class="text-gray-800 fw-semibold fs-2">
                                                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $class['start_time'])->format('H:i') }}
                                                                            -
                                                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $class['end_time'])->format('H:i') }}
                                                                        </div>
                                                                        <div class="text-gray-700 fw-semibold fs-6">
                                                                            {{ $class['class_name'] }}</div>
                                                                        <div class="text-gray-400 fw-semibold fs-7">
                                                                            Coach by <a
                                                                                href="{{ route('personal-trainer.show', $class['coach_id']) }}"
                                                                                class="text-primary opacity-75-hover fw-semibold">{{ $class['coach_name'] ?? 'Tidak tersedia' }}</a>
                                                                        </div>
                                                                        <div class="text-gray-400 fw-semibold fs-7">
                                                                            Jumlah member <a href="#"
                                                                                class="text-primary opacity-75-hover fw-semibold">{{ $class['total_participant'] ?? 'Tidak tersedia' }}</a>
                                                                        </div>
                                                                    </div>
                                                                    <a href="{{ route('gym-class.show', $class['class_id']) }}"
                                                                        class="btn btn-sm">View</a>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <p class="text-muted">Tidak ada kelas untuk hari ini</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!--end::Tab Content-->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xl-4">
                                    <div class="card h-100">
                                        <div class="card-header align-items-center d-flex justify-content-between">
                                            <div>
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-dark">Jadwal Sesi Coach</span>
                                                    <span id="total-coach-today"
                                                        class="text-muted mt-1 fw-semibold fs-7">0
                                                        jadwal hari ini</span>
                                                </h3>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="card-toolbar d-none">
                                                    <x-form.month-datepicker inputId="filterCoach" />
                                                </div>
                                                <div class="d-flex justify-content-between gap-2">
                                                    <button id="prev-date-coach" class="btn btn-light">⟨</button>
                                                    <button id="next-date-coach" class="btn btn-light">⟩</button>
                                                </div>
                                                {{-- <div class="card-menu">
                                                    <button class="card-menu-toggle" type="button"
                                                        onclick="toggleCardMenu(this)">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="card-menu-dropdown">
                                                        <button class="card-menu-item"
                                                            onclick="exportCoachScheduleData()">
                                                            <i class="fas fa-file-excel"></i>
                                                            Download Excel
                                                        </button>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                        <div class="card-body pt-7 px-0">
                                            <!--begin::Nav-->
                                            <ul
                                                class="nav nav-stretch nav-pills nav-pills-custom nav-pills-active-custom d-flex justify-content-between mb-8 px-5 nav-date-coach">
                                                @foreach ($dates as $key => $date)
                                                    <li class="nav-item p-0 ms-0">
                                                        <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-primary {{ $key == 0 ? 'active' : '' }}"
                                                            data-bs-toggle="tab"
                                                            href="#kt_timeline_widget_3_tab_content_{{ $key + 1 }}">
                                                            <span class="fs-7 fw-semibold">{{ $date['day'] }}</span>
                                                            <span class="fs-6 fw-bold">{{ $date['date'] }}</span>
                                                            <span class="fs-7 fw-semibold">{{ $date['month'] }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <!--end::Nav-->
                                            <div class="tab-content mb-2 px-9 content-coach"
                                                style="max-height: 400px; overflow-y: auto;">
                                                {{-- @foreach ($dates as $key => $date)
                                                    <div class="tab-pane tab-class fade {{ $key == 0 ? 'show active' : '' }}"
                                                        id="coach_timeline_tab_content_{{ $key + 1 }}">
                                                        @if (isset($coachSchedules[$date['full_date']]))
                                                            @foreach ($coachSchedules[$date['full_date']] as $schedule)
                                                                <div class="d-flex align-items-center mb-6">
                                                                    <span
                                                                        class="bullet bullet-vertical d-flex align-items-center min-h-80px mh-100 me-4 bg-primary"></span>
                                                                    <div class="flex-grow-1 me-5">
                                                                        <div class="text-gray-800 fw-semibold fs-2">
                                                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule['start_time'])->format('H:i') }}
                                                                            -
                                                                            {{ isset($schedule['end_time']) && \Carbon\Carbon::hasFormat($schedule['end_time'], 'H:i:s')
                                                                                ? \Carbon\Carbon::createFromFormat('H:i:s', $schedule['end_time'])->format('H:i')
                                                                                : '' }}
                                                                        </div>
                                                                        <div class="text-gray-700 fw-semibold fs-6">Jadwal
                                                                            Sesi {{ $schedule['coach_name'] }}</div>
                                                                        <div class="text-gray-400 fw-semibold fs-7">
                                                                            Customer: <a
                                                                                href="{{ route('user.show', $schedule['customer_id']) }}"
                                                                                class="text-primary opacity-75-hover fw-semibold">{{ $schedule['customer_name'] }}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <p class="text-muted">Tidak ada jadwal untuk hari ini</p>
                                                        @endif
                                                    </div>
                                                @endforeach --}}
                                            </div>
                                            <!--end::Tab Content-->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xl-4">
                                    <div class="card h-100">
                                        <div class="card-header align-items-center d-flex justify-content-between">
                                            <div>
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-dark">Jadwal Fisioterapi</span>
                                                    <span id="total-physio-today"
                                                        class="text-muted mt-1 fw-semibold fs-7">0
                                                        jadwal hari ini</span>
                                                </h3>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="card-toolbar d-none">
                                                    <x-form.month-datepicker inputId="filterPhysiotherapy" />
                                                </div>
                                                <!-- Tombol Panah -->
                                                <div class="d-flex justify-content-between gap-2">
                                                    <button id="prev-date-physiotherapy" class="btn btn-light">⟨</button>
                                                    <button id="next-date-physiotherapy" class="btn btn-light">⟩</button>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="card-body pt-7 px-0">
                                            <!--begin::Nav-->
                                            <ul
                                                class="nav nav-stretch nav-pills nav-pills-custom nav-pills-active-custom d-flex justify-content-between mb-8 px-5 nav-date-physio">
                                                @foreach ($dates as $key => $date)
                                                    <li class="nav-item p-0 ms-0">
                                                        <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-primary {{ $key == 0 ? 'active' : '' }}"
                                                            data-bs-toggle="tab"
                                                            href="#physio_timeline_tab_content_{{ $key + 1 }}">
                                                            <span class="fs-7 fw-semibold">{{ $date['day'] }}</span>
                                                            <span class="fs-6 fw-bold">{{ $date['date'] }}</span>
                                                            <span class="fs-7 fw-semibold">{{ $date['month'] }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content mb-2 px-9 content-physiotherapy"
                                                style="max-height: 400px; overflow-y: auto;">
                                                {{-- @foreach ($dates as $key => $date)
                                                    <div class="tab-pane tab-class fade {{ $key == 0 ? 'show active' : '' }}"
                                                        id="physio_timeline_tab_content_{{ $key + 1 }}">
                                                        @if (isset($physiotherapySchedules->{$date['full_date']}))
                                                            @foreach ($physiotherapySchedules->{$date['full_date']} as $schedule)
                                                                <div class="d-flex align-items-center mb-6">
                                                                    <span
                                                                        class="bullet bullet-vertical d-flex align-items-center min-h-80px mh-100 me-4 bg-primary"></span>
                                                                    <div class="flex-grow-1 me-5">
                                                                        <div class="text-gray-800 fw-semibold fs-2">
                                                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('H:i') }}
                                                                            -
                                                                            {{ $schedule->end_time ? \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('H:i') : '' }}
                                                                        </div>
                                                                        <div class="text-gray-700 fw-semibold fs-6">Jadwal
                                                                            Fisioterapi
                                                                            {{ $schedule->coach_name }}</div>
                                                                        <div class="text-gray-400 fw-semibold fs-7">
                                                                            Customer: <a
                                                                                href="{{ route('user.show', $schedule->customer_id) }}"
                                                                                class="text-primary opacity-75-hover fw-semibold">{{ $schedule->customer_name }}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <p class="text-muted">Tidak ada kelas untuk hari ini</p>
                                                        @endif
                                                    </div>
                                                @endforeach --}}
                                            </div>
                                            <!--end::Tab Content-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Daftar Coach --}}
                        <!-- Daftar Coach -->
                        <div class="col-xl-6">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title m-0">Daftar Coach</h3>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="card-toolbar">
                                            <a href="{{ route('personal-trainer.recap.index') }}"
                                                class="btn btn-sm btn-primary">Lihat Semua</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <table id="kt_daftar_coach"
                                        class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                                                <th class="min-w-125px">Nama Coach</th>
                                                <th>Level</th>
                                                <th>Training</th>
                                                <th>Kelas</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-dark">
                                            <!-- Data content goes here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan untuk Daftar Fisioterapis dan Daftar Coach External -->
                        <div class="col-xl-6">
                            <!-- Daftar Fisioterapis -->
                            <div class="card mb-3 shadow-sm border-0">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title m-0">Daftar Fisioterapis</h3>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="card-toolbar">
                                            <a href="{{ route('personal-trainer.recap.index', ['tab' => 'physiotherapy']) }}"
                                                class="btn btn-sm btn-primary">Lihat Semua</a>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <table id="kt_daftar_fisioterapis"
                                        class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                                                <th class="min-w-125px">Nama Fisio</th>
                                                <th>Total Sesi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-dark">
                                            <!-- Data content goes here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Daftar Coach External -->
                            <div class="card shadow-sm border-0">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title m-0">Daftar Coach External</h3>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="card-toolbar">
                                            <a href="{{ route('personal-trainer.recap.index', ['tab' => 'external']) }}"
                                                class="btn btn-sm btn-primary">Lihat Semua</a>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <table id="kt_daftar_external"
                                        class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                                                <th class="min-w-125px">Nama Coach</th>
                                                <th>Training</th>
                                                <th>Kelas</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-dark">
                                            <!-- Data content goes here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="infoModalLabel">Informasi Data Member Circulation</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <li>Total Member : Total member yang aktif pada bulan tersebut</li>
                    <li>New Member : Member yang bergabung pada bulan tersebut</li>
                    <li>Expired : Member yang tidak aktif pada bulan ini</li>
                    <li>Expired All : Member yang tidak aktif pada keseluruhan</li>
                    <li>Renewal : Member Expired bulan ini yang melakukan perpanjang pada bulan ini</li>
                    <li>Renew : Member Expired bulan lalu yang melakukan perpanjang pada bulan ini</li>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-base.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ===================================================================================
            // A. KONFIGURASI DAN ELEMEN UTAMA
            // ===================================================================================
            const gymPlaceSelect = document.getElementById("gym_place_id");
            const dateRangePicker = $("#dateRange");
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // Variabel untuk menyimpan instance DataTables
            let coachTable = null;
            let physioTable = null;
            let externalCoachTable = null;

            // Variabel untuk menyimpan instance Chart
            let checkinChart = null;
            let checkoutChart = null;

            // ===================================================================================
            // B. FUNGSI-FUNGSI PENGAMBILAN DATA (DATA FETCHING FUNCTIONS)
            // ===================================================================================

            /**
             * Fungsi umum untuk mengambil data JSON dari server menggunakan Fetch API.
             * @param {string} url - URL endpoint.
             * @param {object} params - Parameter query.
             * @returns {Promise<object>} Data JSON dari server.
             */
            async function fetchData(url, params) {
                const query = new URLSearchParams(params).toString();
                try {
                    const response = await fetch(`${url}?${query}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return await response.json();
                } catch (error) {
                    console.error(`Error fetching from ${url}:`, error);
                    return {
                        success: false,
                        data: []
                    }; // Kembalikan objek default saat error
                }
            }

            /**
             * Mengambil dan memperbarui semua statistik di kartu atas.
             */
            async function updateAllStats() {
                const params = {
                    type: 'membership-data',
                    start_date: startDateInput.value,
                    end_date: endDateInput.value,
                    gym_place_id: gymPlaceSelect.value || '',
                };
                const data = await fetchData('/dashboard', params);
                updateMemberStatsUI(data); // Memanggil fungsi untuk update UI
            }

            /**
             * Mengambil dan memperbarui data untuk tabel sirkulasi (Member, Coach, Checkin).
             * @param {string} type - 'member', 'coach', atau 'checkin'.
             */
            async function updateCirculationTable(type) {
                const config = {
                    member: {
                        yearEl: "yearMember",
                        bodyEl: "membership-body",
                        url: '/membership-circulation',
                        renderer: renderMemberCirculationTable
                    },
                    coach: {
                        yearEl: "year-coach",
                        bodyEl: "coach-data",
                        url: '/coach-circulation',
                        renderer: renderCoachCirculationTable
                    },
                    checkin: {
                        yearEl: "year-checkin",
                        bodyEl: "checkin-data",
                        url: '/dashboard-checkin',
                        renderer: renderCheckinTable
                    }
                };

                const currentConfig = config[type];
                if (!currentConfig) return;

                const yearSelect = document.getElementById(currentConfig.yearEl);
                const tableBody = document.getElementById(currentConfig.bodyEl);
                if (!yearSelect || !tableBody) return;

                const params = {
                    year: yearSelect.value,
                    gym_place_id: gymPlaceSelect.value || ''
                };

                tableBody.innerHTML = `<tr><td colspan="9" class="text-center">Loading...</td></tr>`;
                const response = await fetchData(currentConfig.url, params);
                currentConfig.renderer(response);
            }

            document.querySelectorAll('.custom-tab').forEach((tab) => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.getAttribute('href').replace('#kt_tab_', '');
                    updateCheckInOutCharts(filter, 'all');
                });
            });

            /**
             * Mengambil data untuk check-in/check-out dan memperbarui chart.
             */
            async function updateCheckInOutCharts(filter = 'today') {
                const params = {
                    type: 'membership-stats', // Endpoint ini juga mengembalikan data chart
                    filter: filter || 'today', // defaultnya 'today'
                    start_date: startDateInput.value,
                    end_date: endDateInput.value,
                    gym_place_id: gymPlaceSelect.value || ''
                };
                const data = await fetchData('/dashboard', params);

                $('#checkInCount').attr('data-kt-countup-value', data.checkinTotal || 0).text(data
                    .checkinTotal || 0);
                $('#checkOutCount').attr('data-kt-countup-value', data.checkoutTotal || 0).text(data
                    .checkoutTotal || 0);

                renderActivityChart('checkin', data.checkinCharts || []);
                renderActivityChart('checkout', data.checkoutCharts || []);
            }

            // ===================================================================================
            // C. FUNGSI-FUNGSI PEMBARUAN TAMPILAN (UI UPDATE FUNCTIONS)
            // ===================================================================================

            function updateMemberStatsUI(data) {
                if (!data || typeof data !== 'object') {
                    console.error("Invalid data for updateMemberStatsUI:", data);
                    return;
                }

                function updateText(id, value) {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value;
                }

                function updateProgressBar(id, percent) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.style.width = `${percent}%`;
                        el.textContent = `${percent}%`;
                    }
                }
                const totalMembers = data.totalMembers ?? 0;
                const purchased = data.buyMembership ?? 0;
                const notPurchased = Math.max(0, (data.totalDownload ?? 0) - (purchased ?? 0));
                const membershipActive = data.membershipActive ?? 0;
                const membershipExpired = data.membershipExpired ?? 0;
                const membershipTimeoff = data.membershipTimeoff ?? 0;
                const membershipCompliment = data.membershipCompliment ?? 0;
                const membershipDaily = data.membershipDaily ?? 0;
                const totalMembershipStatus = membershipActive + membershipExpired + membershipTimeoff +
                    membershipCompliment;

                const purchasedPercent = data.totalDownload > 0 ? ((purchased / data.totalDownload) * 100).toFixed(
                    1) : 0;
                const notPurchasedPercent = data.totalDownload > 0 ? ((notPurchased / data.totalDownload) * 100)
                    .toFixed(1) : 0;
                const percentActive = totalMembershipStatus > 0 ? ((membershipActive / totalMembershipStatus) * 100)
                    .toFixed(1) : 0;
                const percentExpired = totalMembershipStatus > 0 ? ((membershipExpired / totalMembershipStatus) *
                    100).toFixed(1) : 0;
                const percentTimeoff = totalMembershipStatus > 0 ? ((membershipTimeoff / totalMembershipStatus) *
                    100).toFixed(1) : 0;
                const percentCompliment = totalMembershipStatus > 0 ? ((membershipCompliment /
                    totalMembershipStatus) * 100).toFixed(1) : 0;

                updateText('total-member-registered', totalMembers);
                updateText('purchase_membership', purchased);
                updateText('never_purchase_membership', notPurchased);
                updateText('total-download-2', data.totalDownload ?? '-');
                updateText('conversion-rate', data.conversionRate ?? '-');
                updateText('timeoff-membership', membershipTimeoff);
                updateText('active-membership', membershipActive);
                updateText('expired-membership', membershipExpired);
                updateText('male-members', data.genderMale ?? '-');
                updateText('female-members', data.genderFemale ?? '-');
                updateText('with-coach', data.buyCoach ?? '-');
                updateText('self-training', data.notBuyCoach ?? '-');
                updateText('purchase_percent', purchasedPercent);
                updateText('never_purchase_percent', notPurchasedPercent);
                updateText('percent-active-membership', percentActive);
                updateText('percent-expired-membership', percentExpired);
                updateText('percent-timeoff-membership', percentTimeoff);
                updateText('total-compliment', membershipCompliment);
                updateText('total-daily', membershipDaily);
                updateText('percent-compliment', percentCompliment);

                updateProgressBar('purchase_bar', purchasedPercent);
                updateProgressBar('never_purchase_bar', notPurchasedPercent);
                updateProgressBar('progress-active-membership', percentActive);
                updateProgressBar('progress-expired-membership', percentExpired);
                updateProgressBar('progress-timeoff-membership', percentTimeoff);
                updateProgressBar('progress-compliment', percentCompliment);

                const male = data.genderMale ?? 0;
                const female = data.genderFemale ?? 0;
                const totalGender = male + female;
                const percentMale = totalGender > 0 ? ((male / totalGender) * 100).toFixed(1) : 0;
                const percentFemale = totalGender > 0 ? ((female / totalGender) * 100).toFixed(1) : 0;
                updateText('percent-male-members', percentMale);
                updateText('percent-female-members', percentFemale);
                updateProgressBar('bar-male-members', percentMale);
                updateProgressBar('bar-female-members', percentFemale);

                const withCoach = data.buyCoach ?? 0;
                const selfTraining = data.notBuyCoach ?? 0;
                const totalCoach = withCoach + selfTraining;
                const percentWithCoach = totalCoach > 0 ? ((withCoach / totalCoach) * 100).toFixed(1) : 0;
                const percentSelfTraining = totalCoach > 0 ? ((selfTraining / totalCoach) * 100).toFixed(1) : 0;
                updateText('percent-with-coach', percentWithCoach);
                updateText('percent-self-training', percentSelfTraining);
                updateProgressBar('bar-with-coach', percentWithCoach);
                updateProgressBar('bar-self-training', percentSelfTraining);
            }

            function renderMemberCirculationTable(data) {
                const tableBody = document.getElementById("membership-body");
                const membershipHeaderRow = document.getElementById("membership-names-row");
                const membershipCategoryHeader = document.getElementById("membership-category-header");
                const dynamicColspanHeader = document.getElementById("dynamic-colspan-header");

                membershipHeaderRow.innerHTML =
                    `<th class="text-center align-middle bg-secondary">RENEWAL</th><th class="text-center align-middle bg-secondary">RENEW</th>`;

                if (!data || data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="9" class="text-center">No data available</td></tr>';
                    membershipCategoryHeader.setAttribute("colspan", "0");
                    dynamicColspanHeader.setAttribute("colspan", "9");
                    return;
                }

                const membershipCategories = Object.keys(data[0].membership_expired_counts);
                const totalColumns = 7 + membershipCategories.length;
                dynamicColspanHeader.setAttribute("colspan", totalColumns);
                membershipCategoryHeader.setAttribute("colspan", membershipCategories.length);

                membershipCategories.forEach(category => {
                    const th = document.createElement("th");
                    th.className = "text-center bg-secondary";
                    th.textContent = category;
                    membershipHeaderRow.appendChild(th);
                });

                tableBody.innerHTML = data.map(item => {
                    const categoryCells = membershipCategories.map(category =>
                        `<td class="text-center">${item.membership_expired_counts[category] || 0}</td>`
                    ).join("");
                    return `
                        <tr>
                            <td>${item.month}</td>
                            <td class="text-center">${item.total_member}</td>
                            <td class="text-center">${item.new_member}</td>
                            <td class="text-center">${item.total_expired}</td>
                            <td class="text-center">${item.total_expired_all}</td>
                            <td class="text-center">${item.renewal}</td>
                            <td class="text-center">${item.renew}</td>
                            ${categoryCells}
                        </tr>`;
                }).join("");
            }

            function renderCoachCirculationTable(response) {
                const tableBody = document.getElementById("coach-data");
                if (response.status === "success" && response.data.length > 0) {
                    tableBody.innerHTML = response.data.map(row => `
                        <tr>
                            <td class="text-center">${row.month}</td>
                            <td class="text-center">${row.total_members}</td>
                            <td class="text-center">${row.new_members}</td>
                            <td class="text-center">${row.expired}</td>
                            <td class="text-center">${row.renewal}</td>
                            <td class="text-center">${row.non_renewal}</td>
                            <td class="text-center">${row.active_member_used}</td>
                            <td class="text-center">${row.no_active}</td>
                        </tr>
                    `).join("");
                } else {
                    tableBody.innerHTML = `<tr><td colspan="9" class="text-center">No data available</td></tr>`;
                }
            }

            function renderCheckinTable(response) {
                const tableBody = document.getElementById("checkin-data");
                if (response.success && response.data.length > 0) {
                    tableBody.innerHTML = response.data.map(row => `
                        <tr>
                            <td class="text-center">${row.month}</td>
                            <td class="text-center">${row.total_checkin}</td>
                            <td class="text-center">${row.average?.Monday || 0}</td>
                            <td class="text-center">${row.average?.Tuesday || 0}</td>
                            <td class="text-center">${row.average?.Wednesday || 0}</td>
                            <td class="text-center">${row.average?.Thursday || 0}</td>
                            <td class="text-center">${row.average?.Friday || 0}</td>
                            <td class="text-center">${row.average?.Saturday || 0}</td>
                            <td class="text-center">${row.average?.Sunday || 0}</td>
                        </tr>
                    `).join("");
                } else {
                    tableBody.innerHTML = `<tr><td colspan="9" class="text-center">No data available</td></tr>`;
                }
            }

            function renderActivityChart(type, data) {
                const config = {
                    checkin: {
                        elementId: 'kt_chart_checkin',
                        name: 'Check In',
                        dataKey: 'total_checkin',
                        color: '--bs-primary',
                        chartInstance: checkinChart
                    },
                    checkout: {
                        elementId: 'kt_chart_checkout',
                        name: 'Check Out',
                        dataKey: 'total_checkout',
                        color: '--bs-danger',
                        chartInstance: checkoutChart
                    }
                };

                const chartConfig = config[type];
                const element = document.getElementById(chartConfig.elementId);
                if (!element) return;

                if (chartConfig.chartInstance) chartConfig.chartInstance.destroy();

                const options = {
                    series: [{
                        name: chartConfig.name,
                        data: data.map(item => item[chartConfig.dataKey])
                    }],
                    chart: {
                        fontFamily: 'inherit',
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 12
                        }
                    },
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: data.map(item => item.date || item.hour),
                        labels: {
                            style: {
                                colors: KTUtil.getCssVariableValue('--bs-gray-500'),
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: KTUtil.getCssVariableValue('--bs-gray-500'),
                                fontSize: '12px'
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    states: {
                        normal: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        hover: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        active: {
                            allowMultipleDataPointsSelection: false,
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        }
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        }
                    },
                    colors: [KTUtil.getCssVariableValue(chartConfig.color)],
                    grid: {
                        borderColor: KTUtil.getCssVariableValue('--bs-border-dashed-color'),
                        strokeDashArray: 4,
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    }
                };

                const chart = new ApexCharts(element, options);
                chart.render();

                if (type === 'checkin') checkinChart = chart;
                else checkoutChart = chart;
            }

            // ===================================================================================
            // D. FUNGSI UTAMA & INISIALISASI EVENT
            // ===================================================================================

            function updateAllDashboardData() {
                console.log("Updating all dashboard data for Gym Place ID:", gymPlaceSelect.value);

                updateAllStats();
                updateCirculationTable('member');
                updateCirculationTable('coach');
                updateCirculationTable('checkin');
                updateCheckInOutCharts();

                if (window.fetchSchedules) window.fetchSchedules();

                if (coachTable) coachTable.ajax.reload();
                if (physioTable) physioTable.ajax.reload();
                if (externalCoachTable) externalCoachTable.ajax.reload();

                toggleTopLevelMetrics();
            }

            function initializeDataTables() {
                const commonOptions = {
                    ordering: false,
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    pageLength: 5,
                    searching: false,
                    lengthChange: false,
                    language: {
                        "paginate": {
                            "next": "<i class='fa fa-angle-right'>",
                            "previous": "<i class='fa fa-angle-left'>"
                        }
                    }
                };

                const ajaxConfig = (url) => ({
                    url: url,
                    type: 'GET',
                    data: (d) => {
                        d.start_date = moment().startOf('month').format('YYYY-MM-DD');
                        d.end_date = moment().endOf('month').format('YYYY-MM-DD');
                        d.gym_place_id = gymPlaceSelect.value || '';
                    }
                });

                coachTable = $('#kt_daftar_coach').DataTable({
                    ...commonOptions,
                    ajax: ajaxConfig("{{ route('personal-trainer.recap.index', 'data=internal') }}"),
                    columns: [{
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'personal_trainer_level.name',
                        name: 'personal_trainer_level.name'
                    }, {
                        data: 'total_session',
                        name: 'total_session'
                    }, {
                        data: 'total_class',
                        name: 'total_class'
                    }]
                });
                physioTable = $('#kt_daftar_fisioterapis').DataTable({
                    ...commonOptions,
                    ajax: ajaxConfig("{{ route('personal-trainer.recap.index', 'data=physiotherapy') }}"),
                    columns: [{
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'total_session',
                        name: 'total_session'
                    }]
                });
                externalCoachTable = $('#kt_daftar_external').DataTable({
                    ...commonOptions,
                    ajax: ajaxConfig("{{ route('personal-trainer.recap.index', 'data=external') }}"),
                    columns: [{
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'total_session',
                        name: 'total_session'
                    }, {
                        data: 'total_class',
                        name: 'total_class'
                    }]
                });
            }

            function toggleTopLevelMetrics() {
                const topLevelMetrics = document.getElementById('top-level-metrics');
                const memberBreakdown = document.getElementById('member-status-breakdown');
                const shouldShow = !gymPlaceSelect.value;
                if (topLevelMetrics) topLevelMetrics.style.display = shouldShow ? 'flex' : 'none';
                if (memberBreakdown) memberBreakdown.style.display = shouldShow ? 'flex' : 'none';
            }

            // --- Inisialisasi Event Listeners ---
            gymPlaceSelect.addEventListener("change", updateAllDashboardData);

            document.getElementById("yearMember").addEventListener('change', () => updateCirculationTable(
                'member'));
            document.getElementById("year-coach").addEventListener('change', () => updateCirculationTable('coach'));
            document.getElementById("year-checkin").addEventListener('change', () => updateCirculationTable(
                'checkin'));

            // Inisialisasi Date Range Picker
            const defaultStart = '2023-01-01';
            const defaultEnd = moment().format('YYYY-MM-DD');
            startDateInput.value = defaultStart;
            endDateInput.value = defaultEnd;

            dateRangePicker.daterangepicker({
                startDate: moment(defaultStart),
                endDate: moment(defaultEnd),
                opens: 'left',
                ranges: {
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                    'Semua Waktu': [moment('2023-01-01'), moment()]
                }
            }, (start, end) => {
                startDateInput.value = start.format('YYYY-MM-DD');
                endDateInput.value = end.format('YYYY-MM-DD');
                dateRangePicker.val(startDateInput.value + ' - ' + endDateInput.value);
                updateAllDashboardData();
            });
            dateRangePicker.val(startDateInput.value + ' - ' + endDateInput.value);

            // --- Panggilan Awal ---
            initializeDataTables();
            updateAllDashboardData();
        });

        const gymPlaceSelect = document.getElementById("gym_place_id");
        const yearCheckinSelect = document.getElementById("year-checkin");
        const yearMemberSelect = document.getElementById("yearMember");
        const yearCoachSelect = document.getElementById("year-coach");
        const exportCheckinBtn = document.getElementById("exportCheckinData");
        const exportMemberBtn = document.getElementById("exportMembershipData");
        const exportCoachBtn = document.getElementById("exportCoachData");

        // Event Listener untuk tombol export
        exportCheckinBtn.addEventListener("click", function() {
            const year = yearCheckinSelect.value;
            const gymPlaceId = gymPlaceSelect.value || '';
            window.location.href =
                `/checkin/export?year=${year}${gymPlaceId ? `&gym_place_id=${gymPlaceId}` : ''}`;
        });

        exportMemberBtn.addEventListener("click", function() {
            const year = yearMemberSelect.value;
            const gymPlaceId = gymPlaceSelect.value || '';
            window.location.href =
                `/membership-circulation/export?year=${year}${gymPlaceId ? `&gym_place_id=${gymPlaceId}` : ''}`;
        });

        exportCoachBtn.addEventListener("click", function() {
            const year = yearCoachSelect.value;
            const gymPlaceId = gymPlaceSelect.value || '';
            window.location.href =
                `/coach-circulation/export?year=${year}${gymPlaceId ? `&gym_place_id=${gymPlaceId}` : ''}`;
        });

        // ===================================================================================
        // E. FUNGSI-FUNGSI JADWAL (DIBUAT GLOBAL AGAR MUDAH DIPANGGIL)
        // ===================================================================================

        function createScheduleManager(config) {
            const {
                type,
                prevBtnId,
                nextBtnId,
                dateListClass,
                contentClass,
                totalId,
                initialStartDate
            } = config;

            const prevButton = document.getElementById(prevBtnId);
            const nextButton = document.getElementById(nextBtnId);
            const dateList = document.querySelector(dateListClass);
            const contentContainer = document.querySelector(contentClass);
            const totalEl = document.getElementById(totalId);

            let currentStartDate = initialStartDate;
            let scheduleDataCache = {};

            async function fetchSchedules(startDate) {
                currentStartDate = startDate;
                const gymPlaceId = document.getElementById('gym_place_id').value || '';

                try {
                    const response = await fetch(
                        `/dashboard?type=schedules&start_date=${startDate}&gym_place_id=${gymPlaceId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                    const data = await response.json();
                    if (data.success) {
                        scheduleDataCache = data;
                        updateUI();
                    }
                } catch (error) {
                    console.error(`Error fetching ${type} schedules:`, error);
                }
            }

            function updateUI() {
                dateList.innerHTML = '';
                contentContainer.innerHTML = '';

                if (totalEl) totalEl.textContent =
                    `${scheduleDataCache[`total${type.charAt(0).toUpperCase() + type.slice(1)}Today`] || 0} jadwal hari ini`;

                if (!scheduleDataCache.dates || scheduleDataCache.dates.length === 0) {
                    contentContainer.innerHTML = '<p class="text-muted text-center p-5">Tidak ada jadwal ditemukan.</p>';
                    return;
                }

                scheduleDataCache.dates.forEach((date, index) => {
                    const monthName = new Date(date.full_date).toLocaleString('id-ID', {
                        month: 'short'
                    });
                    dateList.innerHTML += `
                        <li class="nav-item p-0 ms-0">
                            <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-primary ${index === 0 ? 'active' : ''}"
                               data-bs-toggle="tab" data-full-date="${date.full_date}" href="#${type}-tab-${index}">
                                <span class="fs-7 fw-semibold">${date.day}</span>
                                <span class="fs-6 fw-bold">${date.date}</span>
                                <span class="fs-7 fw-semibold">${monthName}</span>
                            </a>
                        </li>`;
                });

                // Attach event listeners after rendering
                dateList.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
                    tab.addEventListener('click', (e) => {
                        e.preventDefault();
                        updateContentForDate(e.currentTarget.dataset.fullDate);
                    });
                });

                // Show content for the first date
                if (scheduleDataCache.dates.length > 0) {
                    updateContentForDate(scheduleDataCache.dates[0].full_date);
                }
            }

            function updateContentForDate(fullDate) {
                const schedules = scheduleDataCache[`${type}Schedules`] ? (scheduleDataCache[`${type}Schedules`][
                    fullDate
                ] || []) : [];

                if (schedules.length > 0) {
                    contentContainer.innerHTML = schedules.map(item => {
                        const isClass = type === 'gymClass';
                        const title = isClass ? item.gym_class.name : `Jadwal Sesi ${item.coach_name}`;
                        const coachLink = isClass ? `/personal-trainer/${item.coach_id}` : '#';
                        const coachName = isClass ? item.gym_class.personal_trainer_name : item.coach_name;
                        const customerInfo = !isClass ?
                            `<div class="text-gray-400 fw-semibold fs-7">Customer: <a href="/user/${item.customer_id}" class="text-primary opacity-75-hover fw-semibold">${item.customer_name}</a></div>` :
                            '';
                        const participantInfo = isClass ?
                            `<div class="text-gray-400 fw-semibold fs-7">Jumlah member <a href="#" class="text-primary opacity-75-hover fw-semibold">${item.total_participant ?? 'N/A'}</a></div>` :
                            '';

                        // ✅ PERBAIKAN DI SINI:
                        // Tambahkan pengecekan null sebelum memanggil .slice()
                        const startTime = isClass ? (item.gym_class.start_time ? item.gym_class.start_time.slice(0,
                                5) : '') :
                            (item.start_time ? item.start_time.slice(0, 5) : '');
                        const endTime = isClass ? (item.gym_class.end_time ? item.gym_class.end_time.slice(0, 5) :
                            '') : (item.end_time ? item.end_time.slice(0, 5) : '');

                        return `
                <div class="d-flex align-items-center mb-6">
                    <span class="bullet bullet-vertical d-flex align-items-center min-h-80px mh-100 me-4 bg-primary"></span>
                    <div class="flex-grow-1 me-5">
                        <div class="text-gray-800 fw-semibold fs-2">${startTime} ${endTime ? '- ' + endTime : ''}</div>
                        <div class="text-gray-700 fw-semibold fs-6">${title}</div>
                        <div class="text-gray-400 fw-semibold fs-7">Coach by <a href="${coachLink}" class="text-primary opacity-75-hover fw-semibold">${coachName}</a></div>
                        ${participantInfo}
                        ${customerInfo}
                    </div>
                </div>`;
                    }).join('');
                } else {
                    contentContainer.innerHTML =
                        '<p class="text-muted text-center p-5">Tidak ada jadwal pada tanggal ini.</p>';
                }
            }



            prevButton.addEventListener('click', () => {
                const newDate = new Date(currentStartDate);
                newDate.setDate(newDate.getDate() - 6);
                fetchSchedules(newDate.toISOString().split('T')[0]);
            });

            nextButton.addEventListener('click', () => {
                const newDate = new Date(currentStartDate);
                newDate.setDate(newDate.getDate() + 6);
                fetchSchedules(newDate.toISOString().split('T')[0]);
            });

            // Expose the fetch function globally
            window[`fetch${type.charAt(0).toUpperCase() + type.slice(1)}Schedules`] = fetchSchedules;
        }

        const initialDate = "{{ $dates[0]['full_date'] }}";

        createScheduleManager({
            type: 'gymClass',
            prevBtnId: 'prev-date',
            nextBtnId: 'next-date',
            dateListClass: '.nav-date',
            contentClass: '.content-class',
            totalId: 'total-classes-today',
            initialStartDate: initialDate
        });

        createScheduleManager({
            type: 'coach',
            prevBtnId: 'prev-date-coach',
            nextBtnId: 'next-date-coach',
            dateListClass: '.nav-date-coach',
            contentClass: '.content-coach',
            totalId: 'total-coach-today',
            initialStartDate: initialDate
        });

        createScheduleManager({
            type: 'physiotherapy',
            prevBtnId: 'prev-date-physiotherapy',
            nextBtnId: 'next-date-physiotherapy',
            dateListClass: '.nav-date-physio',
            contentClass: '.content-physiotherapy',
            totalId: 'total-physio-today',
            initialStartDate: initialDate
        });

        // Global fetcher for all schedules
        window.fetchSchedules = () => {
            const today = new Date().toISOString().split('T')[0];
            window.fetchGymClassSchedules(today);
            window.fetchCoachSchedules(today);
            window.fetchPhysiotherapySchedules(today);
        }

        // Function to toggle card menu dropdown
        window.toggleCardMenu = function(button) {
            const dropdown = button.nextElementSibling;
            const isVisible = dropdown.classList.contains('show');

            // Close all other dropdowns
            document.querySelectorAll('.card-menu-dropdown').forEach(menu => {
                menu.classList.remove('show');
            });

            // Toggle current dropdown
            if (!isVisible) {
                dropdown.classList.add('show');
            }
        };

        // Function to export active membership data
        window.exportMembershipStats = function(type) {
            const startDate = $('#start_date').val() || '2023-01-01';
            const endDate = $('#end_date').val() || moment().format('YYYY-MM-DD');
            const gymPlaceId = $('#gym_place_id').val() || '';

            // Create a form to submit the export request
            const form = document.createElement('form');
            form.method = 'GET';
            form.action =
                '{{ route('dashboard.export') }}'; // Make sure this Blade directive is in a .blade.php file

            // Create and append hidden inputs...
            const createInput = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            };

            createInput('start_date', startDate);
            createInput('end_date', endDate);
            createInput('type', type);

            if (gymPlaceId) {
                createInput('gym_place_id', gymPlaceId);
            }

            // Submit the form
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        };
    </script>
@endpush
