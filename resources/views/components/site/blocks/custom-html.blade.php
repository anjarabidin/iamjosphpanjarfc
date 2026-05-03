{{-- Custom HTML Block - For custom content --}}
@props(['block', 'data' => []])

@php
$config = $block->config ?? [];
$htmlContent = $config['html_content'] ?? '';
$cssClasses = $config['css_classes'] ?? '';
@endphp

@if(!empty($htmlContent))
<section class="py-8 {{ $cssClasses }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            {!! $htmlContent !!}
        </div>
    </div>
</section>
@endif
