<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>{{ env('APP_NAME') }} {{ @$title ? '| ' . $title : '' }}</title>
<meta charset="utf-8" />
<meta name="description" content="{{ env('APP_NAME') }}">
<meta name="author" content="{{ env('APP_NAME') }}">
<meta name="robots" content="noindex, nofollow">

<!-- Chrome for Android theme color -->
<!-- Open Graph Meta -->
<meta property="og:title" content="{{ env('APP_NAME') }}">
<meta property="og:site_name" content="{{ env('APP_NAME') }}">
<meta property="og:description" content="{{ env('APP_NAME') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="">
<meta property="og:image" content="">

<link rel="canonical" href="{{ env('APP_NAME') }}" />
<link rel="shortcut icon" href="{{ asset('assets/media/logos/logo.webp') }}" />
<!--begin::Fonts-->
<link href="https://fonts.cdnfonts.com/css/gothic-a1" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<!--end::Fonts-->
<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Page Vendor Stylesheets-->
<!--begin::Global Stylesheets Bundle(used by all pages)-->
<!--end::Global Stylesheets Bundle-->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!--end::Fonts-->
<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
    type="text/css" />
<!--end::Page Vendor Stylesheets-->
<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ url('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css') }}" rel="stylesheet" />

<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Page Vendor Stylesheets-->

{{--
<link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet"> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" />
<link rel="stylesheet" type="text/css"
    href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css" />


<livewire:styles />
<style>
    .menu-link.active .menu-icon img.icon {
        filter: invert(99%) sepia(100%) saturate(0%) hue-rotate(261deg) brightness(102%) contrast(105%);
    }

    .content.pt {
        font-family: 'Inter', sans-serif;
    }

    span.anychart-credits-text,
    img.anychart-credits-logo {
        color: white !important;
        display: none !important;
        visibility: hidden !important;
    }

    .symbol img {
        object-fit: cover;
    }

    .btn-batal {
        border: 1px solid var(--B40, #D7DBFF) !important;
        color: var(--B50, #9397B6);
        font-family: 'Gothic A1';
        font-weight: 700;
        line-height: 150%;
        /* 1.125rem */
        letter-spacing: 0.00375rem;
    }

    .btn-download {
        border: 1px solid var(--Grey, #A0A0A0) !important;
        color: var(--B70, #3B4CED);
        font-family: 'Gothic A1';
        font-weight: 400;
        line-height: 150%;
        /* 1.125rem */
        letter-spacing: 0.00375rem;
    }

    .btn-import {
        background: var(--B30, #EFF1FF) !important;
        color: var(--B80, #1B236E);
        font-family: 'Gothic A1';
        font-weight: 400;
        line-height: 150%;
        /* 1.125rem */
        letter-spacing: 0.00375rem;
    }

    input#dateRange,
    input#dateRange:focus {
        border: none;
        outline: none;
        color: white;
    }

    .menu-icon.back {
        width: 2rem !important;
        color: #7f8194;
    }

    .menu-icon.back.pt-1 i {
        font-size: 2rem !important;
    }

    .btn-translate {
        background-color: white;
        color: black;
        outline: 2px solid black !important;
        /* Mengganti outline dengan border */
    }

    .btn-translate:hover {
        background-color: #b18d41;
        color: white;
    }
</style>

@stack('css')
@if (!env('LOCAL_EN_ACTIVE'))
    <style>
        .en-feature {
            display: none !important;
        }
    </style>
@endif
