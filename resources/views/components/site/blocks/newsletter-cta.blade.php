{{-- Newsletter CTA Block - Call-to-action for newsletter subscription --}}
@props(['block', 'data' => []])

@php
$config = $block->config ?? [];
$headline = $config['headline'] ?? 'Stay Updated';
$subheadline = $config['subheadline'] ?? 'Get the latest publications delivered to your inbox';
$buttonText = $config['button_text'] ?? 'Subscribe';
$background = $config['background'] ?? 'gradient';
@endphp

<section class="py-16 {{ $background === 'gradient' ? 'bg-gradient-to-r from-blue-600 to-indigo-600' : 'bg-blue-600' }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            {{ $headline }}
        </h2>
        <p class="text-lg text-blue-100 mb-8">
            {{ $subheadline }}
        </p>

        <form action="#" method="POST" class="max-w-xl mx-auto">
            @csrf
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="email" 
                       name="email" 
                       placeholder="Enter your email address"
                       required
                       class="flex-1 px-6 py-4 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-white/30">
                <button type="submit"
                        class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-colors shadow-lg">
                    {{ $buttonText }}
                </button>
            </div>
        </form>

        <p class="mt-4 text-sm text-blue-200">
            We respect your privacy. Unsubscribe at any time.
        </p>
    </div>
</section>
