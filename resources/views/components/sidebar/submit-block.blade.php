{{-- Submit Article Block - CTA for article submission --}}
@props(['journal', 'settings' => [], 'block' => null])

@php
$primaryColor = $settings['primary_color'] ?? '#4F46E5';
$secondaryColor = $settings['secondary_color'] ?? '#7C3AED';
@endphp

<div class="text-center">
    <p class="text-sm text-gray-600 mb-4">
        Ready to share your research with the world?
    </p>
    <a href="{{ route('journal.submissions.create', $journal->slug) }}"
        class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-white rounded-lg shadow-md hover:shadow-lg transition-all"
        style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
        <i class="fa-solid fa-paper-plane mr-2"></i>
        Submit Your Article
    </a>
    <p class="text-xs text-gray-400 mt-3">
        Open access • Peer-reviewed
    </p>
</div>