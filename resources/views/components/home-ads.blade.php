<!-- Home Ads Implementation Example -->
<!-- File: resources/views/components/home-ads.blade.php -->

@props(['ads'])

<div class="home-ads-container">
    @foreach ($ads as $ad)
        <div class="ads-item mb-4" data-ads-id="{{ $ad->id }}" data-ads-link="{{ $ad->link }}">
            @if ($ad->media_type === 'video')
                <div class="ads-video-container position-relative">
                    <video class="w-100 rounded cursor-pointer" poster="{{ asset($ad->media_url) }}" controls
                        data-ads-id="{{ $ad->id }}">
                        <source src="{{ asset($ad->media_url) }}" type="video/mp4">
                        Video tidak didukung pada browser ini.
                    </video>

                    @if ($ad->link)
                        <div
                            class="ads-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                            <button class="btn btn-primary btn-lg" data-ads-id="{{ $ad->id }}"
                                data-ads-link="{{ $ad->link }}">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Kunjungi Situs
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="ads-image-container position-relative">
                    <img src="{{ asset($ad->media_url) }}" alt="Iklan" class="w-100 rounded cursor-pointer"
                        data-ads-id="{{ $ad->id }}" />

                    @if ($ad->link)
                        <div
                            class="ads-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 hover-opacity-100 transition-opacity">
                            <button class="btn btn-primary btn-lg" data-ads-id="{{ $ad->id }}"
                                data-ads-link="{{ $ad->link }}">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Kunjungi Situs
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Optional: Display basic stats for debugging -->
            @if (config('app.debug'))
                <div class="ads-debug mt-2 small text-muted">
                    ID: {{ $ad->id }} | Views: {{ $ad->total_view }} | Clicks: {{ $ad->total_click }}
                </div>
            @endif
        </div>
    @endforeach
</div>

<style>
    .ads-item {
        max-width: 100%;
        margin: 0 auto;
    }

    .ads-overlay {
        background: rgba(0, 0, 0, 0.7);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .ads-item:hover .ads-overlay {
        opacity: 1;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .transition-opacity {
        transition: opacity 0.3s ease;
    }

    .hover-opacity-100:hover {
        opacity: 1 !important;
    }
</style>

@push('scripts')
    <script>
        // Konfigurasi ADS Tracker
        window.ADS_TRACKER_CONFIG = {
            apiKey: '{{ config('app.api_key') }}', // Pastikan ini dikonfigurasi di config/app.php
            baseUrl: '{{ url('/api/v1') }}'
        };
    </script>
    <script src="{{ asset('js/ads-tracker.js') }}"></script>
@endpush
