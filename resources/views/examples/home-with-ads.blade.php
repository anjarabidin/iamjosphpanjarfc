<!-- Example usage in home page -->
<!-- File: resources/views/home.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Hero Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h1>Selamat Datang di PHRI</h1>
            </div>
        </div>

        <!-- Ads Section -->
        @if ($ads->count() > 0)
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="mb-4">Iklan & Promosi</h3>
                    <x-home-ads :ads="$ads" />
                </div>
            </div>
        @endif

        <!-- Other content sections -->
        <div class="row">
            <!-- Member section -->
            <div class="col-md-6 mb-4">
                <h4>Anggota Kami</h4>
                <!-- member content -->
            </div>

            <!-- Articles section -->
            <div class="col-md-6 mb-4">
                <h4>Artikel Terbaru</h4>
                <!-- articles content -->
            </div>
        </div>
    </div>

    <!-- Advanced tracking for single page applications -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Example: Manual tracking untuk dynamic content
                if (window.adsTracker) {
                    // Track specific ads programmatically
                    // window.adsTracker.manualTrackView('ads-id-here');

                    // Get real-time statistics
                    // window.adsTracker.getStatistics('ads-id-here').then(stats => {
                    //     console.log('Ads statistics:', stats);
                    // });
                }
            });

            // Example: For AJAX loaded content
            function trackNewlyLoadedAds() {
                if (window.adsTracker) {
                    // Re-initialize tracking untuk konten yang di-load via AJAX
                    window.adsTracker.setupViewTracking();
                    window.adsTracker.setupClickTracking();
                }
            }
        </script>
    @endpush
@endsection
